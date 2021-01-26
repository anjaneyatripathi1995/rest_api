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
    $password = $data->password;
    $query = "SELECT * FROM user_details where user_email='$user_email' LIMIT 0,1";
    $res = $conn->query($query);
    if($res->num_rows >0){
		$result = $res->fetch_assoc();
		$id = $result['id'];
		$password2 = $result['password'];
		$user_name = $result['user_name'];
		if(password_verify($password,$password2)){
			$token = generate_token($id,$user_name,$user_email);
			$update_token = "UPDATE user_details SET token ='$token' where id ='$id'";
			if($conn->query($update_token) === TRUE){
				echo json_encode(array("message"=>"Login Successfull","token"=>$token));
			}
		}
		else{
			http_response_code(300);
			echo json_encode(array("message"=>"Password do not match"));
		  }
	}

?>