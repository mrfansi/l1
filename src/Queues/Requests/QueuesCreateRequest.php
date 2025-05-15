<?php

namespace Mrfansi\L1\Queues\Requests;

use Mrfansi\L1\CloudflareRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class QueuesCreateRequest extends CloudflareRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        $connector,
        protected string $queueName,
    ) {
        parent::__construct($connector);
    }

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/accounts/%s/workers/queues',
            $this->connector->accountId,
        );
    }

    protected function defaultBody(): array
    {
        return [
            'queue_name' => $this->queueName,
        ];
    }
}
