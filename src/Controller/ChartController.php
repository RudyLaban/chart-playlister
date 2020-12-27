<?php

namespace App\Controller;

use App\Entity\Chart;
use App\Entity\ChartSite;
use App\Entity\ChartSong;
use App\Entity\Song;
use App\Manager\ChartManager;
use App\Repository\ChartRepository;
use App\Repository\ChartSiteRepository;
use App\Repository\ChartSongRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChartController extends AbstractController
{
    /** @var ContainerInterface */
    protected $container;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var ChartManager */
    protected $chartManager;

    /** @var ChartRepository */
    protected $chartRepo;

    /** @var ChartSiteRepository */
    protected $chartSiteRepo;

    /** @var ChartSongRepository */
    protected $chartSongRepo;

    /** @var SongRepository */
    protected $songRepo;

    /**
     * ChartController constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $em
     * @param ChartManager $chartManager
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $em, ChartManager $chartManager)
    {
        $this->container = $container;
        $this->em = $em;
        $this->chartRepo = $this->em->getRepository(Chart::class);
        $this->chartSiteRepo = $this->em->getRepository(ChartSite::class);
        $this->chartSongRepo = $this->em->getRepository(ChartSong::class);
        $this->songRepo = $this->em->getRepository(Song::class);
        $this->chartManager = $chartManager;
    }

    /**
     * Route menant à la chart envoyée en paramètre
     *
     * @Route("/chart_site/{chartSiteId}/chart/{chartId}", name="show_chart")
     * @param int $chartSiteId Id du ChartSite de la Chart
     * @param int $chartId Id de la Chart
     * @return Response
     */
    public function showChart(int $chartSiteId, int $chartId): Response
    {
        $elementOfChartSong = [];
        $data = [];
        // récupération du ChartSite et de la Chart
        $chartSite = $this->chartSiteRepo->find($chartSiteId);
        $chart = $this->chartRepo->find($chartId);

        if (!$chartSite || !$chart)
        {
            throw $this->createNotFoundException('La Chart '.$chartId.' du site '.$chartSiteId.' n\'a pas été trouvée.');
        }
        // récupération des ChartSong de la Chart
        $chartSongs = $chart->getChartSongs();

        if (!$chartSongs)
        {
            throw $this->createNotFoundException('La Chart '.$chart->getName().' n\'a pas de chansons liées.');
        }

        // pour chaque ChartSong
        foreach ($chartSongs as $chartSong)
        {
            // récupération du nom et de l'artiste
            $elementOfChartSong['song_name'] = $chartSong->getSong()->getName();
            $elementOfChartSong['artist_name'] = $chartSong->getSong()->getArtist()->getName();
            // stock les infos dans un tableau
            $data[$chartSong->getPosition()] = $elementOfChartSong;
        }

        return $this->render('navigate/episode.html.twig', [
            'chart_name' => $chart->getName(),
            'chart_site_name' => $chartSite->getName(),
            'data' => $data,
        ]);
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
