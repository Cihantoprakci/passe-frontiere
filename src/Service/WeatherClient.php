<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherClient
{
    private const DESCRIPTIONS = [
        0 => 'ciel dégagé',
        1 => 'plutôt dégagé',
        2 => 'nuageux',
        3 => 'couvert',
        45 => 'brouillard',
        48 => 'brouillard givrant',
        51 => 'bruine',
        61 => 'pluie faible',
        63 => 'pluie',
        65 => 'forte pluie',
        71 => 'neige faible',
        73 => 'neige',
        75 => 'forte neige',
        80 => 'averses',
        95 => 'orage',
    ];

    public function __construct(
        private HttpClientInterface $httpClient,
        private CacheInterface $cache,
    ) {
    }

    public function getCurrent(): ?array
    {
        return $this->cache->get('weather_annemasse', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            try {
                $response = $this->httpClient->request('GET', 'https://api.open-meteo.com/v1/forecast', [
                    'query' => [
                        'latitude' => 46.19,
                        'longitude' => 6.24,
                        'current' => 'temperature_2m,weather_code',
                        'timezone' => 'Europe/Paris',
                    ],
                ]);
                $data = $response->toArray();
            } catch (\Exception $e) {
                $item->expiresAfter(60);

                return null;
            }

            $code = (int) $data['current']['weather_code'];

            return [
                'temperature' => (int) round($data['current']['temperature_2m']),
                'description' => self::DESCRIPTIONS[$code] ?? 'temps variable',
            ];
        });
    }
}
