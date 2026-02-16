<?php

namespace App\Factory;

use App\Entity\Chart;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Chart>
 */
final class ChartFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Chart::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->words(3, true),
            'slug' => self::faker()->unique()->slug(),
            'imageUrl' => self::faker()->imageUrl(640, 480, 'music'),
            'scrapingUrl' => self::faker()->url(),
            'provider' => ChartProviderFactory::random(),
        ];
    }
}
