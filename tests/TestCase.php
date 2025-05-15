<?php

namespace Mrfansi\L1\Test;

use Mockery;
use Dotenv\Dotenv;
use Orchestra\Testbench\TestCase as Orchestra;
use Mrfansi\L1\CloudflareKVConnector;
use Mrfansi\L1\CloudflareQueuesConnector;

abstract class TestCase extends Orchestra
{
    /**
     * Flag to determine if real Cloudflare connections should be used
     * 
     * @var bool
     */
    protected bool $useRealCloudflare = false;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        // Load .env.testing file if it exists
        $this->loadEnvironmentVariables();

        // Check if we should use real Cloudflare
        $this->useRealCloudflare = (bool) ($_SERVER['USE_REAL_CLOUDFLARE'] ?? false);

        // Skip migrations for KV and Queues tests
        if (!$this->isKVOrQueuesTest()) {
            $this->loadLaravelMigrations(['--database' => 'd1']);
        }

        $this->withFactories(__DIR__.'/database/factories');
    }

    /**
     * Load environment variables from .env.testing file
     */
    protected function loadEnvironmentVariables(): void
    {
        if (file_exists(__DIR__ . '/../.env.testing')) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/..', '.env.testing');
            $dotenv->load();
            
            // Copy from .env to $_SERVER for Laravel's environment detection
            foreach ($_ENV as $key => $value) {
                $_SERVER[$key] = $value;
            }
        }
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
     * Create a KV connector instance (real or mock based on configuration)
     * 
     * @return \Mrfansi\L1\CloudflareKVConnector|\Mockery\MockInterface
     */
    protected function createKVConnector()
    {
        if ($this->useRealCloudflare) {
            return new CloudflareKVConnector(
                $_SERVER['CLOUDFLARE_TOKEN'],
                $_SERVER['CLOUDFLARE_ACCOUNT_ID']
            );
        }

        // Return a mock by default
        $mock = Mockery::mock();
        $mock->shouldReceive('__toString')->andReturn('MockedKVConnector');
        return $mock;
    }

    /**
     * Create a Queues connector instance (real or mock based on configuration)
     * 
     * @return \Mrfansi\L1\CloudflareQueuesConnector|\Mockery\MockInterface
     */
    protected function createQueuesConnector()
    {
        if ($this->useRealCloudflare) {
            return new CloudflareQueuesConnector(
                $_SERVER['CLOUDFLARE_TOKEN'],
                $_SERVER['CLOUDFLARE_ACCOUNT_ID']
            );
        }

        // Return a mock by default
        $mock = Mockery::mock();
        $mock->shouldReceive('__toString')->andReturn('MockedQueuesConnector');
        return $mock;
    }

    /**
     * Get the KV namespace ID for testing
     * 
     * @return string
     */
    protected function getKVNamespaceId(): string
    {
        return $_SERVER['CLOUDFLARE_KV_NAMESPACE_ID'] ?? 'test-namespace-id';
    }

    /**
     * Get the KV namespace name for testing
     * 
     * @return string
     */
    protected function getKVNamespaceName(): string
    {
        return $_SERVER['CLOUDFLARE_KV_NAMESPACE_NAME'] ?? 'test-namespace';
    }

    /**
     * Get the queue name for testing
     * 
     * @return string
     */
    protected function getQueueName(): string
    {
        return $_SERVER['CLOUDFLARE_QUEUE_NAME'] ?? 'test-queue';
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
