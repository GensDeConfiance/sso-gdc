# SSO - GDC OAuth to your application

## Declare a new application

To use the GDC SSO, you have to create a new application in your profile settings, in **"Mes applications"**.
You have to set the name of your application, the URI of redirection and the informations you want to get back in the response.

Be careful with the redirection, the URI has to be the same as the one you send in the request.

The GDC SSO exposes 4 different scopes :
- `profile` => first_name, last_name, picture and birthdate
- `email` => main email address
- `groups` => list of user public networks
- `friends` => list of user friend ids

At the end of the application's registration, you get your secret application key in a confirmation message. Save it : you can't get it twice!

## Configure your application with GDC SSO

In your application configuration, you have to use these informations to use SSO :

- Authorization URL : `http://gendeconfiance.fr/oauth/v2/auth` (used to login)
- Access token URL : `http://gendeconfiance.fr/oauth/v2/token`
- Informations URL : `http://gensdeconfiance.fr/api-oauth/info` (used to get user informations based on the selected scopes)

## Process Response

Here is a sample response from the GDC api :

```json
{
    response: {
        id: 12,
        email: "sso-user@example.org",
        first_name: "sso-first_name",
        last_name: "sso_last_name",
        nbFriends: 5,
        gender: "male,
        url: "http://gensdeconfience.fr/m/sso-user"
        picture: 'http://path-to-picture.com/sso-ser'
        friends: [4,5,6]
        groups: [
            {
                id: 3,
                name: "sso-group"
            }
        ]

    }
}
```

## References

[FOSOAuthServerBundle](https://github.com/FriendsOfSymfony/FOSOAuthServerBundle/blob/master/Resources/doc/index.md)
