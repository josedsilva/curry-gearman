# Curry Gearman
The Gearman package for Curry cms.

### Setup (for Ubuntu):

1. Install the gearman job server    
``sudo apt-get install gearman-job-server``
2. Install PECL    
```
sudo apt-get install php-pear;    
sudo pecl install gearman
```
3. Update php.ini for CLI and server and add the line ``extension=gearman.so`` in "Dynamic Extensions" section.

or see appropriate instruction at [gearman.org](http://gearman.org/getting-started/)

### Integration with Curry:

1. Start the gearman job server    
``sudo gearmand -d``    
(This should have already been started by default: ``service --status-all | grep 'gearman-job-server'``)
2. Merge the directory structure with your project.
3. Start the gearman_listener script or add it to ``/etc/rc.local``    
``php gearman_listener.php > /dev/null 2>&1 &``

### Examples
Create a job to email users.

*Filename: Project/Job/EmailUsers.php*

```php
use Monolog\Logger;

class Project_Job_EmailUsers extends Common_JobAbstract
{
    public function handle()
    {
        $users = MemberQuery::create()
            ->find();
            
        foreach($users as $user)
        {
            $mail = new Curry_Mail();
            $mail->addTo($user->getEmail(), $user->getName());
            $mail->setFrom('noreply@somedomain.com', 'CurryPlayground');
            $mail->setSubject('Test mail');
            $mail->setBodyText('Hello World');
            try {
                $mail->send();
                $this->logger->log(Logger::INFO, "sent email to {$user->getEmail()}");
            } catch (Exception $e) {
                $this->logger->log(Logger::ERROR, $e->getMessage());
            }
            unset($mail);
        }
    }
}
```

Push this job into the queue:

```php
$client = new Common_Gearman_Client();
// this is an asynchronous job.
$client->addJobBackground(new Project_Job_EmailUsers());
$client->runTasks();
```

Logging is done to the default Curry cms log folder.

