<?php

namespace Eclipse\Frontend\Settings;

use Spatie\LaravelSettings\Settings;

class FrontendSettings extends Settings
{
    public bool $guest_access = false;

    public static function group(): string
    {
        return 'frontend';
    }
}
