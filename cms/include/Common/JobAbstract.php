<?php

use Monolog\Logger;

abstract class Common_JobAbstract
{
    protected $logger = null;
    
    abstract public function handle();
    
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
}