<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\TodoRepository;
use App\Repositories\TodoRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(TodoRepositoryInterface::class, TodoRepository::class);
    }
}
