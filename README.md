# Curry Gearman
The Gearman package for Curry cms.

Setup (for Ubuntu):

1. Install the gearman job server:
``sudo apt-get install gearman-job-server``
2. Install PECL
<code>
sudo apt-get install php-pear;
sudo pecl install gearman
</code>
3. Update php.ini for CLI and server and add the line ``extension=gearman.so`` in "Dynamic Extensions" section.

or see appropriate instruction at [gearman.org](http://gearman.org/getting-started/)

Integration with Curry:

1. Start the gearman job server ``sudo gearmand -d``
(This should already be started by default: ``service --status-all | grep 'gearman-job-server'``)
2. Merge the directory structure with your project.
3. Start the gearman_listener script or add it to /etc/rc.local
<code>
php gearman_listener.php 2>&1 /dev/null &
</code>
