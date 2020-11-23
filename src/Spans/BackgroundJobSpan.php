<?php

namespace Anik\ElasticApm\Spans;

use Anik\ElasticApm\Contracts\SpanContract;

class BackgroundJobSpan implements SpanContract
{
    use SpanEmptyFieldsTrait;

    private $job;

    private $time;

    private $state;

    public function __construct(string $job, string $state, string $time)
    {
        $this->job = $job;
        $this->state = $state;
        $this->time = $time;
    }

    public function getName(): string
    {
        return $this->state;
    }

    public function getType(): string
    {
        return config('elastic-apm.types.background', 'job');
    }

    public function getSubType(): string
    {
        return $this->job;
    }

    public function getLabels(): array
    {
        return $this->getSpanData();
    }

    public function getSpanData(): array
    {
        return [
            'time' => $this->time,
            'state' => $this->state,
        ];
    }
}
