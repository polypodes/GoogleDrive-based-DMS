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
- In the Google Drive web interface, (right-click) **share a file or an entire folder** 
with the same email address used to fill `dms.service_account_email` above.

Then open the `/files` route URL in a browser to see, view & download filesize, filetype & thubmnail of each file & folder.

## TODO

There is a lot to do ! See [TODO.md](TODO.md)


## License

MIT
