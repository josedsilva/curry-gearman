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
    
    public function __construct($server = '127.0.0.1', $port = 4730, Logger $logger = null)
    {
        parent::__construct();
        $this->server = $server;
        $this->port = $port;
        $this->addServer($this->server, $this->port);
        $this->logger = $logger;
    }
    
}