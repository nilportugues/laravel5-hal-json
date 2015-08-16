<?php

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 8/15/15
 * Time: 5:45 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Laravel5\HalJsonSerializer;

use Illuminate\Support\ServiceProvider;

class Laravel5HalJsonSerializerServiceProvider extends ServiceProvider
{
    const PATH = '/../../../config/haljson.php';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([__DIR__.self::PATH => config('haljson.php')]);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.self::PATH, 'haljson_mapping');
        $this->app->singleton(\NilPortugues\Serializer\Serializer::class, function ($app) {
            return HalJsonSerializer::instance($app['config']->get('haljson_mapping'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['haljson_mapping'];
    }
}
