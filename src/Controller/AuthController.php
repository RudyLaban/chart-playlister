<?php

namespace App\Controller;

use App\Entity\Chart;
use App\Repository\ChartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use SpotifyWebAPI;

//require 'vendor/autoload.php';

class AuthController extends AbstractController
{
    private $spotifyParams;
    private $spotify;
    /** @var ChartRepository  */
    private $chartRepo;
    /** @var EntityManagerInterface  */
    private $em;

    /**
     * AuthController constructor. Variable venant du service App\Controller\AuthController
     * @param string $spotifyClientId
     * @param string $spotifyClientSecret
     * @param string $spotifyRedirectUri
     * @param EntityManagerInterface $em
     */
    public function __construct(string $spotifyClientId, string $spotifyClientSecret, string $spotifyRedirectUri, EntityManagerInterface $em)
    {
        $this->spotifyParams = [
            'client_id'     => $spotifyClientId,
            'client_secret' => $spotifyClientSecret,
            'scope'         => [
                'user-read-email','user-read-private','playlist-read-private', 'playlist-read-collaborative',
                'playlist-modify-public', 'playlist-modify-private','user-follow-read','user-follow-modify'
            ]
        ];

        $this->spotify = new SpotifyWebAPI\Session(
            $this->spotifyParams['client_id'],
            $this->spotifyParams['client_secret'],
            $spotifyRedirectUri
        );
        $this->em = $em;
        $this->chartRepo = $em->getRepository(Chart::class);
    }

    /**
     * Connection OAuth à Spotify
     *
     * @Route("/login", name="login")
     * @param SessionInterface $session
     * @return Response
     */
    public function login( SessionInterface $session ): Response
    {
        // Récupération des 3 Chart à afficher en page d'accueil
        $chartListForHome = $this->chartRepo->findThreeLastChart();

        $options = [
            'scope' => $this->spotifyParams['scope']
        ];

        $spotifyAuthUrl = $this->spotify->getAuthorizeUrl($options);

        return $this->render('auth/login.html.twig', array(
            'spotify_auth_url' => $spotifyAuthUrl,
            'chart_list_for_home' => $chartListForHome,
        ));
    }

    /**
     * @Route("/login/oauth", name="oauth")
     * @param Request $request
     * @param SessionInterface $session
     * @return RedirectResponse
     */
    public function oauth(Request $request, SessionInterface $session)
    {

        $accessCode = $request->get('code');
        $session->set('accessCode', $accessCode); // symfony session

        $this->spotify->requestAccessToken($accessCode);
        $accessToken = $this->spotify->getAccessToken();
        $session->set('accessToken', $accessToken); // symfony session

        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     * @param SessionInterface $session
     * @return Response
     */
    public function profile(Request $request, SessionInterface $session )
    {
        $accessToken = $session->get('accessToken');
        if( ! $accessToken ) {
            $session->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        $me = $api->me();

        return $this->render('auth/profile.html.twig', array(
            'me' => $me
        ));
    }


    /**
     * @Route("/logout", name="logout")
     * @param SessionInterface $session
     * @return RedirectResponse
     */
    public function logout( SessionInterface $session )
    {
        $session->clear();
        $session->getFlashBag()->add('success', 'Vous avez été déconnecté de Spotify avec succès');

        return $this->redirectToRoute('home');
    }
}
