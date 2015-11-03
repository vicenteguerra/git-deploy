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

In your public folder (public_html or something) you need put the __git-deploy__ folder (or __hook__ for example) with __deploy.php__ and __config.php__

Change your configuration in __config.php__  File.

```PHP
  define('TOKEN', 'your-secret-token'); 
  define('REMOTE_REPOSITORY', 'your-repository');
  define('DIR','your-absolute-path-git'); 
```

---

## On GitLab | GitHub | Bitbucket

### Github 

In your repo:

Settings -> Webhooks and Services -> Add Webhooks

Payload Url: http://yoursite.com/hook/deploy.php?token=your-secret-token

Select Just the push event.

Mark "Active"

Add WebHook

![Alt text](https://lh3.googleusercontent.com/3JdfqcpD_Z26mxYHZSqxFXpGbjJI9gZ5R6ukFkKaI18VJD5OUcig9ejibIN2Z5PCIlx0Uss7Q4Tqz9eeovU52TEW7r0kCxTUvfumTFcVcQJ6qRXDN_2VGiE28s2iNTB_5BfjHUGvuJPw-4HXmNpPuklRTZCJfIZW9_a0MGA3F-plxUyWr14fslb1T585sakdoy2um9noDCCjoq0-IGrWtu5OjMfeFoy2rZd3ukHcyZUFZpW2E5I6in5sCXE8TlwNZn1P5zrpdKkUr-3oUcmX5WEr-sYISP8-vdh7fCu5BGNVZ5OKyvxQRbOq1q-1RVGZr8AccTU2rP8FA3vIfXe1_arIC0hzjeC1lzeQWshXjcwlP-WvMu3E7kpnezF1J1H4XxCWpJTbvPn7zAMIdL_k_WttMqyg3h05gEOPiDIsMorL2fddErSGfT50YlSx4YmWbxI5Hula-bTUviwGNP4wGAWHtiEcL2YtaZ6GLK6YrS9n5kV98or1P_P4p2aCmdBivY52oHGMJsubCrHI2qPC44iHf_i24KLiONVmD6orXkY=w996-h630-no "Optional title")

### Gitlab

In your project:

Settings -> Web Hooks  

Url: http://yoursite.com/hook/deploy.php?token=your-secret-token

Select Just the push event. 

Add WebHook

![Alt text](https://lh3.googleusercontent.com/ChFTifUafMA7DocGldkkIgqVDPRvSKgjeFWNu4NbNSCmiZxkFmZHhRBUFwSV5WmkDfBxGjn5FVW9PVRPi2kzFn1MM3S0EWVPavNTKx1UwDdKL2kZiFQzsIyawhIqGHfwMWxMsdANZ8RP7bnGXu9SxN7cAIwWFYCx7b0RNTgVjrPZzFzwHU_Cwb0YXmfgiQgGHKypZDEiFgwqWjqPja1AtckGX9dzG894jC7ecQFxCOBeCzveYbL8RG9_xb2fj2fqJu79WzBVqKuyILU00qsoglWBWYvEJLYC3VDpKba-OsomnRTkGqcnNaErrM_NR_URvOcs4CLZkOgZK0Cztj3wEdY5h8kfAdSfCWlki9Y0RAU0Xh7UUAhRWsQESsHNpi5uES22GO-oWoHf_uQ_297g9CRpbMPv4quWpYezvX-SbqHJy-o8ywVilvmcvRD2eSexwM6CH2ERPGwhwcJLbNu5AGsSnjNoVFCQaaffyMMFVwczGE0KrmOTQgwFcJM8HazA0X8tvPLnOgUwej_cLRKnI7T9Wpal-2sBCfkJ16Teu9U=w1234-h611-no "Optional title")

---

See the Wiki for more information about config (Customizing)







