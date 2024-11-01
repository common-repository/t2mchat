<?php
namespace T2mchat\Traits;

use Firebase\JWT\JWT;
use T2mchat\Statics\JwtHelper;
use T2mchat\Providers\Filters\Oauth2Request;
use T2mchat\Providers\Filters;

trait TokenHandler {
    
    protected $provider = null;
    protected $proxy  = null;
    protected $API_URL = null;
    protected $filter = null;
    protected $scope = null;

    function getAccessToken($request) {
        global $wpdb;
        $jwt = $request->get_header("t2mchatJwt");
        if ($jwt == null) {
            return wp_send_json(array("message" => "invalid"), 401);
        }   
        try {
            $jwt = JwtHelper::VerifyJwt($jwt);
        } catch (\Exception  $e) {
            return wp_send_json(array("message" => $ex->getMessage()), 401);
        }
        $token = Oauth2Request::getAccessToken();
        $token = json_decode($token, true);
        $accessToken = $token['accessToken'];

        if ($token['expires'] <= time()) 
        {
            $scope = array('scope' => $this->scope);
            $token = $this->provider->getAccessToken("client_credentials", $scope);
            Oauth2Request::saveAccessToken($token);
            $accessToken = $token->accessToken;
        }
        $response = new \WP_REST_Response($accessToken);
        $response->header("t2mchatJwt", $jwt);
        return $response;
    }
}