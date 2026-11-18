<?php

namespace App\Controller;

use App\Service\AlertSender;
use App\Service\BorderReadingRecorder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CronController extends AbstractController
{
    #[Route('/cron/releve', name: 'cron_record', methods: ['GET'])]
    public function record(Request $request, BorderReadingRecorder $recorder, AlertSender $alertSender, string $cronToken): JsonResponse
    {
        $token = (string) $request->query->get('token');

        if ($cronToken === '' || !hash_equals($cronToken, $token)) {
            return $this->json(['error' => 'Accès refusé'], 403);
        }

        return $this->json([
            'readings' => $recorder->recordAll(),
            'alerts' => $alertSender->sendDueAlerts(),
        ]);
    }
}
