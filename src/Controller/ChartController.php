<?php

namespace App\Controller;

use App\Entity\Chart;
use App\Entity\ChartSite;
use App\Entity\ChartSong;
use App\Entity\Song;
use App\Form\ChartAddImageType;
use App\Manager\ChartManager;
use App\Repository\ChartRepository;
use App\Repository\ChartSiteRepository;
use App\Repository\ChartSongRepository;
use App\Repository\SongRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * Gère et traite le formulaire de modification d'image de Chart
     *
     * @Route("/chart_site/{chartSiteId}/chart/{chartId}", name="show_chart")
     * @param int $chartSiteId Id du ChartSite de la Chart
     * @param int $chartId Id de la Chart
     * @param Request $request
     * @param UploaderHelper $uploaderHelper
     * @return Response
     */
    public function showChart(int $chartSiteId, int $chartId, Request $request, UploaderHelper $uploaderHelper): Response
    {
        // récupération du ChartSite et de la Chart
        $chartSite = $this->chartSiteRepo->find($chartSiteId);
        $chart = $this->chartRepo->find($chartId);

        if (!$chartSite || !$chart)
        {
            $this->addFlash('warning', 'La Chart  n\'a pas été trouvée.');
            return $this->redirectToRoute('home');
        }

        // formulaire de soumissions d'image pour une chart
        $form = $this->createForm(ChartAddImageType::class);
        // récupération du formulaire
        $form->handleRequest($request);
        // le formulaire est soumis
        if($form->isSubmitted())
        {
            // le formulaire est valide
            if($form->isValid())
            {
                // récupération de de l'image dans le form
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $form['imageFile']->getData();
                if ($uploadedFile) {
                    // utilisation du service UploaderHelper
                    $newFilename = $uploaderHelper->uploadChartImage($uploadedFile, $chart);
                    // set de l'image
                    $chart->setImageFileName($newFilename);
                    $this->em->persist($chart);
                    $this->em->flush();

                    $this->addFlash('success', 'La pochette de la playlist '. $chart->getName() .' a bien été mise à jours.');
                }
            }
            else
            {
                // récupération du message d'erreur si le form n'est pas valide
                $errorMessage = $form->all()['imageFile']->getErrors()->getChildren()->getMessage();
                $this->addFlash('warning', $errorMessage);
            }
        }

        return $this->render('navigate/episode.html.twig', [
            'chart' => $chart,
            'chartAddImageForm' => $form->createView(),
        ]);
    }

    /**
     * Route menant à la liste de toutes les Charts
     *
     * @Route("/charts", name="charts")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function showAllCharts(PaginatorInterface $paginator, Request $request)
    {
        // formatage de la requête en utilisant le paginator
        $chartList = $paginator->paginate($this->chartRepo->findAllChartQuery(), $request->query->getInt('page',1),6);

        if (!$chartList)
        {
            $this->addFlash('warning', 'Aucunes playlist n\'existe pour le moment.');
            return $this->redirectToRoute('home');
        }

        return $this->render('navigate/episodes.html.twig', [
            'charts' => $chartList,
        ]);

    }
}
