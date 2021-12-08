<?php

namespace App\Controller;

use App\Entity\Chart;
use App\Repository\ChartRepository;
use App\Util\SpotifyWebAPIBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIAuthException;

/**
 * Chargée de la connexion à l'API Spotify via le bundle PHP SpotifyWebAPI
 */
class AuthController extends AbstractController
{
    private $spotifyParams;
    private $spotify;
    /** @var SpotifyWebAPIBuilder */
    private $spotifyWebAPIBuilder;
    /** @var ChartRepository  */
    private $chartRepo;

    /**
     * AuthController constructor. Variable venant du service _defaults.bind
     * @param string $spotifyClientId
     * @param string $spotifyClientSecret
     * @param string $spotifyRedirectUri
     * @param EntityManagerInterface $em
     * @param SpotifyWebAPIBuilder $spotifyWebAPIBuilder
     */
    public function __construct(string $spotifyClientId, string $spotifyClientSecret, string $spotifyRedirectUri, EntityManagerInterface $em, SpotifyWebAPIBuilder $spotifyWebAPIBuilder)
    {
        $this->spotifyParams = [
            'client_id'     => $spotifyClientId,
            'client_secret' => $spotifyClientSecret,
            'scope'         => [
                'user-read-email','user-read-private','playlist-read-private', 'playlist-read-collaborative',
                'playlist-modify-public', 'playlist-modify-private','user-follow-read','user-follow-modify', 'ugc-image-upload'
            ]
        ];

        $this->spotifyWebAPIBuilder = $spotifyWebAPIBuilder;

        $this->spotify = new SpotifyWebAPI\Session(
            $this->spotifyParams['client_id'],
            $this->spotifyParams['client_secret'],
            $spotifyRedirectUri
        );
        $this->chartRepo = $em->getRepository(Chart::class);
    }

    /**
     * Connection OAuth à Spotify
     *
     * @Route("/login", name="login")
     * @return Response
     */
    public function login(): Response
    {
        // Récupération des 3 Chart à afficher en page d'accueil
        $threeLastChart = $this->chartRepo->findThreeLastChart();

        $options = [
            'scope' => $this->spotifyParams['scope']
        ];

        $spotifyAuthUrl = $this->spotify->getAuthorizeUrl($options);

        return $this->render('auth/login.html.twig', array(
            'spotify_auth_url' => $spotifyAuthUrl,
            'three_last_chart' => $threeLastChart,
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

        $this->spotify->requestAccessToken($session->get('accessCode'));
        $accessToken = $this->spotify->getAccessToken();
        $session->set('accessToken', $accessToken); // symfony session
        $refreshToken = $this->spotify->getRefreshToken();
        $session->set('refreshToken', $refreshToken); // symfony session

        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/profile", name="profile")
     * @param SessionInterface $session
     * @return Response
     */
    public function profile(SessionInterface $session): Response
    {
        if (!$session->get('accessCode'))
        {
            return $this->redirectToRoute('login');
        }

        try
        {
            $api = $this->spotifyWebAPIBuilder->buildSpotifyWebAPIBuilder($session);
        } catch (SpotifyWebAPIAuthException $e)
        {
            $this->addFlash('info', 'Merci de vous connecter a votre compte Spotify');
            return $this->redirectToRoute('login');
        }

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
    public function logout( SessionInterface $session ): RedirectResponse
    {
        $session->clear();
        $session->getFlashBag()->add('success', 'Vous avez été déconnecté de Spotify avec succès');

        return $this->redirectToRoute('login');
    }

}
