<?php

namespace ROH\Jobs\Provider;

use \ROH\Jobs\Jobs;

class JobProvider extends \Bono\Provider\Provider
{
    public function initialize()
    {
        $app = $this->app;

        if (empty($this->options)) {
            $this->options = array();
        }

        Jobs::init($this->options);

        // do something here
    }
}
