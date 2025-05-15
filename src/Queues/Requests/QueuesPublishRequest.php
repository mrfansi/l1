<?php

namespace Mrfansi\L1\Queues\Requests;

use Mrfansi\L1\CloudflareRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

class QueuesPublishRequest extends CloudflareRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        $connector,
        protected string $queueName,
        protected mixed $messageBody,
        protected array $options = [],
    ) {
        parent::__construct($connector);
    }

    public function resolveEndpoint(): string
    {
        return sprintf(
            '/accounts/%s/workers/queues/%s/messages',
            $this->connector->accountId,
            $this->queueName,
        );
    }

    protected function defaultBody(): array
    {
        $message = [
            'body' => $this->messageBody,
        ];

        if (!empty($this->options)) {
            $message = array_merge($message, $this->options);
        }

        return $message;
    }
}
