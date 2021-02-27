<?php


namespace App\Manager;


use App\Entity\ChartSong;
use App\Entity\Playlist;
use App\Entity\PlaylistChartSong;
use Doctrine\ORM\EntityManagerInterface;
use SpotifyWebAPI\SpotifyWebAPI;

class PlaylistChartSongManager
{

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createPlaylistChartSongs(SpotifyWebAPI $api, $spotifyTracks, Playlist $playlist, $spotifyPlaylist)
    {
        $tracksIds = [];
        foreach ($spotifyTracks as $spotifyTrack)
        {
            if (!empty($spotifyTrack))
            {
                $track = $spotifyTrack['song'];
                array_push($tracksIds, $track->id);
            }
        }
            // ajout du track à la playlist spotify
            $this->addTrackToSpotifyPlaylist($api, $tracksIds, $playlist);
            // création du PlaylistChartSong en base
            $this->createPlaylistChartSong($spotifyTracks, $playlist);
    }

    /**
     * Ajoute un track à la playlist Spotify
     *
     * @param SpotifyWebAPI $api
     * @param $tracksIds
     * @param Playlist $playlist
     */
    private function addTrackToSpotifyPlaylist(SpotifyWebAPI $api, $tracksIds, Playlist $playlist)
    {
        $api->replacePlaylistTracks($playlist->getExternalId(), $tracksIds);
    }

    /**
     * ajoute un track à la playlist
     *
     * @param $spotifyTracks
     * @param $playlist
     */
    public function createPlaylistChartSong($spotifyTracks, $playlist)
    {
        foreach ($spotifyTracks as $spotifyTrack)
        {
            if (!empty($spotifyTrack))
            {
                $track = $spotifyTrack['song'];
                // on
                $playlistChartSong = $spotify = $this->em->getRepository(PlaylistChartSong::class)->findOneBy(
                    [
                        'playlist' => $playlist->getId(),
                        'chartSong' => $track->cp_chart_song_id,
                    ]);

                if (empty($playlistChartSong))
                {
                    /** @var ChartSong $chartSong */
                    $chartSong = $this->em->getRepository(ChartSong::class)->find($track->cp_chart_song_id);

                    $playlistChartSong = new PlaylistChartSong();
                    $playlistChartSong->setPlaylist($playlist);
                    $playlistChartSong->setChartSong($chartSong);
                    $playlistChartSong->setUrl($track->external_urls->spotify);
                    $playlistChartSong->setExternalId($track->id);

                    $this->em->persist($playlistChartSong);
                    $this->em->flush();
                }
            }
        }
    }
}