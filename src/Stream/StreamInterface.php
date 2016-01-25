<?php

namespace Nijens\ProtocolStream\Stream;

/**
 * StreamInterface.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
interface StreamInterface
{
    /**
     * Returns the stream wrapper class name.
     *
     * @return string
     */
    public function getStreamWrapperClass();

    /**
     * Returns the protocol.
     *
     * @return string
     */
    public function getProtocol();

    /**
     * Returns the available paths.
     *
     * @return array
     */
    public function getPaths();

    /**
     * Returns true if the paths are writable.
     *
     * @return bool
     */
    public function isWritable();
}
