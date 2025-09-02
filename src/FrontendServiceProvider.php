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
            ->hasTranslations()
            ->hasViews()
            ->hasSettings();
    }

    public function register(): self
    {
        parent::register();

        if ($this->isFrontendRequest()) {
            $this->app->register(FrontendPanelProvider::class);
        }

        return $this;
    }

    public function isFrontendRequest(): bool
    {
        $uri = explode('/', trim(request()->getRequestUri(), '/'));

        if (count($uri) > 0 && $uri[0] === 'admin') {
            return false;
        }

        return true;
    }
}
