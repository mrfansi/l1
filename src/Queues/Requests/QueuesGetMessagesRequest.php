<?php

namespace Mrfansi\L1\Queues\Requests;

use Mrfansi\L1\CloudflareRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class QueuesGetMessagesRequest extends CloudflareRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        $connector,
        protected string $queueName,
        protected int $batch = 1,
        protected int $visibility = 30,
    ) {
        parent::__construct($connector);
    }

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/accounts/%s/workers/queues/%s/messages/consume',
            $this->connector->accountId,
            $this->queueName,
        );
    }

    protected function defaultBody(): array
    {
        return [
            'batch_size' => $this->batch,
            'visibility_timeout' => $this->visibility,
        ];
    }
}
