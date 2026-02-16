<?php

namespace App\Controller\Api;

use App\Entity\Chart;
use App\Repository\ChartEntryRepository;
use App\Repository\ChartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/charts', name: 'api_charts_')]
final class ChartController extends AbstractController
{
    public function __construct(
        private readonly ChartRepository $chartRepository,
        private readonly ChartEntryRepository $entryRepository
    ) {}

    /**
     * Liste tous les charts
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $charts = $this->chartRepository->findAll();
        return $this->json($charts, Response::HTTP_OK, [], [
            'groups' => ['chart:read']
        ]);
    }

    /**
     * Détails d'un chart
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Chart $chart): JsonResponse 
    {
        return $this->json($chart, Response::HTTP_OK, [], [
            'groups' => ['chart:read', 'provider:read']
        ]);
    }

    /**
     * Liste les entries d'un chart
     * Paramètres optionnels :
     * - date (YYYY-MM-DD) : filtre par snapshot date
     * - limit : nombre max de résultats
     */
    #[Route('/{id}/entries', name: 'entries', methods: ['GET'])]
    public function entries(Chart $chart, Request $request): JsonResponse
    {
        $date = $request->query->get('date');
        $limit = $request->query->getInt('limit', 100);
        $criteria = ['chart' => $chart];

        $snapshotDate = null;

        // Si une date est fournie, filtrer par snapshotDate
        if ($date) {
            try {
                $snapshotDate = new \DateTimeImmutable($date);
                $criteria['snapshotDate'] = $snapshotDate;
            } catch (\Exception $e) {
                return $this->json([
                    'error' => 'Format de date invalide. Utiliser le format YYYY-MM-DD.'
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $entries = $this->entryRepository->findByChartAndDate($chart, $snapshotDate, $limit);

        return $this->json($entries, Response::HTTP_OK, [], [
            'groups' => ['entry:read', 'song:read', 'artist:read']
        ]);
    }
}
