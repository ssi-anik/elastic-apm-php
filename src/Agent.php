<?php

namespace Anik\ElasticApm;

use Anik\ElasticApm\Exceptions\RequirementMissingException;
use Anik\ElasticApm\Exceptions\RequirementUnsatisfiedException;
use Elastic\Apm\ElasticApm;

class Agent
{
    private static $instance = null;
    /** @var \Anik\ElasticApm\Transaction $transaction */
    private $transaction;

    private function __construct () {
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

    private function getElasticApmTransaction () {
        return ElasticApm::getCurrentTransaction();
    }

    public function capture () {
        if (!isset($this->transaction)) {
            throw new RequirementMissingException('Transaction must be set');
        }

        $apmTransaction = $this->getElasticApmTransaction();
        $apmTransaction->setName($this->transaction->getName());
        $apmTransaction->setType($this->transaction->getType());
    }
}