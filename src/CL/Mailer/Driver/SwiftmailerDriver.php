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
        $swiftMailerMessage = new Swift_Message();

        if ($sender = $message->getSender()) {
            $swiftMailerMessage->setSender($sender->getEmail(), $sender->getName());
        }

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

        $parts = $message->getParts();

        if ($firstPart = array_shift($parts)) {
            $swiftMailerMessage->setBody(
                $firstPart->getContent(),
                $firstPart->getContentType(),
                $firstPart->getCharset()
            );
        }

        foreach ($parts as $part) {
            $swiftMailerMessage->attach(Swift_MimePart::newInstance(
                $part->getContent(),
                $part->getContentType(),
                $part->getCharset()
            ));
        }

        foreach ($message->getAttachments() as $attachment) {
            $swiftMailerMessage->attach(Swift_Attachment::newInstance(
                $attachment->getData(),
                $attachment->getName(),
                $attachment->getContentType()
            ));
        }

        return $this->mailer->send($swiftMailerMessage) > 0;
    }
}
