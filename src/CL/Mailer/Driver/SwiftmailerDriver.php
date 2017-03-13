<?php

declare(strict_types=1);

namespace CL\Mailer\Driver;

use CL\Mailer\Message\ResolvedMessage;
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
    public function send(ResolvedMessage $message): bool
    {
        $header = $message->getMessageHeader();
        $body = $message->getMessageBody();
        $swiftMailerMessage = new Swift_Message();

        $swiftMailerMessage->setSubject($message->getMessageHeader()->getSubject());

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

        foreach ($body->getParts() as $part) {
            $swiftMailerMessage->attach(Swift_MimePart::newInstance(
                $part->getContent(),
                $part->getContentType(),
                $part->getCharset()
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
