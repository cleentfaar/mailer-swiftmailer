<?php

declare(strict_types=1);

namespace CL\Mailer\Driver;

use CL\Mailer\ResolvedMessageInterface;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Swift_MimePart;

class SwiftmailerDriver implements DriverInterface
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @param Swift_Mailer $mailer
     */
    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @inheritdoc
     */
    public function send(ResolvedMessageInterface $message): bool
    {
        $header = $message->getHeader();
        $body = $message->getBody();
        $swiftMailerMessage = new Swift_Message();

        $swiftMailerMessage->setSubject($header->getSubject());

        foreach ($header->getFrom() as $recipient) {
            $swiftMailerMessage->addFrom($recipient->getEmail(), $recipient->getName());
        }

        foreach ($header->getTo() as $recipient) {
            $swiftMailerMessage->addTo($recipient->getEmail(), $recipient->getName());
        }

        foreach ($header->getCc() as $recipient) {
            $swiftMailerMessage->addCc($recipient->getEmail(), $recipient->getName());
        }

        foreach ($header->getBcc() as $recipient) {
            $swiftMailerMessage->addBcc($recipient->getEmail(), $recipient->getName());
        }

        foreach ($header->getReplyTo() as $recipient) {
            $swiftMailerMessage->addReplyTo($recipient->getEmail(), $recipient->getName());
        }

        $swiftMailerMessage->setBody(
            $body->getMainPart()->getContent(),
            $body->getMainPart()->getContentType(),
            $body->getMainPart()->getCharset()
        );

        if ($alternativePart = $body->getAlternativePart()) {
            $swiftMailerMessage->attach(Swift_MimePart::newInstance(
                $alternativePart->getContent(),
                $alternativePart->getContentType(),
                $alternativePart->getCharset()
            ));
        }

        foreach ($body->getAttachments() as $attachment) {
            $swiftMailerMessage->attach(Swift_Attachment::newInstance(
                $attachment->getData(),
                $attachment->getName(),
                $attachment->getContentType()
            ));
        }

        return $this->mailer->send($swiftMailerMessage) > 0;
    }
}
