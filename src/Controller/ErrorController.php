<?php

namespace App\Controller;

use App\Entity\Chart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ErrorController extends AbstractController
{
    /**
     * @Route("/erreur", name="error")
     */
    public function error(Request $request): Response
    {
        $headTitle = 'Oups ...';
        $image = '/images/logo_broken.png';
        $text = sprintf('Une erreur est survenue. Merci de <a href="%s">revenir à l\'accueil</a>.', $this->generateUrl('home'));

        // verification du status code pour adapter la vue
            if ($request->attributes->get('exception') instanceof NotFoundHttpException && $request->attributes->get('exception')->getStatusCode() == 404)
            {
                $headTitle = 'Page non trouvée';
                $image = '/images/logo_not_found.png';
                $text = sprintf('La page demandée n\'existe pas. Vérifiez l\'URL ou <a href="%s">revenez à l\'accueil</a>.', $this->generateUrl('home'));
            }

        // Récupération des 3 Chart à afficher en page d'accueil
        $threeLastChart = $this->getDoctrine()->getRepository(Chart::class)->findThreeLastChart();

        return $this->render('bundles/TwigBundle/Exception/error.html.twig', [
            'three_last_chart' => $threeLastChart,
            'head_title' => $headTitle,
            'image' => $image,
            'text' => $text,
        ]);
    }
}
