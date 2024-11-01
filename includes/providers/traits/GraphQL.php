<?php

namespace T2mchat\Traits;

use Symfony\Component\HttpFoundation\Request;
use Firebase\JWT\JWT;
use T2mchat\Statics\JwtHelper;

trait GraphQL {
    
    protected $proxy  = null;
    protected $API_URL = null;
    protected $provider = null;
    
    public function graphql($request)
    { 
        $jwt = $request->get_header("t2mchatJwt");
        if ($jwt == null) {
           return wp_send_json(array("message" => "invalid"), 401);
        }     
        try {
            $jwt = JwtHelper::VerifyJwt($jwt);
        } catch (\Exception $e) {
            return wp_send_json(array("message" => $e->getMessage()), 401);
        }
        // get the raw POST data
        $request = Request::createFromGlobals();
        $provider = $this->provider;
        $accessToken = null;
        try {
            $result = $this->getResponse($request);
            $response = new \WP_REST_Response($result);
            $response->header("t2mchatJwt", $jwt);
            return $response;
        } catch (\Exception $ex) {
            return wp_send_json(array("message" => $ex->getMessage()), 500);
        }
    }
    protected function getResponse($request)
    {
        $response = $this->proxy->forward($request)
        ->to($this->API_URL);
        $result = json_decode($response->getContent());
        return $result;
    }
}
