<?php

namespace App\Factory;

use App\Entity\Song;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Song>
 */
final class SongFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Song::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->sentence(3),
            'spotifyId' => null,
        ];
    }

    /**
     * Ajouter 1 à 3 artistes à chaque chanson
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(Song $song): void {
                $artistCount = self::faker()->numberBetween(1, 3);
                for ($i = 0; $i < $artistCount; $i++) {
                    $song->addArtist(ArtistFactory::random());
                }
            })
        ;
    }
}
