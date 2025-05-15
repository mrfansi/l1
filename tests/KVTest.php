<?php

namespace Mrfansi\L1\Test;

use Mrfansi\L1\CloudflareKVConnector;
use Mrfansi\L1\KV\KVStore;
use Saloon\Contracts\Response;

class KVTest extends TestCase
{
    public function testKVStoreImplementsLaravelCacheStoreInterface()
    {
        $connector = new CloudflareKVConnector(
            'test-namespace',
            'test-token',
            'test-account-id'
        );
        
        $store = new KVStore($connector, ['prefix' => 'test_']);
        
        $this->assertInstanceOf('Illuminate\Contracts\Cache\Store', $store);
    }
    
    public function testKVStoreHasExpectedMethods()
    {
        $methods = [
            'get',
            'put',
            'increment',
            'decrement',
            'forever',
            'forget',
            'many',
            'putMany',
            'flush',
            'getPrefix'
        ];
        
        foreach ($methods as $method) {
            $this->assertTrue(method_exists(KVStore::class, $method), "KVStore missing method: {$method}");
        }
    }
    
    public function testKVStoreConstructorSetsPrefix()
    {
        $connector = new CloudflareKVConnector(
            'test-namespace',
            'test-token',
            'test-account-id'
        );
        
        $store = new KVStore($connector, ['prefix' => 'custom_prefix_']);
        
        $this->assertEquals('custom_prefix_', $store->getPrefix());
    }
}
