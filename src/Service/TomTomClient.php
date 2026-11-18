<?php

namespace App\Service;

use App\Entity\Border;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TomTomClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $tomtomApiKey,
    ) {
    }

    public function hasKey(): bool
    {
        return $this->tomtomApiKey !== '';
    }

    public function getTravelTimes(Border $border): ?array
    {
        if (!$this->hasKey()) {
            return null;
        }

        $points = sprintf(
            '%s,%s:%s,%s',
            $border->getStartLat(),
            $border->getStartLng(),
            $border->getEndLat(),
            $border->getEndLng()
        );

        try {
            $response = $this->httpClient->request('GET', 'https://api.tomtom.com/routing/1/calculateRoute/' . $points . '/json', [
                'query' => [
                    'key' => $this->tomtomApiKey,
                    'traffic' => 'true',
                    'computeTravelTimeFor' => 'all',
                    'travelMode' => 'car',
                ],
            ]);
            $data = $response->toArray();
        } catch (\Exception $e) {
            return null;
        }

        $summary = $data['routes'][0]['summary'] ?? null;
        if (!$summary) {
            return null;
        }

        return [
            'travel' => (int) $summary['travelTimeInSeconds'],
            'freeFlow' => (int) $summary['noTrafficTravelTimeInSeconds'],
        ];
    }
}
