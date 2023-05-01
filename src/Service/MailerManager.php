<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerManager
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function notifyOwnersOnOrderCompleted(User $owner, array $products)
    {
        $email = (new TemplatedEmail())
            ->to($owner->getEmail())
            ->subject('Order completed')
            ->htmlTemplate('emails/order_completed.html.twig')
            ->context([
                'owner' => $owner,
                'products' => $products,
            ]);

        $this->mailer->send($email);
    }
}
