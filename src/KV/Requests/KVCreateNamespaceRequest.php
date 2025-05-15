<?php

namespace RenokiCo\L1\KV\Requests;

use RenokiCo\L1\CloudflareRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class KVCreateNamespaceRequest extends CloudflareRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        $connector,
        protected string $title,
    ) {
        parent::__construct($connector);
    }

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/accounts/%s/storage/kv/namespaces',
            $this->connector->accountId,
        );
    }

    protected function defaultBody(): array
    {
        return [
            'title' => $this->title,
        ];
    }
}
