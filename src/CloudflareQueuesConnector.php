<?php

namespace Mrfansi\L1;

use Saloon\Contracts\Response;

class CloudflareQueuesConnector extends CloudflareConnector
{
    public function __construct(
        public ?string $queue = null,
        protected ?string $token = null,
        public ?string $accountId = null,
        public string $apiUrl = 'https://api.cloudflare.com/client/v4',
    ) {
        parent::__construct($token, $accountId, $apiUrl);
    }

    /**
     * List all queues.
     *
     * @return \Saloon\Contracts\Response
     */
    public function listQueues(): Response
    {
        return $this->send(
            new Queues\Requests\QueuesListRequest($this),
        );
    }

    /**
     * Create a new queue.
     *
     * @param string $queueName The name of the queue
     * @return \Saloon\Contracts\Response
     */
    public function createQueue(string $queueName): Response
    {
        return $this->send(
            new Queues\Requests\QueuesCreateRequest($this, $queueName),
        );
    }

    /**
     * Delete a queue.
     *
     * @param string $queueName The name of the queue to delete
     * @return \Saloon\Contracts\Response
     */
    public function deleteQueue(string $queueName): Response
    {
        return $this->send(
            new Queues\Requests\QueuesDeleteRequest($this, $queueName),
        );
    }

    /**
     * Get queue details.
     *
     * @param string $queueName The name of the queue
     * @return \Saloon\Contracts\Response
     */
    public function getQueue(string $queueName): Response
    {
        return $this->send(
            new Queues\Requests\QueuesGetRequest($this, $queueName),
        );
    }

    /**
     * Publish a message to a queue.
     *
     * @param string $queueName The name of the queue
     * @param mixed $body The message body
     * @param array $options Optional message options
     * @return \Saloon\Contracts\Response
     */
    public function publishMessage(string $queueName, mixed $body, array $options = []): Response
    {
        return $this->send(
            new Queues\Requests\QueuesPublishRequest($this, $queueName, $body, $options),
        );
    }

    /**
     * Publish multiple messages to a queue.
     *
     * @param string $queueName The name of the queue
     * @param array $messages Array of message objects with body and optional metadata
     * @return \Saloon\Contracts\Response
     */
    public function publishBulkMessages(string $queueName, array $messages): Response
    {
        return $this->send(
            new Queues\Requests\QueuesPublishBulkRequest($this, $queueName, $messages),
        );
    }

    /**
     * Get messages from a queue.
     *
     * @param string $queueName The name of the queue
     * @param int $batch The number of messages to get
     * @param int $visibility The visibility timeout in seconds
     * @return \Saloon\Contracts\Response
     */
    public function getMessages(string $queueName, int $batch = 1, int $visibility = 30): Response
    {
        return $this->send(
            new Queues\Requests\QueuesGetMessagesRequest($this, $queueName, $batch, $visibility),
        );
    }

    /**
     * Acknowledge a message (delete after processing).
     *
     * @param string $queueName The name of the queue
     * @param string $messageId The ID of the message to acknowledge
     * @return \Saloon\Contracts\Response
     */
    public function acknowledgeMessage(string $queueName, string $messageId): Response
    {
        return $this->send(
            new Queues\Requests\QueuesAcknowledgeRequest($this, $queueName, $messageId),
        );
    }
}
