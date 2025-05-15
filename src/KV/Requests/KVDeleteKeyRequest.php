<?php

namespace Mrfansi\L1\KV\Requests;

use Mrfansi\L1\CloudflareRequest;
use Saloon\Enums\Method;

class KVDeleteKeyRequest extends CloudflareRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        $connector,
        protected string $namespaceId,
        protected string $key,
    ) {
        parent::__construct($connector);
    }

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/accounts/%s/storage/kv/namespaces/%s/values/%s',
            $this->connector->accountId,
            $this->namespaceId,
            $this->key,
        );
    }
}
