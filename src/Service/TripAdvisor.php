<?php

namespace App\Service;

use App\Entity\BorderReading;

class TripAdvisor
{
    public const CAR_MINUTES = [
        'Annemasse' => 22,
        'Reignier' => 30,
        'La Roche-sur-Foron' => 40,
        'Thonon-les-Bains' => 45,
        'Évian-les-Bains' => 55,
    ];

    public function getCarMinutes(string $station, ?BorderReading $reading): int
    {
        $minutes = self::CAR_MINUTES[$station] ?? 30;

        if ($reading) {
            $minutes += $reading->getDelayMinutes();
        }

        return $minutes;
    }

    public function getTrainMinutes(?array $train): ?int
    {
        if (!$train) {
            return null;
        }

        return $train['minutes'] + $train['delay'];
    }

    public function compare(int $carMinutes, ?int $trainMinutes): array
    {
        if ($trainMinutes === null) {
            return ['best' => 'car', 'gain' => 0];
        }

        if ($trainMinutes <= $carMinutes) {
            return ['best' => 'train', 'gain' => $carMinutes - $trainMinutes];
        }

        return ['best' => 'car', 'gain' => $trainMinutes - $carMinutes];
    }
}
