<?php

namespace Anik\ElasticApm\Spans;

use Anik\ElasticApm\Contracts\SpanContract;

class HttpRequestSpan implements SpanContract
{
    use SpanEmptyFieldsTrait;

    private $host;

    private $path;

    private $duration;

    private $options;

    public function __construct(string $host, string $path, float $duration, array $options = [])
    {
        $this->host = $host;
        $this->path = $path;
        $this->duration = $duration;
        $this->options = $options;
    }

    public function getLabels(): array
    {
        return [
            'duration' => $this->duration,
            'positive' => $this->options['positive'] ?? false,
        ];
    }

    public function getName(): string
    {
        return $this->host;
    }

    public function getType(): string
    {
        return config('elastic-apm.types.http', 'external');
    }

    public function getSubType(): string
    {
        return 'http';
    }

    public function getSpanData(): array
    {
        return [
            'host' => $this->host,
            'path' => $this->path,
            'duration' => $this->duration,
            'options' => $this->options,
        ];
    }
}
