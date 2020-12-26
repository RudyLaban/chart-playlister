<?php


namespace App\Manager;


use App\Entity\ChartSite;
use App\Repository\ChartSiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Goutte\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChartSiteManager
{
    /** @var ContainerInterface */
    protected $container;

    /** @var EntityManagerInterface */
    public $em;

    /** @var LoggerInterface */
    protected $logger;

    /** @var  ChartSiteRepository */
    protected $chartSiteRepo;

    /**
     * ChartSiteManager constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $em, LoggerInterface $logger){
        $this->container = $container;
        $this->em = $em;
        $this->chartSiteRepo = $this->em->getRepository(ChartSite::class);
        $this->logger = $logger;
    }

    /**
     * Chargé de crawler la page afin de récupérer les éléments nécessaire à la création de l'objet ChartSite
     *
     * @param String $url Url du site de chart
     * @return array Un tableau contenant le nom et l'url du ChartSite: ['chart_site_name' => name, 'chart_site_url' => url]
     */
    public function crawlChartSite(String $url): array
    {
        // création du crawler
        $client = new Client();
        $crawler = $client->request('GET', $url);
        // récupère le nom de site via un balise meta
        $chartSiteName = $crawler->filter('meta[property="og:site_name"]')->eq(0)->attr('content');
        // récupère l'url de base via $url
        $baseUrl = parse_url($url, PHP_URL_HOST);

        return ['chart_site_name' => $chartSiteName, 'chart_site_url' => $baseUrl];
    }

    /**
     * Crée le ChartSite en base s'il n'extiste pas encore
     *
     * @param String $url Url Du ChartSite
     * @param String $title Nom du ChartSite
     * @return ChartSite Le ChartSite créé
     */
    public function createChartSite(String $url, String $title): ChartSite
    {
        // cherche si le ChartSite existe déjà en base
        $chartSite = $this->chartSiteRepo->findOneBy(['name' => $title]);
        // s'il n'existe pas, on le crée
        if(empty($chartSite))
        {
            $chartSite = new ChartSite();
            $chartSite->setName($title);
            $chartSite->setUrl($url);

            $this->em->persist($chartSite);
            $this->em->flush();
        }
        return $chartSite;
    }

}