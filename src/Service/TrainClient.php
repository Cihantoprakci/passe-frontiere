<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TrainClient
{
    public const STATIONS = ['Annemasse', 'Reignier', 'La Roche-sur-Foron', 'Thonon-les-Bains', 'Évian-les-Bains'];

    public const ARRIVAL = 'Genève-Cornavin';

    public function __construct(
        private HttpClientInterface $httpClient,
        private CacheInterface $cache,
    ) {
    }

    public function getNextTrains(string $station, int $limit = 6): array
    {
        $key = 'trains_' . md5($station);

        return $this->cache->get($key, function (ItemInterface $item) use ($station, $limit) {
            $item->expiresAfter(60);

            try {
                $response = $this->httpClient->request('GET', 'https://transport.opendata.ch/v1/connections', [
                    'query' => [
                        'from' => $station,
                        'to' => self::ARRIVAL,
                        'limit' => $limit,
                    ],
                ]);
                $data = $response->toArray();
            } catch (\Exception $e) {
                $item->expiresAfter(10);

                return [];
            }

            $trains = [];
            foreach ($data['connections'] ?? [] as $connection) {
                $section = $connection['sections'][0] ?? null;
                if (!$section || empty($section['journey'])) {
                    continue;
                }

                $line = $section['journey']['number'];
                if (!str_starts_with($line, 'L')) {
                    $line = $section['journey']['category'] . ' ' . $line;
                }

                $trains[] = [
                    'departure' => new \DateTimeImmutable($connection['from']['departure']),
                    'arrival' => new \DateTimeImmutable($connection['to']['arrival']),
                    'line' => $line,
                    'direction' => $section['journey']['to'],
                    'platform' => $connection['from']['platform'],
                    'delay' => (int) ($connection['from']['delay'] ?? 0),
                    'minutes' => $this->durationInMinutes($connection['duration']),
                ];
            }

            return $trains;
        });
    }

    private function durationInMinutes(string $duration): int
    {
        preg_match('/(\d+)d(\d+):(\d+):(\d+)/', $duration, $parts);

        if (!$parts) {
            return 0;
        }

        return (int) $parts[1] * 1440 + (int) $parts[2] * 60 + (int) $parts[3];
    }
}
