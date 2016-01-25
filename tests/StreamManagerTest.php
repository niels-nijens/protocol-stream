<?php

namespace Nijens\ProtocolStream\Test;

use Nijens\ProtocolStream\StreamManager;
use PHPUnit_Framework_Error;
use PHPUnit_Framework_TestCase;
use ReflectionProperty;

/**
 * StreamManagerTest.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class StreamManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Unregister the 'test' stream wrapper.
     */
    public function setUp()
    {
        parent::setUp();

        $property = new ReflectionProperty('Nijens\ProtocolStream\StreamManager', 'streams');
        $property->setAccessible(true);
        $property->setValue(array());
        $property->setAccessible(false);

        @stream_wrapper_unregister('test');
    }

    /**
     * Tests if StreamManager::create creates a new StreamManager instance.
     */
    public function testCreate()
    {
        $this->assertInstanceOf('Nijens\ProtocolStream\StreamManager', StreamManager::create());
    }

    /**
     * Tests if StreamManager::registerStream registers a stream with the stream manager and registers the stream wrapper.
     *
     * @depends testCreate
     */
    public function testRegisterStream()
    {
        $streamMock = $this->getMockBuilder('Nijens\ProtocolStream\Stream\StreamInterface')->getMock();
        $streamMock->expects($this->once())
                ->method('getProtocol')
                ->willReturn('test');
        $streamMock->expects($this->once())
                ->method('getStreamWrapperClass')
                ->willReturn('Nijens\ProtocolStream\Wrapper\StreamWrapper');

        $streamManager = StreamManager::create();

        $this->assertInstanceOf('Nijens\ProtocolStream\StreamManager', $streamManager->registerStream($streamMock));
        $this->assertContains('test', stream_get_wrappers());
    }

    /**
     * Tests if calling StreamManager::registerStream twice for a stream instance with the same protocol triggers an error.
     *
     * @depends testRegisterStream
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testRegisterStreamTwiceTriggersError()
    {
        $streamMock = $this->getMockBuilder('Nijens\ProtocolStream\Stream\StreamInterface')->getMock();
        $streamMock->expects($this->exactly(2))
                ->method('getProtocol')
                ->willReturn('test');
        $streamMock->expects($this->exactly(2))
                ->method('getStreamWrapperClass')
                ->willReturn('Nijens\ProtocolStream\Wrapper\StreamWrapper');

        $streamManager = StreamManager::create();
        $streamManager->registerStream($streamMock)
                ->registerStream($streamMock);
    }

    /**
     * Tests if calling StreamManager::registerStream twice for a stream instance with the same protocol with replace argument does not trigger an error.
     *
     * @depends testRegisterStreamTwiceTriggersError
     */
    public function testRegisterStreamTwiceWithReplaceDoesNotTriggerError()
    {
        $streamMock = $this->getMockBuilder('Nijens\ProtocolStream\Stream\StreamInterface')->getMock();
        $streamMock->expects($this->exactly(2))
                ->method('getProtocol')
                ->willReturn('test');
        $streamMock->expects($this->exactly(2))
                ->method('getStreamWrapperClass')
                ->willReturn('Nijens\ProtocolStream\Wrapper\StreamWrapper');

        $streamManager = StreamManager::create();
        $streamManager->registerStream($streamMock)
                ->registerStream($streamMock, true);
    }

    /**
     * Tests StreamManager::unregisterStream unregisters the stream wrapper.
     *
     * @depends testRegisterStream
     */
    public function testUnregisterStream()
    {
        $streamMock = $this->getMockBuilder('Nijens\ProtocolStream\Stream\StreamInterface')->getMock();
        $streamMock->expects($this->once())
                ->method('getProtocol')
                ->willReturn('test');
        $streamMock->expects($this->once())
                ->method('getStreamWrapperClass')
                ->willReturn('Nijens\ProtocolStream\Wrapper\StreamWrapper');

        $streamManager = StreamManager::create();
        $streamManager->registerStream($streamMock)
                ->unregisterStream('test');

        $this->assertNotContains('test', stream_get_wrappers());
    }

    /**
     * Tests if StreamManager::getStream returns the StreamInterface instance registered for the protocol.
     */
    public function testGetStream()
    {
        $streamMock = $this->getMockBuilder('Nijens\ProtocolStream\Stream\StreamInterface')->getMock();
        $streamMock->expects($this->once())
                ->method('getProtocol')
                ->willReturn('test');
        $streamMock->expects($this->once())
                ->method('getStreamWrapperClass')
                ->willReturn('Nijens\ProtocolStream\Wrapper\StreamWrapper');

        $streamManager = StreamManager::create();
        $streamManager->registerStream($streamMock);

        $this->assertSame($streamMock, $streamManager->getStream('test'));
    }

    /**
     * Tests if StreamManager::getStream returns null when the stream manager does not have the protocol registered.
     */
    public function testGetStreamReturnsNullWhenNoStreamRegisteredForProtocol()
    {
        $this->assertNull(StreamManager::create()->getStream('test'));
    }
}
