<?php

namespace App\Factory;

use App\Entity\Artist;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Artist>
 */
final class ArtistFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Artist::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->name(),
            'spotifyId' => null,
        ];
    }
}
