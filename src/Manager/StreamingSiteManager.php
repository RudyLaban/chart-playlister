<?php

namespace App\Manager;

use App\Entity\StreamingSite;
use Doctrine\ORM\EntityManagerInterface;

class StreamingSiteManager
{
    /** @var EntityManagerInterface */
    protected $em;
    /*** @var string */
    private $cpSpotifyUserUrl;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, string $cpSpotifyUserUrl)
    {
        $this->em = $em;
        $this->cpSpotifyUserUrl = $cpSpotifyUserUrl;
    }

    /** Crée l'entré Spotify en base si elle n'extiste pas encore
     *
     * @param $name
     * @return StreamingSite
     */
    function createSpotifyInDB($name): StreamingSite
    {
        $spotify = $this->em->getRepository(StreamingSite::class)->findOneBy(['name' => 'Spotify']);
        if (empty($spotify)) {
            $spotify = new StreamingSite();
            $spotify->setName($name);
            $spotify->setUrl($this->cpSpotifyUserUrl);

            $this->em->persist($spotify);
            $this->em->flush();
        }

        return $spotify;
    }


}