<?php

namespace App\Controller\Api;

use App\Entity\ChartProvider;
use App\Repository\ChartProviderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/providers', name: 'api_providers_')]
final class ChartProviderController extends AbstractController
{

    public function __construct(private readonly ChartProviderRepository $providerRepository) {}

    /**
     * Liste tous les providers actifs
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list() : JsonResponse
    {
        $provider = $this->providerRepository->findBy(
            ['active' => true],
            ['name' => 'ASC']
        );
        
        return $this->json($provider, Response::HTTP_OK, [], [
            'groups' => ['provider:read']
        ]);
    }

    /**
     * Détails d'un provider
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(ChartProvider $provider)
    {
        return $this->json($provider, Response::HTTP_OK, [], [
            'groups' => ['provider:read']
        ]);
    }

    /**
     * Liste les charts d'un provider
     */
    #[Route('/{id}/charts', name: 'charts', methods: ['GET'])]
    public function charts(ChartProvider $provider): JsonResponse
    {
        return $this->json($provider->getCharts(), Response::HTTP_OK, [], [
            'groups' => ['chart:read']
        ]);
    }
}
