<?php
namespace T2mchat\Providers\Filters;

use Proxy\Request\Filter\RequestFilterInterface;
use Symfony\Component\HttpFoundation\Request;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\AbstractProvider;
use Carbon\Carbon;

class Oauth2Request implements RequestFilterInterface
{
    private $scope;
    private $provider;

    function __construct($scope, AbstractProvider $provider, $removeAccessToken = false) {
        $this->scope = $scope;
        $this->provider = $provider;
        if ($removeAccessToken) {
            self::clearAcessToken();
        }
    }
    public function filter(Request $request)
    {
        $accessTokenJSON = self::getAccessToken();
        $scope = array('scope' => $this->scope);
        if (!$accessTokenJSON) {            
           $accessToken = $this->provider->getAccessToken("client_credentials", $scope);
            self::saveAccessToken($accessToken);        
        } else {
            $accessTokenJSON = json_decode($accessTokenJSON, true);
            $accessTokenJSON = array('access_token' => $accessTokenJSON['accessToken'], 'expires' => intval($accessTokenJSON['expires']), 
                'refresh_token' => $accessTokenJSON['refresh_token'], 'uid' => $accessTokenJSON['uid']);
            $accessToken = null;
            if ($accessTokenJSON['expires'] <= time()) 
            {
                self::clearAcessToken();
                $accessToken = $this->provider->getAccessToken('client_credentials', $scope);
                self::saveAccessToken($accessToken);
            } else {
                $accessToken = new AccessToken($accessTokenJSON);
            }
        }
        $request->headers->set('Authorization', 'Bearer '.$accessToken->accessToken);
        return $request;
    }
    public static function saveAccessToken ($accessToken)
    {
        
        global $wpdb;
        $table_name = $wpdb->prefix . "t2mtokens";
        $data = array(
                'token' => $accessToken->accessToken,
                'data' => json_encode($accessToken)
        );
        $wpdb->insert("$table_name", 
            $data,
            array(
            '%s',
            '%s'
            ) 
        ); 
    }
    public static function getAccessToken()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "t2mtokens";
        $data = $wpdb->get_row("SELECT * FROM $table_name ORDER BY ID DESC", "OBJECT", 0);
        return $data->data;
    }
    public static function clearAcessToken()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "t2mtokens";
        $wpdb->query("TRUNCATE TABLE $table_name");
    }    
}
