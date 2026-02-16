<?php

namespace App\Factory;

use App\Entity\SpotifyPlaylist;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<SpotifyPlaylist>
 */
final class SpotifyPlaylistFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return SpotifyPlaylist::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'chart' => ChartFactory::random(),
            'spotifyId' => self::faker()->uuid(),
            'name' => self::faker()->sentence(4),
            'snapshotId' => self::faker()->sha256(),
            'userId' => null,
        ];
    }
}
