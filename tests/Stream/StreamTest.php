<?php

namespace Nijens\ProtocolStream\Test\Stream;

use Nijens\ProtocolStream\Stream\Stream;
use PHPUnit_Framework_TestCase;

/**
 * StreamTest.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class StreamTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests if constructing a new Stream instance sets the properties.
     */
    public function testConstruct()
    {
        $stream = new Stream('test', array(), true);

        $this->assertAttributeSame('test', 'protocol', $stream);
        $this->assertAttributeSame(array(), 'paths', $stream);
        $this->assertAttributeSame(true, 'writable', $stream);
    }

    /**
     * Tests if Stream::getStreamWrapperClass returns a class name implementing the StreamWrapperInterface.
     */
    public function testGetStreamWrapperClass()
    {
        $stream = new Stream('test', array());

        $this->assertContains('Nijens\ProtocolStream\Wrapper\StreamWrapperInterface', class_implements($stream->getStreamWrapperClass()));
    }

    /**
     * Tests if Stream::getProtocol returns the property value.
     */
    public function testGetProtocol()
    {
        $stream = new Stream('test', array());

        $this->assertSame('test', $stream->getProtocol());
    }

    /**
     * Tests if Stream::getPaths returns the property value.
     */
    public function testGetPaths()
    {
        $stream = new Stream('test', array());

        $this->assertSame(array(), $stream->getPaths());
    }

    /**
     * Tests if Stream::isWritable returns the property value.
     */
    public function testIsWritable()
    {
        $stream = new Stream('test', array(), true);

        $this->assertSame(true, $stream->isWritable());
    }
}
