<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Chart;
use App\Entity\Playlist;
use App\Entity\Song;
use App\Form\ChartFormType;
use App\Manager\ChartManager;
use App\Repository\ArtistRepository;
use App\Repository\ChartRepository;
use App\Repository\PlaylistRepository;
use App\Repository\SongRepository;
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
    /** @var ArtistRepository */
    protected $artistRepo;
    /**
     * @var PlaylistRepository
     */
    protected $playlistRepo;

    /** @var SongRepository */
    protected $songRepo;

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

        $this->artistRepo = $em->getRepository(Artist::class);
        $this->chartRepo = $em->getRepository(Chart::class);
        $this->songRepo = $em->getRepository(Song::class);
        $this->playlistRepo = $em->getRepository(Playlist::class);
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
        $chartListForHome = $this->chartRepo->findThreeLastChart();


        $renderParameters = [
            'chartForm' => $form->createView(),
            'chart_list_for_home'    => $chartListForHome,
        ];

        $randomChart = $this->chartRepo->findRandomChart();
        if(!is_null($randomChart->getId()))
        {
            $renderParameters['randomChart'] = $randomChart;
        }
        // si le formulaire n'est pas soumis
        return $this->render('navigate/index.html.twig', $renderParameters);
    }

    /**
     * @Route("/blog" , name="blog")
     */
    public function blogAction(): Response
    {
        // Récupération des 3 Chart à afficher en page d'accueil
        $chartListForHome = $this->chartRepo->findThreeLastChart();

        return $this->render('navigate/blog.html.twig', [
            'chart_list_for_home'    => $chartListForHome,
        ]);
    }

    /**
     * @Route("/contact" , name="contact")
     */
    public function contactAction(): Response
    {
        // Récupération des 3 Chart à afficher en page d'accueil
        $chartListForHome = $this->chartRepo->findThreeLastChart();

        return $this->render('navigate/contact.html.twig', [
            'chart_list_for_home'    => $chartListForHome,
        ]);
    }

    /**
     * @Route("/about" , name="about")
     */
    public function aboutAction(): Response
    {
        // compteur pour les Fun Facts
        $artistCount = count($this->artistRepo->findAll());
        $playlistCount = count($this->playlistRepo->findAll());
        $songCount = count($this->songRepo->findAll());
        $chartCount = count($this->chartRepo->findAll());

        // Récupération des 3 Chart à afficher en page d'accueil
        $chartListForHome = $this->chartRepo->findThreeLastChart();


        return $this->render('navigate/about.html.twig', [
            'artist_count' => $artistCount,
            'playlist_count' => $playlistCount,
            'song_count' => $songCount,
            'chart_count' => $chartCount,
            'chart_list_for_home' => $chartListForHome
        ]);
    }
}
