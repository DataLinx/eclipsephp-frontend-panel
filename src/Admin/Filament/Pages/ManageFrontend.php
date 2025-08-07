<?php

namespace Eclipse\Frontend\Admin\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Eclipse\Common\CommonPlugin;
use Eclipse\Frontend\Settings\FrontendSettings;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;

class ManageFrontend extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static string $settings = FrontendSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Toggle::make('guest_access')
                    ->label('Enable guest access')
                    ->helperText('If enabled, users will not be required to login to access the frontend.'),
            ]);
    }

    public static function getCluster(): ?string
    {
        return app(CommonPlugin::class)->getSettingsCluster();
    }

    public static function getNavigationLabel(): string
    {
        return 'Frontend';
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getNavigationLabel();
    }
}
