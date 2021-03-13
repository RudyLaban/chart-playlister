<?php


namespace App\Manager;


use App\Entity\Chart;
use App\Entity\ChartSong;
use App\Entity\Playlist;
use App\Entity\PlaylistChartSong;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use SpotifyWebAPI\SpotifyWebAPI;

class PlaylistChartSongManager
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var PlaylistRepository */
    private $playlistRepo;

    public function __construct(EntityManagerInterface $em, PlaylistRepository $playlistRepo)
    {
        $this->em = $em;

        $this->playlistRepo = $playlistRepo;
    }

    public function createPlaylistChartSongs(SpotifyWebAPI $api, $spotifyTracks, Playlist $playlist)
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

    /**
     * Supprime les PlaylistChartSong liés au Playlist d'une Chart
     *
     * @param Chart $chart
     */
    public function deletePlaylistChartSongsOfPlaylist(Chart $chart)
    {
        // récupère les Playlists liées à la Chart
        $playlists = $this->playlistRepo->findBy(['chart' => $chart->getId()]);
        // pour chaque Playlist
        foreach ($playlists as $playlist)
        {
            $playlistChartSongList = $playlist->getPlaylistChartSongs();
            // supprime chaque PlaylistChartSongs
            foreach ($playlistChartSongList as $pcs)
            {
                $this->deletePlaylistChartSong($pcs);
            }
        }
    }

    /**
     * Supprime un PlaylistChartSong de la base
     *
     * @param PlaylistChartSong $pcs
     */
    public function deletePlaylistChartSong(PlaylistChartSong $pcs)
    {
        $this->em->remove($pcs);
        $this->em->flush();
    }
}