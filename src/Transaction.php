<?php

namespace Anik\ElasticApm;

class Transaction
{
    private $name, $type;

    public function __construct ($name = null, $type = null) {
        $this->setName($name);
        $this->setType($type);
    }

    public function setName ($name) : self {
        if (!is_null($name)) {
            $this->name = $name;
        }

        return $this;
    }

    public function getName () : ?string {
        return $this->name;
    }

    public function setType ($type) : self {
        if (!is_null($type)) {
            $this->type = $type;
        }

        return $this;

    }

    public function getType () : ?string {
        return $this->type;
    }
}