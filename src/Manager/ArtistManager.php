<?php


namespace App\Manager;


use App\Entity\Artist;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ArtistManager
{
    /** @var ContainerInterface */
    protected $container;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var ArtistRepository */
    protected $artistRepo;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * ArtistManager constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $em, LoggerInterface $logger){

        $this->container = $container;
        $this->em = $em;
        $this->artistRepo = $this->em->getRepository(Artist::class);
        $this->logger = $logger;
    }

    /**
     * Récupère et retourne la liste d'artiste de la chart
     *
     * @param array $chartSongsElements Une liste contenant les infos des artistes
     * @return array|mixed La liste d'artiste de la chart
     */
    public function artistListFormatter(array $chartSongsElements): array
    {
        $artistList = [];
        foreach ($chartSongsElements as $item)
        {
            array_push($artistList, $item['artist']);
        }
        return $artistList;
    }

    /**
     * Crée les Artist en base s'ils n'existent pas encore
     *
     * @param array $artistElementList Une liste contenant les infos des Artist à créer
     * @return Artist[]|array Une liste des Artist créés
     */
    public function createArtistsOfChart(array $artistElementList): array
    {
        $artistList = [];
        foreach ($artistElementList as $artistElement)
        {
            $artist = $this->createArtist($artistElement);
            // rempli la liste d'Artist à retourner
            array_push($artistList, $artist);
        }
        return $artistList;
    }

    /**
     * Creation en base d'un Artist
     *
     * @param String $artistElement Le nom de l'Artist
     * @return Artist L'Artist créé ou trouvé en base s'il existe
     */
    public function createArtist(String $artistElement): Artist
    {
        // cherche si l'Artist existe déjà en base
        $artist = $this->artistRepo->findOneBy(['name' => $artistElement]);
        // si il n'existe pas, on le crée
        if (empty($artist))
        {
            $artist = new Artist();
            $artist->setName($artistElement);

            $this->em->persist($artist);
            $this->em->flush();
        }
        return $artist;
    }
}