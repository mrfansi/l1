<?php

namespace RenokiCo\L1\KV\Requests;

use RenokiCo\L1\CloudflareRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class KVDeleteKeysRequest extends CloudflareRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::DELETE;

    public function __construct(
        $connector,
        protected string $namespaceId,
        protected array $keys,
    ) {
        parent::__construct($connector);
    }

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/accounts/%s/storage/kv/namespaces/%s/bulk',
            $this->connector->accountId,
            $this->namespaceId,
        );
    }

    protected function defaultBody(): array
    {
        return [
            'keys' => $this->keys,
        ];
    }
}
