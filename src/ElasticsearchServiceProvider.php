<?php
namespace Starme\Laravel\Es;

use Illuminate\Support\ServiceProvider;
use Starme\Laravel\Es\Eloquent\Model;

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
            return $app['elastic.search']->connection();
        });

        $this->app->singleton('es.schema', function ($app){
            return $app['elastic.connection']->getSchemaBuilder();
        });
    }

}