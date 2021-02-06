<?php

namespace App\Controller;

use App\Entity\Chart;
use App\Form\ChartFormType;
use App\Manager\ChartManager;
use App\Repository\ChartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NavigateController extends AbstractController
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var ChartManager */
    protected $chartManager;

    /** @var ChartRepository */
    protected $chartRepo;

    /**
     * ChartController constructor.
     * @param EntityManagerInterface $em
     * @param ChartManager $chartManager
     */
    public function __construct(
        EntityManagerInterface $em,
        ChartManager $chartManager)
    {
        $this->em = $em;

        $this->chartManager = $chartManager;
        $this->chartRepo = $em->getRepository(Chart::class);
    }

    /**
     * Route vers la page d'accueil :
     *      - Formulaire de soumission de chart
     *      - En cas de formulaire valide, création de la Chart et des Entités associées,
     *        puis redirection vers la page de la Chart.
     *
     * @Route("/" , name="home")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
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
        $chartLisForHome = $this->chartRepo->findThreeLastChart();


        $renderParameters = [
            'chartForm' => $form->createView(),
            'charts'    => $chartLisForHome,
            ];

        $randomChart = $this->chartRepo->findRandomChart();
        if(!is_null($randomChart->getId()))
        {
            array_push($renderParameters, $randomChart);
        }
        // si le formulaire n'est pas soumis
        return $this->render('navigate/index.html.twig', $renderParameters);
    }

    /**
     * @Route("/blog" , name="blog")
     */
    public function blogAction(): Response
    {
        return $this->render('navigate/blog.html.twig');
    }

    /**
     * @Route("/contact" , name="contact")
     */
    public function contactAction(): Response
    {
        return $this->render('navigate/contact.html.twig');
    }

    /**
     * @Route("/about" , name="about")
     */
    public function aboutAction(): Response
    {
        return $this->render('navigate/about.html.twig');
    }
}
