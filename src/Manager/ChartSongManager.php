<?php


namespace App\Manager;


use App\Entity\Chart;
use App\Entity\ChartSong;
use App\Entity\Song;
use App\Repository\ChartRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Goutte\Client;
use phpDocumentor\Reflection\Types\This;
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

    protected const URL_BILl_JAPAN_HOT_100 = "https://www.billboard.com/charts/japan-hot-100";
    protected const URL_BILl_HOT_100 = "https://www.billboard.com/charts/hot-100";
    protected const URL_BILl_200 = "https://www.billboard.com/charts/billboard-200";

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
     * Chargé de tester les méthodes de crawle afin de trouver celle qui renvoi un résultat
     * @param String $url
     * @return array La liste des éléments de la chart : ['song' => song, 'artist' => artist]
     */
    public function dispatcher(String $url): array
    {
        // on test toutes les méthodes de crawl pour récupérer des données
        $chartSongs = $this->crawlBillHot100($url);
        if(empty($chartSongs)) {
            $chartSongs = $this->crawlBillJapanHot100($url);
        }

        return $chartSongs;
    }


    /**
     * @param String $url
     * @return array
     */
    public function crawlBillHot100(String $url = self::URL_BILl_HOT_100): array
    {
        // création du crawler
        $client = new Client();
        $crawler = $client->request('GET', $url);
        //$title = $crawler->filter()
        // compte les elements de la liste à racuperer
        $elementCount = $crawler->filter('div > div > ol')->count();
        $displayElement = [];
        // si des eléments ont été trouvés
        if ($elementCount > 0) {
            // stock les éléments dans un objet Crawler
            $olElementsList = $crawler->filter('div > div > ol')->children();
            // boucle sur la liste
            for ($i = 0; $olElementsList->count() > $i; $i++) {
                // stock un élément de la list dans un objet Crawler
                $liElementsList = $olElementsList->filter('li')->eq($i);
                // récupère les l'éléments
                $songInfoCrawler = $liElementsList->filter('button > span > span.chart-element__information__song');
                $songArtistCrawler = $liElementsList->filter('button > span > span.chart-element__information__artist');
                /// récupère les valeurs des éléments en supprimant les espaces
                $songInfo = trim($songInfoCrawler->getNode(0)->nodeValue);
                $songArtist = trim($songArtistCrawler->getNode(0)->nodeValue);
                // stock les infos dans un tableau pour affichage
                $displayElement[$i+1] = [
                    'position' => $i+1,
                    'song' => $songInfo,
                    'artist' => $songArtist
                ];
            }
        }
        return $displayElement;
    }

    /**
     * @param string $url
     * @return array
     */
    public function crawlBillJapanHot100($url = self::URL_BILl_JAPAN_HOT_100): array
    {
        // création du crawler
        $client = new Client();
        $crawler = $client->request('GET', $url);
        // compte les elements de la liste à racuperer
        $listItemCount = $crawler->filter('div > div.chart-list-item')->count();
        $displayElement = [];
        // si des éléments ont été trouvés
        if ($listItemCount > 0) {
            // stock les éléments dans un objet Crawler
            //$listItem = $crawler->filter('div > div.chart-list')->children();
            $listItem = $crawler->filter('div > div.chart-list-item');
            // boucle sur la liste
            for ($i = 0; $listItem->count() > $i; $i++) {
                // stock un élément de la list dans un objet Crawler
                $liElementsList = $listItem->filter('div.chart-list-item__text')->eq($i);
                // récupère les l'éléments
                $songInfoCrawler = $liElementsList->filter('div.chart-list-item__title > span');
                $songArtistCrawler = $liElementsList->filter('div.chart-list-item__artist');
                // récupère les valeurs des éléments en supprimant les espaces
                $songInfo = trim($songInfoCrawler->getNode(0)->textContent);
                $songArtist = trim($songArtistCrawler->getNode(0)->nodeValue);
                // stock les infos dans un tableau pour affichage
                $displayElement[$i+1] = [
                    'position' => $i+1,
                    'song' => $songInfo,
                    'artist' => $songArtist
                ];
            }
        }
        return $displayElement;
    }

    /**
     * Crée tous les ChartSong d'une Chart. S'ils existent, met leur position à jours si besoin
     *
     * @param array $chartSongsElements Une liste contenant les infos des ChartSong à créer :
     * chartSongsElements[element['position' => int, 'song' => String, 'artist" => String]]
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
        return $chartSongList;
    }

    /**
     * Création en base d'un ChartSong. S'il existe, met à jour sa position si besoin
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
        // s'il n'existe pas, on le crée
        if (empty($chartSong))
        {
            $chartSong = new ChartSong();
            $chartSong->setPosition($position);
            $chartSong->setChart($chart);
            $chartSong->setSong($song);

            $this->em->persist($chartSong);
            $this->em->flush();
        }
        else { // s'il existe, on verifies si sa position doit être mise à jour
            if ($chartSong->getPosition() != $position)
            {
                $chartSong->setPosition($position);
                $this->em->flush();
            }
        }

        return $chartSong;
    }

}