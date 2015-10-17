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

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use NilPortugues\Api\HalJson\HalJsonTransformer;
use NilPortugues\Laravel5\HalJsonSerializer\Mapper\Mapper;

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
        $this->mergeConfigFrom(__DIR__.self::PATH, 'haljson');
        $this->app->singleton(\NilPortugues\Laravel5\HalJsonSerializer\HalJsonSerializer::class, function ($app) {
                $mapping = $app['config']->get('haljson');
                $key = md5(json_encode($mapping));
                $cachedMapping = Cache::get($key);
                if (!empty($cachedMapping)) {
                    return unserialize($cachedMapping);
                }
                self::parseNamedRoutes($mapping);
                $serializer = new HalJsonSerializer(new HalJsonTransformer(new Mapper($mapping)));
                Cache::put($key, serialize($serializer),60*60*24);

                return $serializer;
            });
    }
    /**
     * @param array $mapping
     *
     * @return mixed
     */
    private static function parseNamedRoutes(array &$mapping)
    {
        foreach ($mapping as &$map) {
            self::parseUrls($map);
        }
    }
    /**
     * @param array $map
     */
    private static function parseUrls(array &$map)
    {
        if (!empty($map['urls'])) {
            foreach ($map['urls'] as &$namedUrl) {
                $namedUrl = urldecode(route($namedUrl));
            }
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['haljson'];
    }
}
