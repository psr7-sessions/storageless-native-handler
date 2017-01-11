<?php

namespace PSR7SessionsHandlerTest;

use PSR7Session\Session\SessionInterface;
use PSR7SessionsHandler\NativeSessionHandler;

class NativeSessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NativeSessionHandler
     */
    private $sessionHandler;

    /**
     * @var SessionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionMock;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->sessionMock = $this->createMock(SessionInterface::class);

        $this->sessionHandler = new NativeSessionHandler($this->sessionMock);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @backupGlobals
     */
    public function it_should_save_data_in_session()
    {
        $this
            ->sessionMock
            ->expects($this->once())
            ->method('get')
            ->willReturn('');

        $this
            ->sessionMock
            ->expects($this->once())
            ->method('close');

        session_set_save_handler($this->sessionHandler);
        session_start();

        $_SESSION['name'] = 'heya';

    }
}
