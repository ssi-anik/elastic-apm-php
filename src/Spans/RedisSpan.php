<?php

namespace Anik\ElasticApm\Spans;

use Anik\ElasticApm\Contracts\SpanContract;

class RedisSpan implements SpanContract
{
    use SpanEmptyFieldsTrait;
    private $connection, $command, $executionTime;

    public function __construct (?string $connection, string $command, ?float $executionTime) {
        $this->connection = $connection;
        $this->command = $command;
        $this->executionTime = $executionTime;
    }

    public function getLabels () : array {
        return [
            'execution_time' => $this->executionTime,
        ];
    }

    public function getSpanData () : array {
        return [
            'connection' => $this->connection,
            'command'    => $this->command,
            'time'       => $this->executionTime,
        ];
    }

    public function getName () : string {
        return $this->command;
    }

    public function getType () : string {
        return config('elastic-apm.types.redis', 'redis');
    }

    public function getSubType () : string {
        return $this->connection ?? 'NULL';
    }
}