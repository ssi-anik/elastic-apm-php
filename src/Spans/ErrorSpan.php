<?php

namespace Anik\ElasticApm\Spans;

use Anik\ElasticApm\Contracts\SpanContract;
use Exception;
use Illuminate\Support\Collection;

class ErrorSpan implements SpanContract
{
    use SpanEmptyFieldsTrait;

    private $traces, $exception;

    public function __construct (Exception $e, Collection $traces) {
        $this->exception = $e;
        $this->traces = $traces;
    }

    public function getSpanData () : array {
        return $this->traces->toArray();
    }

    public function getName () : string {
        return get_class($this->exception);
    }

    public function getType () : string {
        return config('elastic-apm.types.error', 'error');
    }

    public function getSubType () : string {
        return class_basename($this->exception);
    }
}