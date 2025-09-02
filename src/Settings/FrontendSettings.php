<?php

namespace Eclipse\Frontend\Settings;

use Spatie\LaravelSettings\Settings;

class FrontendSettings extends Settings
{
    /**
     * @var bool Allow non-logged in users to access the frontend
     */
    public bool $guest_access = false;

    /**
     * @var bool Enable login functionality
     */
    public bool $enable_logins = true;

    /**
     * @var bool Allow new users to register
     */
    public bool $allow_registration = true;

    public static function group(): string
    {
        return 'frontend';
    }
}
