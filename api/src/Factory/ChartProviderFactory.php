<?php

namespace App\Factory;

use App\Entity\ChartProvider;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<ChartProvider>
 */
final class ChartProviderFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return ChartProvider::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->unique()->company(),
            'slug' => self::faker()->unique()->slug(),
            'baseUrl' => self::faker()->url(),
            'scrapingConfig' => null,
            'active' => true,
        ];
    }
}
