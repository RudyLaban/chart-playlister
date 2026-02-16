<?php

namespace App\DataFixtures;

use App\Factory\ArtistFactory;
use App\Factory\ChartEntryFactory;
use App\Factory\ChartFactory;
use App\Factory\ChartProviderFactory;
use App\Factory\SongFactory;
use App\Factory\SpotifyPlaylistFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. Créer 3 ChartProviders réalistes
        $billboard = ChartProviderFactory::createOne([
            'name' => 'Billboard',
            'slug' => 'billboard',
            'baseUrl' => 'https://www.billboard.com',
            'active' => true,
        ]);

        $ukCharts = ChartProviderFactory::createOne([
            'name' => 'UK Official Charts',
            'slug' => 'uk-official-charts',
            'baseUrl' => 'https://www.officialcharts.com',
            'active' => true,
        ]);

        $spotifyCharts = ChartProviderFactory::createOne([
            'name' => 'Spotify Charts',
            'slug' => 'spotify-charts',
            'baseUrl' => 'https://charts.spotify.com',
            'active' => false, // Pas encore implémenté
        ]);

        // 2. Créer 10 Charts répartis sur les providers
        ChartFactory::createOne([
            'name' => 'Hot 100',
            'slug' => 'hot-100',
            'provider' => $billboard,
            'imageUrl' => 'https://picsum.photos/seed/hot100/640/480',
            'scrapingUrl' => 'https://www.billboard.com/charts/hot-100/',
        ]);

        ChartFactory::createOne([
            'name' => 'Billboard 200',
            'slug' => 'billboard-200',
            'provider' => $billboard,
            'imageUrl' => 'https://picsum.photos/seed/bb200/640/480',
            'scrapingUrl' => 'https://www.billboard.com/charts/billboard-200/',
        ]);

        ChartFactory::createOne([
            'name' => 'Global 200',
            'slug' => 'global-200',
            'provider' => $billboard,
            'imageUrl' => 'https://picsum.photos/seed/global200/640/480',
        ]);

        ChartFactory::createOne([
            'name' => 'UK Singles Chart',
            'slug' => 'uk-singles',
            'provider' => $ukCharts,
            'imageUrl' => 'https://picsum.photos/seed/uksingles/640/480',
        ]);

        ChartFactory::createOne([
            'name' => 'UK Albums Chart',
            'slug' => 'uk-albums',
            'provider' => $ukCharts,
            'imageUrl' => 'https://picsum.photos/seed/ukalbums/640/480',
        ]);

        // 5 charts aléatoires supplémentaires
        ChartFactory::createMany(5);

        // 3. Créer 20 Artists
        ArtistFactory::createMany(20);

        // 4. Créer 30 Songs (avec 1-3 artistes chacune grâce au afterInstantiate)
        SongFactory::createMany(30);

        // 5. Créer 50 ChartEntries (simuler 3-4 snapshots de charts)
        ChartEntryFactory::createMany(50);

        // 6. Créer 3 SpotifyPlaylists
        SpotifyPlaylistFactory::createMany(3);
    }
}
