<?php

namespace App\Controller;

use App\Form\ChartFormType;
use App\Manager\ArtistManager;
use App\Manager\ChartManager;
use App\Manager\ChartSiteManager;
use App\Manager\ChartSongManager;
use App\Manager\SongManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NavigateController extends AbstractController
{
    /** @var ChartManager */
    protected $chartManager;

    /** @var ChartSiteManager */
    protected $chartSiteManager;

    /** @var ChartSongManager */
    protected $chartSongManager;

    /** @var ArtistManager */
    protected $artistManager;

    /** @var SongManager */
    protected $songManager;

    /**
     * ChartController constructor.
     * @param ChartManager $chartManager
     * @param ChartSiteManager $chartSiteManager
     * @param ChartSongManager $chartSongManager
     * @param ArtistManager $artistManager
     * @param SongManager $songManager
     */
    public function __construct(
        ChartManager $chartManager,
        ChartSiteManager $chartSiteManager,
        ChartSongManager $chartSongManager,
        ArtistManager $artistManager,
        SongManager $songManager)
    {
        $this->chartManager = $chartManager;
        $this->chartSiteManager = $chartSiteManager;
        $this->chartSongManager = $chartSongManager;
        $this->artistManager = $artistManager;
        $this->songManager = $songManager;
    }

    /**
     * Route vers la page d'accueil :
     *      - Formulaire de soumission de chart
     *      - En cas de formulaire valide, création de la Chart et des Entités associées,
     *        puis redirection vers la page de la Chart.
     *
     * @Route("/home" , name="home")
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
            // récupération de l'url soumis dans le formulaire
            $url = $form->getData()['url'];
            // crawle de la page pour récupérer le site de la chart
            $chartSiteElement = $this->chartSiteManager->crawlChartSite($url);
            // crawl de la page pour récupérer la chart
            $chartElement = $this->chartManager->crawlChart($url);
            // crawl de la page de l'url soumis par le formulaire pour récupérer un liste de ChartsSong
            $chartSongsElements = $this->chartSongManager->dispatcher($url);
            // récupération de la liste des artistes de la chart
            $artistsElementsList = $this->artistManager->artistListFormatter($chartSongsElements);
            // si le crawl ne renvoi pas d'éléments
            if(empty($chartSiteElement) || empty($chartElement) || empty($chartSongsElements ) || empty($artistsElementsList))
            {
                $this->addFlash('warning', 'La playlist n\'a pas pu être analyser. Merci de laisser un commentaire pour que je puisse analyser ce cas particulier.');
            }
            else { // si le crawler renvoi bien des éléments
                // on crée en base le ChartSite si il n'existe pas
                $chartSite = $this->chartSiteManager->createChartSite($chartSiteElement['chart_site_url'], $chartSiteElement['chart_site_name']);
                // on crée en base la Chart si elle n'existe pas
                $chart = $this->chartManager->createChart($chartElement, $chartSite);
                // on crée en base les Artist de la Chart si ils n'existent pas
                $this->artistManager->createArtistsOfChart($artistsElementsList);
                // on crée en base les Song de la Chart si ils n'existent pas
                $this->songManager->createSongsOfChart($chartSongsElements);
                // on crée en base les ChartSong si ils n'existent pas
                // si ils existent, ont met à jours le champs position
                $this->chartSongManager->createChartSongs($chartSongsElements, $chart);
                // redirection vers la page de la chart créée avec le flash message qui pete sec
                $this->addFlash('success', 'La playlist a bien été créée !');
                return $this->redirectToRoute('show_chart', ['chartSiteId' => $chartSite->getId(), 'chartId' => $chart->getId()]);
            }
        }

        return $this->render('navigate/index.html.twig', [
            'chartForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/playlists" , name="playlists")
     */
    public function playlistsAction(): Response
    {
        return $this->render('navigate/episodes.html.twig');
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
