<?php

namespace Anik\ElasticApm;

use Anik\ElasticApm\Contracts\SpanContract;
use Anik\ElasticApm\Exceptions\RequirementMissingException;
use Anik\ElasticApm\Exceptions\RequirementUnsatisfiedException;
use Elastic\Apm\ElasticApm;
use Elastic\Apm\TransactionInterface;

class Agent
{
    private static $instance = null;
    /** @var \Anik\ElasticApm\Transaction $transaction */
    private $transaction;

    /** @var array $spans */
    private $spans;

    private function __construct () {
        $this->spans = [];
    }

    public static function instance () {
        return static::$instance ? static::$instance : (static::$instance = new self);
    }

    public function setTransaction (Transaction $transaction) : self {
        $this->transaction = $transaction;

        return $this;
    }

    public function getTransaction () : ?Transaction {
        return $this->transaction;
    }

    public function addSpan (SpanContract $span) {
        $this->spans[] = $span;

        return $this;
    }

    private function getElasticApmTransaction () : TransactionInterface {
        return ElasticApm::getCurrentTransaction();
    }

    public function capture () {
        if (!isset($this->transaction)) {
            throw new RequirementMissingException('Transaction must be set');
        }

        $apmTransaction = $this->getElasticApmTransaction();
        $apmTransaction->setName($this->transaction->getName());
        $apmTransaction->setType($this->transaction->getType());

        foreach ( $this->spans as $span ) {
            $this->includeSpansToTransaction($apmTransaction, $span);
        }
    }

    private function includeSpansToTransaction (TransactionInterface $transaction, SpanContract $span) {
        $childSpan = $transaction->beginChildSpan($span->getName(), $span->getType(), $span->getSubType());
        if ($span->getLabelKey()) {
            $childSpan->setLabel($span->getLabelKey(), $span->getLabelValue());
        }
        $childSpan->setAction(json_encode($span->getSpanData()));
        $childSpan->end();
    }
}