<?php

namespace Mrfansi\L1\Queues\Requests;

use Mrfansi\L1\CloudflareRequest;
use Saloon\Enums\Method;

class QueuesGetRequest extends CloudflareRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        $connector,
        protected string $queueName,
    ) {
        parent::__construct($connector);
    }

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/accounts/%s/workers/queues/%s',
            $this->connector->accountId,
            $this->queueName,
        );
    }
}
