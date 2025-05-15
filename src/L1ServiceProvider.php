<?php

namespace Mrfansi\L1;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Mrfansi\L1\D1\D1Connection;

class L1ServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerD1();
        $this->registerKV();
        $this->registerQueues();
    }

    /**
     * Register the D1 service.
     *
     * @return void
     */
    protected function registerD1()
    {
        $this->app->resolving('db', function ($db) {
            $db->extend('d1', function ($config, $name) {
                $config['name'] = $name;

                $connection = new D1Connection(
                    new CloudflareD1Connector(
                        $config['database'],
                        $config['auth']['token'],
                        $config['auth']['account_id'],
                        $config['api'] ?? 'https://api.cloudflare.com/client/v4',
                    ),
                    $config,
                );

                return $connection;
            });
        });
    }

    /**
     * Register the KV service.
     *
     * @return void
     */
    protected function registerKV()
    {
        $this->app->singleton('cloudflare.kv', function ($app) {
            $config = $app['config']['cloudflare.kv'] ?? [];

            return new CloudflareKVConnector(
                $config['namespace'] ?? null,
                $config['auth']['token'] ?? null,
                $config['auth']['account_id'] ?? null,
                $config['api'] ?? 'https://api.cloudflare.com/client/v4',
            );
        });

        // Register KV as a cache driver
        $this->app->resolving('cache', function ($cache) {
            $cache->extend('cloudflare-kv', function ($app, $config) {
                return Cache::repository(new KV\KVStore(
                    $app->make('cloudflare.kv'),
                    $config
                ));
            });
        });
    }

    /**
     * Register the Queues service.
     *
     * @return void
     */
    protected function registerQueues()
    {
        $this->app->singleton('cloudflare.queues', function ($app) {
            $config = $app['config']['cloudflare.queues'] ?? [];

            return new CloudflareQueuesConnector(
                $config['queue'] ?? null,
                $config['auth']['token'] ?? null,
                $config['auth']['account_id'] ?? null,
                $config['api'] ?? 'https://api.cloudflare.com/client/v4',
            );
        });
    }
}
