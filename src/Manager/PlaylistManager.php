<?php


namespace App\Manager;


use App\Entity\Chart;
use App\Entity\Playlist;
use App\Entity\StreamingSite;
use Doctrine\ORM\EntityManagerInterface;
use SpotifyWebAPI\SpotifyWebAPI;

class PlaylistManager
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var PlaylistChartSongManager */
    private $pscManager;
    /** @var SpotifyManager */
    private $spotifyManager;

    public function __construct(EntityManagerInterface $em, PlaylistChartSongManager $pscManager, SpotifyManager $spotifyManager)
    {
        $this->em = $em;

        $this->pscManager = $pscManager;
        $this->spotifyManager = $spotifyManager;
    }

    /**
     * Gestion de la création de la Playlist dans Chart Playlister et dans Spotify
     *
     * @param StreamingSite $spotify
     * @param $spotifyTracks
     * @param Chart $chart
     * @param SpotifyWebAPI $api
     * @return Playlist
     */
    public function spotifyPlaylistBuilder(StreamingSite $spotify, $spotifyTracks, Chart $chart, SpotifyWebAPI $api): Playlist
    {
        // création de la Playlist dans Chart playlister
        $playlist = $this->createPlaylist($spotify, $chart);
        // création de la playlist dans spotify
        $spotifyPlaylist = $this->spotifyManager->createSpotifyPlaylist($api, $playlist, $chart);
        // on complete la Playlist Chart Playlister avec les info de la playlist Spotify
        $playlist->setExternalId($spotifyPlaylist->id);
        $playlist->setUrl($spotifyPlaylist->external_urls->spotify);
        $this->em->flush();

        // création des PlaylistChartSong de la playlist
        $this->pscManager->createPlaylistChartSongs($api, $spotifyTracks, $playlist);

        return $playlist;
    }

    /**
     * Crée la Playlist en base si elle n'existe pas déjà
     *
     * @param StreamingSite $spotify
     * @param Chart $chart
     * @return Playlist
     */
    private function createPlaylist(StreamingSite $spotify, Chart $chart): Playlist
    {
        // le nom de la playlist est composé du nom
        $playlistName = $chart->getName().' - '.$chart->getChartSite()->getName();
        $playlist = $this->em->getRepository(Playlist::class)->findOneBy(['name' => $playlistName]);
        if (empty($playlist)) {
            $playlist = new Playlist();
            $playlist->setName($playlistName);
            $playlist->setStreamingSite($spotify);
            $playlist->setExternalId('');
            $playlist->setChart($chart);

            $this->em->persist($playlist);
            $this->em->flush();
        }

        return $playlist;
    }
}