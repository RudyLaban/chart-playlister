<?php

namespace App\Controller;

use App\Manager\ChartManager;
use Goutte\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChartController extends AbstractController
{
    /**
     * @var ChartManager $chartManager
     */
    protected $chartManager;

    /**
     * ChartController constructor.
     * @param ChartManager $chartManager
     */
    public function __construct(ChartManager $chartManager){
        $this->chartManager = $chartManager;
    }

    /**
     * @Route("/" , name="home")
     */
    public function indexAction(): Response {


        return $this->render('index.html.twig');

    }

    /**
     * @Route("/bill-hot-100", name="bill-hot-100")
     */
    public function billHot100Action(): Response
    {
        $title ='Billboard Hot 100';
        $displayElement = $this->chartManager->crawlBillHot100();

        return $this->render('chart/index.html.twig', [
            'display_element' => $displayElement,
            'title' => $title,
        ]);
    }

    /**
     * @Route("/bill-japan-hot-100", name="bill-japan-Hot-100")
     */
    public function billJapanHot100Action(): Response
    {
        $title ='Billboard Japan Hot 100';
        $displayElement = $this->chartManager->crawlBillJapanHot100();

        return $this->render('chart/index.html.twig', [
            'display_element' => $displayElement,
            'title' => $title,
        ]);
    }
}
