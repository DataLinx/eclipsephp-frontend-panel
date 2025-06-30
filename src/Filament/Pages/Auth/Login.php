<?php

namespace Eclipse\Frontend\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BasePage;
use Illuminate\Support\Facades\App;

class Login extends BasePage
{
    public function mount(): void
    {
        parent::mount();

        if (App::environment('local')) {
            $this->form->fill([
                'email' => 'test@example.com',
                'password' => 'test123',
                'remember' => true,
            ]);
        }
    }
}