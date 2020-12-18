<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NavigateController extends AbstractController
{
    /**
     * @Route("/home" , name="home")
     */
    public function indexAction(): Response {

        return $this->render('navigate/index.html.twig');
    }

    /**
     * @Route("/playlist" , name="playlist")
     */
    public function playlistAction(): Response {

        return $this->render('navigate/episode.html.twig');
    }

    /**
     * @Route("/playlists" , name="playlists")
     */
    public function playlistsAction(): Response {

        return $this->render('navigate/episodes.html.twig');
    }

    /**
     * @Route("/blog" , name="blog")
     */
    public function blogAction(): Response {

        return $this->render('navigate/blog.html.twig');
    }

    /**
     * @Route("/contact" , name="contact")
     */
    public function contactAction(): Response {

        return $this->render('navigate/contact.html.twig');
    }

    /**
     * @Route("/about" , name="about")
     */
    public function aboutAction(): Response {

        return $this->render('navigate/about.html.twig');
    }

}
