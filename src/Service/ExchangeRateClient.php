<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRateClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private CacheInterface $cache,
    ) {
    }

    public function getEuroToFranc(): ?float
    {
        return $this->cache->get('rate_eur_chf', function (ItemInterface $item) {
            $item->expiresAfter(86400);

            try {
                $response = $this->httpClient->request('GET', 'https://api.frankfurter.dev/v1/latest', [
                    'query' => ['from' => 'EUR', 'to' => 'CHF'],
                ]);
                $data = $response->toArray();
            } catch (\Exception $e) {
                $item->expiresAfter(60);

                return null;
            }

            return (float) $data['rates']['CHF'];
        });
    }
}
