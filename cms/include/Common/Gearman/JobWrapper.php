<?php
/**
 * Job wrapper for the Gearman package.
 *
 * @author  Jose F. D'Silva
 * @package  Gearman
 */
class Common_Gearman_JobWrapper
{
    private $gearmanJob = null;
    
    public function __construct(GearmanJob $gearmanJob)
    {
        $this->gearmanJob = $gearmanJob;
    }
    
    public function getJob()
    {
        $ret = @unserialize($this->gearmanJob->workload());
        if (!($ret instanceof Common_JobAbstract)) {
            $ret = null;
            // TODO: log error: invalid class type.
        }
        return $ret;
    }
}