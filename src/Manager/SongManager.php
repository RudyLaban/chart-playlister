<?php


namespace App\Manager;


use App\Entity\Artist;
use App\Entity\Song;
use App\Repository\ArtistRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SongManager
{
    /** @var ContainerInterface */
    protected $container;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var SongRepository */
    protected $songRepo;

    /** @var ArtistRepository */
    protected $artistRepo;

    /** @var ArtistManager */
    protected $artistManager;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * ArtistManager constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $em
     * @param ArtistManager $artistManager
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $em, ArtistManager $artistManager, LoggerInterface $logger){

        $this->container = $container;
        $this->em = $em;
        $this->songRepo = $this->em->getRepository(Song::class);
        $this->artistRepo = $this->em->getRepository(Artist::class);
        $this->artistManager = $artistManager;
        $this->logger = $logger;
    }

    /**
     * Crée les Song en base s'ils n'existent pas encore
     *
     * @param array $chartSongsElements Une liste des Song à créer
     * @return Song|Song[]|object[] Une liste des Song créés
     */
    public function createSongsOfChart(array $chartSongsElements)
    {
        $songsList = [];
        // pour chaque element de la chart, on crée un Song en base
        foreach ($chartSongsElements as $songElement)
        {
            // cherche si l'Artist du Song existe déjà en base
            /** @var Artist $songArtist */
            $songArtist = $this->artistRepo->findOneBy(['name' => $songElement['artist']]);
            // si il n'existe pas, on le crée
            if (empty($songArtist))
            {
                $songArtist = $this->artistManager->createArtistsOfChart($songElement['artist']);
            }
            $song = $this->createSong($songElement['song'], $songArtist);
            array_push($songsList, $song);
        }
        return $songsList;
    }

    /**
     * Creation en base d'un Song
     *
     * @param String $songElement Le nom du Song
     * @param Artist $songArtist L'Artist du Song
     * @return Song Le Song créé ou trouvé en base s'il existe
     */
    public function createSong(String $songElement, Artist $songArtist): Song
    {
        // cherche si le Song existe déjà en base
        $song = $this->songRepo->findOneBy([
            'name' => $songElement,
            'artist' => $songArtist->getId(),
        ]);
        // si il n'existe pas, on le crée
        if (empty($song))
        {
            $song = new Song();
            $song->setName($songElement);
            $song->setArtist($songArtist);

            $this->em->persist($song);
            $this->em->flush();
        }
        return $song;
    }

}