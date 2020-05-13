<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

use GuzzleHttp\Client as HttpClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Cashier::useCurrency(config('cart.currency'), config('cart.currency_symbol'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 以单例模式绑定 HttpClient 实例到 App 容器
        $this->app->singleton('HttpClient', function ($app) {
            return new HttpClient([
                'base_uri' => config('services.micro.api_gateway'),
                'timeout'  => config('services.micro.timeout'),
                'headers'  => [
                    'Content-Type' => 'application/json'
                ]
            ]);
        });
    }
}
