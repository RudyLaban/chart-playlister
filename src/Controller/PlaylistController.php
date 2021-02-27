<?php

namespace App\Controller;

use App\Entity\Chart;
use App\Entity\StreamingSite;
use App\Manager\PlaylistManager;
use App\Manager\SpotifyManager;
use App\Repository\StreamingSiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use SpotifyWebAPI;

class PlaylistController extends AbstractController
{
    /** @var SpotifyManager */
    private $spotifyManager;
    /** @var PlaylistManager */
    private $playlistManager;

    public function __construct(SpotifyManager $spotifyManager, PlaylistManager $playlistManager)
    {
        $this->spotifyManager = $spotifyManager;
        $this->playlistManager = $playlistManager;
    }
//    /**
//     * @Route("/playlist", name="playlist")
//     */
//    public function index(): Response
//    {
//        return $this->render('playlist/index.html.twig', [
//            'controller_name' => 'PlaylistController',
//        ]);
//    }

    /**
     * Création de la playlist d'une Chart (Spotify)
     *
     * @Route("/create_playlist_s/{chart}", name="create_playlist_s")
     * @param SessionInterface $session
     * @param Chart $chart
     * @return Response
     */
    public function createSpotifyPlaylist(SessionInterface $session, Chart $chart): Response
    {
        if (!$chart)
        {
            $this->addFlash('warning', 'La Chart n\'a pas été trouvée.');
            return $this->redirectToRoute('home');
        }

        // todo-rla : mettre en place un service permettant d'utiliser SpotifyWebAPI n'importe ou sans trop de code
        $accessToken = $session->get('accessToken');
        if( ! $accessToken )
        {
            $session->getFlashBag()->add('error', 'Accès refusé. Veuillez réessayer.');
            return $this->redirectToRoute('login');
        }

        $playlist = null;

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        $spotifyTracks = $this->spotifyManager->getSpotifyTracks($api, $chart);
        // todo-rla: si $spotifyTracks ne renvoit pas d'élement, stopper la création, renvoyer sur la page de depart
        if (!is_null($spotifyTracks) && !empty($spotifyTracks))
        {
            $spotify = $this->spotifyManager->create();
            $playlist = $this->playlistManager->spotifyPlaylistBuilder($spotify, $spotifyTracks, $chart, $api);

        }

        if (!is_null($playlist)){
            return $this->redirectToRoute('show_chart',
                [
                    'chartSiteId' => $chart->getChartSite()->getId(),
                    'chartId' => $chart->getId(),
                ]);
        }

        return $this->redirectToRoute('home');


//        $count = 0;
//
//        foreach ($spotifyTracks as $element)
//        {
//            if(count($element) < 2)
//            {
//                $count++;
//            }
//        }
//
//        return $this->render('playlist/index.html.twig', [
//            'controller_name' => 'PlaylistController',
//            'artists' => $spotifyTracks,
//            'count' => $count,
//        ]);
    }
}
