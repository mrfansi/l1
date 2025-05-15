<?php

namespace Mrfansi\L1\KV;

use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Store;
use Mrfansi\L1\CloudflareKVConnector;

class KVStore extends TaggableStore implements Store
{
    /**
     * The Cloudflare KV connector instance.
     *
     * @var \Mrfansi\L1\CloudflareKVConnector
     */
    protected $connector;

    /**
     * The prefix for the keys.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Create a new KV store.
     *
     * @param  \Mrfansi\L1\CloudflareKVConnector  $connector
     * @param  array  $config
     * @return void
     */
    public function __construct(CloudflareKVConnector $connector, array $config = [])
    {
        $this->connector = $connector;
        $this->prefix = $config['prefix'] ?? '';
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {
        $key = $this->prefix . $key;
        $response = $this->connector->getValue($key);

        if ($response->failed()) {
            return null;
        }

        $value = $response->body();

        return is_string($value) ? $this->unserialize($value) : $value;
    }

    /**
     * Store an item in the cache for a given number of seconds.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $seconds
     * @return bool
     */
    public function put($key, $value, $seconds)
    {
        $key = $this->prefix . $key;
        $value = $this->serialize($value);

        $response = $this->connector->putValue(
            $key,
            $value,
            [],
            null,
            $seconds
        );

        return $response->successful();
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        $key = $this->prefix . $key;
        $current = $this->get($key);

        if (is_null($current)) {
            $this->put($key, $value, 0);
            return $value;
        }

        $new = $current + $value;
        $this->put($key, $new, 0);

        return $new;
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        return $this->increment($key, -$value);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return bool
     */
    public function forever($key, $value)
    {
        return $this->put($key, $value, 0);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key)
    {
        $key = $this->prefix . $key;
        $response = $this->connector->deleteKey($key);

        return $response->successful();
    }

    /**
     * Retrieve multiple items from the cache by key.
     *
     * @param  array  $keys
     * @return array
     */
    public function many(array $keys)
    {
        $results = [];

        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }

        return $results;
    }

    /**
     * Store multiple items in the cache for a given number of seconds.
     *
     * @param  array  $values
     * @param  int  $seconds
     * @return bool
     */
    public function putMany(array $values, $seconds)
    {
        $result = true;

        foreach ($values as $key => $value) {
            $result = $result && $this->put($key, $value, $seconds);
        }

        return $result;
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        $keys = [];
        $cursor = null;

        do {
            $response = $this->connector->listKeys($this->prefix, $cursor);

            if ($response->failed()) {
                return false;
            }

            $data = $response->json();
            $cursor = $data['result_info']['cursor'] ?? null;

            foreach ($data['result'] as $key) {
                $keys[] = $key['name'];
            }

            if (count($keys) >= 1000) {
                $this->connector->deleteKeys(array_slice($keys, 0, 1000));
                $keys = array_slice($keys, 1000);
            }
        } while ($cursor !== null);

        if (!empty($keys)) {
            $this->connector->deleteKeys($keys);
        }

        return true;
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Serialize the value.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function serialize($value)
    {
        return is_numeric($value) || is_string($value) ? $value : serialize($value);
    }

    /**
     * Unserialize the value.
     *
     * @param  string  $value
     * @return mixed
     */
    protected function unserialize($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        $unserializedValue = @unserialize($value);

        return $unserializedValue === false ? $value : $unserializedValue;
    }
}
