<?php

namespace Anik\ElasticApm\Spans;

use Anik\ElasticApm\Contracts\SpanContract;

class QuerySpan implements SpanContract
{
    use SpanEmptyFieldsTrait;

    private $connection;

    private $query;

    private $executionTime;

    public function __construct(string $connection, string $query, float $executionTime)
    {
        $this->connection = $connection;
        $this->query = $query;
        $this->executionTime = $executionTime;
    }

    public function getLabels(): array
    {
        return [
            'execution_time' => $this->executionTime,
        ];
    }

    public function getSpanData(): array
    {
        return [
            'connection' => $this->connection,
            'query' => $this->query,
            'time' => $this->executionTime,
        ];
    }

    public function getName(): string
    {
        return $this->query;
    }

    public function getType(): string
    {
        return config('elastic-apm.types.query', 'db');
    }

    public function getSubType(): string
    {
        return $this->connection;
    }
}
