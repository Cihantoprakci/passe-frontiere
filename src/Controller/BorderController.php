<?php

namespace App\Controller;

use App\Entity\Border;
use App\Repository\BorderReadingRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class BorderController extends AbstractController
{
    public const DAYS = [1 => 'lundi', 2 => 'mardi', 3 => 'mercredi', 4 => 'jeudi', 5 => 'vendredi', 6 => 'samedi', 7 => 'dimanche'];

    private const FIRST_HOUR = 5;

    private const LAST_HOUR = 21;

    private const BUSY_MINUTES = 10;

    #[Route('/douanes', name: 'border_index')]
    public function index(BorderReadingRepository $readingRepository): Response
    {
        $readings = $readingRepository->findLatestForAllBorders();

        return $this->render('border/index.html.twig', [
            'readings' => $readings,
            'fastest' => $readings[0] ?? null,
        ]);
    }

    #[Route('/douanes/{slug}', name: 'border_show')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])] Border $border,
        Request $request,
        BorderReadingRepository $readingRepository,
        ChartBuilderInterface $chartBuilder,
    ): Response {
        $day = $request->query->getInt('jour', (int) date('N'));
        if (!isset(self::DAYS[$day])) {
            $day = 1;
        }

        $averages = $readingRepository->findAverageDelayByHour($border, $day);
        $hours = [];
        for ($hour = self::FIRST_HOUR; $hour <= self::LAST_HOUR; $hour++) {
            $hours[$hour] = $averages[$hour] ?? 0;
        }

        return $this->render('border/show.html.twig', [
            'border' => $border,
            'reading' => $readingRepository->findLatest($border),
            'day' => $day,
            'days' => self::DAYS,
            'hasData' => count($averages) > 0,
            'busyRanges' => $this->getBusyRanges($hours),
            'bestHour' => $this->getBestMorningHour($hours),
            'chart' => $this->createChart($chartBuilder, $hours),
        ]);
    }

    private function getBusyRanges(array $hours): array
    {
        $ranges = [];
        $start = null;

        foreach ($hours as $hour => $minutes) {
            if ($minutes >= self::BUSY_MINUTES && $start === null) {
                $start = $hour;
            }

            if ($minutes < self::BUSY_MINUTES && $start !== null) {
                $ranges[] = [$start, $hour];
                $start = null;
            }
        }

        if ($start !== null) {
            $ranges[] = [$start, self::LAST_HOUR + 1];
        }

        return $ranges;
    }

    private function getBestMorningHour(array $hours): int
    {
        $best = self::FIRST_HOUR;

        for ($hour = self::FIRST_HOUR; $hour <= 9; $hour++) {
            if ($hours[$hour] < $hours[$best]) {
                $best = $hour;
            }
        }

        return $best;
    }

    private function createChart(ChartBuilderInterface $chartBuilder, array $hours): Chart
    {
        $labels = [];
        $colors = [];
        foreach ($hours as $hour => $minutes) {
            $labels[] = $hour . ' h';
            $colors[] = $minutes >= self::BUSY_MINUTES ? '#0f3a80' : '#1a5dc8';
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Retard moyen en minutes',
                    'data' => array_values($hours),
                    'backgroundColor' => $colors,
                    'borderRadius' => 4,
                ],
            ],
        ]);
        $chart->setOptions([
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 5],
                    'title' => ['display' => true, 'text' => 'minutes'],
                ],
                'x' => ['grid' => ['display' => false]],
            ],
        ]);

        return $chart;
    }
}
