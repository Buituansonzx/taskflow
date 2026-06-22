<?php

namespace App\Containers\ClientSection\Task\Providers;

use App\Containers\ClientSection\Task\Models\Task;
use App\Observers\TaskObserver;
use App\Ship\Parents\Providers\ServiceProvider as ParentServiceProvider;

final class ServiceProvider extends ParentServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Task::observe(TaskObserver::class);
    }
}
