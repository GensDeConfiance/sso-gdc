<?php

namespace GDC;

class SDK
{
    const SCOPE_PROFILE = 'profile';
    const SCOPE_EMAIL = 'email';
    const SCOPE_GROUPS = 'groups';
    const SCOPE_FRIENDS = 'friends';

    const ENDPOINT = 'https://gensdeconfiance.Fr';

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $redirectUri;

    /**
     * @var string|null
     */
    private $jsonToken;

    /**
     * @var \stdClass
     */
    private $infos;

    /**
     * @var string[]
     */
    private $scope;

    public function __construct($clientId, $clientSecret, $redirectUri, $scope = [self::SCOPE_PROFILE])
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->scope = $scope;

        if (isset($_GET['code'])) {
            $this->getAccessToken();
        }
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return sprintf('%s/oauth/v2/auth?client_id=%s&response_type=code&scope=%s&redirect_uri=%s', self::ENDPOINT, $this->clientId, rawurlencode(implode(' ', $this->scope)), rawurlencode($this->redirectUri));
    }

    public function setAccessToken($accessToken)
    {
        $stdClass = new \stdClass();
        $stdClass->access_token = $accessToken;

        $this->jsonToken = $stdClass;
    }

    public function getAccessToken($code = null)
    {
        if (null !== $this->jsonToken) {
            return $this->jsonToken->access_token;
        }

        if (null === $code && isset($_GET['code'])) {
            $code = $_GET['code'];
        }
        if (!$code) {
            return;
        }
        $askTokenUrl = sprintf('%s/oauth/v2/token?grant_type=authorization_code&redirect_uri=%s&client_id=%s&client_secret=%s&code=%s', self::ENDPOINT, rawurlencode($this->redirectUri), $this->clientId, $this->clientSecret, $code);
        $this->jsonToken = json_decode(file_get_contents($askTokenUrl));

        return $this->jsonToken->access_token;
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        if (null !== $this->infos) {
            return $this->infos->response;
        }
        $this->infos = $this->query('/api-oauth/info');

        return $this->infos->response;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        $infos = $this->getInfo();
        if (!isset($infos->groups)) {
            throw new \Exception('No groups found on info endpoint. Please verify permissions and parameter scope');
        }

        return $info->groups;
    }

    /**
     * @return array
     */
    public function getFriends()
    {
        $infos = $this->getInfo();
        if (!isset($infos->friends)) {
            throw new \Exception('No friends found on info endpoint. Please verify permissions and parameter scope');
        }

        return $infos->friends;
    }

    public function refreshToken($refreshToken = null)
    {
        if (null === $refreshToken) {
            $refreshToken = $this->jsonToken->refresh_token;
        }
        $refreshUrl = sprintf('%s/oauth/v2/token?grant_type=refresh_token&redirect_uri=%s&client_id=%s&client_secret=%s&code=%s', self::ENDPOINT, rawurlencode($this->redirectUri), $this->clientId, $this->clientSecret, $refreshToken);
        $this->jsonToken = json_decode(file_get_contents($refreshURL));

        return $this->getAccessToken();
    }

    private function query($endpoint, $parameters = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl($endpoint));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        if ($parameters && !empty($parameters)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        }
        $headers = ['AUTHORIZATION: '.sprintf('Bearer %s', $this->getAccessToken())];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Error:'.curl_error($ch));
        }
        curl_close($ch);

        return json_decode($data);
    }

    private function getUrl($url)
    {
        return sprintf('%s%s', self::ENDPOINT, $url);
    }
}
