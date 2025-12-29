<?php

namespace App\Providers;

use App\Http\Services\Api\V1\Admin\Medium\MediumService;
use App\Http\Services\Api\V1\Admin\Medium\MediumServiceImpl;
use App\Http\Services\Api\V1\Admin\Permission\PermissionService;
use App\Http\Services\Api\V1\Admin\Permission\PermissionServiceImpl;
use App\Http\Services\Api\V1\Admin\Role\RoleService;
use App\Http\Services\Api\V1\Admin\Role\RoleServiceImpl;
use App\Http\Services\Api\V1\Admin\User\UserService;
use App\Http\Services\Api\V1\Admin\User\UserServiceImpl;
use App\Http\Services\Api\V1\Auth\AuthService;
use App\Http\Services\Api\V1\Auth\AuthServiceImpl;
use Illuminate\Support\ServiceProvider;

class ServiceBindingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            AuthService::class,
            AuthServiceImpl::class
        );

        $this->app->bind(
            PermissionService::class,
            PermissionServiceImpl::class
        );

        $this->app->bind(
            RoleService::class,
            RoleServiceImpl::class
        );

        $this->app->bind(
            UserService::class,
            UserServiceImpl::class
        );

        $this->app->bind(
            MediumService::class,
            MediumServiceImpl::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}