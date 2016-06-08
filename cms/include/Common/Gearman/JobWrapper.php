<?php
/**
 * Job wrapper for the Gearman package.
 *
 * @author  Jose F. D'Silva
 * @package  Gearman
 */
use Monolog\Logger;

class Common_Gearman_JobWrapper
{
    protected $gearmanJob = null;
    protected $logger = null;
    
    public function __construct(GearmanJob $gearmanJob, Logger $logger = null)
    {
        $this->gearmanJob = $gearmanJob;
        $this->logger = $logger;
    }
    
    public function getJob()
    {
        $ret = @unserialize($this->gearmanJob->workload());
        if (!($ret instanceof Common_JobAbstract)) {
            $ret = null;
            if (!is_null($this->logger)) {
                $s = sprintf("Job[%s] is not an instance of Common_JobAbstract.", $this->gearmanJob->handle());
                $this->logger->log(Logger::ERROR, $s);
            }
        }
        return $ret;
    }
}