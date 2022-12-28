<?php

declare(strict_types=1);

namespace App\Providers;

use App\ApplicationServices\TodoApplicationService;
use App\ApplicationServices\TodoApplicationServiceInterface;
use Illuminate\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(TodoApplicationServiceInterface::class, TodoApplicationService::class);
    }
}
