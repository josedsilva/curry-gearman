<?php
/**
 * The queue listener class for the the Gearman package.
 *
 * @author  Jose F. D'Silva
 * @package  Gearman
 */

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Common_Gearman_Listener
{
    protected $worker;
    protected $server;
    protected $port;
    protected $isNonBlocking = false;
    protected $isConsole;
    protected $logger;
    protected $logFile;
    
    public function __construct($server = '127.0.0.1', $port = 4730)
    {
        $this->server = $server;
        $this->port = $port;
        $this->initLogger();
        $this->isConsole = $this->getIsConsole();
        $this->validateToken();
        $this->worker = new Common_Gearman_Worker($this->server, $this->port, $this->logger);
        if (!$this->isConsole) {
            // set non-blocking listener when executed in browser.
            $this->setNonBlocking();
        }
    }
    
    public function getWorker()
    {
        return $this->worker;
    }
    
    /**
     * Determine whether we are executing in a console or browser.
     * @return boolean
     */
    protected function getIsConsole()
    {
        if (function_exists('php_sapi_name')) {
            return (php_sapi_name() === 'cli');
        }
        return !((boolean) $_SERVER['REQUEST_METHOD']);
    }
    
    protected function initLogger()
    {
        $this->logger = new Logger('Curry_Gearman');
        $logPath = realpath(dirname(Curry_Core::$config->curry->configPath) . '/../data/log');
        $this->logFile = "{$logPath}/gearman-".date('Y-m-d').'.log';
        $handler = new StreamHandler($this->logFile, Logger::DEBUG);
        $this->logger->pushHandler($handler);
    }
    
    public function getLogger()
    {
        return $this->logger;
    }
    
    protected function validateToken()
    {
        if ($this->isConsole) {
            return;
        }
        
        if (!isset($_GET['hash'])) {
            throw new Exception('Missing hash parameter.');
        }
        
        if (!$this->isValidHash($_GET['hash'])) {
            $this->logger->log(Logger::WARNING, 'Invalid hash specified. Should be: '.$this->getHash());
            throw new Exception('Invalid hash.');
        }
    }
    
    public function getHash()
    {
        return md5(Curry_Core::$config->curry->secret . __CLASS__);
    }
    
    protected function isValidHash($hash)
    {
        return ($hash == $this->getHash());
    }
    
    public function log($msg, $level = Logger::INFO, array $context = array())
    {
        return $this->logger->log($level, $msg, $context);
    }
    
    /**
     * Put the worker in non-blocking mode.
     * The worker will process the queue and exit immediately.
     * @param boolean $nonblocking
     * @return Common_Gearman_Listener
     */
    public function setNonBlocking($nonblocking = true)
    {
        $this->isNonBlocking = $nonblocking;
        if ($this->isNonBlocking) {
            $this->worker->addOptions(GEARMAN_WORKER_NON_BLOCKING);
        } else {
            $this->worker->removeOptions(GEARMAN_WORKER_NON_BLOCKING);
        }
        
        return $this;
    }
    
    /**
     * Setup a hook callback.
     * @param string $hook  The registered function name.
     * @param callable $callback    The callback is passed an instance of GearmanJob.
     * @throws Exception
     */
    public function setHook($hook, $callback)
    {
        if (!is_callable($callback)) {
            throw new Exception('The hook callback must be a valid callback function.');
        }
        $this->worker->addFunction($hook, $callback);
        return $this;
    }
    
    /**
     * Bind callback to the DEFAULT_JOB_HANDLER hook.
     * @param callable $callback
     */
    public function setDefaultJobHandler($callback)
    {
        return $this->setHook(Common_Gearman_Client::getDefaultJobHandler(), $callback);
    }
    
    /**
     * Listen for jobs indefinitely.
     * This is a blocking call unless non-blocking is set.
     */
    public function listen()
    {
        while ($this->worker->work());
    }
}