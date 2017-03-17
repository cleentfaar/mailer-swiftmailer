<?php

declare(strict_types=1);

namespace CL\Mailer\Tests\Driver;

use CL\Mailer\Driver\SwiftmailerDriver;
use CL\Mailer\ResolvedMessageInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Swift_Mailer;
use Swift_Message;

class SwiftmailerDriverTest extends TestCase
{
    /**
     * @var ObjectProphecy|Swift_Mailer
     */
    private $swiftmailer;

    /**
     * @var SwiftmailerDriver
     */
    private $driver;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->swiftmailer = $this->prophesize(Swift_Mailer::class);
        $this->driver = new SwiftmailerDriver($this->swiftmailer->reveal());
    }

    /**
     * @test
     */
    public function it_can_send_a_swift_mailer_message()
    {
        $this->swiftmailer->send(Argument::type(Swift_Message::class))
            ->shouldBeCalledTimes(1)
        ;

        $message = $this->prophesize(ResolvedMessageInterface::class)->reveal();

        $this->driver->send($message);
    }
}
