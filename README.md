# git-deploy

A PHP script to automatically pull from a repository to a web server (using a webhook on GitHub, GitLab, or Bitbucket).

You can configure which branch this script pulls from. This script is useful for both development and production servers.

---

## On your server

### SSH

Generate an SSH key and add it to your account so that `git pull` can be run without a password.

- [GitHub documentation](https://help.github.com/articles/generating-ssh-keys/)
- [GitLab documentation](http://doc.gitlab.com/ce/ssh/README.html)
- [Bitbucket documentation](https://confluence.atlassian.com/bitbucket/add-an-ssh-key-to-an-account-302811853.html)

When __deploy.php__ is called by the web-hook, the webserver user (`www`, `www-data`, `apache`, etc...) will attempt to run `git pull ...`. You must ensure that the SSH key you generate is for the webserver user.

First, find the home directory of our webserver user, for example, by looking into /etc/passwd and looking for the `www-data` user or whatever the webserver user of your distribution is called. The home directory is likely `/var/www`.

Then, run (replacing `/var/www` with the home directory of the webserver user on your setup):

```bash
$ mkdir "$HOME/www-data.ssh"
$ ssh-keygen -q -t rsa -f "$HOME/www-data.ssh/id_rsa" -N ""
$ chown -R www-data:www-data "$HOME/www-data.ssh"
$ mkdir /var/www/.ssh
$ cat << END > /var/www/.ssh/config
> Host *
>     IdentityFile $HOME/www-data.ssh/id_rsa
> END
$ chown -R www-data:www-data /var/www/.ssh
```
Now, your webserver user will use the SSH key in $HOME/www-data.ssh/id_rsa for all its SSH connections.

### Configuration

Copy the __git-deploy__ folder and its contents in to your public folder (typically public_html). Note that you can change the name of the folder if desired.

Rename __git-deploy/deploy.sample.php__ to __git-deploy/deploy.php__, and update each variable to a value that suits your needs. Multiple copies of __git-deploy/deploy.sample.php__ can be made for multiple projects or versions (you just need to change the webhook url to match the new name). An example of a live configuration is below.

```PHP
define("TOKEN", "secret-token");
define("REMOTE_REPOSITORY", "git@github.com:username/custom-project.git");
define("DIR", "/var/www/vhosts/repositories/custom-project");
define("BRANCH", "refs/heads/master");
define("LOGFILE", "deploy.log");
define("GIT", "/usr/bin/git");
define("MAX_EXECUTION_TIME", 180);
define("BEFORE_PULL", "/usr/bin/git reset --hard @{u}");
define("AFTER_PULL", "/usr/bin/node ./node_modules/gulp/bin/gulp.js default");
```
### Permissions

When __deploy.php__ is called by the web-hook, the webserver user (`www`, `www-data`, `apache`, etc...) will attempt to run `git pull ...`. Since you probably cloned into the repository as yourself, and your user therefore owns it, the webserver user needs to be given write access. It is suggested this be accomplished by changing the repository group to the webserver user's and giving the group write permissions:

1. Open a terminal to the directory containing the repository on the server.
2. run `sudo chown -R $USER:webserverusername custom-project-repo-dir/.git/` to change the group of the repo.
3. run `sudo chmod -R g+s custom-project-repo-dir/.git/` to make the group assignment inherited for new files/dirs.
4. run `sudo chmod -R 775 custom-project-repo-dir/.git/` to set read & write for both owner and group.

---

## On GitHub | GitLab | Bitbucket

### GitHub

In your repository, navigate to Settings &rarr; Webhooks &rarr; Add webhook, and use the following settings:

- Payload URL: https://www.yoursite.com/git-deploy/deploy.php
- Content type: application/json
- Secret: The value of TOKEN in config.php
- Which events would you like to trigger this webhook?: :radio_button: Just the push event
- Active: :ballot_box_with_check:

Click "Add webhook" to save your settings, and the script should start working.

![Example screenshot showing GitHub webhook settings](https://cloud.githubusercontent.com/assets/1123997/25409764/f05526d0-29d8-11e7-858d-f28de59bd300.png)

### GitLab

In your repository, navigate to Settings &rarr; Integrations, and use the following settings:

- URL: https://www.yoursite.com/git-deploy/deploy.php
- Secret Token: The value of TOKEN in config.php
- Trigger: :ballot_box_with_check: Push events
- Enable SSL verification: :ballot_box_with_check: (only if using SSL, see [GitLab's documentation](https://gitlab.com/help/user/project/integrations/webhooks#ssl-verification) for more details)

Click "Add webhook" to save your settings, and the script should start working.

![Example screenshot showing GitLab webhook settings](https://cloud.githubusercontent.com/assets/1123997/25409763/f0540a16-29d8-11e7-95d1-5570c574fde0.png)

### Bitbucket

In your repository, navigate to Settings &rarr; Webhooks &rarr; Add webhook, and use the following settings:

- Title: git-deploy
- URL: https://www.yoursite.com/git-deploy/deploy.php?token=secret-token
- Active: :ballot_box_with_check:
- SSL / TLS: :white_large_square: Skip certificate verification (only if using SSL, see [Bitbucket's documentation](https://confluence.atlassian.com/bitbucket/manage-webhooks-735643732.html#ManageWebhooks-skip_certificate) for more details)
- Triggers: :radio_button: Repository push

Click "Save" to save your settings, and the script should start working.

![Example screenshot showing Bitbucket webhook settings](https://cloud.githubusercontent.com/assets/1123997/25353602/7aee9cde-28f5-11e7-9baa-eb1e1330017e.png)

## Integration with CI/CD

If you'd prefer to integrate git-deploy with your CI scripts rather than using traditional Webhooks, you can trigger the hook via the following `wget` command.

```sh
wget --quiet --output-document=- --content-on-error --header="Content-Type: application/json" --post-data='{"ref":"refs/heads/master"}' 'https://www.example.com/git-deploy/deploy.php?token=secret-token'
```

Additionally, you can add the parameters `sha=COMMIT_HASH` and `reset=true` to the URL in order to instruct git-deploy to reset to a specific commit. **Note that this will overwrite any local changes you may have made.** This can be useful for integration with things like [GitLab's Environments feature](https://gitlab.com/help/ci/environments).

---

I appreciate the collaboration of @JacobDB
