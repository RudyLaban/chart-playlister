<?php


namespace App\Manager;


use App\Entity\Chart;
use App\Entity\ChartSite;
use App\Entity\ChartSong;
use App\Repository\ChartRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Goutte\Client;
use phpDocumentor\Reflection\Types\This;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChartManager
{
    /** @var ContainerInterface */
    protected $container;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var ChartRepository */
    protected $chartRepo;

    /**  @var LoggerInterface */
    protected $logger;

    protected const URL_BILl_JAPAN_HOT_100 = "https://www.billboard.com/charts/japan-hot-100";
    protected const URL_BILl_HOT_100 = "https://www.billboard.com/charts/hot-100";
    protected const URL_BILl_200 = "https://www.billboard.com/charts/billboard-200";

    /**
     * ChartManager constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $em, LoggerInterface $logger){
        $this->container = $container;
        $this->em = $em;
        $this->chartRepo = $this->em->getRepository(Chart::class);
        $this->logger = $logger;
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
        $chart = $this->chartRepo->findOneBy(['name' => $chartElement['chart_name']]);
        // si elle n'existe pas, on la crée
        if(empty($chart))
        {
            $chart = new Chart();
            $chart->setName($chartElement['chart_name']);
            $chart->setUrl($chartElement['chart_url']);
            $chart->setChartSite($chartSite);

            $this->em->persist($chart);
            $this->em->flush();
        }
        return $chart;
    }

}