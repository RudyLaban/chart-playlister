<?php


namespace App\Manager;


use App\Entity\Chart;
use App\Entity\ChartSite;
use App\Repository\ChartRepository;
use App\Util\BillboardUtil;
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
    /** @var BillboardUtil  */
    private $billboardUtil;
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
     * @param BillboardUtil $billboardUtil
     */
    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        ChartSiteManager $chartSiteManager,
        ChartSongManager $chartSongManager,
        ArtistManager $artistManager,
        SongManager $songManager,
        PlaylistChartSongManager $playlistChartSongManager,
        BillboardUtil $billboardUtil)
    {
        $this->container = $container;
        $this->em = $em;

        $this->chartSiteManager = $chartSiteManager;
        $this->chartSongManager = $chartSongManager;
        $this->artistManager = $artistManager;
        $this->songManager = $songManager;
        $this->playlistChartSongManager = $playlistChartSongManager;
        $this->billboardUtil = $billboardUtil;

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
        $chartElement = $this->billboardUtil->getNameAndUrl($url);
        // crawl de la page de l'url soumis par le formulaire pour récupérer une liste de ChartsSong
        $chartSongsElements = $this->billboardUtil->dispatcher($url);
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