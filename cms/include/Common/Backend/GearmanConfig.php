<?php

class Common_Backend_GearmanConfig extends Curry_Backend
{
    public static function getGroup()
    {
        return 'System';
    }
    
    public function showMain()
    {
        $this->flushConfigCache();
        $form = new Curry_Form(array(
            'url' => url('', $_GET),
            'method' => 'post',
            'elements' => array(
                'server_ip' => array('text', array(
                    'label' => 'Server IP address',
                    'value' => Curry_Core::$config->modules->contrib->CurryGearman->server->ip,
                    'placeholder' => 'Leave empty to use default (127.0.0.1)',
                )),
                'server_port' => array('text', array(
                    'label' => 'Server port',
                    'value' => Curry_Core::$config->modules->contrib->CurryGearman->server->port,
                    'placeholder' => 'Leave empty to use default (4730)',
                )),
                'job_handler' => array('text', array(
                    'label' => 'Job handler',
                    'value' => Curry_Core::$config->modules->contrib->CurryGearman->jobHandler,
                    'placeholder' => 'Leave empty to use default',
                )),
                'token' => array('text', array(
                    'label' => 'Token',
                    'description' => 'To execute the listener from the browser, a hash is required.',
                    'value' => Curry_Core::$config->modules->contrib->CurryGearman->token,
                    'required' => true,
                    'placeholder' => 'Enter a random string',
                )),
                'save' => array('submit', array('label' => 'Save')),
            ),
        ));
        
        if (isPost() && $form->isValid($_POST)) {
            $values = $form->getValues(true);
            $this->saveConfig($values);
            return;
        }
        
        $webWorkerUrl = url(Curry_Core::$config->curry->baseUrl.'gearman_listener.php', array('hash' => Common_Gearman_Listener::getHash()))->getAbsolute();
        $html =<<<HTML
$form
<p><a href="{$webWorkerUrl}" target="_blank">Click here to execute Gearman web worker.</a></p>
HTML;
        
        $this->addMainContent($html);
    }
    
    protected function saveConfig(array $values)
    {
        $this->addBreadcrumb('Configurations', url('', array('module')));
        $this->addBreadcrumb('saveConfig', url());
        
        $config = new Zend_Config(require(Curry_Core::$config->curry->configPath), true);
        $config->modules = array(
            'contrib' => array(
                'CurryGearman' => array(
                    'server' => array(
                        'ip' => trim($values['server_ip']) ?: null,
                        'port' => trim($values['server_port']) ?: null,
                    ),
                    'jobHandler' => trim($values['job_handler']) ?: null,
                    'token' => $values['token'],
                ),
            ),
        );
        
        try {
            $writer = new Zend_Config_Writer_Array();
            $writer->write(Curry_Core::$config->curry->configPath, $config);
            $this->flushConfigCache();
            $this->addMessage("Settings saved.", self::MSG_SUCCESS);
        } catch (Exception $e) {
            $this->addMessage($e->getMessage(), self::MSG_ERROR);
        }
    }
    
    protected function flushConfigCache()
    {
        if (extension_loaded('apc')) {
            if (function_exists('apc_delete_file')) {
                @apc_delete_file(Curry_Core::$config->curry->configPath);
            } else {
                @apc_clear_cache();
            }
        }
    }
}