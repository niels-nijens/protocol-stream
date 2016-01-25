<?php

namespace Nijens\ProtocolStream\Stream;

/**
 * Stream.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class Stream implements StreamInterface
{
    /**
     * The protocol of the stream instance.
     *
     * @var string
     */
    private $protocol;

    /**
     * The paths available through this stream instance.
     *
     * @var array
     */
    private $paths;

    /**
     * Indication paths within this stream instance are writable.
     *
     * @var bool
     */
    private $writable;

    /**
     * Constructs a new Stream instance.
     *
     * @param string $protocol
     * @param array  $paths
     * @param bool   $writable
     */
    public function __construct($protocol, array $paths, $writable = true)
    {
        $this->protocol = $protocol;
        $this->paths = $paths;
        $this->writable = $writable;
    }

    /**
     * {@inheritdoc}
     */
    public function getStreamWrapperClass()
    {
        return 'Nijens\ProtocolStream\Wrapper\StreamWrapper';
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        return $this->writable;
    }
}
