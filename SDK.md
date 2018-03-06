# SSO - Sample PHP SDK

## Sample code using the sample SDK.php file

redirectUri.php
```php

// Include the SDK file
include "SDK.php";

// Prepare some configuration values
$clientId = 'my-client-id';
$clientSecret = 'my-client-secret';
$redirectUri = 'https://myapp.io/redirectUri.php';
$scope = ['profile', 'groups'];

$gdc  = new GDC\SDK($clientId, $clientSecret, redirectUri, $scope);
$accessToken = $gdc->getAccessToken();

// If we do not have a token, we will redirect user to the authorization page
if (!$accessToken) {
    $authorizationUrl = $gdc->getLoginUrl();
    header("Location: ".$authorizationUrl);
    die();
}

// We will retrieve informations about the Member
$infos = $gdc->getInfos();
echo 'Welcome '.$infos->firstName;
```
