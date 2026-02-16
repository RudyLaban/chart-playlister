<?php

namespace App\Factory;

use App\Entity\ChartEntry;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<ChartEntry>
 */
final class ChartEntryFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return ChartEntry::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'chart' => ChartFactory::random(),
            'song' => SongFactory::random(),
            'position' => self::faker()->numberBetween(1, 100),
            'snapshotDate' => self::faker()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
