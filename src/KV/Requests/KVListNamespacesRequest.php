<?php

namespace RenokiCo\L1\KV\Requests;

use RenokiCo\L1\CloudflareRequest;
use Saloon\Enums\Method;

class KVListNamespacesRequest extends CloudflareRequest
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/accounts/%s/storage/kv/namespaces',
            $this->connector->accountId,
        );
    }
}
