<?php

namespace Nijens\ProtocolStream\Wrapper;

use Nijens\ProtocolStream\Stream\StreamInterface;
use Nijens\ProtocolStream\StreamManager;

/**
 * StreamWrapper.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class StreamWrapper implements StreamWrapperInterface
{
    /**
     * The resource context.
     *
     * @var resource
     */
    public $context;

    /**
     * The stream manager instance.
     *
     * @var StreamManager
     */
    private $streamManager;

    /**
     * Constructs a new StreamWrapper instance.
     */
    public function __construct()
    {
        $this->streamManager = StreamManager::create();
    }

    /**
     * {@inheritdoc}
     */
    public function dir_opendir($path, $options)
    {
        $this->context = opendir($this->getPath($path));

        return is_resource($this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function dir_readdir()
    {
        return readdir($this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function dir_rewinddir()
    {
        $result = is_resource($this->context);
        rewinddir($this->context);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function dir_closedir()
    {
        $result = is_resource($this->context);
        closedir($this->context);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function mkdir($path, $mode, $options)
    {
        if ($this->isStreamWritable($path) === false) {
            return false;
        }

        $recursive = (($options & STREAM_MKDIR_RECURSIVE) > 0);

        return mkdir($this->getPath($path), $mode, $recursive);
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path_from, $path_to)
    {
        if ($this->isStreamWritable($path_from) === false) {
            return false;
        }

        return rename(static::getPath($path_from), static::getPath($path_to));
    }

    /**
     * {@inheritdoc}
     */
    public function rmdir($path, $options)
    {
        if ($this->isStreamWritable($path) === false) {
            return false;
        }

        return rmdir($this->getPath($path));
    }

    /**
     * {@inheritdoc}
     */
    public function stream_cast($cast_as)
    {
        if (is_resource($this->context)) {
            return $this->context;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function stream_close()
    {
        return fclose($this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_eof()
    {
        return feof($this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_flush()
    {
        return fflush($this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_lock($operation)
    {
        return flock($this->context, $operation);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_metadata($path, $option, $value)
    {
        if ($this->isStreamWritable($path) === false) {
            return false;
        }

        switch ($option) {
            case STREAM_META_ACCESS:
                return chmod($this->getPath($path), $value);
            case STREAM_META_GROUP:
            case STREAM_META_GROUP_NAME:
                return chgrp($this->getPath($path), $value);
            case STREAM_META_OWNER:
            case STREAM_META_OWNER_NAME:
                return chown($this->getPath($path), $value);
            case STREAM_META_TOUCH:
                array_unshift($value, $this->getPath($path));
                return call_user_func_array('touch', $value);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function stream_open($path, $mode, $options, & $opened_path)
    {
        $use_path = (($options & STREAM_USE_PATH) > 0);

        if ($this->isStreamWritable($path) === false && strpos($mode, 'w') !== false) {
            return false;
        }

        $this->context = fopen($this->getPath($path), $mode, false);
        if ($this->context !== false && $use_path) {
            $opened_path = $this->getPath($path);
        }

        return ($this->context !== false);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_read($count)
    {
        return fread($this->context, $count);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->context, $offset, $whence);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_set_option($option, $arg1, $arg2)
    {
        // Not implemented.
    }

    /**
     * {@inheritdoc}
     */
    public function stream_stat()
    {
        return fstat($this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_tell()
    {
        return ftell($this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_truncate($new_size)
    {
        return ftruncate($this->context, $new_size);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_write($data)
    {
        return fwrite($this->context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function unlink($path)
    {
        if ($this->isStreamWritable($path) === false) {
            return false;
        }

        return unlink($this->getPath($path));
    }

    /**
     * {@inheritdoc}
     */
    public function url_stat($path, $flags)
    {
        return stat($this->getPath($path));
    }

    /**
     * Returns the stream instance by the uri scheme in path.
     *
     * @param string $path
     *
     * @return StreamInterface
     */
    private function getStreamByPathScheme($path)
    {
        $scheme = parse_url($path, PHP_URL_SCHEME);

        return $this->streamManager->getStream($scheme);
    }

    /**
     * Returns true if the stream for the path is writable.
     *
     * @param string $path
     *
     * @return bool
     */
    private function isStreamWritable($path)
    {
        $stream = $this->getStreamByPathScheme($path);
        if ($stream instanceof StreamInterface) {
            return $stream->isWritable();
        }

        return false;
    }

    /**
     * Returns the real path.
     *
     * @param string $path
     *
     * @return string
     */
    private function getPath($path)
    {
        $uri = parse_url($path);

        $stream = $this->getStreamByPathScheme($path);
        if ($stream instanceof StreamInterface) {
            foreach ($stream->getPaths() as $streamPrefix => $streamPath) {
                $realPath = $this->getRealPath($uri, $streamPrefix, $streamPath);
                if (strpos($realPath, $streamPath) === 0) {
                    return $realPath;
                }
            }
        }
    }

    /**
     * Returns the real path.
     *
     * @param array      $uri
     * @param string|int $streamPrefix
     * @param string     $streamPath
     *
     * @return string
     */
    private function getRealPath(array $uri, $streamPrefix, $streamPath)
    {
        $path = $streamPath;
        if (is_int($streamPrefix)) {
            $path .= '/'.$uri['host'];
        }
        if (isset($uri['path'])) {
            $path .= $uri['path'];
        }

        $realParts = array();

        $path = preg_replace('/\/+/', '/', str_replace('\\', '/', $path));
        $pathParts = explode('/', $path);
        foreach ($pathParts as $pathPart) {
            if ($pathPart === '..') {
                $realPart = array_pop($realParts);
                if ($realPart !== null && $realPart !== '..') {
                    continue;
                }
                if ($realPart === '..') {
                    $realParts[] = $realPart;
                }
            }
            if ($pathPart !== '.') {
                $realParts[] = $pathPart;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $realParts);
    }
}
