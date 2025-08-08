<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\TerminalSettingInterface;
use App\Repositories\Interfaces\ClientBaseRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\TerminalSettingRepository;
use App\Repositories\ClientBaseRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TerminalSettingInterface::class, TerminalSettingRepository::class);
        $this->app->bind(ClientBaseRepositoryInterface::class, ClientBaseRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('en');
        date_default_timezone_set(config('app.timezone'));
    }
}
