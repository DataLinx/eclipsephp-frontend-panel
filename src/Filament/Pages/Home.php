<?php

namespace Eclipse\Frontend\Filament\Pages;

use Filament\Pages\Page;

class Home extends Page
{
    protected static ?int $navigationSort = -1;

    protected static ?string $navigationIcon = '';

    protected static string $view = 'frontend-panel::filament.pages.home';

    public function getHeading(): string
    {
        return '';
    }

    public static function getSlug(): string
    {
        return '/';
    }
}
