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
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EntityManagerInterface
     */
    public $em;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var  ChartSiteRepository
     */
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
     * @param String $url
     * @return array
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
     * Création du ChartSite en base
     *
     * @param String $url
     * @param String $title
     * @return bool
     */
    public function createChartSite(String $url, String $title): bool
    {
        //$chartSite = $this->em->getRepository(ChartSite::class)->findOneBy(['name' => $title]);
        $chartSite = $this->chartSiteRepo->findOneBy(['name' => '$title']);
        // verifies si l'objet existe déjà en base avant de le créer
        if(empty($chartSite)) {
            $chartSite = new ChartSite();
            $chartSite->setName($title);
            $chartSite->setUrl($url);

            $this->em->persist($chartSite);
            $this->em->flush();

            return true;
        }
        return false;
    }

}