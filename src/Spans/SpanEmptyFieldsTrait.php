<?php

namespace Anik\ElasticApm\Spans;

trait SpanEmptyFieldsTrait
{
    public function getAction(): string
    {
        return '';
    }

    public function getLabels(): array
    {
        return [];
    }
}
