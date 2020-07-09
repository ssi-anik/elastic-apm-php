<?php

namespace Anik\ElasticApm\Spans;

use Anik\ElasticApm\Contracts\SpanContract;

class HttpRequestSpan implements SpanContract
{
    use SpanEmptyFieldsTrait;
    private $host, $path, $duration, $options;

    public function __construct (string $host, string $path, float $duration, array $options = []) {
        $this->host = $host;
        $this->path = $path;
        $this->duration = $duration;
        $this->options = $options;
    }

    public function getLabelKey () : string {
        return 'duration';
    }

    public function getLabelValue () {
        return $this->duration;
    }

    public function getName () : string {
        return $this->host;
    }

    public function getType () : string {
        return config('elastic-apm.types.http', 'external');
    }

    public function getSubType () : string {
        return 'http';
    }

    public function getSpanData () : array {
        return [
            'host'     => $this->host,
            'path'     => $this->path,
            'duration' => $this->duration,
            'options'  => $this->options,
        ];
    }
}