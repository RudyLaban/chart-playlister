<?php

namespace App\Controller;

use App\Entity\Chart;
use App\Form\ChartFormType;
use App\Manager\ChartManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChartScanneController extends AbstractController
{
    /** @var EntityManagerInterface $em */
    private $em;
    /** @var ChartManager $chartManager */
    private $chartManager;


    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, ChartManager $chartManager)
    {
        $this->em = $em;
        $this->chartManager = $chartManager;
    }

    /**
     * @Route("/chart/scanne", name="chart_scanne")
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        // formulaire de soumissions d'url vers une chart
        $form = $this->createForm(ChartFormType::class);
        // récupération du formulaire
        $form->handleRequest($request);
        // le formulaire est soumis et valide
        if($form->isSubmitted() && $form->isValid())
        {
            $chartFormTraitement = $this->chartManager->chartFormTraitement($form);
            // le traitement du formulaire a été correctement effectué
            if ($chartFormTraitement)
            {
                $this->addFlash('success', 'La playlist a bien été créée !');
                return $this->redirectToRoute('show_chart', [
                    'chartSiteId' => $chartFormTraitement['chart_site'],
                    'chartId' => $chartFormTraitement['chart'],
                ]);
            }
            else // le traitement du formulaire n'a pas été correctement effectué
            {
                $this->addFlash('warning', 'La playlist n\'a pas pu être analyser. Merci de laisser un commentaire pour que je puisse analyser ce cas particulier.');
            }
        }

        // Récupération des 3 Chart à afficher en page d'accueil
        $threeLastChart = $this->em->getRepository(Chart::class)->findThreeLastChart();

        return $this->render('chart_scanne/index.html.twig', [
            'three_last_chart' => $threeLastChart,
            'chartForm' => $form->createView(),
        ]);
    }
}
