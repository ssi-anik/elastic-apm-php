<?php

namespace Anik\ElasticApm\Contracts;

interface SpanContract
{
    public function getName () : string;

    public function getType () : string;

    public function getSubType () : string;

    public function getAction () : string;

    public function getLabels () : array;

    public function getSpanData () : array;
}