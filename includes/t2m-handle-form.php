<?php

namespace T2mchat\Handleform;

use T2mchat\Traits\GraphQL;
use T2mchat\Traits\TokenHandler;
use Proxy\Proxy;
use Symfony\Component\HttpFoundation\Request;
use Proxy\Adapter\Guzzle\GuzzleAdapter;
use Proxy\Response\Filter\RemoveEncodingFilter;
use League\OAuth2\Client\Token\AccessToken;
use T2mchat\Providers\GenericProvider;
use T2mchat\Providers\Filters\Oauth2Request;
use T2mchat\Providers\Proxies\Oauth2Proxy;
use GuzzleHttp\Client;

class HandleForm extends \WP_REST_Controller {
    use GraphQl;
    use TokenHandler;
    
    protected $namespace = 'rest';

    function __construct() {
        //getting wordpress database data
        global $wpdb;
        $table_name = $wpdb->prefix . "t2mchat";
        $params = $wpdb->get_results("SELECT * FROM $table_name limit 1");
        // $scope = "T2MChat";
         // Create a guzzle client
         $guzzle = new Client();
         // Create the proxy instance
         $this->proxy = new Oauth2Proxy(new GuzzleAdapter($guzzle));
         // Add a response filter that removes the encoding headers.
         $this->proxy->addResponseFilter(new RemoveEncodingFilter());
        //  $input = JFactory::getApplication()->input;
        // DETERMINE IF THIS IS DEV OR PROD ENVIRONMENT
        $env = "";
        $url = "https://tech-stacks.io";
        if ($env == "dev") {
            $url = "http://192.168.33.10:3000";
        }
         $this->API_URL = "$url/graphql";
         $this->provider = new GenericProvider(array(
            'clientId'      =>$params[0]->ClientID,
            'clientSecret'  =>$params[0]->ClientSecret,
            'api_url' => "$url/oauth/token",
            'scope' => $params[0]->ClientService,
            'redirectUri'   => ''
        ));
         $filter = new Oauth2Request($params[0]->ClientService, $this->provider);
         $this->proxy->addRequestFilter($filter);
         $this->scope = $params[0]->ClientService;
    }
    public function register_routes() {
            register_rest_route($this->namespace, '/graphql', array(
                array(
                    'methods'         => \WP_REST_Server::CREATABLE,
                    'callback'        => array( $this, 'graphql' ),
                ),
                ) 
             );
             register_rest_route($this->namespace, '/token', array(
                array(
                    'methods'         => \WP_REST_Server::CREATABLE,
                    'callback'        => array( $this, 'getAccessToken' ),
                ),
                ) 
             );
    }
}
?>