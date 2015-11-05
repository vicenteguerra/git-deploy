<?php
define('TOKEN', 'secret-token'); // This TOKEN put in Payload URL in GitLab or GitHub, Example: http://mydomain.com/deploy.php?token=mytoken
define('REMOTE_REPOSITORY', 'git@gitlab.com:HelloWorld.git');
define('DIR','/var/www/vhosts/HelloWordRepo/'); // It's important that the path ends with '/'
define('BRANCH','refs/heads/master'); // (Gitlab branch Route)
define('LOGFILE', "log.txt"); // Log File Name
define('GIT', '/usr/bin/git'); // Unix git command route

