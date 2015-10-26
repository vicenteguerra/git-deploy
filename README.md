# git-deploy
Php Script for Auto-Pull in server (Using WebHook from GitLab, GitHub or Bitbucket)

You can select the branch for auto pull, this is util for Development and Production Server config.

---

##On your server

You need to generate and config SSH Key

### Github

https://help.github.com/articles/generating-ssh-keys/

### Gitlab 

http://doc.gitlab.com/ce/ssh/README.html

When you have done ssh config, you can do "git pull" without put your password

In your public folder (public_html or something) you need put the __git-deploy__ folder with __deploy.php__ and __config.php__

Change your configuration in __config.php__  File.

```PHP
  define('TOKEN', 'your-secret-token'); 
  define('REMOTE_REPOSITORY', 'your-repository');
  define('DIR','your-absolute-path-git'); 
```


