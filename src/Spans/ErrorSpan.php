<?php

namespace Anik\ElasticApm\Spans;

use Anik\ElasticApm\Contracts\SpanContract;
use Illuminate\Support\Collection;
use Throwable;

class ErrorSpan implements SpanContract
{
    use SpanEmptyFieldsTrait;

    private $traces;

    private $exception;

    public function __construct(Throwable $e, Collection $traces)
    {
        $this->exception = $e;
        $this->traces = $traces;
    }

    public function getSpanData(): array
    {
        return $this->traces->toArray();
    }

    public function getName(): string
    {
        return get_class($this->exception);
    }

    public function getType(): string
    {
        return config('elastic-apm.types.error', 'error');
    }

    public function getSubType(): string
    {
        return class_basename($this->exception);
    }
}
