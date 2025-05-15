<?php

namespace Mrfansi\L1\Test;

use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        // Skip migrations for KV and Queues tests
        if (!$this->isKVOrQueuesTest()) {
            $this->loadLaravelMigrations(['--database' => 'd1']);
        }

        $this->withFactories(__DIR__.'/database/factories');
    }

    /**
     * Determine if the current test is a KV or Queues test.
     *
     * @return bool
     */
    protected function isKVOrQueuesTest(): bool
    {
        $testClass = get_class($this);
        return $testClass === 'Mrfansi\L1\Test\KVTest' || $testClass === 'Mrfansi\L1\Test\QueuesTest';
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            \Mrfansi\L1\L1ServiceProvider::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'wslxrEFGWY6GfGhvN9L3wH3KSRJQQpBD');
        $app['config']->set('auth.providers.users.model', Models\User::class);
        $app['config']->set('database.default', 'd1');
        $app['config']->set('database.connections.d1', [
            'driver' => 'd1',
            'prefix' => '',
            'database' => 'DB1',
            'api' => 'http://127.0.0.1:8787/api/client/v4',
            'auth' => [
                'token' => env('CLOUDFLARE_TOKEN', getenv('CLOUDFLARE_TOKEN')),
                'account_id' => env('CLOUDFLARE_ACCOUNT_ID', getenv('CLOUDFLARE_ACCOUNT_ID')),
            ],
        ]);

        // Configure cache for KV tests
        $app['config']->set('cache.stores.kv', [
            'driver' => 'kv',
            'prefix' => 'test_',
        ]);

        // Configure queue for Queues tests
        $app['config']->set('queue.connections.cloudflare', [
            'driver' => 'cloudflare',
            'queue' => 'test-queue',
        ]);
    }
}
