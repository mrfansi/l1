<?php

namespace Mrfansi\L1\Queues\Requests;

use Mrfansi\L1\CloudflareRequest;
use Saloon\Enums\Method;

class QueuesDeleteRequest extends CloudflareRequest
{
    protected Method $method = Method::DELETE;

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
