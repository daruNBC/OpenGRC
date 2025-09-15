<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use Filament\Resources\Pages\Page;

class AssetsMap extends Page
{
    protected static string $resource = ApplicationResource::class;

    protected static string $view = 'filament.resources.application-resource.pages.assets-map';

    // Set the custom route URL
    protected static ?string $slug = 'map';

    public function getAssetsWithCoordinates()
    {
        // Fetch only assets that have coordinates set
        return Application::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get(['name', 'latitude', 'longitude']);
    }
}