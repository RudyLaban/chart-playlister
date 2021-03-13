<?php


namespace App\Manager;


use App\Entity\Chart;
use App\Entity\ChartSite;
use App\Repository\ChartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Goutte\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChartManager
{
    /** @var ContainerInterface */
    protected $container;
    /** @var EntityManagerInterface */
    protected $em;

    /** @var ChartSiteManager */
    protected $chartSiteManager;
    /** @var ChartSongManager */
    protected $chartSongManager;
    /** @var ArtistManager */
    protected $artistManager;
    /** @var SongManager */
    protected $songManager;
    /** @var PlaylistChartSongManager */
    private $playlistChartSongManager;
    /** @var ChartRepository */
    protected $chartRepo;

    /**  @var LoggerInterface */
    protected $logger;

    /**
     * ChartManager constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     * @param ChartSiteManager $chartSiteManager
     * @param ChartSongManager $chartSongManager
     * @param ArtistManager $artistManager
     * @param SongManager $songManager
     * @param PlaylistChartSongManager $playlistChartSongManager
     */
    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        ChartSiteManager $chartSiteManager,
        ChartSongManager $chartSongManager,
        ArtistManager $artistManager,
        SongManager $songManager,
        PlaylistChartSongManager $playlistChartSongManager)
    {
        $this->container = $container;
        $this->em = $em;

        $this->chartSiteManager = $chartSiteManager;
        $this->chartSongManager = $chartSongManager;
        $this->artistManager = $artistManager;
        $this->songManager = $songManager;
        $this->playlistChartSongManager = $playlistChartSongManager;

        $this->chartRepo = $this->em->getRepository(Chart::class);
        $this->logger = $logger;
    }

    /**
     * Traitement du formulaire ChartForm si soumis
     *
     * @param $form
     * @return array|false
     */
    public function chartFormTraitement($form)
    {
        // récupération de l'url soumis dans le formulaire
        $url = $form->getData()['url'];
        // crawle de la page pour récupérer le site de la chart
        $chartSiteElement = $this->chartSiteManager->crawlChartSite($url);
        // crawl de la page pour récupérer la chart
        $chartElement = $this->crawlChart($url);
        // crawl de la page de l'url soumis par le formulaire pour récupérer un liste de ChartsSong
        $chartSongsElements = $this->chartSongManager->dispatcher($url);
        // récupération de la liste des artistes de la chart
        $artistsElementsList = $this->artistManager->artistListFormatter($chartSongsElements);
        // si le crawl ne renvoi pas d'éléments
        if(empty($chartSiteElement) || empty($chartElement) || empty($chartSongsElements ) || empty($artistsElementsList))
        {
            return false;
        }
        // si le crawler renvoi bien des éléments
        // on crée en base le ChartSite si il n'existe pas
        $chartSite = $this->chartSiteManager->createChartSite($chartSiteElement['chart_site_url'], $chartSiteElement['chart_site_name']);
        // on crée en base la Chart si elle n'existe pas
        $chart = $this->createChart($chartElement, $chartSite);
        // on crée en base les Artist de la Chart si ils n'existent pas
        $this->artistManager->createArtistsOfChart($artistsElementsList);
        // on crée en base les Song de la Chart si ils n'existent pas
        $this->songManager->createSongsOfChart($chartSongsElements);
        // on supprime les PlaylistChartSong liés aux Playlists de la Chart
        $this->playlistChartSongManager->deletePlaylistChartSongsOfPlaylist($chart);
        // on crée en base les ChartSong si ils n'existent pas
        // si ils existent, ont met à jours le champs position
        $this->chartSongManager->createChartSongs($chartSongsElements, $chart);

        // retour les Ids du ChartSite et de la Chart pour redirection vers la page de la Chart créée
        return ['chart_site' => $chartSite->getId(), 'chart' => $chart->getId()];
    }

    /**
     * Chargé de crawler la page afin de récupérer les éléments nécessaire à la création de l'objet Chart
     *
     * @param String $url Url de la chart
     * @return array Un tableau contenant les info de la Chart et son url : ['chart_name' => name, 'chart_url' => url]
     */
    public function crawlChart(String $url): array
    {
        // création du crawler
        $client = new Client();
        $crawler = $client->request('GET', $url);

        $chartValue = $crawler->filter('meta[property="title"]')->eq(0)->attr('content');
        $chartValue = str_ireplace(' chart', '', $chartValue);

        // renvoi le nom de la chart et son url
        return ['chart_name' => $chartValue, 'chart_url' => $url];
    }

    /**
     * Crée la Chart en base si elle n'extiste pas encore
     *
     * @param array $chartElement Info de la Chart
     * @param ChartSite $chartSite ChartSite Lié
     * @return Chart La Chart créée
     */
    public function createChart(array $chartElement, ChartSite $chartSite): Chart
    {
        // cherche si la Chart existe déjà en base
        $chart = $this->chartRepo->findOneBy(['url' => $chartElement['chart_url']]);
        // si elle n'existe pas, on la crée
        if(empty($chart))
        {
            $chart = new Chart();
            // on set l'image par défaut en pochette de Chart
            $chart->setImageFileName('default_chart_image.png');
            $chart->setChartSite($chartSite);
            $chart->setUrl($chartElement['chart_url']);
        }
        // si elle existe, on set le nom ici au cas ou il aurai changé
        $chart->setName($chartElement['chart_name']);

        $this->em->persist($chart);
        $this->em->flush();

        return $chart;
    }
}