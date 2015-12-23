<?php
/**
 * Job listener for the Gearman package.
 *
 * @author  Jose F. D'Silva
 * @package  Gearman
 */

use Monolog\Logger;

require_once 'init.php';

$listener = new Common_Gearman_Listener();
$logger = $listener->getLogger();
$listener->setDefaultJobHandler(function(GearmanJob $gearmanJob) use ($logger)
{
    $s = sprintf("Fetched job [handle: %s, uniqueId: %s]", $gearmanJob->handle(), $gearmanJob->unique());
    $logger->log(Logger::INFO, $s);
    $jobWrapper = new Common_Gearman_JobWrapper($gearmanJob, $logger);
    $task = $jobWrapper->getJob();
    if ($task) {
        return $task->handle();
    }
    
    $s = sprintf("%s[uniqId: %s] is not a valid job instance. Job ignored.", $job->handle(), $job->unique());
    $logger->log(Logger::ERROR, $s);
});
$listener->listen();