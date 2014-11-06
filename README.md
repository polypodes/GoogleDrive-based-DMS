# SimpleDMS

A proof of concept for a Google Drive based document management system (DMS)  

Inspired from [genj/GenjGoogleDriveBundle)](https://github.com/genj/GenjGoogleDriveBundle)

## Requirements
  
  * Symfony 2.5
  * GooglApiClient - https://github.com/google/google-api-php-client


## Configuration

You need to get the following information from the Google API Console ( https://code.google.com/apis/console ):

* Service account API key file (this file is expected to be in the ```app/config/``` folder)
* Service account e-mail address

Add these to your parameters.yml:

```
genj_google_drive.service_account_key_file:
genj_google_drive.service_account_email:
```

## License

MIT
