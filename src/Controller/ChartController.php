<?php

namespace App\Controller;

use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChartController extends AbstractController
{
    /**
     * @Route("/chart", name="chart")
     */
    public function index(): Response
    {
        // TODO-RLA: Externaliser le traitement dans un Manager dédié
        // création du crawler
        $client = new Client();
        $url = "https://www.billboard.com/charts/hot-100";
        $crawler = $client->request('GET', $url);
        // compte les elements de la liste à racuperer
        $elementCount = $crawler->filter('div > div > ol')->count();
        $displayElement = [];
        // si des eléments ont été trouvés
        if($elementCount > 0){
            // stock les éléments dans un objet Crawler
            $olElementsList = $crawler->filter('div > div > ol')->children();
            // boucle sur la liste
            for($i = 0; $olElementsList->count() > $i; $i++) {
                // stock un élément de la list dans un objet Crawler
                $liElementsList = $olElementsList->filter('li')->eq($i);
                // recupère les infos de l'element
                $songInfo = $liElementsList->filter('button > span > span.chart-element__information__song');
                $songArtist = $liElementsList->filter('button > span > span.chart-element__information__artist');
                // stock les infos dans un tableau pour affichage
                $displayElement[$i] = [$songInfo->getNode(0)->nodeValue, $songArtist->getNode(0)->nodeValue];
            }
            echo "Plailist de la page $url <pre>"; print_r($displayElement);echo "</pre>";
        } else {
            echo "No Links Found";
        }
        die;


        /*return $this->render('chart/index.html.twig', [
            'controller_name' => 'ChartController',
        ]);*/
    }
}
