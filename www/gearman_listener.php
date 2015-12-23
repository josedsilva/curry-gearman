<?php
/**
 * Job listener for the Gearman package.
 *
 * @author  Jose F. D'Silva
 * @package  Gearman
 *
 * TODO:
 * 1. Create non-blocking listener than can be executed from the browser.
 * 2. Logging for Gearman. Use Monolog and created logs in cms/data/log/gearman/ rotary type logging.
 */
require_once 'init.php';

$worker = new Common_Gearman_Worker();
$worker->addFunction(Common_Gearman_Client::REGISTERED_FUNC_NAME, function(GearmanJob $gearmanJob)
{
    printf("Fetched job [handle: %s, uniqueId: %s]\n", $gearmanJob->handle(), $gearmanJob->unique());
    $jobWrapper = new Common_Gearman_JobWrapper($gearmanJob);
    $task = $jobWrapper->getJob();
    if ($task) {
        return $task->handle();
    }
    
    print $job->handle().' is not a valid job instance. Task ignored.'."\n";
});

print "Listening for jobs...\n";
while ($worker->work());