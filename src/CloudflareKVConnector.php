<?php

namespace RenokiCo\L1;

use Saloon\Contracts\Response;

class CloudflareKVConnector extends CloudflareConnector
{
    public function __construct(
        public ?string $namespace = null,
        protected ?string $token = null,
        public ?string $accountId = null,
        public string $apiUrl = 'https://api.cloudflare.com/client/v4',
    ) {
        parent::__construct($token, $accountId, $apiUrl);
    }

    /**
     * List all KV namespaces.
     *
     * @return \Saloon\Contracts\Response
     */
    public function listNamespaces(): Response
    {
        return $this->send(
            new KV\Requests\KVListNamespacesRequest($this),
        );
    }

    /**
     * Create a new KV namespace.
     *
     * @param string $title The title of the namespace
     * @return \Saloon\Contracts\Response
     */
    public function createNamespace(string $title): Response
    {
        return $this->send(
            new KV\Requests\KVCreateNamespaceRequest($this, $title),
        );
    }

    /**
     * Delete a KV namespace.
     *
     * @param string $namespaceId The ID of the namespace to delete
     * @return \Saloon\Contracts\Response
     */
    public function deleteNamespace(string $namespaceId): Response
    {
        return $this->send(
            new KV\Requests\KVDeleteNamespaceRequest($this, $namespaceId),
        );
    }

    /**
     * List keys in a namespace.
     *
     * @param string|null $prefix Filter keys by prefix
     * @param string|null $cursor Pagination cursor
     * @param int|null $limit Maximum number of keys to return
     * @return \Saloon\Contracts\Response
     */
    public function listKeys(?string $prefix = null, ?string $cursor = null, ?int $limit = null): Response
    {
        return $this->send(
            new KV\Requests\KVListKeysRequest($this, $this->namespace, $prefix, $cursor, $limit),
        );
    }

    /**
     * Get a value from KV.
     *
     * @param string $key The key to retrieve
     * @return \Saloon\Contracts\Response
     */
    public function getValue(string $key): Response
    {
        return $this->send(
            new KV\Requests\KVGetValueRequest($this, $this->namespace, $key),
        );
    }

    /**
     * Put a value in KV.
     *
     * @param string $key The key to store
     * @param mixed $value The value to store
     * @param array $metadata Optional metadata to store with the key
     * @param int|null $expiration Optional expiration in seconds
     * @param int|null $expirationTtl Optional expiration TTL in seconds
     * @return \Saloon\Contracts\Response
     */
    public function putValue(string $key, mixed $value, array $metadata = [], ?int $expiration = null, ?int $expirationTtl = null): Response
    {
        return $this->send(
            new KV\Requests\KVPutValueRequest($this, $this->namespace, $key, $value, $metadata, $expiration, $expirationTtl),
        );
    }

    /**
     * Delete a key from KV.
     *
     * @param string $key The key to delete
     * @return \Saloon\Contracts\Response
     */
    public function deleteKey(string $key): Response
    {
        return $this->send(
            new KV\Requests\KVDeleteKeyRequest($this, $this->namespace, $key),
        );
    }

    /**
     * Delete multiple keys from KV.
     *
     * @param array $keys The keys to delete
     * @return \Saloon\Contracts\Response
     */
    public function deleteKeys(array $keys): Response
    {
        return $this->send(
            new KV\Requests\KVDeleteKeysRequest($this, $this->namespace, $keys),
        );
    }
}
