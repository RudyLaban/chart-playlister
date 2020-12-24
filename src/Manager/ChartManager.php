<?php


namespace App\Manager;


use App\Entity\Chart;
use App\Entity\ChartSong;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChartManager
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected const URL_BILl_JAPAN_HOT_100 = "https://www.billboard.com/charts/japan-hot-100";
    protected const URL_BILl_HOT_100 = "https://www.billboard.com/charts/hot-100";
    protected const URL_BILl_200 = "https://www.billboard.com/charts/billboard-200";

    public function __construct(ContainerInterface $container, LoggerInterface $logger){
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * Chargé de crawler la page afin de récupérer les éléments nécessaire à la création de l'objet Chart
     *
     * @param String $url
     * @return array Le set de données crawlé
     */
    public function crawlChart(String $url): array
    {
        // création du crawler
        $client = new Client();
        $crawler = $client->request('GET', $url);

        $chartCrawler = $crawler->filter('title');
        $chartValue = $chartCrawler->getNode(0)->nodeValue;
        $chartExplode = explode(' | ', $chartValue);

        // recuperation du nom du site et du titre de la chart
        return ['chart_name' => $chartExplode[0], 'chart_url' => $url];
    }

    /**
     * TODO
     *
     * @param $chartElement
     */
    public function creatChart($chartElement)
    {
        $chart = new ChartSong();
        $chart->setPosition();
    }

}