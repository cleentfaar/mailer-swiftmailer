<?php

namespace CL\Mailer\Tests\Driver;

use CL\Mailer\Driver\SwiftmailerDriver;
use CL\Mailer\Message\Address;
use CL\Mailer\Message\MessageBuilder;
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
            ->willReturn(true)
        ;

        $message = new ResolvedMessage(
            [new Address('from@example.com', 'John From')],
            [new Address('to@example.com', 'John To')],
            'Happy birthday!',
            [],
            [],
            [],
            [new HtmlPart('<p>Sorry I could not make it!</p>', 'utf-8')],
            []
        );

        $this->assertTrue($this->driver->send($message));
    }
}