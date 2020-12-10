<?php


namespace App\Manager;


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

    public function __construct(ContainerInterface $container, LoggerInterface $logger){
        $this->container = $container;
        $this->logger = $logger;
    }
    public function crawlBillHot100(): array
    {
        // création du crawler
        $client = new Client();
        $url = self::URL_BILl_HOT_100;
        $crawler = $client->request('GET', $url);
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
                $displayElement[$i] = ['song' => $songInfo->getNode(0)->nodeValue, 'artist' => $songArtist->getNode(0)->nodeValue];
            }

        }
        return $displayElement;
    }

    public function crawlBillJapanHot100(): array
    {
        // création du crawler
        $client = new Client();
        $url = self::URL_BILl_JAPAN_HOT_100;
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

}