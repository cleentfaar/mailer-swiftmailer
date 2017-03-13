<?php

declare(strict_types=1);

namespace CL\Mailer\Driver;

use CL\Mailer\Message\ResolvedMessage;

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
        $swiftMailerMessage = new Swift_Message();

        $swiftMailerMessage->setSubject($message->getSubject());

        foreach ($message->getFrom() as $recipient) {
            $swiftMailerMessage->addFrom($recipient->getEmail(), $recipient->getName());
        }

        foreach ($message->getTo() as $recipient) {
            $swiftMailerMessage->addTo($recipient->getEmail(), $recipient->getName());
        }

        foreach ($message->getCc() as $recipient) {
            $swiftMailerMessage->addCc($recipient->getEmail(), $recipient->getName());
        }

        foreach ($message->getBcc() as $recipient) {
            $swiftMailerMessage->addBcc($recipient->getEmail(), $recipient->getName());
        }

        foreach ($message->getReplyTo() as $recipient) {
            $swiftMailerMessage->addReplyTo($recipient->getEmail(), $recipient->getName());
        }

        foreach ($message->getParts() as $part) {
            $swiftMailerMessage->attach(Swift_MimePart::newInstance(
                $part->getContent(),
                $part->getContentType(),
                $part->getCharset()
            ));
        }

        foreach ($message->getAttachments() as $attachment) {
            $swiftMailerMessage->attach(Swift_Attachment::newInstance(
                $attachment->getData(),
                $attachment->getFilename(),
                $attachment->getContentType()
            ));
        }

        return $this->mailer->send($swiftMailerMessage) > 0;
    }
}
