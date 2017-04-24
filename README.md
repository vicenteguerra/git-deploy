# git-deploy

A PHP script to automatically pull from a repository to a web server (using a webhook on GitLab, GitHub or Bitbucket).

You can configure which branch this script pulls from. This utility is useful for both development and production servers.

---

## On your server

### SSH

Generate an SSH key and add it to your account so that "git pull" can be run without a password.

- [GitHub instructions](https://help.github.com/articles/generating-ssh-keys/)
- [GitLab instructions](http://doc.gitlab.com/ce/ssh/README.html)

### Configuration

Copy the __git-deploy__ folder and its contents in to your public folder (typically public_html). Note that you can change the name of the folder if desired.

Open __git-deploy/config.php__, and update each variable.

```PHP
  define('TOKEN', 'your-secret-token');
  define('REMOTE_REPOSITORY', 'your-repository');
  define('DIR','your-absolute-path-git');
  define('AFTER_PULL','your-shell-commands');
```

---

## On GitHub | GitLab | Bitbucket

### GitHub

In your repository, navigate to Settings &rarr; Webhooks &rarr; Add webhook, and use the following settings:

- Payload URL: https://www.yoursite.com/git-deploy/deploy.php?token=your-secret-token
- Content type: application/json
- Secret: blank (this script uses a token at the end of the URL as the secret)
- Which events would you like to trigger this webhook?: &#9745; Just the push event
- Active: &#9745;

Click "Add webhook" to save your settings, and the script should start working.

![Example screenshot showing GitHub webhook settings](https://cloud.githubusercontent.com/assets/1123997/25352059/4e38f734-28f0-11e7-8f2c-e7ca5ef153ea.png)

### GitLab

In your repository, navigate to Settings &rarr; Integrations, and use the following settings:

- URL: https://www.yoursite.com/git-deploy/deploy.php?token=your-secret-token
- Secret Token: blank (this script uses a token at the end of the URL as the secret token)
- Trigger: &#9745; Push events
- Enable SSL verification: &#9745; (only if using SSL, see [GitLab's documentation](https://gitlab.com/help/user/project/integrations/webhooks#ssl-verification) for more details)

Click "Add webhook" to save your settings, and the script should start working.

![Example screenshot showing GitLab webhook settings](https://cloud.githubusercontent.com/assets/1123997/25352520/e76ff672-28f1-11e7-8570-112f3eec8567.png)

### Bitbucket

In your repository, navigate to Settings &rarr; Webhooks &rarr; Add webhook, and use the following settings:

- Title: git-deploy
- URL: https://www.yoursite.com/git-deploy/deploy.php?token=your-secret-token
- Active: &#9745;
- SSL / TLS: &#9744; (only if using SSL, see [Bitbucket's documentation](https://confluence.atlassian.com/bitbucket/manage-webhooks-735643732.html#ManageWebhooks-skip_certificate) for more details)
- Triggers: &#9745; Repository push

Click "Save" to save your settings, and the script should start working.

![Example screenshot showing Bitbucket webhook settings](https://cloud.githubusercontent.com/assets/1123997/25353037/97ec1052-28f3-11e7-88e4-b45c4ca68220.png)

---

## Support on Beerpay

Hey dude! Help me out by buying me a couple of :beers:!

[![Beerpay](https://beerpay.io/vicenteguerra/git-deploy/badge.svg?style=beer-square)](https://beerpay.io/vicenteguerra/git-deploy)  [![Beerpay](https://beerpay.io/vicenteguerra/git-deploy/make-wish.svg?style=flat-square)](https://beerpay.io/vicenteguerra/git-deploy?focus=wish)
