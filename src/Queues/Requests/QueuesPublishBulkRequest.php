<?php

namespace RenokiCo\L1\Queues\Requests;

use RenokiCo\L1\CloudflareRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class QueuesPublishBulkRequest extends CloudflareRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        $connector,
        protected string $queueName,
        protected array $messages,
    ) {
        parent::__construct($connector);
    }

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/accounts/%s/workers/queues/%s/messages/batch',
            $this->connector->accountId,
            $this->queueName,
        );
    }

    protected function defaultBody(): array
    {
        return [
            'messages' => $this->messages,
        ];
    }
}
