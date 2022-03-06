<?php

namespace App\Service\Common;

use App\Entity\Club;
use App\Entity\Person;
use App\Entity\PersonClub;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;


class NotificationManager
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /** @var ContainerBagInterface */
    private $params; // we could set params at env file or a yaml file.

    private $email_template;

    public function __construct(MailerInterface $mailer, ContainerBagInterface $params)
    {
        $this->mailer = $mailer;
        $this->params = $params;
        $this->email_template = 'email/newMember.html.twig';
    }

    private function setUpMail(string $from, string $to, string $subject, array $data): Email
    {
        return (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($this->email_template)
            ->context([
                'data' => $data,
            ]);
    }


    private function emailNotification(?string $clubName, ?string $memberName, ?string $type, $subject): void
    {
        // If we have club or player or coach data we will send email to them

        // Set up email
        $email = $this->setUpMail(
            'testLaliga@laliga.com',
            'whoever@laliga.com',
            $subject,
            [
                'club' => $clubName,
                'member' => $memberName,
                'type' => $type
            ]);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            throw $exception;
        }

    }


    public function sendNotification(Club $club, PersonClub $clubMember): void
    {
        $memberName = $clubMember->getPerson()->getName(). ' ' .$clubMember->getPerson()->getSurname();
        $this->emailNotification($club->getName(), $memberName, $clubMember->getType(), 'Asignación de usuario');
    }

    public function sendRemoveNotification(Club $club, PersonClub $clubMember): void
    {
        $this->email_template = 'email/removeMember.html.twig';
        $memberName = $clubMember->getPerson()->getName(). ' ' .$clubMember->getPerson()->getSurname();
        $this->emailNotification($club->getName(), $memberName, $clubMember->getType(), 'Baja de usuario');
    }

    public function sendNewUserNotification(Person $user): void
    {
        $memberName = $user->getName(). ' ' .$user->getSurname();
        $this->emailNotification(null, $memberName, null, 'Alta de nuevo usuario');
    }

    public function sendNewClubNotification(Club $club): void
    {
        $clubName = $club->getName();
        $this->emailNotification($clubName, null, 'type_club', 'Alta de club');
    }

    /***
     * TODO: Implement notification for whatever way we want to send messages here.
     */
}
