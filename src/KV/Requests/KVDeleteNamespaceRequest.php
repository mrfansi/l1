<?php

namespace Mrfansi\L1\KV\Requests;

use Mrfansi\L1\CloudflareRequest;
use Saloon\Enums\Method;

class KVDeleteNamespaceRequest extends CloudflareRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        $connector,
        protected string $namespaceId,
    ) {
        parent::__construct($connector);
    }

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/accounts/%s/storage/kv/namespaces/%s',
            $this->connector->accountId,
            $this->namespaceId,
        );
    }
}
