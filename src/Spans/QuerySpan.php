<?php

namespace Anik\ElasticApm\Spans;

use Anik\ElasticApm\Contracts\SpanContract;

class QuerySpan implements SpanContract
{
    use SpanEmptyFieldsTrait;
    private $connection, $query, $executionTime;

    public function __construct ($connection, $query, $executionTime) {
        $this->connection = $connection;
        $this->query = $query;
        $this->executionTime = $executionTime;
    }

    public function getSpanData () : array {
        return [
            'connection' => $this->connection,
            'query'      => $this->query,
            'time'       => $this->executionTime,
        ];
    }

    public function getName () : string {
        return $this->query;
    }

    public function getType () : string {
        return 'db';
    }

    public function getSubType () : string {
        return $this->connection;
    }
}