<?php

namespace App\Tests\Service;

use App\Entity\BorderReading;
use App\Service\TripAdvisor;
use PHPUnit\Framework\TestCase;

class TripAdvisorTest extends TestCase
{
    public function testCarMinutesWithoutReading(): void
    {
        $advisor = new TripAdvisor();

        $this->assertSame(22, $advisor->getCarMinutes('Annemasse', null));
    }

    public function testCarMinutesAddsBorderDelay(): void
    {
        $advisor = new TripAdvisor();
        $reading = new BorderReading();
        $reading->setDelaySeconds(600);

        $this->assertSame(32, $advisor->getCarMinutes('Annemasse', $reading));
    }

    public function testCarMinutesForUnknownStation(): void
    {
        $advisor = new TripAdvisor();

        $this->assertSame(30, $advisor->getCarMinutes('Lyon', null));
    }

    public function testTrainMinutesAddsTrainDelay(): void
    {
        $advisor = new TripAdvisor();

        $this->assertSame(26, $advisor->getTrainMinutes(['minutes' => 23, 'delay' => 3]));
        $this->assertNull($advisor->getTrainMinutes(null));
    }

    public function testTrainIsBestWhenFaster(): void
    {
        $advisor = new TripAdvisor();

        $this->assertSame(['best' => 'train', 'gain' => 5], $advisor->compare(28, 23));
    }

    public function testCarIsBestWhenFaster(): void
    {
        $advisor = new TripAdvisor();

        $this->assertSame(['best' => 'car', 'gain' => 8], $advisor->compare(22, 30));
    }

    public function testCarIsBestWhenNoTrain(): void
    {
        $advisor = new TripAdvisor();

        $this->assertSame(['best' => 'car', 'gain' => 0], $advisor->compare(22, null));
    }
}
