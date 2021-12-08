<?php

namespace App\Manager;

use App\Entity\StreamingSite;
use Doctrine\ORM\EntityManagerInterface;

class StreamingSiteManager
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /** Crée l'entré Spotify en base si elle n'extiste pas encore
    *
    * @return StreamingSite
    */
    function createSotifyInDB($name): StreamingSite
    {
        $spotify = $this->em->getRepository(StreamingSite::class)->findOneBy(['name' => 'Spotify']);
        if (empty($spotify)) {
            $spotify = new StreamingSite();
            $spotify->setName($name);
            $spotify->setUrl($_ENV('CHART_PLAYLISTER_SPOTIFY_USER_URL'));

            $this->em->persist($spotify);
            $this->em->flush();
        }

        return $spotify;
    }


}