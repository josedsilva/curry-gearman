# Curry Gearman
The Gearman package for Curry cms.

## Setup (for Ubuntu):

1. Install the gearman job server:
``sudo apt-get install gearman-job-server``
2. Install PECL
<code>
sudo apt-get install php-pear;
sudo pecl install gearman
</code>
3. Update php.ini for CLI and server and add the line ``extension=gearman.so`` in "Dynamic Extensions" section.

or see appropriate instruction at [gearman.org](http://gearman.org/getting-started/)

## Integration with Curry:

1. Start the gearman job server ``sudo gearmand -d``
(This should already be started by default: ``service --status-all | grep 'gearman-job-server'``)
2. Merge the directory structure with your project.
3. Start the gearman_listener script or add it to ``/etc/rc.local``
<code>
php gearman_listener.php > /dev/null 2>&1 &
</code>

## Examples
1. Create a job to email users:
Filename: Project/Job/EmailUsers.php
<code>
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
</code>

Push this job into the queue:
<code>
$client = new Common_Gearman_Client();
// this is an asynchronous job.
$client->addJobBackground(new Project_Job_EmailUsers());
$client->runTasks();
</code>

Logging is done to the default Curry cms log folder.

