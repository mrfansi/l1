<?php

namespace Mrfansi\L1\Queues\Requests;

use Mrfansi\L1\CloudflareRequest;
use Saloon\Enums\Method;

class QueuesListRequest extends CloudflareRequest
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/accounts/%s/workers/queues',
            $this->connector->accountId,
        );
    }
}
