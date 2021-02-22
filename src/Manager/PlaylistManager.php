<?php


namespace App\Manager;


use App\Entity\Artist;
use App\Entity\Chart;
use App\Entity\ChartSong;
use App\Entity\Song;
use Psr\Log\LoggerInterface;
use SpotifyWebAPI\SpotifyWebAPI;

class PlaylistManager
{
    private const FEATURED_ARTISTS = ["featuring", "feat", "ft", "x", "&", "and", "with"];
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger){
        $this->logger = $logger;
    }

    /**
     *
     *
     * @param SpotifyWebAPI $api
     * @param Chart $chart
     * @return array|object
     */
    public function create(SpotifyWebAPI $api, Chart $chart)
    {
        $count = 0;
        // creation de la playlist
        $resultsDisplay = [];
        $matchedArtists = null;
        // pour chaque ChartSong de la Chart
        /** @var ChartSong $chartSong */
        foreach ($chart->getChartSongs() as $chartSong)
        {
            $count++;
            // récupère le Song
            /** @var Song $song */
            $song = $chartSong->getSong();
            // récupère l'Artist
            /** @var Artist $artist */
            $artist = $song->getArtist();
            $key = $count .". ". $artist->getName() ." - ". $song->getName(); // une clé du tableau qui retourne les résultats
            $resultsDisplay[$key] = []; // initialise une clé du tableau retournant les résultats

            // cherche les artistes dans Spotify
            $resultsArtists = $this->searchSpotifyArtist($api, $artist);

            // pour chaque artistes Spotify trouvés
            if(!empty($resultsArtists->artists->items) && !is_null($resultsArtists->artists->items))
            {
                // cherche une correspondance entre l'Artist du Song et les artistes Spotify
                $matchedArtists = $this->getSpotifyArtist($resultsArtists, $artist);
                if(is_null($matchedArtists) || empty($matchedArtists))
                {
                    $this->logger->info('Pas de correspondance entre l\'artiste « '. $artist->getName() .' » du morceau « '. $song->getName() .' » de Chart Playlister et les artistes Spotify.');
                }
            }
            else
            {
                $this->logger->info('L\'artiste « '. $artist->getName() .' » pour le morceau « '. $song->getName() .' » n\'a pas été trouvé dans Spotify.');
                continue;
            }

            // on cherche les morceaux dans Spotify
            $resultsTrack = $api->search($song->getName(), 'track', ['limit' => 50]);
            // on vérifies la correspondance avec le Song de Chart Playlister
            if (!empty($resultsTrack->tracks->items) && !is_null($resultsTrack->tracks->items))
            {
                $spotifyTrack = $this->getSpotifyTrack($resultsTrack, $song, $matchedArtists, $count);
                if(is_null($spotifyTrack) || empty($spotifyTrack))
                {
                    $this->logger->notice('Pas de correspondance entre le morceau « '. $song->getName() .' » de Chart Playlister et Spotify.');
                }

                $resultsDisplay[$key] += $spotifyTrack;
            }
            else
            {
                $this->logger->info('Le morceau « '. $song->getName() .' » n\'a pas été trouvé dans Spotify.');
            }
        }

        return $resultsDisplay;
    }

    /**
     * Cherche dans Spotify des artistes pouvant correspondre au Song (prend en compte les collaboration d'artistes)
     *
     * @param $api | Instance de SpotifyWebAPI
     * @param $artist | Artist du Song
     * @return mixed Liste d'artiste retournée par Spotify
     */
    public function searchSpotifyArtist($api, $artist)
    {
        $resultsArtists = $api->search($artist->getName(), 'artist', ['limit' => 10]);

        // si $resultsArtists->artists->items est vide, on essayes
        if(empty($resultsArtists->artists->items)) {
            // $artist->getName() comporte un terme comme ft, feat, x, and, & ou with
            foreach (self::FEATURED_ARTISTS as $item)
            {
                if (str_contains(strtolower($artist->getName()), strtolower($item)))
                {
                    // on sépare $artist->getName() en deux chaînes
                    $featuredArtists = preg_split('/ '.$item.' /i' ,$artist->getName());
                    foreach ($featuredArtists as $featArtist)
                    {
                        // on cherche un artiste dans Spotify correspondant une des deux chaînes
                        $resultsArtists = $api->search($featArtist, 'artist', ['limit' => 10]);
                        if(!empty($resultsArtists->artists->items)) {break;}
                    }
                }
                if(!empty($resultsArtists->artists->items)) {break;}
            }
        }
        return $resultsArtists;
    }

    /**
     * Parmi la liste d'artiste ($results) retournée par Spotify, cherche les artistes pouvant correspondre au Song
     *
     * @param $resultsArtists | Liste d'artistes retournée par Spotify
     * @param $artist | Artist du Song de la Chart
     * @return array Les artistes potentiels
     */
    public function getSpotifyArtist($resultsArtists, $artist): array
    {
        $resultsDisplay = [];
        // pour chaque artistes Spotify trouvés
        foreach ($resultsArtists->artists->items as $spotifyArtist)
        {
            $cleanArtistName = $this->removeAccents($artist->getName());
            $cleanSpotifyArtistName = $this->removeAccents($spotifyArtist->name);

            // on compare l'artiste de Spotify et l'Artist de Chart Playlister
            $artistCorrespondance =
                str_contains($cleanArtistName, $cleanSpotifyArtistName) ||
                str_contains($cleanSpotifyArtistName, $cleanArtistName);
            if(!is_null($spotifyArtist) && $artistCorrespondance)
            {
                array_push($resultsDisplay, $spotifyArtist);
            }
        }

        return $resultsDisplay;
    }

    /**
     * Parmi la liste de track ($resultsTrack) retournée par Spotify, cherche un track correspondant à un des artistes
     *
     * @param $resultsTrack | Liste de track retournée par Spotify
     * @param $song | Song de la Chart
     * @param $matchedArtists | Artistes Spotify retournés par getSpotifyArtist() pouvant correspondre au Track
     * @param $count
     * @return array
     */
    public function getSpotifyTrack($resultsTrack, $song, $matchedArtists, $count): array
    {
        $resultsDisplay = [];
        // pour chaque track Spotify
        foreach ($resultsTrack->tracks->items as $track)
        {
            $cleanSongName = $this->removeAccents($song->getName());
            $cleanTrackName = $this->removeAccents($track->name);
            // on compare le track de Spotify et le Song de Chart Playlister
            $songCorrespondance =
                str_contains($cleanSongName, $cleanTrackName) ||
                str_contains($cleanTrackName, $cleanSongName);
            if (!is_null($track) && $songCorrespondance){
                // pour chaque artistes Spotify pouvant correspondre
                foreach ($matchedArtists as $matchedArtist)
                {
                    // pour chaque artistes du track Spotify
                    foreach ($track->artists as $trackArtists)
                    {
                        // on compare l'artiste du track Spotify à l'Artist du Song Chart Playlister
                        if ($trackArtists->id == $matchedArtist->id)
                        {
                            $resultsDisplay = [
                                'artist '. $count => $matchedArtist,
                                'song '. $count => $track,
                                ];
                            break;
                        }
                    }
                    if(!empty($resultsDisplay)) {break;}
                }
                // s'ils matchent, pour chaque artiste du track Spotify
            }
            if(!empty($resultsDisplay)) {break;}
        }

        return $resultsDisplay;
    }

    /**
     * Supprime les accents
     *
     * @param $string
     * @return string
     */
    function removeAccents($string): string
    {
        return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'))), ' '));
    }

}