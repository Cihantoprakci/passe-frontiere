<?php

namespace App\Tests\Service;

use App\Entity\Border;
use App\Entity\BorderReading;
use App\Repository\BorderRepository;
use App\Service\BorderReadingRecorder;
use App\Service\TomTomClient;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class BorderReadingRecorderTest extends TestCase
{
    public function testRecordsOneReadingPerBorder(): void
    {
        $moillesulaz = (new Border())->setName('Moillesulaz');
        $bardonnex = (new Border())->setName('Bardonnex');

        $repository = $this->createStub(BorderRepository::class);
        $repository->method('findActive')->willReturn([$moillesulaz, $bardonnex]);

        $tomTom = $this->createStub(TomTomClient::class);
        $tomTom->method('getTravelTimes')->willReturn(['travel' => 540, 'freeFlow' => 120]);

        $saved = [];
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(2))
            ->method('persist')
            ->willReturnCallback(function (BorderReading $reading) use (&$saved) {
                $saved[] = $reading;
            });
        $entityManager->expects($this->once())->method('flush');

        $recorder = new BorderReadingRecorder($repository, $tomTom, $entityManager);

        $this->assertSame(2, $recorder->recordAll());
        $this->assertSame($moillesulaz, $saved[0]->getBorder());
        $this->assertSame(420, $saved[0]->getDelaySeconds());
        $this->assertSame(7, $saved[0]->getDelayMinutes());
    }

    public function testSkipsBorderWhenTomTomFails(): void
    {
        $repository = $this->createStub(BorderRepository::class);
        $repository->method('findActive')->willReturn([new Border()]);

        $tomTom = $this->createStub(TomTomClient::class);
        $tomTom->method('getTravelTimes')->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())->method('persist');

        $recorder = new BorderReadingRecorder($repository, $tomTom, $entityManager);

        $this->assertSame(0, $recorder->recordAll());
    }

    public function testDelayIsNeverNegative(): void
    {
        $repository = $this->createStub(BorderRepository::class);
        $repository->method('findActive')->willReturn([new Border()]);

        $tomTom = $this->createStub(TomTomClient::class);
        $tomTom->method('getTravelTimes')->willReturn(['travel' => 100, 'freeFlow' => 130]);

        $saved = null;
        $entityManager = $this->createStub(EntityManagerInterface::class);
        $entityManager->method('persist')->willReturnCallback(function (BorderReading $reading) use (&$saved) {
            $saved = $reading;
        });

        $recorder = new BorderReadingRecorder($repository, $tomTom, $entityManager);
        $recorder->recordAll();

        $this->assertSame(0, $saved->getDelaySeconds());
    }
}
