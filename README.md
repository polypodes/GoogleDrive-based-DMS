# Google Drive based DMS (atop Symfony2)

A proof of concept for a Google Drive based document management system (DMS)  

Inspired from [genj/GenjGoogleDriveBundle](https://github.com/genj/GenjGoogleDriveBundle)

![snapshot](https://raw.githubusercontent.com/polypodes/GoogleDrive-based-DMS/master/doc/design/view-list.png)

## Requirements
  
- [Symfony 2.5](http://symfony.com/get-started), as declared in [composer.json](composer.json)
- [GooglApiClient](https://github.com/google/google-api-php-client), as declared in [composer.json](composer.json)
 
## 1/3 Setup Google API Client App credentials

To consume the Google Drive API, you need to follow this steps in [Google Developers Console (GDC)](https://console.developers.google.com/):
 
- Create a Google Drive API Project on [Google Developers Console (GDC)](https://console.developers.google.com/)
- Activate Google Drive API (status: ON) in [GDC](https://console.developers.google.com/) > API & auth > APIs
- Generate a *Service Account* application type OAuth Client ID in [GDC](https://console.developers.google.com/) > API & auth > Credentials 
- Download a `.p12` key file, via the same API Credentials form in GDC, to be then renamed as `ServiceAccountAPIKey.p12` 
 
## 2/3 Symfony2 App configuration

You need to configure your app using this informations you just obtained from [Google API Console](https://code.google.com/apis/console):

- Service account API key file: the `ServiceAccountAPIKey.p12`, generated above, pasted in the `app/config/` folder
- Service account e-mail address: a long `...@developer.gserviceaccount.com` e-mail address, generated above by Google API Console while creating your OAuth Credentials Client ID.

Add these to your parameters.yml:

```
dms.service_account_key_file:
dms.service_account_email:
```

## 3/3 Installation:

```bash
$~: git clone https://github.com/polypodes/GoogleDrive-based-DMS.git
$~: cd GoogleDrive-based-DMS
$~: make
$~: make install
```



## Usage:

- Use / Create a [Google Drive](https://www.google.com/drive/) User Account
- In the Google Drive web interface, (right-click)**share a file or an entire folder** 
with the same email address used to fill `dms.service_account_email` above.

Then open the `/files` route URL in a browser to see, view & download filesize, filetype & thubmnail of each file & folder.

## VPS deployement

```bash
ssh login@production
cd current_release
make deploy
```

## Hackin' & Slashin'

You may want to temporary avoid the OAuth login/authorize process while you're developing new features:
Just comment these lines at the very end of `app/config/security.yml`

```
#     Commenting these lines below = DISABLING login process & security controls
#        - { path: ^/api, roles: ROLE_USER }
#        - { path: ^/files, roles: ROLE_USER }

```

## Heroku deployment

### One-click way:

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)

### Manual way:

see https://devcenter.heroku.com/articles/getting-started-with-symfony2
and http://symfony.com/doc/current/cookbook/deployment/heroku.html

```bash
heroku config:set SYMFONY_ENV=prod
git push heroku master
```


### Set up Heroku parameters

Method: [a Composer script hadling your ignored parameter file](https://github.com/Incenteev/ParameterHandler#using-environment-variables-to-set-the-parameters)

#### `ServiceAccounAPIKey.p12` file:

Log in using `heroku run bash` and use curl to deploy manually the ServiceAccountAPIKey.p12 from a remote server

[Alternative solution](http://bezhermoso.github.io/2014/08/19/handling-parameters-for-heroku-deploy-in-symfony2/#alternate-solution)

### Heroku debug

Heroku allows you to run commands in a one-off dyno with heroku run.
Use this for scripts and applications that only need to be executed when needed,
or to launch an interactive PHP shell attached to your local terminal for experimenting in you app’s environment:

```
$ heroku run "php -a"
Running `php -a` attached to terminal... up, run.8081
Interactive shell
php > echo PHP_VERSION;
5.5.11
```

For debugging purposes, e.g. to examine the state of your application after a deploy,
you can use `heroku run bash` for a full shell into a one-off dyno.
But remember that this will not connect you to one of the web dynos that may be running at the same time!

([source](https://yitdeveloper.wordpress.com/2014/06/18/getting-started-with-php-on-heroku/))



ex: [vast-temple-3501](https://vast-temple-3501.herokuapp.com)


## TODO

There is a lot to do ! See [TODO.md](TODO.md)


## License

MIT
