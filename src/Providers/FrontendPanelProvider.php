<?php

namespace Eclipse\Frontend\Providers;

use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use Eclipse\Common\Providers\GlobalSearchProvider;
use Eclipse\Core\Models\Site;
use Eclipse\Core\Services\Registry;
use Eclipse\Frontend\Filament\Pages as CustomPages;
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
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin;

class FrontendPanelProvider extends PanelProvider
{
    private const PANEL_ID = 'frontend';

    public function panel(Panel $panel): Panel
    {
        $middleware = [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ];

        $widgets = [];
        $pages = [];

        if (self::isGuestPanel()) {
            $middleware[] = AuthenticateSession::class;
            $pages[] = CustomPages\Home::class;
        } else {
            $widgets = array_merge($widgets, [
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ]);
            $pages[] = Pages\Dashboard::class;
        }

        $panel
            ->id(self::PANEL_ID)
            ->path('')
            ->login()
            ->passwordReset()
            ->emailVerification()
            ->colors([
                'primary' => Color::Cyan,
                'gray' => Color::Slate,
            ])
            ->authGuard(self::PANEL_ID)
            ->topNavigation()
            ->brandName(fn () => Registry::getSite()->name)
            ->discoverResources(in: app_path('Filament/Frontend/Resources'), for: 'App\\Filament\\Frontend\\Resources')
            ->discoverPages(in: app_path('Filament/Frontend/Pages'), for: 'App\\Filament\\Frontend\\Pages')
            ->discoverWidgets(in: app_path('Filament/Frontend/Widgets'), for: 'App\\Filament\\Frontend\\Widgets')
            ->pages($pages)
            ->globalSearch(GlobalSearchProvider::class)
            ->globalSearchKeyBindings(['ctrl+k', 'command+k'])
            ->globalSearchFieldSuffix(fn (): ?string => match (Platform::detect()) {
                Platform::Windows, Platform::Linux => 'CTRL+K',
                Platform::Mac => '⌘K',
                default => null,
            })
            ->tenant(Site::class, slugAttribute: 'domain')
            ->tenantDomain('{tenant:domain}')
            ->tenantMiddleware([
                SyncShieldTenant::class,
            ], isPersistent: true)
            ->tenantMenu(false)
            ->widgets($widgets)
            ->middleware($middleware)
            ->plugins(array_merge([
                EnvironmentIndicatorPlugin::make(),
            ], app(Registry::class)->getPlugins()));

        match (self::isGuestPanel()) {
            true => $panel
                ->renderHook(
                    PanelsRenderHook::TOPBAR_END,
                    fn () => view('frontend-panel::filament.components.theme-switcher')
                ),
            false => $panel
                ->authMiddleware([
                    Authenticate::class,
                ])
                ->renderHook(
                    PanelsRenderHook::HEAD_START,
                    fn (): string => self::getThemeIsolationScript(self::PANEL_ID)
                )
        };

        return $panel;
    }

    public function register(): void
    {
        parent::register();
        FilamentView::registerRenderHook('panels::body.end', fn (): string => Blade::render("@vite('resources/js/app.js')"));
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

    private static function isGuestPanel(): bool
    {
        return config('frontend-panel.guest_access');
    }
}
