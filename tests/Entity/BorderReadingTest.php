<?php

namespace App\Tests\Entity;

use App\Entity\BorderReading;
use PHPUnit\Framework\TestCase;

class BorderReadingTest extends TestCase
{
    public function testStatusDependsOnDelay(): void
    {
        $reading = new BorderReading();

        $reading->setDelaySeconds(120);
        $this->assertSame('fluide', $reading->getStatus());

        $reading->setDelaySeconds(300);
        $this->assertSame('ralenti', $reading->getStatus());

        $reading->setDelaySeconds(900);
        $this->assertSame('bouche', $reading->getStatus());
    }

    public function testDelayIsRoundedToMinutes(): void
    {
        $reading = new BorderReading();
        $reading->setDelaySeconds(89);

        $this->assertSame(1, $reading->getDelayMinutes());
    }
}
