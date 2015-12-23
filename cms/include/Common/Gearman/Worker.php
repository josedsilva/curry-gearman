<?php
/**
 * Curry cms worker wrapper for the Gearman package.
 *
 * @author  Jose F. D'Silva
 * @package  Gearman
 */
class Common_Gearman_Worker extends GearmanWorker
{
    protected $server;
    
    public function __construct($server = '127.0.0.1')
    {
        parent::__construct();
        $this->server = $server;
        $this->addServer($this->server);
    }
    
}