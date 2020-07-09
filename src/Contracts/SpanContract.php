<?php

namespace Anik\ElasticApm\Contracts;

interface SpanContract
{
    public function getName () : string;

    public function getType () : string;

    public function getSubType () : string;

    public function getAction () : string;

    public function getLabelKey () : string;

    public function getLabelValue ();

    public function getSpanData () : array;
}