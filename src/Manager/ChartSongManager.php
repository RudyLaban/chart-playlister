<?php


namespace App\Manager;


use App\Entity\Chart;
use App\Entity\ChartSong;
use App\Entity\Song;
use App\Repository\ChartRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Goutte\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChartSongManager
{
    /** @var ContainerInterface */
    protected $container;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var ChartRepository */
    protected $chartSongRepo;

    /** @var SongRepository */
    protected $songRepo;

    /** @var SongManager */
    protected $songManager;

    /** @var ArtistManager */
    protected $artistManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ChartSongManager constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $em
     * @param SongManager $songManager
     * @param ArtistManager $artistManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $em,
        SongManager $songManager,
        ArtistManager $artistManager,
        LoggerInterface $logger)
    {
        $this->container = $container;
        $this->em = $em;
        $this->chartSongRepo = $this->em->getRepository(ChartSong::class);
        $this->songRepo = $this->em->getRepository(Song::class);
        $this->songManager = $songManager;
        $this->artistManager = $artistManager;
        $this->logger = $logger;
    }

    /**
     * Crée tous les ChartSong d'une Chart. S'ils existent, met leur position à jours si besoin
     *
     * @param array $chartSongsElements Une liste contenant les infos des ChartSong à créer :
     *      chartSongsElements[element['position' => int, 'song' => String, 'artist" => String]]
     * @param Chart $chart La Chart liée
     * @return array La liste des ChartSong Créé
     */
    public function createChartSongs(array $chartSongsElements, Chart $chart): array
    {
        $chartSongList = [];
        foreach ($chartSongsElements as $chartSongsElement)
        {
            $artist = $this->artistManager->createArtist($chartSongsElement['artist']);
            $song = $this->songManager->createSong($chartSongsElement['song'], $artist);
            $chartSong = $this->createChartSong($chartSongsElement['position'], $song, $chart);
            array_push($chartSongList, $chartSong);
        }

        // verifies via la liste des ChartSong créés s'il y en a à supprimer
        $allChartSongList = $chart->getChartSongs();
        foreach($allChartSongList as $chartSong)
        {
            if(array_search($chartSong, $chartSongList) === false)
            {
                $this->deleteChartSong($chartSong);
            }
        }

        return $chartSongList;
    }

    /**
     * Création en base d'un ChartSong.
     * S'il n'existe pas, on le crée
     *
     * @param Int $position La position du ChartSong
     * @param Song $song Le Song lié
     * @param Chart $chart La Chart liée
     * @return Chart|ChartSong|object Le ChartSong créé ou trouvé en base
     */
    public function createChartSong(int $position, Song $song, Chart $chart)
    {
        // cherche si le ChartSong existe déjà en base
        $chartSong = $this->chartSongRepo->findOneBy([
            'song'  => $song->getId(),
            'chart' => $chart->getId(),
        ]);

        // cherche un ChartSong de la Chart avec la même position afin de le remplacer par le nouveau
        /** @var ChartSong $chartSongToReplace */
        $chartSongToReplace = $this->chartSongRepo->findOneBy([
            'chart' => $chart->getId(),
            'position' => $position,
        ]);

        // supprime le ChartSong à remplacer
        if(!empty($chartSong) && !empty($chartSongToReplace) && ($chartSongToReplace == $chartSong))
        {
            $this->deleteChartSong($chartSongToReplace);
        }

        // s'il n'existe pas, on le crée
        if(empty($chartSong))
        {
            $chartSong = new ChartSong();
            $chartSong->setChart($chart);
            $chartSong->setSong($song);

        }

        $chartSong->setPosition($position);

        $this->em->persist($chartSong);
        $this->em->flush();

        return $chartSong;
    }

    /**
     * Supprime un ChartSong
     *
     * @param ChartSong $chartSong
     */
    public function deleteChartSong(ChartSong $chartSong)
    {
        $this->em->remove($chartSong);
        $this->em->flush();
    }

}