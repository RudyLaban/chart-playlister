<?php

namespace App\Util;

use Goutte\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Panther\DomCrawler\Crawler;

/**
 * Classe chargée des actions liées au crawl de Billboard
 */
class BillboardUtil
{
    /**  @var LoggerInterface */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Chargé de crawler la page afin de récupérer les éléments nécessaire à la création de l'objet Chart
     *
     * @param String $url Url de la chart
     * @return array Un tableau contenant le nom de la Chart et son url : ['chart_name' => name, 'chart_url' => url]
     */
    public function getNameAndUrl(String $url): array
    {
        // création du crawler
        $client = new Client();
        $crawler = $client->request('GET', $url);

        $chartValue = $crawler->filter('meta[property="og:title"]')->eq(0)->attr('content');
        $chartValue = str_ireplace(' chart', '', $chartValue);

        // renvoi le nom de la chart et son url
        return ['chart_name' => $chartValue, 'chart_url' => $url];
    }

    /**
     * Chargé de tester les méthodes de crawl afin de trouver celle qui renvoi un résultat
     * @param String $url
     * @return array La liste des éléments de la chart : ['song' => song, 'artist' => artist]
     */
    public function dispatcher(String $url): array
    {
        // on test toutes les méthodes de crawl pour récupérer des données
        $chartSongs = $this->crawlPattern1($url);
/*        if(empty($chartSongs))
        {
            $chartSongs = $this->crawlPattern2($url);
        }*/

        return $chartSongs;
    }

    /**
     * @param String $url
     * @return array
     */
    public function crawlPattern1(String $url): array
    {
        // création du crawler
        $client = new Client();
        $crawler = $client->request('GET', $url);
        //$title = $crawler->filter()
        // compte les elements de la liste à récupérer
        $elementCount = $crawler->filter('div > div.o-chart-results-list-row-container')->count();
        $displayElement = [];
        // si des elements ont été trouvés
        if ($elementCount > 0) {

            // catch si le nœud elements HTML est vide
            try
            {
                // stock les éléments dans un objet Crawler
                $chartResultsList = $crawler->filter('div.o-chart-results-list-row-container');

                // boucle sur la liste
                for ($i = 0; $chartResultsList->count() > $i; $i++) {
                    // stock un élément de la list dans un objet Crawler
                    $chartResult = $chartResultsList->eq($i);
                    // récupère les éléments
                    $songInfoCrawler = $chartResult->filter('ul > li.lrv-u-width-100p > ul > li > h3');
                    $songArtistCrawler = $chartResult->filter('ul > li.lrv-u-width-100p > ul > li > span.c-label.a-no-trucate');
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

            } catch (\InvalidArgumentException $e)
            {
                $this->logger->warning($e->getMessage());
                $this->logger->warning('Un crawl de la page "'. $url .'" a échoué.');
                return [];
            }
        }
        return $displayElement;
    }


/*    /**
     * @param string $url
     * @return array
     *//*
    public function crawlPattern2(string $url): array
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
            $listItem = $crawler->filter('div > div.chart-list-item');
            // boucle sur la liste
            for ($i = 0; $listItem->count() > $i; $i++) {
                // stock un élément de la list dans un objet Crawler
                $chartResult = $listItem->filter('div.chart-list-item__text')->eq($i);
                // récupère les éléments
                $songInfoCrawler = $chartResult->filter('div.chart-list-item__title > span');
                $songArtistCrawler = $chartResult->filter('div.chart-list-item__artist');
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
    }*/
}