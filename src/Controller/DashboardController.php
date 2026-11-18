<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BorderReadingRepository;
use App\Repository\BorderRepository;
use App\Service\ExchangeRateClient;
use App\Service\TrainClient;
use App\Service\TripAdvisor;
use App\Service\WeatherClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(
        BorderRepository $borderRepository,
        BorderReadingRepository $readingRepository,
        TrainClient $trainClient,
        WeatherClient $weatherClient,
        ExchangeRateClient $exchangeRateClient,
        TripAdvisor $tripAdvisor,
    ): Response {
        $user = $this->getUser();
        $commute = $user instanceof User ? $user->getCommute() : null;

        $station = $commute ? $commute->getStationFrom() : 'Annemasse';
        $border = $commute?->getBorder() ?? $borderRepository->findOneBy(['slug' => 'moillesulaz']);
        $reading = $border ? $readingRepository->findLatest($border) : null;

        $trains = $trainClient->getNextTrains($station, 4);
        $train = $trains[0] ?? null;

        $carMinutes = $tripAdvisor->getCarMinutes($station, $reading);
        $trainMinutes = $tripAdvisor->getTrainMinutes($train);

        return $this->render('dashboard/index.html.twig', [
            'station' => $station,
            'arrival' => TrainClient::ARRIVAL,
            'leaveAt' => $commute?->getLeaveAt(),
            'border' => $border,
            'reading' => $reading,
            'train' => $train,
            'carMinutes' => $carMinutes,
            'trainMinutes' => $trainMinutes,
            'advice' => $tripAdvisor->compare($carMinutes, $trainMinutes),
            'alert' => $user instanceof User ? $user->getAlerts()->first() : null,
            'weather' => $weatherClient->getCurrent(),
            'rate' => $exchangeRateClient->getEuroToFranc(),
        ]);
    }
}
