<?php


namespace App\Manager;


use Goutte\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChartSongManager
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
     * Chargé de tester les méthodes de crawle afin de trouver celle qui renvoi un résultat
     * @param String $url
     * @return array Le set de données crawlé
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
                // recupère les infos de l'element
                $songInfo = $liElementsList->filter('button > span > span.chart-element__information__song');
                $songArtist = $liElementsList->filter('button > span > span.chart-element__information__artist');
                // stock les infos dans un tableau pour affichage
                $displayElement[$i] = [
                    'song' => $songInfo->getNode(0)->nodeValue,
                    'artist' => $songArtist->getNode(0)->nodeValue];
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
        $listItemCout = $crawler->filter('div > div.chart-list-item')->count();
        $displayElement = [];
        // si des eléments ont été trouvés
        if ($listItemCout > 0) {
            // stock les éléments dans un objet Crawler
            //$listItem = $crawler->filter('div > div.chart-list')->children();
            $listItem = $crawler->filter('div > div.chart-list-item');
            // boucle sur la liste
            for ($i = 0; $listItemCout > $i; $i++) {
                // stock un élément de la list dans un objet Crawler
                $liElementsList = $listItem->filter('div.chart-list-item__text')->eq($i);
                // recupère les infos de l'element
                $songInfoCr = $liElementsList->filter('div.chart-list-item__title > span');
                $songInfoNode = $songInfoCr->getNode(0)->textContent;
                $songArtistCr = $liElementsList->filter('div.chart-list-item__artist');
                $songArtistNode = $songArtistCr->getNode(0)->nodeValue;
                // stock les infos dans un tableau pour affichage
                $displayElement[$i] = [
                    'song' => str_replace("\n","", $songInfoNode),
                    'artist' => str_replace("\n","", $songArtistNode)];
            }

        }
        return $displayElement;
    }

    /**
     * TODO
     */
    public function creatChartSongs()
    {

    }

}