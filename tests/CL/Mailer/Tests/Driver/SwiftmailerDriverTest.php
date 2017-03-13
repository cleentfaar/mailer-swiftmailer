<?php

namespace CL\Mailer\Tests\Driver;

use CL\Mailer\Driver\SwiftmailerDriver;
use CL\Mailer\Message\Address;
use CL\Mailer\Message\MessageBody;
use CL\Mailer\Message\MessageBodyInterface;
use CL\Mailer\Message\MessageHeader;
use CL\Mailer\Message\MessageHeaderInterface;
use CL\Mailer\Message\Part\HtmlPart;
use CL\Mailer\Message\ResolvedMessage;
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

        $message = new ResolvedMessage(new MessageHeader(), new MessageBody());

        $this->driver->send($message);
    }
}