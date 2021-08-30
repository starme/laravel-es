<?php
namespace Starme\LaravelEs;

use Illuminate\Support\ServiceProvider;
use Starme\LaravelEs\Eloquent\Model;

class ElasticsearchServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/es.php' => config_path('es.php'),
        ], 'es-config');
        
        Model::setConnectionResolver($this->app['es']);

//        Model::setEventDispatcher($this->app['events']);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('es', function ($app){
            return new ConnectionResolver($app);
        });

        $this->app->singleton('es.connection', function ($app){
            return $app['es']->connection();
        });

        $this->app->singleton('es.schema', function ($app){
            return $app['es.connection']->schema();
        });
    }

}