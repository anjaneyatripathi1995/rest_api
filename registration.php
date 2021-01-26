<?php
    include("../config/database.php");
    include("../config/function.php");
	header("Access-Control-Allow-Origin: * ");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    $conn = connect_db();

   $data = json_decode(file_get_contents("php://input"));

   $user_email = $data->user_email;
   $password = password_hash($data->password, PASSWORD_DEFAULT);
   $user_phone = $data->user_phone;
   $user_name = $data->user_name;
   $added_at = date('Y-m-d H:i:s');
   $query = "INSERT INTO user_details(user_email,password,user_phone,user_name,added_at) VALUES('$user_email','$password','$user_phone','$user_name','$added_at')";

   if($conn->query($query) === TRUE){
	   $last_id = $conn -> insert_id;
	   $token = generate_token($last_id,$user_name,$user_email);
	   $query_token ="UPDATE user_details SET token = '$token' WHERE id ='$last_id'";
	   if($conn->query($query_token)){
		     http_response_code(200);
             echo json_encode(array("message" => "User was successfully registered.",'token'=>$token));   
	   }
	   else{
		   http_response_code(400);
		   echo json_encode(array("message"=>"User can not be created Some error occured"));
	   }
   }
	else{
		http_response_code(400);
		echo json_encode(array("message"=>"User can not be created"));
	}

?>