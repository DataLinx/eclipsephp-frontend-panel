<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('frontend.enable_logins', true);
        $this->migrator->add('frontend.allow_registration', true);
    }
};
