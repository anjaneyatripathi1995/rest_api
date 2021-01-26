<?php
include_once '../config/database.php';
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;

function generate_token($user_id, $user_name, $user_email){
	
        $now = new DateTime();
        //$future = new DateTime("now +2 hours");
        $future = new DateTime("+43200 minutes");
        //$jti = (new Base62)->encode(random_bytes(16));
        $secret = "@3524#$%4654ythfDG$%^&*Sn*(41t)";
        
            $payload = [
             "iat" => $now->getTimeStamp(),
             "exp" => $future->getTimeStamp(),
             //"jti" => $jti,
             'data' => [
              'userId'   => $user_id,
              'Name' => $user_name,
              'Email' => $user_email,
            ] ];
            $token = JWT::encode($payload, $secret, "HS256");
            return $token;
        }

function decode_token($data){
	 $data = trim(str_replace('Bearer', '', $data));
	 return JWT::decode($data, "@3524#$%4654ythfDG$%^&*Sn*(41t)", array('HS256'));
}


function hashedString() {
	 $sal = '#*c5c^d9!@fz';
	 return hash("sha512", uniqid(rand(), true) . $sal);
}


?>