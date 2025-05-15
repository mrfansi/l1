<?php

namespace Mrfansi\L1\D1\Pdo;

use Illuminate\Support\Arr;
use PDO;
use PDOException;
use PDOStatement;

class D1PdoStatement extends PDOStatement
{
    protected int $fetchMode = PDO::FETCH_ASSOC;
    protected array $bindings = [];
    protected array $responses = [];

    public function __construct(
        protected D1Pdo &$pdo,
        protected string $query,
        protected array $options = [],
    ) {
        //
    }

    /**
     * Set the fetch mode for this statement.
     *
     * @param int $mode The fetch mode must be one of the PDO::FETCH_* constants.
     * @param string|object|null $classNameObject The class name or object to fetch.
     * @param array|null $constructorArgs Constructor arguments for the class.
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function setFetchMode($mode = PDO::FETCH_CLASS, $classNameObject = null, $constructorArgs = null): bool
    {
        $this->fetchMode = $mode;

        return true;
    }

    public function bindValue($param, $value, $type = PDO::PARAM_STR): bool
    {
        $this->bindings[$param] = match ($type) {
            PDO::PARAM_STR => (string) $value,
            PDO::PARAM_BOOL => (bool) $value,
            PDO::PARAM_INT => (int) $value,
            PDO::PARAM_NULL => null,
            default => $value,
        };

        return true;
    }

    public function execute($params = []): bool
    {
        $this->bindings = array_values($this->bindings ?: $params);

        $response = $this->pdo->d1()->databaseQuery(
            $this->query,
            $this->bindings,
        );

        if ($response->failed() || ! $response->json('success')) {
            throw new PDOException(
                (string) $response->json('errors.0.message'),
                (int) $response->json('errors.0.code'),
            );
        }

        $this->responses = $response->json('result');

        $lastId = Arr::get(Arr::last($this->responses), 'meta.last_row_id', null);

        if (! in_array($lastId, [0, null])) {
            $this->pdo->setLastInsertId(value: $lastId);
        }

        return true;
    }

    /**
     * Fetches all rows from a result set.
     *
     * @param int $mode The fetch mode must be one of the PDO::FETCH_* constants.
     * @param string|object|null $class The class name or object to fetch.
     * @param array|null $constructorArgs Constructor arguments for the class.
     * @return array Returns an array containing all of the result set rows.
     */
    public function fetchAll($mode = PDO::FETCH_CLASS, $class = null, $constructorArgs = null): array
    {
        // Use the provided mode or fall back to the previously set fetch mode
        $fetchMode = ($mode !== PDO::FETCH_DEFAULT) ? $mode : $this->fetchMode;

        $response = match ($fetchMode) {
            PDO::FETCH_ASSOC => $this->rowsFromResponses(),
            PDO::FETCH_OBJ => collect($this->rowsFromResponses())->map(function ($row) {
                return (object) $row;
            })->toArray(),
            PDO::FETCH_CLASS => $this->fetchAsClass($class, $constructorArgs),
            default => throw new PDOException('Unsupported fetch mode.'),
        };

        return $response;
    }

    public function rowCount(): int
    {
        return count($this->rowsFromResponses());
    }

    /**
     * Extract rows from the response array.
     *
     * @return array
     */
    protected function rowsFromResponses(): array
    {
        return collect($this->responses)
            ->map(fn ($response) => $response['results'])
            ->collapse()
            ->toArray();
    }

    /**
     * Fetch results as a specific class.
     *
     * @param string|object|null $class The class name or object to fetch.
     * @param array|null $constructorArgs Constructor arguments for the class.
     * @return array
     */
    protected function fetchAsClass($class, $constructorArgs = null): array
    {
        if (!$class) {
            throw new PDOException('Class name must be provided for FETCH_CLASS mode');
        }

        return collect($this->rowsFromResponses())->map(function ($row) use ($class, $constructorArgs) {
            // If $class is an object, use its class
            $className = is_object($class) ? get_class($class) : $class;

            if ($constructorArgs) {
                return new $className(...$constructorArgs, ...$row);
            }

            $instance = new $className();
            foreach ($row as $key => $value) {
                $instance->$key = $value;
            }

            return $instance;
        })->toArray();
    }
}
