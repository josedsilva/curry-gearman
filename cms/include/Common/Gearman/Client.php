<?php
/**
 * Curry cms client wrapper for the Gearman package
 *
 * @author  Jose F. D'Silva
 * @package  Gearman
 */
use Monolog\Logger;

class Common_Gearman_Client extends GearmanClient
{
    const PRIORITY_NORMAL = 'Normal';
    const PRIORITY_HIGH = 'High';
    const PRIORITY_LOW = 'Low';
    
    const DEFAULT_SERVER_IP = '127.0.0.1';
    const DEFAULT_SERVER_PORT = 4730;
    
    protected $server;
    protected $port;
    protected $logger = null;
    
    public function __construct(Logger $logger = null)
    {
        parent::__construct();
        $this->server = Curry_Core::$config->modules->contrib->CurryGearman->server->ip ?: self::DEFAULT_SERVER_IP;
        $this->port = Curry_Core::$config->modules->contrib->CurryGearman->server->port ?: self::DEFAULT_SERVER_PORT;
        $this->addServer($this->server, $this->port);
        $this->logger = $logger;
        if (!is_null($this->logger)) {
            $this->logger->log(Logger::INFO, 'job server added to client', ['server' => $this->server, 'port' => $this->port]);
        }
    }
    
    /**
     * Helper function to add a job to the client.
     * The job is registered with the default job handler.
     * The object is automatically serialized.
     * @param Common_JobAbstract $job
     * @param string $priority
     * @param mixed|null $context   The application context.
     * @param string|null $unique   If unique is a variable set to false, the generated id is returned,
     *                              else the unique id of the task is set to the value of $unique.
     *                              If null, then the system will generate the unique id.
     * @param boolean $background   Whether this is a background job?
     */
    public function addJob(Common_JobAbstract $job, $priority = self::PRIORITY_NORMAL, &$context = null, &$unique = null, $background = false)
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
        return $this->{$taskMethod}(self::getJobHandler(), @serialize($job), $context, $uniqueId);
    }
    
    /**
     * Add a background job.
     * @param Common_JobAbstract $job
     * @param string $priority
     * @param mixed|null $context
     * @param string|null $unique
     */
    public function addJobBackground(Common_JobAbstract $job, $priority = self::PRIORITY_NORMAL, &$context = null, &$unique = null)
    {
        return $this->addJob($job, $priority, $context, $unique, true);
    }
    
    /**
     * Return the name of the default job handler hook.
     * The worker for this project will listen to this hook.
     */
    protected static function getDefaultJobHandler()
    {
        return 'curry_gearman_job_handler_'.md5(Curry_Core::$config->curry->projectName);
    }
    
    public static function getJobHandler()
    {
        $jobHandler = Curry_Core::$config->modules->contrib->CurryGearman->jobHandler;
        if (!$jobHandler) {
            $jobHandler = self::getDefaultJobHandler();
        }
        return $jobHandler;
    }
}