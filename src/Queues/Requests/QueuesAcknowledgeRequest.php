<?php

namespace RenokiCo\L1\Queues\Requests;

use RenokiCo\L1\CloudflareRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class QueuesAcknowledgeRequest extends CloudflareRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        $connector,
        protected string $queueName,
        protected string $messageId,
    ) {
        parent::__construct($connector);
    }

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/accounts/%s/workers/queues/%s/messages/%s/acknowledge',
            $this->connector->accountId,
            $this->queueName,
            $this->messageId,
        );
    }

    protected function defaultBody(): array
    {
        return [];
    }
}
