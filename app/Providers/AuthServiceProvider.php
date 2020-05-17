<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Services\Auth\JwtGuard;
use App\Services\Auth\MicroUserProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // 扩展 User Provider
        Auth::provider('micro', function($app, array $config){
            // 返回一个实例 Illuminate\Contracts\Auth\UserProvider
            return new MicroUserProvider($config['model']);
        });

        // 扩展 Auth Guard
        Auth::extend('jwt', function($app, $name, array $config) {
            // 返回一个实例 Illuminate\Contracts\Auth\Guard
            return new JwtGuard(Auth::createUserProvider($config['provider']), $app->make('request'));
        });

    }
}
