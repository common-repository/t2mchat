<?php

namespace T2mchat\UserCategory;

use T2mchat\Traits\GraphQL;
use Proxy\Proxy;
use Symfony\Component\HttpFoundation\Request;
use Proxy\Adapter\Guzzle\GuzzleAdapter;
use Proxy\Response\Filter\RemoveEncodingFilter;
use League\OAuth2\Client\Token\AccessToken;
use T2mchat\Providers\GenericProvider;
use T2mchat\Providers\Filters\Oauth2Request;
use T2mchat\Providers\Proxies\Oauth2Proxy;
use GuzzleHttp\Client;
use T2mchat\Handleform\HandleForm;

class UserCategory extends HandleForm {
    function __construct(){
       parent::__construct();
    }

    function UserUpdate($user_id)
    {
        $user_obj = get_userdata( $user_id );
        if ($user_obj->account_id) {
            $email = $user_obj->user_email;
            $dname = $user_obj->display_name;  
            $pass = $user_obj->user_pass;
            $id = $user_obj->account_id;
            $name = explode(" ", $dname);   
            $fname = $name[0];
            $lname = $name[1] ? $name[1] : '';
     
            $obj = new \stdClass();
            $obj->operationName = "updateAccount";
            $obj->query = "mutation updateAccount {"
                            .'accountUpdateMutation (input: {id: "'.$id.'", email: "'.$email.'", firstName: "'.$fname.'", lastName: "'.$lname.'", enabled: true, password: "'.$pass.'"}) {'
                            ."account {
                            id
                            email
                            firstName
                            }
                        }
                        }";
            $obj->variables = null;
            $request = Request::create("", "POST", array(), array(), array(), array(), json_encode($obj));
            $request->headers->set('Content-Type', 'application/json');
            $result = $this->getResponse($request);
        }
    }


    function UserDelete($user_id)
    {
        $user_obj = get_userdata( $user_id );        
        $_SESSION["D_DATA"] = json_encode($user_obj);
    }

    function UserDeleted($user_id)
    {

        $user_obj = json_decode($_SESSION["D_DATA"]);
        $id = $user_obj->data->account_id;

        if ($id) {
            $email = $user_obj->data->user_email;
            $dname = $user_obj->data->display_name;  
            $pass = $user_obj->data->user_pass;            
            $name = explode(" ", $dname);   
            $fname = $name[0];
            $lname = $name[1] ? $name[1] : '';

            $obj = new \stdClass();
            $obj->operationName = "updateAccount";
            $obj->query = "mutation updateAccount {"
                                .'accountUpdateMutation (input: {id: "'.$id.'", email: "'.$email.'", firstName: "'.$fname.'", lastName: "'.$lname.'", enabled: false, password: "'.$pass.'"}) {'
                                ."account {
                                id
                                email
                                firstName
                                }
                            }
                            }";
            $obj->variables = null;
            $request = Request::create("", "POST", array(), array(), array(), array(), json_encode($obj));
            $request->headers->set('Content-Type', 'application/json');
            $result = $this->getResponse($request);
        }
    }
}

?>