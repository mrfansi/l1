<?php

namespace RenokiCo\L1\KV\Requests;

use RenokiCo\L1\CloudflareRequest;
use Saloon\Enums\Method;

class KVListKeysRequest extends CloudflareRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        $connector,
        protected string $namespaceId,
        protected ?string $prefix = null,
        protected ?string $cursor = null,
        protected ?int $limit = null,
    ) {
        parent::__construct($connector);
    }

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/accounts/%s/storage/kv/namespaces/%s/keys',
            $this->connector->accountId,
            $this->namespaceId,
        );
    }

    protected function defaultQuery(): array
    {
        $query = [];

        if ($this->prefix !== null) {
            $query['prefix'] = $this->prefix;
        }

        if ($this->cursor !== null) {
            $query['cursor'] = $this->cursor;
        }

        if ($this->limit !== null) {
            $query['limit'] = $this->limit;
        }

        return $query;
    }
}
