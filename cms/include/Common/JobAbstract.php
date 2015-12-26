<?php

use Monolog\Logger;

abstract class Common_JobAbstract
{
    protected $logger = null;
    protected $unique = null;
    
    abstract public function handle();
    
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Set the uniqueId of the job
     * when dequeued from the Gearman job queue.
     * @param string $unique
     */
    public function setUniqueId($unique)
    {
        $this->unique = $unique;
    }
    
    public function getUniqueId()
    {
        return $this->unique;
    }
}