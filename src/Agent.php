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

    private function __construct()
    {
        $this->spans = [];
    }

    public static function reset(): void
    {
        static::$instance = null;
    }

    public static function reinstance()
    {
        static::reset();

        return static::instance();
    }

    public static function instance()
    {
        return static::$instance ? static::$instance : (static::$instance = new self());
    }

    public function setTransaction(Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function addSpan(SpanContract $span)
    {
        $this->spans[] = $span;

        return $this;
    }

    public function newApmTransaction($name, $type): TransactionInterface
    {
        return ElasticApm::beginCurrentTransaction($name, $type);
    }

    public function getElasticApmTransaction(): TransactionInterface
    {
        return ElasticApm::getCurrentTransaction();
    }

    public function capture()
    {
        if (!isset($this->transaction)) {
            throw new RequirementMissingException('Transaction must be set');
        }

        $apmTransaction = $this->getElasticApmTransaction();
        $apmTransaction->setName($this->transaction->getName());
        $apmTransaction->setType($this->transaction->getType());

        foreach ($this->spans as $span) {
            $this->includeSpansToTransaction($apmTransaction, $span);
        }
    }

    public function captureOnNew()
    {
        if (!isset($this->transaction)) {
            throw new RequirementMissingException('Transaction must be set');
        }

        $apmTransaction = $this->newApmTransaction($this->transaction->getName(), $this->transaction->getType());

        foreach ($this->spans as $span) {
            $this->includeSpansToTransaction($apmTransaction, $span);
        }
        $apmTransaction->end();
    }

    private function includeSpansToTransaction(TransactionInterface $transaction, SpanContract $span)
    {
        $childSpan = $transaction->beginChildSpan($span->getName(), $span->getType(), $span->getSubType());
        if ($labels = $span->getLabels()) {
            foreach ($labels as $key => $value) {
                $childSpan->context()->setLabel($key, $value);
            }
        }
        $childSpan->setAction(json_encode($span->getSpanData()));
        $childSpan->end();
    }
}
