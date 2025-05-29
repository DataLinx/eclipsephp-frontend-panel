<?php

namespace Eclipse\Frontend;

use Eclipse\Common\Foundation\Providers\PackageServiceProvider;
use Eclipse\Common\Package;
use Spatie\LaravelPackageTools\Package as SpatiePackage;;

class FrontendPanelServiceProvider extends PackageServiceProvider
{
    public static string $name = 'frontend-panel';

    public function configurePackage(SpatiePackage|Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations();
    }
}
