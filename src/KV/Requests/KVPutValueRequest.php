<?php

namespace RenokiCo\L1\KV\Requests;

use RenokiCo\L1\CloudflareRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class KVPutValueRequest extends CloudflareRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        $connector,
        protected string $namespaceId,
        protected string $key,
        protected mixed $value,
        protected array $metadata = [],
        protected ?int $expiration = null,
        protected ?int $expirationTtl = null,
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

    protected function defaultBody(): array
    {
        $body = [
            'value' => $this->value,
        ];

        if (!empty($this->metadata)) {
            $body['metadata'] = $this->metadata;
        }

        if ($this->expiration !== null) {
            $body['expiration'] = $this->expiration;
        }

        if ($this->expirationTtl !== null) {
            $body['expiration_ttl'] = $this->expirationTtl;
        }

        return $body;
    }
}
