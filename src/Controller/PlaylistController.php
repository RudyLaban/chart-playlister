<?php

namespace App\Controller;

use App\Entity\Chart;
use App\Manager\PlaylistManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use SpotifyWebAPI;

class PlaylistController extends AbstractController
{
    /** @var PlaylistManager */
    private $playlistManager;

    public function __construct(PlaylistManager $playlistManager)
    {
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
     * @Route("/create_playlist/{chart}", name="create")
     * @param SessionInterface $session
     * @param Chart $chart
     * @return Response
     */
    public function create(SessionInterface $session, Chart $chart): Response
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

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        $resultsDisplay = $this->playlistManager->create($api, $chart);

        $count = 0;

        foreach ($resultsDisplay as $element)
        {
            if(count($element) < 2)
            {
                $count++;
            }
        }

        return $this->render('playlist/index.html.twig', [
            'controller_name' => 'PlaylistController',
            'artists' => $resultsDisplay,
            'count' => $count,
        ]);
    }
}
