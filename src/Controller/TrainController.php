<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\TrainClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TrainController extends AbstractController
{
    #[Route('/trains', name: 'train_index')]
    public function index(Request $request, TrainClient $trainClient): Response
    {
        $user = $this->getUser();
        $default = $user instanceof User && $user->getCommute() ? $user->getCommute()->getStationFrom() : 'Annemasse';

        $station = $request->query->get('gare', $default);
        if (!in_array($station, TrainClient::STATIONS, true)) {
            $station = 'Annemasse';
        }

        $trains = $trainClient->getNextTrains($station, 6);

        return $this->render('train/index.html.twig', [
            'stations' => TrainClient::STATIONS,
            'station' => $station,
            'arrival' => TrainClient::ARRIVAL,
            'next' => $trains[0] ?? null,
            'others' => array_slice($trains, 1),
        ]);
    }
}
