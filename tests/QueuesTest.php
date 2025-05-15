<?php

namespace Mrfansi\L1\Test;

use Mrfansi\L1\CloudflareQueuesConnector;
use Mrfansi\L1\Queues\Requests\QueuesListRequest;
use Mrfansi\L1\Queues\Requests\QueuesPublishRequest;
use Mrfansi\L1\Queues\Requests\QueuesPublishBulkRequest;
use Mrfansi\L1\Queues\Requests\QueuesGetMessagesRequest;
use Mrfansi\L1\Queues\Requests\QueuesAcknowledgeRequest;

class QueuesTest extends TestCase
{
    public function testQueuesConnectorHasExpectedMethods()
    {
        $methods = [
            'listQueues',
            'createQueue',
            'deleteQueue',
            'getQueue',
            'publishMessage',
            'publishBulkMessages',
            'getMessages',
            'acknowledgeMessage'
        ];
        
        foreach ($methods as $method) {
            $this->assertTrue(method_exists(CloudflareQueuesConnector::class, $method), "CloudflareQueuesConnector missing method: {$method}");
        }
    }
    
    public function testQueuesRequestClassesExist()
    {
        $requestClasses = [
            QueuesListRequest::class,
            QueuesPublishRequest::class,
            QueuesPublishBulkRequest::class,
            QueuesGetMessagesRequest::class,
            QueuesAcknowledgeRequest::class
        ];
        
        foreach ($requestClasses as $class) {
            $this->assertTrue(class_exists($class), "Request class does not exist: {$class}");
        }
    }

    public function testQueuesConnectorConstructorSetsProperties()
    {
        $connector = new CloudflareQueuesConnector(
            'test-queue',
            'test-token',
            'test-account-id'
        );
        
        $this->assertInstanceOf(CloudflareQueuesConnector::class, $connector);
        
        // Test that the connector has the expected properties using reflection
        $reflection = new \ReflectionClass($connector);
        $this->assertTrue($reflection->hasProperty('queue'), 'Connector missing queue property');
        $this->assertTrue($reflection->hasProperty('accountId'), 'Connector missing accountId property');
    }
}
