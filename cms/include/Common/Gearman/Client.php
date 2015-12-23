<?php
/**
 * Curry cms client wrapper for the Gearman package
 *
 * @author  Jose F. D'Silva
 * @package  Gearman
 */
class Common_Gearman_Client extends GearmanClient
{
    const PRIORITY_NORMAL = 'Normal';
    const PRIORITY_HIGH = 'High';
    const PRIORITY_LOW = 'Low';
    const REGISTERED_FUNC_NAME = 'curry_gearman_job_handler';
    
    protected $server;
    
    public function __construct($server = '127.0.0.1')
    {
        parent::__construct();
        $this->server = $server;
        $this->addServer($this->server);
    }
    
    /**
     * Helper function to add a task to the client.
     * The object is automatically serialized.
     * @param Project_JobAbstract $job
     * @param string $priority
     * @param mixed|null $context   The application context.
     * @param string|null $unique   If unique is a variable set to false, the generated id is returned,
     *                              else the unique id of the task is set to the value of $unique.
     *                              If null, then the system will generate the unique id.
     * @param boolean $background   Whether this is a background job?
     */
    public function addJob(Project_JobAbstract $job, $priority = self::PRIORITY_NORMAL, &$context = null, &$unique = null, $background = false)
    {
        $uniqueId = uniqid();
        if (!is_null($unique)) {
            if ($unique) {
                // user has set a uniqueId. Use that instead.
                $uniqueId = $unique;
            } else {
                // return the job's generated uniqueId.
                $unique = $uniqueId;
            }
        }
        
        $taskMethod = 'addTask'.($priority !== self::PRIORITY_NORMAL ? $priority : '').($background ? 'Background' : '');
        return $this->{$taskMethod}(self::REGISTERED_FUNC_NAME, @serialize($job), $context, $uniqueId);
    }
    
    /**
     * Add a background job.
     * @param Project_JobAbstract $job
     * @param string $priority
     * @param mixed|null $context
     * @param string|null $unique
     */
    public function addJobBackground(Project_JobAbstract $job, $priority = self::PRIORITY_NORMAL, &$context = null, &$unique = null)
    {
        return $this->addJob($job, $priority, $context, $unique, true);
    }
}