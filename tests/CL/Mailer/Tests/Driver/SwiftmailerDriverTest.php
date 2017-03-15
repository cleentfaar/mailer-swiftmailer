<?php

declare(strict_types=1);

namespace CL\Mailer\Tests\Driver;

use CL\Mailer\Driver\SwiftmailerDriver;
use CL\Mailer\Message\Body;
use CL\Mailer\Message\Header;
use CL\Mailer\ResolvedMessage;
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

        $body = new Body();
        $body->setMainPart(new Body\Part\HtmlPart('<strong>Hello, world!</strong>'));

        $message = ResolvedMessage::fromHeaderAndBody(new Header(), $body);

        $this->driver->send($message);
    }
}
