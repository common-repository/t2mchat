<?php
namespace T2mchat\Providers;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

class GenericProvider extends AbstractProvider {
    
    private $API_URL;
    
    public function __construct($options)
    {
        parent::__construct($options);
        $this->API_URL = $options['api_url'];
    }
    
    public function urlAuthorize()
    {}
    
    public function urlAccessToken()
    {
        return $this->API_URL;
    }

    public function urlUserDetails(AccessToken $token)
    {}
    
    public function userDetails($response, AccessToken $token)
    {}
    protected function prepareAccessTokenResult(array $result)
    {
        $this->setResultUid($result);
        $result["expires"] = strtotime($result['expires']);
        return $result;
    }

}