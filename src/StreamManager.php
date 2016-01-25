<?php

namespace Nijens\ProtocolStream;

use Nijens\ProtocolStream\Stream\StreamInterface;

/**
 * StreamManager.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class StreamManager
{
    /**
     * The array with stream instances.
     *
     * @var StreamInterface[]
     */
    private static $streams = array();

    /**
     * Constructs a new stream manager instance.
     *
     * @return self
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Registers stream instance for a protocol.
     *
     * @param StreamInterface $stream
     * @param bool            $replaceWrapper
     *
     * @return self
     */
    public function registerStream(StreamInterface $stream, $replaceWrapper = false)
    {
        $protocol = $stream->getProtocol();
        if ($replaceWrapper === true && in_array($protocol, stream_get_wrappers())) {
            stream_wrapper_unregister($protocol);
        }

        if (stream_wrapper_register($protocol, $stream->getStreamWrapperClass())) {
            self::$streams[$protocol] = $stream;
        }

        return $this;
    }

    /**
     * Unregisters a stream instance by protocol.
     *
     * @param string $protocol
     *
     * @return self
     */
    public function unregisterStream($protocol)
    {
        if (isset(self::$streams[$protocol])) {
            $result = stream_wrapper_unregister($protocol);
            if ($result === true) {
                unset(self::$streams[$protocol]);
            }
        }

        return $this;
    }

    /**
     * Returns the stream instance by protocol.
     *
     * @param string $protocol
     *
     * @return StreamInterface|null
     */
    public function getStream($protocol)
    {
        if (isset(self::$streams[$protocol])) {
            return self::$streams[$protocol];
        }
    }
}
