<?php

namespace App\Controller;

use App\Entity\Chart;
use App\Manager\PlaylistManager;
use App\Util\SpotifyUtil;
use App\Util\SpotifyWebAPIBuilder;
use SpotifyWebAPI\SpotifyWebAPIAuthException;
use SpotifyWebAPI\SpotifyWebAPIException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PlaylistController extends AbstractController
{
    /** @var SpotifyUtil */
    private $spotifyUtil;
    /** @var PlaylistManager */
    private $playlistManager;
    /** @var SpotifyWebAPIBuilder */
    private $spotifyWebAPIBuilder;

    public function __construct(SpotifyUtil $spotifyUtil, PlaylistManager $playlistManager, SpotifyWebAPIBuilder $spotifyWebAPIBuilder)
    {
        $this->spotifyUtil = $spotifyUtil;
        $this->playlistManager = $playlistManager;
        $this->spotifyWebAPIBuilder = $spotifyWebAPIBuilder;
    }

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
        // stock en session l'url menant à la chart pour l'afficher après connexion à Spotify
        $currentChartRoute = $this->generateUrl('show_chart', [
            'chartSiteId' => $chart->getChartSite()->getId(),
            'chartId' => $chart->getId(),
        ]);

        $session->set('chart_origin_route', $currentChartRoute);

        $playlist = '';
        if (!$chart) {
            $this->addFlash('warning', 'La Chart n\'a pas été trouvée.');
            return $this->redirectToRoute('home');
        }

        // DEBUT construction de l'objet SpotifyWebAPI
        if (!$session->get('accessCode'))
        {
            return $this->redirectToRoute('login');
        }

        try
        {
            $api = $this->spotifyWebAPIBuilder->buildSpotifyWebAPIBuilder($session);
        } catch (SpotifyWebAPIAuthException $e)
        {
            return $this->redirectToRoute('login');
        }
        // FIN construction de l'objet SpotifyWebAPI

        // composition de la playlist
        $spotifyTracks = $this->spotifyUtil->getSpotifyTracks($api, $chart);

        // création de la playlist
        try {
            if (!empty($spotifyTracks))
            {
                $playlist = $this->playlistManager->spotifyPlaylistBuilder($spotifyTracks, $chart, $api);
            }
        } catch (SpotifyWebAPIException $e)
        {
            $this->addFlash('warning', 'La playlist n\'a pas pu être créée. Merci de laisser un commentaire pour analyse de ce cas particulier.');
            return $this->redirectToRoute('show_chart', [
            'chartSiteId' => $chart->getChartSite()->getId(),
            'chartId' => $chart->getId(),
        ]);
        }

        $this->addFlash('success', sprintf('La playlist a été créée. Vous pouvez la retrouver <a href="%s" target="_blank">à cette adresse</a>.', $playlist->getUrl()));
        return $this->redirectToRoute('show_chart', [
            'chartSiteId' => $chart->getChartSite()->getId(),
            'chartId' => $chart->getId(),
            ]);
    }
}
