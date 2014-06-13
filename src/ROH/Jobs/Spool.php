<?php

namespace ROH\Jobs;

abstract class Spool
{
    protected $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    abstract public function find();
}
