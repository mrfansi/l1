# L1 - Cloudflare bindings for Laravel

![CI](https://github.com/renoki-co/l1/workflows/CI/badge.svg?branch=master)
[![codecov](https://codecov.io/gh/renoki-co/l1/branch/master/graph/badge.svg)](https://codecov.io/gh/renoki-co/l1/branch/master)
[![StyleCI](https://github.styleci.io/repos/651202208/shield?branch=master)](https://github.styleci.io/repos/651202208)
[![Latest Stable Version](https://poser.pugx.org/renoki-co/l1/v/stable)](https://packagist.org/packages/renoki-co/l1)
[![Total Downloads](https://poser.pugx.org/renoki-co/l1/downloads)](https://packagist.org/packages/renoki-co/l1)
[![Monthly Downloads](https://poser.pugx.org/renoki-co/l1/d/monthly)](https://packagist.org/packages/renoki-co/l1)
[![License](https://poser.pugx.org/renoki-co/l1/license)](https://packagist.org/packages/renoki-co/l1)

Extend your PHP/Laravel application with Cloudflare bindings.

This package offers support for:

- [x] [Cloudflare D1](https://developers.cloudflare.com/d1)
- [x] [Cloudflare KV](https://developers.cloudflare.com/kv/)
- [x] [Cloudflare Queues](https://developers.cloudflare.com/queues)

## 🚀 Installation

You can install the package via Composer:

```bash
composer require renoki-co/l1
```

## 🙌 Usage

### D1 with raw PDO

Though D1 is not connectable via SQL protocols, it can be used as a PDO driver via the package connector. This proxies the query and bindings to the D1's `/query` endpoint in the Cloudflare API.

```php
use RenokiCo\L1\D1\D1Pdo;
use RenokiCo\L1\D1\D1PdoStatement;
use RenokiCo\L1\CloudflareD1Connector;

$pdo = new D1Pdo(
    dsn: 'sqlite::memory:', // irrelevant
    connector: new CloudflareD1Connector(
        database: 'your_database_id',
        token: 'your_api_token',
        accountId: 'your_cf_account_id',
    ),
);
```

### D1 with Laravel

In your `config/database.php` file, add a new connection:

```php
'connections' => [
    'd1' => [
        'driver' => 'd1',
        'prefix' => '',
        'database' => env('CLOUDFLARE_D1_DATABASE_ID', ''),
        'api' => 'https://api.cloudflare.com/client/v4',
        'auth' => [
            'token' => env('CLOUDFLARE_TOKEN', ''),
            'account_id' => env('CLOUDFLARE_ACCOUNT_ID', ''),
        ],
    ],
]
```

Then in your `.env` file, set up your Cloudflare credentials:

```env
CLOUDFLARE_TOKEN=
CLOUDFLARE_ACCOUNT_ID=
CLOUDFLARE_D1_DATABASE_ID=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
```

The `d1` driver will proxy the PDO queries to the Cloudflare D1 API to run queries.

### Cloudflare KV

Cloudflare KV can be used as a Laravel cache driver. First, add the following to your `config/cache.php` file:

```php
'stores' => [
    'cloudflare-kv' => [
        'driver' => 'cloudflare-kv',
        'prefix' => 'laravel_cache',
    ],
],
```

Then, create a `config/cloudflare.php` configuration file:

```php
<?php

return [
    'kv' => [
        'namespace' => env('CLOUDFLARE_KV_NAMESPACE', ''),
        'auth' => [
            'token' => env('CLOUDFLARE_TOKEN', ''),
            'account_id' => env('CLOUDFLARE_ACCOUNT_ID', ''),
        ],
        'api' => env('CLOUDFLARE_API_URL', 'https://api.cloudflare.com/client/v4'),
    ],
];
```

Add the following to your `.env` file:

```env
CLOUDFLARE_TOKEN=
CLOUDFLARE_ACCOUNT_ID=
CLOUDFLARE_KV_NAMESPACE=your-namespace-id
```

You can then use the KV cache driver in your application:

```php
Cache::store('cloudflare-kv')->put('key', 'value', 60);
$value = Cache::store('cloudflare-kv')->get('key');
```

Alternatively, you can use the KV connector directly:

```php
use RenokiCo\L1\CloudflareKVConnector;

$kv = new CloudflareKVConnector(
    'your-namespace-id',
    'your-api-token',
    'your-account-id'
);

// List all namespaces
$namespaces = $kv->listNamespaces();

// Get a value
$value = $kv->getValue('key');

// Put a value
$kv->putValue('key', 'value');

// Delete a key
$kv->deleteKey('key');
```

### Cloudflare Queues

To use Cloudflare Queues, add the following to your `config/cloudflare.php` configuration file:

```php
'queues' => [
    'queue' => env('CLOUDFLARE_QUEUE_NAME', ''),
    'auth' => [
        'token' => env('CLOUDFLARE_TOKEN', ''),
        'account_id' => env('CLOUDFLARE_ACCOUNT_ID', ''),
    ],
    'api' => env('CLOUDFLARE_API_URL', 'https://api.cloudflare.com/client/v4'),
],
```

Add the following to your `.env` file:

```env
CLOUDFLARE_TOKEN=
CLOUDFLARE_ACCOUNT_ID=
CLOUDFLARE_QUEUE_NAME=your-queue-name
```

You can then use the Queues connector in your application:

```php
use RenokiCo\L1\CloudflareQueuesConnector;

$queues = app('cloudflare.queues');

// List all queues
$queues = $queues->listQueues();

// Publish a message
$queues->publishMessage('your-queue-name', ['data' => 'value']);

// Get messages from a queue
$messages = $queues->getMessages('your-queue-name', 10, 30);

// Acknowledge a message (delete after processing)
$queues->acknowledgeMessage('your-queue-name', 'message-id');
```

## 🐛 Testing

Start the built-in Worker that simulates the Cloudflare API:

```bash
cd tests/worker
npm ci
npm run start
```

In a separate terminal, run the tests:

``` bash
vendor/bin/phpunit
```

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔒  Security

If you discover any security related issues, please email <alex@renoki.org> instead of using the issue tracker.

## 🎉 Credits

- [Alex Renoki](https://github.com/rennokki)
- [All Contributors](../../contributors)
