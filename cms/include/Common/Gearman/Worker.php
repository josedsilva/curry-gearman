<?php
/**
 * Curry cms worker wrapper for the Gearman package.
 *
 * @author  Jose F. D'Silva
 * @package  Gearman
 */
use Monolog\Logger;

class Common_Gearman_Worker extends GearmanWorker
{
    protected $server;
    protected $port;
    protected $logger = null;
    
    public function __construct(Logger $logger = null)
    {
        parent::__construct();
        $this->server = Curry_Core::$config->modules->contrib->CurryGearman->server->ip ?: Common_Gearman_Client::DEFAULT_SERVER_IP;
        $this->port = Curry_Core::$config->modules->contrib->CurryGearman->server->port ?: Common_Gearman_Client::DEFAULT_SERVER_PORT;
        $this->addServer($this->server, $this->port);
        $this->logger = $logger;
        $this->logger->log(Logger::INFO, 'job server added to worker', ['server' => $this->server, 'port' => $this->port]);
    }
    
}