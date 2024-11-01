<?php 
namespace T2mchat\Statics;

use Firebase\JWT\JWT;

class JwtHelper {
    static function VerifyJWT($jwt)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "t2mkeys";
        $key = $wpdb->get_row("SELECT key_data FROM $table_name limit 1");
        try {  
            $decoded = JWT::decode($jwt, $key->key_data, array('HS256'));
        } 
        catch (\Firebase\JWT\ExpiredException $ex)
        {
           $jwtExist = true;
           $table_name = $wpdb->prefix . "t2mjwts";
           $record = $wpdb->get_row("SELECT * from $table_name WHERE jwt = $jwt");
           if ($record == null) {
               $jwtExist = false;
           }
           if ($jwtExist) {
                 $newJwt = JWT::encode(
                    array(
                        "iss" => "t2mchat",
                        "iat" => time(),
                        "exp" => time() + (60 * 60)
                    ), $$key->key_data);
                $wpdb->insert("$table_name", 
                    array(
                        'jwt' => $newJwt
                    ),
                    array(
                        '%s'
                        ) 
                ); 
                $wpdb->delete("$table_name", 
                    array(
                        'jwt' => $jwt
                    )
                ); 
                $jwt = $newJwt;
           } else {
               throw new \Exception("JWT Token does not exist");
           }
        }
        return $jwt;
    }
}

?>