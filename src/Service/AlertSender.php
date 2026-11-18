<?php

namespace App\Service;

use App\Entity\Alert;
use App\Repository\AlertRepository;
use App\Repository\BorderReadingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class AlertSender
{
    public function __construct(
        private AlertRepository $alertRepository,
        private BorderReadingRepository $readingRepository,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager,
        private string $mailerFrom,
    ) {
    }

    public function sendDueAlerts(): int
    {
        $sent = 0;
        $now = new \DateTimeImmutable();

        foreach ($this->alertRepository->findActive() as $alert) {
            if (!$this->isInWindow($alert, $now) || !$this->canSendAgain($alert, $now)) {
                continue;
            }

            $reading = $this->readingRepository->findLatest($alert->getBorder());
            if (!$reading || $reading->getDelayMinutes() < $alert->getThresholdMinutes()) {
                continue;
            }

            $email = (new TemplatedEmail())
                ->from($this->mailerFrom)
                ->to($alert->getUser()->getEmail())
                ->subject('Ça bouchonne à ' . $alert->getBorder()->getName())
                ->htmlTemplate('emails/alert.html.twig')
                ->context(['alert' => $alert, 'reading' => $reading]);

            $this->mailer->send($email);
            $alert->setLastSentAt($now);
            $sent++;
        }

        $this->entityManager->flush();

        return $sent;
    }

    private function isInWindow(Alert $alert, \DateTimeImmutable $now): bool
    {
        $time = $now->format('H:i');

        return $time >= $alert->getWindowStart()->format('H:i') && $time <= $alert->getWindowEnd()->format('H:i');
    }

    private function canSendAgain(Alert $alert, \DateTimeImmutable $now): bool
    {
        if (!$alert->getLastSentAt()) {
            return true;
        }

        return $alert->getLastSentAt() < $now->modify('-1 hour');
    }
}
