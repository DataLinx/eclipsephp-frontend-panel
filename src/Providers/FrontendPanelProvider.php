<?php

namespace Eclipse\Frontend\Providers;

use Eclipse\Common\Providers\GlobalSearchProvider;
use Eclipse\Core\Models\Site;
use Eclipse\Core\Services\Registry;
use Eclipse\Frontend\Filament\Pages\Auth\Login;
use Eclipse\Frontend\Http\Middleware\RedirectAfterLogin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Platform;
use Filament\Support\Facades\FilamentView;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin;
use Filament\View\PanelsRenderHook;

class FrontendPanelProvider extends PanelProvider
{
    private const PANEL_ID = 'frontend';

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id(self::PANEL_ID)
            ->path('')
            ->login(Login::class)
            ->passwordReset()
            ->emailVerification()
            ->colors([
                'primary' => Color::Cyan,
                'gray' => Color::Slate,
            ])
            ->authGuard(self::PANEL_ID)
            ->topNavigation()
            ->brandName(fn() => Registry::getSite()->name)
            ->discoverResources(in: app_path('Filament/Frontend/Resources'), for: 'App\\Filament\\Frontend\\Resources')
            ->discoverPages(in: app_path('Filament/Frontend/Pages'), for: 'App\\Filament\\Frontend\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Frontend/Widgets'), for: 'App\\Filament\\Frontend\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                RedirectAfterLogin::class
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->globalSearch(GlobalSearchProvider::class)
            ->globalSearchKeyBindings(['ctrl+k', 'command+k'])
            ->globalSearchFieldSuffix(fn(): ?string => match (Platform::detect()) {
                Platform::Windows, Platform::Linux => 'CTRL+K',
                Platform::Mac => '⌘K',
                default => null,
            })
            ->tenant(Site::class, slugAttribute: 'domain')
            ->tenantDomain('{tenant:domain}')
            ->tenantMenu(false)
            ->plugins([
                EnvironmentIndicatorPlugin::make(),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn(): string => self::getThemeIsolationScript(self::PANEL_ID)
            )
            ;
    }

    public function register(): void
    {
        parent::register();

        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/app.js')"));
    }

    private static function getThemeIsolationScript(string $panelId): string
    {
        return "<script>
            ['setItem', 'getItem'].forEach(method => {
                localStorage[method] = new Proxy(localStorage[method], {
                    apply(target, thisArg, args) {
                        if (args[0] === 'theme') args[0] = 'theme_{$panelId}';
                        return target.apply(thisArg, args);
                    }
                });
            });
        </script>";
    }
}
