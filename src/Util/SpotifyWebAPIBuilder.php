<?php

namespace App\Util;

use SpotifyWebAPI;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Construit l'objet SpotifyWebAPI suite à la connexion à l'API Spotify via le bundle PHP SpotifyWebAPI
 */
class SpotifyWebAPIBuilder
{
    private $spotifyParams;
    private $spotify;

    /**
     * AuthController constructor. Variable venant du service _defaults.bind
     * @param string $spotifyClientId
     * @param string $spotifyClientSecret
     * @param string $spotifyRedirectUri
     */
    public function __construct(string $spotifyClientId, string $spotifyClientSecret, string $spotifyRedirectUri)
    {
        $this->spotifyParams = [
            'client_id'     => $spotifyClientId,
            'client_secret' => $spotifyClientSecret,
            'scope'         => [
                'user-read-email','user-read-private','playlist-read-private', 'playlist-read-collaborative',
                'playlist-modify-public', 'playlist-modify-private','user-follow-read','user-follow-modify', 'ugc-image-upload'
            ]
        ];

        $this->spotify = new SpotifyWebAPI\Session(
            $this->spotifyParams['client_id'],
            $this->spotifyParams['client_secret'],
            $spotifyRedirectUri
        );
    }

    /**
     * Création de l'objet SpotifyWebAPI avec refresh des acces token
     * @param SessionInterface $session
     * @return SpotifyWebAPI\SpotifyWebAPI
     */
    public function buildSpotifyWebAPIBuilder(SessionInterface $session): SpotifyWebAPI\SpotifyWebAPI
    {
        $accessToken = $session->get('accessToken');
        $refreshToken = $session->get('refreshToken');

        if(!$accessToken || !$refreshToken)
        {
            $this->spotify->requestAccessToken($session->get('accessCode'));
        }
        else {
            // Or request a new access token
            $this->spotify->refreshAccessToken($refreshToken);
        }
        $this->spotify->setAccessToken($accessToken);
        $session->set('accessToken', $accessToken);
        $this->spotify->setRefreshToken($refreshToken);
        $session->set('refreshToken', $refreshToken);

        $options = [
            'auto_refresh' => true,
        ];

        return new SpotifyWebAPI\SpotifyWebAPI($options, $this->spotify);
    }

}