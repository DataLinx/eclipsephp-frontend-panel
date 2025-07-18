<?php

namespace Eclipse\Frontend;

use Eclipse\Common\Foundation\Providers\PackageServiceProvider;
use Eclipse\Common\Package;
use Eclipse\Frontend\Providers\FrontendPanelProvider;
use Spatie\LaravelPackageTools\Package as SpatiePackage;

class FrontendServiceProvider extends PackageServiceProvider
{
    public static string $name = 'frontend-panel';

    public function configurePackage(SpatiePackage|Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews();
    }

    public function register(): self
    {
        parent::register();

        $this->app->register(FrontendPanelProvider::class);

        return $this;
    }
}
