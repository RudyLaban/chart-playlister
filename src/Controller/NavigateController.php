<?php

namespace App\Controller;

use App\Form\ChartFormType;
use App\Manager\ChartManager;
use App\Manager\ChartSiteManager;
use App\Manager\ChartSongManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NavigateController extends AbstractController
{
    /**
     * @var ChartManager $chartManager
     */
    protected $chartManager;

    /**
     * @var ChartSiteManager $chartSiteManager
     */
    protected $chartSiteManager;

    /**
     * @var ChartSongManager
     */
    protected $chartSongManager;

    /**
     * ChartController constructor.
     * @param ChartManager $chartManager
     * @param ChartSiteManager $chartSiteManager
     * @param ChartSongManager $chartSongManager
     */
    public function __construct(ChartManager $chartManager, ChartSiteManager $chartSiteManager, ChartSongManager $chartSongManager){
        $this->chartManager = $chartManager;
        $this->chartSiteManager = $chartSiteManager;
        $this->chartSongManager = $chartSongManager;
    }

    /**
     * @Route("/home" , name="home")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response {

        // formulaire de soumissions d'url vers une chart
        $form = $this->createForm(ChartFormType::class);
        // récupération du formulaire
        $form->handleRequest($request);
        // le formulaire est soumis et valide
        if($form->isSubmitted() && $form->isValid()){
            // récupération de l'url soumis dans le formulaire
            $url = $form->getData()['url'];
            // crawle de la page pour récupérer le ChartSite
            $chartSiteElement = $this->chartSiteManager->crawlChartSite($url);
            // crawl de la page pour récupérer la Chart
            $chartElement = $this->chartManager->crawlChart($url);
            // crawl de la page de l'url soumis par le formulaire pour récupérer un liste de ChartsSong
            $chartSongsElements = $this->chartSongManager->dispatcher($url);
            // TODO : Mettre en place la recuperation et le tri des éléments nécessaire à la création de Artist et Song afin de pouvoir créer les ChartSong
            // si le crawl ne renvoi pas d'éléments
            if(empty($chartSiteElement) && empty($chartElement) && empty($chartSongsElements)) {
                $this->addFlash('warning', 'La playlist n\'a pas pu être analyser. Merci de laisser un commentaire pour que je puisse analyser ce cas particulier.');
            }
            else { // si le crawler renvoi bien des éléments
                // on crée en base le ChartSite si il n'existe pas
                $this->chartSiteManager->createChartSite($url, $chartSongsElements['chart_name']);
                // TODO : on crée en base la Chart si elle n'existe pas
                $this->chartManager->createChart($url, $chartElement);
                // TODO : on crée en base l'Artist' si il n'existe pas
                // TODO : on crée en base les Song de la chart si il n'existe pas
                // TODO : on crée en base les ChartSong si ils n'existent pas
                $this->chartSongManager->creatChartSongs($chartSongsElements);
                // si ils existent, ont met à jours le champs position
                $this->addFlash('success', 'La playlist a bien été créée !');
                return $this->render('chart/index.html.twig', [
                    'display_element' => $chartSongsElements,
                    'title' => 'Titre à définir mais ca à marché 😀',
                ]);
            }
        }

        return $this->render('navigate/index.html.twig', [
            'chartForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/playlist" , name="playlist")
     */
    public function playlistAction(): Response {

        return $this->render('navigate/episode.html.twig');
    }

    /**
     * @Route("/playlists" , name="playlists")
     */
    public function playlistsAction(): Response {

        return $this->render('navigate/episodes.html.twig');
    }

    /**
     * @Route("/blog" , name="blog")
     */
    public function blogAction(): Response {

        return $this->render('navigate/blog.html.twig');
    }

    /**
     * @Route("/contact" , name="contact")
     */
    public function contactAction(): Response {

        return $this->render('navigate/contact.html.twig');
    }

    /**
     * @Route("/about" , name="about")
     */
    public function aboutAction(): Response {

        return $this->render('navigate/about.html.twig');
    }

}
