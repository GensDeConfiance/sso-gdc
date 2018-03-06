# SSO - GDC OAuth to your application

## Declare a new application

To use the GDC SSO, you have to create a new application in your profile settings, in **"Vos applications"**.
You have to set the name of your application, the URI of redirection and the informations you want to get back in the response.

Be careful with the redirection, the URI has to be the same as the one you send in the request.

The GDC SSO exposes 4 different scopes :
- `profile` => first_name, last_name, picture
- `email` => main email address
- `groups` => list of user public networks
- `friends` => list of user friend ids (referrerIds and refereeIds)

At the end of the application's registration, you get your secret application key in a confirmation message. Save it : you can't get it twice!

## Configure your application with GDC SSO

In your application configuration, you have to use these informations to use SSO :

- Authorization URL : `https://gensdeconfiance.fr/oauth/v2/auth` (used to login)
- Access token URL : `https://gensdeconfiance.fr/oauth/v2/token`
- Informations URL : `https://gensdeconfiance.fr/api-oauth/info` (used to get user informations based on the selected scopes)

## Process Response

Here is a sample response from the GDC api :

```json
{
  "response": {
    "id": 12,
    "firstName": "sso-first_name",
    "lastName": "sso_last_name",
    "nbReferrers": 3,
    "nbReferees": 4,
    "gender": "male",
    "url": "https://gensdeconfience.fr/m/sso-user",
    "picture": "https://path-to-picture.com/sso-user.jpg",
    "email": "sso-user@example.org",
    "referrerIds": [
      4,
      5,
      6
    ],
    "refereeIds": [
      4,
      5,
      6,
      7
    ],
    "groups": [
      {
        "id": 3,
        "name": "sso-group",
        "image": "https://path-to-picture.com/sso-group.jpg",
      }
    ]
  }
}
```

## References

[FOSOAuthServerBundle](https://github.com/FriendsOfSymfony/FOSOAuthServerBundle/blob/master/Resources/doc/index.md)

## Example

### Ask permission to user

Calling `https://gensdeconfiance.fr/oauth/v2/auth?client_id=YOUR_CLIENT_ID&response_type=code&redirect_uri=YOUR_REDIRECT_URI&scope=PERMISSION_ASKED` will redirect the user to your callback url with a `code` parameter.

The scope parameter is a space separated values of permissions between: ``email``, ``friends``, ``groups`` and ``profile`` . For example: scope=email%20friends%20profile .

* Example:
  * `https://gensdeconfiance.fr/oauth/v2/auth?client_id=1_23ABCDE&response_type=code&redirect_uri=https%3A%2F%2Fmyapp.io%2Fconnect%2Fgdc&scope=profile`
  * Will redirect to:
    * `https://myapp.io/connect/gdc?code=abcde1234`

### Retrieve the `access_token`

Using the `code` parameter on your callback endpoint, you can retrieve the `access token` calling the following URL :
* `https://gensdeconfiance.fr/oauth/v2/token?grant_type=authorization_code&redirect_uri=YOUR_REDIRECT_URI&client_id=YOUR_CLIENT_ID&client_secret=YOUR_-_CLIENT_SECRET&code=THE_CODE_VALUE`

The response will look like :
* if successful :
```json
{
  "access_token": "someacccesstoken",
  "expires_in": 3600,
  "refresh_token": "somerefreshtoken",
  "scope": null,
  "token_type": "bearer"
}
```
* if failed :
```json
{
  "error": "invalid_grant",
  "error_description": "The authorization code has expired"
}
```

## Debugging

* If encoutered a 500 error, you should regenerate your user token in your application list : https://gensdeconfiance.fr/m/me/applications
