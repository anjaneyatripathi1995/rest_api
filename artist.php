<?php
error_reporting(1);
ini_set('display_errors', 1);

/************************************************************************
 * status 
 * 0 = Booking done;
 * 1 = Quote Send;
 * 2 = Counter Offer;
 * 3 = Accepted;
 * 4 = Decline;
 * 5 = Completed;
 * 6 = Payment Done(Not For Now);
**************************************************************************/


 




/********************************************************************************/
      $app->get("/artist/category" , function( $request, $response, $args){
            $connection = connect_db();
            $connection->query("SET NAMES 'utf8'");
            $statement = "SELECT * FROM tbl_category WHERE status = '1'";
            $result=$connection->query($statement);
            $cat_data = array();
            
        	if($result->num_rows>0){
        	    while($row = $result->fetch_assoc()){
        	        $cat_data[] = $row;
        	    }
        	  $respon = ['success' => true, 'main_category' =>$cat_data];
        	}
        	
        	else{
        	    $respon = ['success' => false, 'message' => 'No data found'];
        	}
    	
         return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
      });

/********************************************************************************/

      $app->post('/artist/check_details', function($request,$response,$args){
          $data = $request->getParsedBody();
          $connection = connect_db();
          $user_name = sanitizeString($data['user_name']);
          $artist_email = sanitizeString($data['artist_email']);
          $artist_contact = sanitizeString($data['artist_contact']);
              if($artist_email != '' || $artist_contact != ''){
                  $statement = "SELECT * FROM tbl_artist WHERE status = '1'";
                  $result = $connection->query($statement);
                  if($result->num_rows > 0 ){
                      while($row = $result->fetch_assoc()){
                        // if($row['user_name'] == $user_name){
                        //       $respon = ['success'=> false , 'message' => "Username already exist"];
                        // }
                        if($row['artist_email'] == $artist_email){
                            $respon = ['success'=> false , 'message' => "User Email already exists"];  
                        }
                        // else if( $row['artist_contact'] == $artist_contact){
                        //     $respon = ['success'=> false , 'message' => "User contact number already exist"];
                        // }
                        else{
                            $respon = ['success'=>true, 'message' => "No previous record found"];
                        }
                      }
                    }
                    
                    else{
                        $respon = ['success' => true, 'message' => "No records exist in database"];
                    }
                }
            
               else{
                   $respon = ['success' => false, 'message' => "Fields are required"];
               }
           
          return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
      });

/********************************************************************************/

      $app->post('/artist/signup', function ($request,$response, $args){
              $data = $request->getParsedBody();
              $connection = connect_db();
              $user_name= sanitizeString($data['user_name']);
              $artist_email = sanitizeString($data['artist_email']);
              $artist_contact = sanitizeString($data['artist_contact']);
              $artist_password = md5(sanitizeString($data['artist_password']));
              $artist_name = sanitizeString($data['artist_name']);
              $building_number = sanitizeString($data['building_number']);
              $street_number = sanitizeString($data['street_number']);
              $town = sanitizeString($data['town']);
              $city = sanitizeString($data['city']);
              $post_code = sanitizeString($data['post_code']);
              $artist_age= sanitizeString($data['artist_age']);
              $category_id = sanitizeString(ucwords($data['category_id']));
              $category_name = sanitizeString(ucwords($data['category_name']));
              $artist_bio = sanitizeString(ucwords($data['artist_bio']));
              $artist_price = sanitizeString($data['artist_price']);
              $artist_travel = sanitizeString($data['artist_travel']);
              $artist_equipment = sanitizeString($data['artist_equipment']);
              $ip = sanitizeString($request->getServerParam('REMOTE_ADDR'));
              $device_id = sanitizeString($data['device_id']);
              $device_type = sanitizeString($data['device_type']);
              
              $social_token = sanitizeString($data['social_token']);
              $social_token_type = sanitizeString($data['social_token_type']);

          $files = $request->getUploadedFiles();
          $directory  =   dirname(__FILE__).'/../../uploads/artist_profile/';
          
              if(!empty($files)){
                $pic = $files['pic'];
        		if(!empty($pic) && $pic->getError() === UPLOAD_ERR_OK) {
        			$extension = pathinfo($pic->getClientFilename(), PATHINFO_EXTENSION);
        			$filename = substr(hash('sha256', mt_rand() . microtime()), 0, 20).'_'.'pic'.'.'.strtolower($extension);
        		    /*$directory = BASE_URL.'uploads/images/';*/
        			$v = $directory.$filename;
        			$pic->moveTo($v);
        			$profile = '../uploads/images/'.$filename;
        			$profile = basename($filename);
            		}
        		else{
            			$respon = ["success" => false, 'message' => "Fail to upload file"];
            			$status = 400;
        		     }
              }
              
              else{
                $profile='';
              }
            
            $statement_urn = $connection->query("SELECT artist_urn FROM tbl_artist ORDER BY id DESC LIMIT 1");
            $urn_result = $statement_urn->fetch_assoc();
            if(empty($urn_result['artist_urn'])){
                $artist_urn = 'A10000';
            }
            else{
                $old_urn = $urn_result['artist_urn'];
                $old_urn++;
                $artist_urn = $old_urn;
            }

            $statement = "INSERT INTO tbl_artist (device_type,artist_urn,device_id,user_name,artist_email,artist_contact,artist_password,artist_name,profile_image,building_number,
            street_number,town,city,post_code,artist_age,artist_category,artist_bio,artist_price,artist_travel,artist_equipment,ip,social_token,social_token_type) 
            VALUES ('$device_type','$artist_urn','$device_id','$user_name','$artist_email','$artist_contact','$artist_password','$artist_name','$profile','$building_number',
            '$street_number','$town','$city','$post_code','$artist_age','$category_name','".$artist_bio."','$artist_price','$artist_travel','$artist_equipment','$ip','$social_token'
            ,'$social_token_type')";
            $result = $connection->query($statement);
            if($result == 1){
                $last_id = $connection->insert_id;
                if($last_id){
                    $connection->query("INSERT INTO tbl_artist_unavailability(artist_id,unavailability_date) VALUES('$last_id','NULL')");
    				$token = generate_token($last_id,$user_name,$artist_email);
    				$connection->query("UPDATE tbl_artist SET token = '$token' WHERE id = '$last_id'");
    				$connection->query("INSERT INTO tbl_artist_categories(artist_id,category_id,category_name) VALUES('$last_id','$category_id','$category_name')");
    				send_sginup_greeting_mail($artist_email);
    				$respon = ['success' => true, 'message' => 'Successfully registered','artist_details' =>['artist_id'=>$last_id,'token'=>$token,'name'=>$artist_name,
    				'profile_image'=> BASE_URL.'uploads/artist_profile/'.''.$profile]];
    			}
    			else {
    				$respon = ['success' => false, 'message' => 'failed to register a artist'];
    			}
            }
        
            else{
                $respon = ['success'=>false,'message'=>'Data could not be inserted'];
            }
        
          return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
      });
      
/********************************************************************************/

    $app->post('/artist/images',function( $request, $response, $args){
        $data = $request->getParsedBody();
    	$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
    	$connection = connect_db();
        $directory  =  'uploads/artist_multiple_image/';
    	if($udata){
    	    $artist_id = $udata['id'];
    	    $files = $request->getUploadedFiles();
            $error=array();
            $extension=array("jpeg","jpg","png","gif");
            foreach($_FILES["images"]["tmp_name"] as $key=>$tmp_name) {
                $file_name=$_FILES["images"]["name"][$key];
                $file_tmp=$_FILES["images"]["tmp_name"][$key];
                $ext=pathinfo($file_name,PATHINFO_EXTENSION);
                
                if(in_array($ext,$extension)) {
                    $filename=basename($ext);
                    $file_name=substr(hash('sha256', mt_rand() . microtime()), 0, 20).'_'.'pic'.'.'.strtolower($filename);
                    move_uploaded_file($file_tmp=$_FILES["images"]["tmp_name"][$key],"$directory".$file_name);
                    $array_data = $file_name;
                    $statement = "INSERT INTO tbl_image (artist_id,image_path) VALUES ('".$artist_id."','".$array_data."')";
                    $result = $connection->query($statement);
                    if($result == 1){
                        $respon = ['success'=> true,'message'=>'Successfully inserted'];
                    }
                    else{
                        $respon = ['success'=> true,'message'=>'Error in insertion'];
                    }
                }
                else {
                    $respon = ['success'=> true,'message'=>'Upload error'];die;
                }
            }
    	}
    	else{
    	    $respon = ['success'=> false,'message'=> 'Authorization refused'];
    	}
    	
    	  return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });

/********************************************************************************/
    
    $app->post('/artist/links_insert', function( $request , $response , $args){
        $data = $request->getParsedBody();
    	$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
    	$connection = connect_db();
    	if($udata){
    	    $artist_id = $udata['id'];
    	    $linkdata = $data['links'];
            $statement = "UPDATE tbl_artist  SET artist_video_link = '".$linkdata."' WHERE id= '".$artist_id."' ";
            $result = $connection->query($statement);
            if($result == 1){
              $respon = ['success'=> true,'message'=>'Data successfully inserted'];   
            }
            else{
                $respon = ['success'=> false, 'message'=> 'Data insertion failed'];
            }
    	}
    	else{
    	    $respon = ['success'=> false,'message'=>'Authorization refused'];
    	}
    	
        return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });
    
/********************************************************************************/

    $app->post('/artist/artist_unavailability', function( $request , $response , $args){
        $data = $request->getParsedBody();
    	$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
    	$connection = connect_db();
    	if($udata){
    	    $artist_id = $udata['id'];
    	    $datedata = $data['dates'];
    	    //$alldate = json_encode($datedata);
    	    $unavail_statement = "SELECT * FROM tbl_artist_unavailability WHERE artist_id = $artist_id  AND status = '1' ";
    	    $unavaility_data = $connection->query($unavail_statement);
    	    if($unavaility_data->num_rows > 0){
                    $statement = "UPDATE tbl_artist_unavailability SET artist_id = '".$artist_id."',unavailability_date = '".$datedata."' WHERE artist_id = '".$artist_id."'";
                    $result = $connection->query($statement);
                    if($result == 1){
                      $respon = ['success'=> true,'message'=>'Data Successfully inserted'];   
                    }
                    else{
                        $respon = ['success'=> false, 'message'=> 'Data insertion failed'];
                    }  
    	    }
    	    else{
        	    $statement = "INSERT into tbl_artist_unavailability(artist_id,unavailability_date) VALUES('".$artist_id."','".$datedata."')";
                $result = $connection->query($statement);
                if($result == 1){
                  $respon = ['success'=> true,'message'=>'Data successfully inserted'];   
                }
                else{
                    $respon = ['success'=> false, 'message'=> 'Data insertion failed'];
                }
    	    }
    	}
    	else{
    	    $respon = ['success'=> false,'message'=>'Authorization refused'];
    	}
    	
        return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });
    
/********************************************************************************/

        $app->get('/artist/get_artist_unavailability', function( $request,$response,$args){
        $data = $request->getParsedBody();
    	$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
    	$connection = connect_db();
    	if($udata){
    	    $artist_id = $udata['id'];
    	    $unavailability_dates = array();
    	    $unavailability_statement = "SELECT * FROM tbl_artist_unavailability WHERE artist_id = $artist_id  AND status = '1' ";
    	    $unavailability_data = $connection->query($unavailability_statement);
    	    if($unavailability_data->num_rows > 0){
    	        while($row = $unavailability_data->fetch_assoc()){
    	            $unavailability_dates = $row['unavailability_date'];
    	        }
    	        
    	        $respon = ['success'=> true ,'message' => 'Data successfully retrieved','unavailability_dates'=>$unavailability_dates];
    	        
    	    }
    	    else{
    	        
    	        $respon = ['success'=> false,'message'=>'No data found'];
    	        
    	    }
    	}
    	else{
    	    $respon = ['success'=> false,'message'=>'Authorization refused'];
    	}
    	
        return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });
/********************************************************************************/
   $app->post('/artist/socail_login',function ($request , $response ,$args){
        $data = $request->getParsedBody();
        $connection = connect_db();
        $social_token = sanitizeString($data['social_token']);
        $device_id = $data['device_id'];
        $device_type = $data['device_type'];
       if($social_token){
           $statement = "SELECT * FROM tbl_artist WHERE social_token = '".$social_token."'";
           $result = $connection->query($statement);
           if($result->num_rows > 0 ){
               $row = $result->fetch_assoc();
               $token = generate_token($row['id'],$row['user_name'],$row['artist_email']);
		       $insert_statement = "UPDATE tbl_artist SET token = '$token',device_type='$device_type',device_id = '$device_id' WHERE id= '".$row['id']."'";
		       $insert_query = $connection->query($insert_statement);
		      if($insert_query == true){
                $respon = ['success'=> true,'message'=>'Login Succesfully','authdata' => ['artist_id'=> $row['id'],'token'=> $token,'name'=> $row['artist_name'], 
				       'email'=> $row['artist_email'],'profile_image'=> BASE_URL.'uploads/artist_profile/'.''.$row['profile_image']]];
		      }
		      else{
				        $respon = ['success'=> false,'message'=>'Some error occurred'];
				}
           }
           else{
               $respon = ['success'=> false,'message'=>'No data found']; 
           }
       }
       return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
       ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
   });




/********************************************************************************/

    $app->post('/artist/artist_login', function( $request , $response ,$args){
        $data = $request->getParsedBody();
    	$connection = connect_db();
        $artist_email = $data['artist_email'];
        $password = $data['user_password'];
        $device_id = $data['device_id'];
        $device_type = $data['device_type'];
        if($artist_email != '' && $password != ''){
            $statement = "SELECT * FROM tbl_artist where artist_email = '".$artist_email."' ";
            $result = $connection->query($statement);
            if($result->num_rows >0 ){
                $row = $result->fetch_assoc();
                if($row['artist_password'] == md5($password)){
				    $token = generate_token($row['id'],$row['user_name'],$row['artist_email']);
				    $insert_statement = "UPDATE tbl_artist SET token = '".$token."',device_type='$device_type',device_id = '".$device_id."' WHERE id= '".$row['id']."'";
				    $insert_query = $connection->query($insert_statement);
				    if($insert_query == true){
				       $respon = ['success'=> true,'message'=>'Login Succesfully','authdata' => ['artist_id'=> $row['id'],'token'=> $token,'name'=> $row['artist_name'], 
				       'email'=> $row['artist_email'],'profile_image'=> BASE_URL.'uploads/artist_profile/'.''.$row['profile_image']]]; 
				    }
				    else{
				        $respon = ['success'=> false,'message'=>'Some error occurred'];
				    }
                }
                
                else{
                    $respon = ['success'=> false,'message'=>"Password doesn't match"];
                }
            }
            else{
                $respon = ['success'=>false,'message'=>"email doesn't exist"];
            }
            
        }
        else{
          $respon = ['success'=> false,'message'=>'Please fill all data'];
        }
        
        return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });


/********************************************************************************/

    $app->post('/artist/forgot_password', function( $request , $response ,$args){
    $data = $request->getParsedBody();
    $connection = connect_db();
    $user_email = $data['user_email'];
    if($user_email != ''){
        $statement = "SELECT * FROM tbl_artist where artist_email = '".$user_email."' ";
        $result = $connection->query($statement);
        if($result->num_rows >0 ){
            $row = $result->fetch_assoc();
            if($row['status'] === '1'){
                $token = hashedString();
                $password = randomPassword();
                $converted_password = md5($password);
                //echo $password;
                $statement = "UPDATE tbl_artist SET token = '$token',artist_password = '$converted_password'  WHERE id = '".$row['id']."' AND artist_email = '$user_email'";
                $connection->query($statement);
                // $msg = 'Your Bookme Password is "'.$password.'"';
            $altBody = 'Bookme Password  Reset';
				$mail_send= send_forget_password_mail($user_email, $row['user_name'], 'Bookme Reset Password', $password, $altBody);
				if($mail_send == true){
				    $respon = ['success' => true, 'message' => 'We send a Password on your mail, please check your mail'];
				}
				else{
				   $respon = ['success' => true, 'message' => 'Email can not be sent']; 
				}
                
            }
            else{
                $respon = ['success' => false, 'message' => 'Your account is blocked'];
            }
            
        }
        else{
            $respon = ['success'=>false,'message'=>"User email doesn't exist"];
        }
        
    }
    else{
      $respon = ['success'=> false,'message'=>'Please fill all data'];
    }
    
    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });
    

/********************************************************************************/


/********************************************************************************/

    $app->post('/artist/forgot_username', function( $request , $response ,$args){
    $data = $request->getParsedBody();
    $connection = connect_db();
    $user_email = $data['user_email'];
    if($user_email != ''){
        $statement = "SELECT * FROM tbl_artist where artist_email = '".$user_email."' ";
        $result = $connection->query($statement);
        if($result->num_rows >0 ){
            $row = $result->fetch_assoc();
            //$msg = 'Your Username "'.$row['user_name'].'"';
            $altBody = 'Your Username';
			$mail_send= send_forget_username_mail($user_email, $row['user_name'], 'Your Username', $row['user_name'], $altBody);
			if($mail_send == true){
			    $respon = ['success' => true, 'message' => 'We send your username on your mail, please check your mail'];
			}
			else{
			   $respon = ['success' => true, 'message' => 'Email can not be sent']; 
			}
            
        }
        else{
            $respon = ['success'=>false,'message'=>"User email doesn't exist"];
        }
        
    }
    else{
      $respon = ['success'=> false,'message'=>'Please fill all data'];
    }
    
    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });
    

/********************************************************************************/


    $app->get("/artist/artist_pofile" , function( $request, $response, $args){
            $data = $request->getParsedBody();
        	$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
        	$connection = connect_db();
            $connection->query("SET NAMES 'utf8'");
        if($udata){
            //$statement = "SELECT * FROM tbl_artist WHERE id = '".$udata['id']."'";
            $statement = "SELECT tbl_artist.*,tbl_artist_categories.*
            FROM tbl_artist 
            LEFT JOIN tbl_artist_categories
            ON tbl_artist_categories.artist_id = tbl_artist.id
            WHERE tbl_artist.id = '".$udata['id']."'";
            $result=$connection->query($statement);
            $profile_data = array();
        	if($result->num_rows>0){
        	     $row = $result->fetch_assoc();
        	        $profile_data['category_id'] = $row['category_id'];
        	        $profile_data['category_name'] = $row['category_name'];
        	        $row['profile_image'] = BASE_URL.'uploads/artist_profile/'.$row['profile_image'];
        	        $profile_data = $row;
        	        $respon = ['success' => true, 'artist_profile' =>$profile_data];
        	    }
        	else{
        	    $respon = ['success' => false, 'message' => 'No data found'];
        	}
        }
        else{
              $respon = ['success'=> false,'message'=> 'Authorization refused'];
        }
    	
         return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
      });
      
/********************************************************************************/    
    $app->get("/artist/artist_links" , function( $request, $response, $args){
            $data = $request->getParsedBody();
        	$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
        	$connection = connect_db();
            $connection->query("SET NAMES 'utf8'");
        if($udata){
            $statement = "SELECT artist_video_link FROM tbl_artist WHERE id = '".$udata['id']."'";
            $result=$connection->query($statement);
            
        	if($result->num_rows>0){
        	    $row = $result->fetch_assoc();
        	    $multiple_links = $row['artist_video_link'];
        	     $respon = ['success' => true, 'links' =>$multiple_links];
        	}
        	else{
        	    $respon = ['success' => false, 'message' => 'No data found'];
        	 }
        }
        else{
              $respon = ['success'=> false,'message'=> 'Authorization refused'];
        }
    	
         return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
      });


/********************************************************************************/


    $app->get("/artist/fetch_multiple_images" , function( $request, $response, $args){
            $data = $request->getParsedBody();
        	$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
        	$connection = connect_db();
            $connection->query("SET NAMES 'utf8'");
        if($udata){
            $statement = "SELECT image_path FROM tbl_image WHERE artist_id = '".$udata['id']."'";
            $result=$connection->query($statement);
            $multiple_images = array();
            
        	if($result->num_rows>0){
        	    while($row = $result->fetch_assoc()){
        	        $row['image_path'] = BASE_URL.'uploads/artist_multiple_image/'.$row['image_path'];
        	        $multiple_images[] = $row['image_path'];
        	    }
        	  $respon = ['success' => true, 'multiple_images' =>$multiple_images];
        	}
        	
        	else{
        	    $respon = ['success' => false, 'message' => 'No data found'];
        	}
        }
    else{
          $respon = ['success'=> false,'message'=> 'Authorization refused'];
    }
    	
          return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
      });

/********************************************************************************/

/********************************************************************************/
  $app->post("/artist/delete_multiple_images" , function( $request, $response, $args){
      $data = $request->getParsedBody();
      $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
      $connection = connect_db();
      $connection->query("SET NAMES 'utf8'");
    if($udata){
        $image_name = $data['image_name'];
        $image_path =   'uploads/artist_multiple_image/'.$image_name;
        if (file_exists($image_path)){
            $statement = "DELETE FROM tbl_image WHERE artist_id= '".$udata['id']."' AND image_path = '$image_name'";
            $result = $connection->query($statement);
            unlink($image_path);
            $respon = ['success' => true, 'message' => 'File '.$image_path.' has been deleted'];
        }
        else {
         $respon = ['success' => false, 'message' => 'Could not delete '.$image_path.', file does not exist'];
        }
    }
    else{
          $respon = ['success'=> false,'message'=> 'Authorization refused'];
    }
    return $response->withStatus(200)
    ->withHeader("Content-Type", "application/json")
    ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
  });
/********************************************************************************/


/********************************************************************************/


    $app->post("/artist/update_password" , function( $request, $response, $args){
            $data = $request->getParsedBody();
        	$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
        	$connection = connect_db();
            $connection->query("SET NAMES 'utf8'");
            
            if($udata){
            $old_password = md5($data['old_pass']);
            $new_password = md5($data['new_pass']);
            $statement = "SELECT * FROM tbl_artist WHERE id = '".$udata['id']."'";
            $result = $connection->query($statement);

        	if($result->num_rows > 0){
        	    $row = $result->fetch_assoc();
        	      if($row['artist_password'] == $old_password ){
        	          $statement_new = "UPDATE tbl_artist SET artist_password = '".$new_password."' WHERE id = '".$udata['id']."'";
        	          $result_new = $connection->query($statement_new);
        	          
        	          if($result_new == 1){
        	                 $respon = ['success'=> true,'message'=> 'Password updated successfully'];
        	          }
        	          else{
        	             $respon = ['success'=> true,'message'=> 'Password can not be updated']; 
        	          }
        	          
        	      }
        	      else{
        	          $respon = ['success'=> false, 'message'=> "Old password doesn't match"];
        	      }
        	}
        	
        	else{
        	    $respon = ['success' => false, 'message' => 'No data found'];
        	}
        }
        else{
              $respon = ['success'=> false,'message'=> 'Authorization refused'];
        }
          return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
      });

/********************************************************************************/

/********************************************************************************/


    $app->get("/artist/my_booking" , function( $request, $response, $args){
            $data = $request->getParsedBody();
        	$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
        	$connection = connect_db();
            $connection->query("SET NAMES 'utf8'");
            if($udata){
                //$venue_id =  $data['venue_id'];
                $statement= "SELECT * FROM tbl_booking WHERE artist_id = '".$udata['id']."' ORDER BY c_date DESC";
                $result = $connection->query($statement);
                $a = array();
        	if($result->num_rows > 0){
        	    while($row = $result->fetch_assoc()){
        	        
        	        if($row['guest_id'] == ''){
            	         $statement_new = "SELECT rating,venue_name,profile_image,venue_manager_name,alt_manager_name,address
            	         ,building_number,street_number,town,city,post_code FROM tbl_venue WHERE id = '".$row['venue_id']."'";
            	         $result_new = $connection->query($statement_new);
            	         $row_new = $result_new->fetch_assoc();
            	         $row['venue_name'] = $row_new['venue_name'];
            	         $row['rating'] = $row_new['rating'];
            	         $row['profile_image'] = BASE_URL.'uploads/venue_images/profile_image/'.$row_new['profile_image'];
            	         $row['venue_manager_name'] = $row_new['venue_manager_name'];
            	         $row['address'] = $row_new['address'];
            	         $row['building_number'] = $row_new['building_number'];
            	         $row['street_number'] = $row_new['street_number'];
            	         $row['town'] = $row_new['town'];
            	         $row['city'] = $row_new['city'];
            	         $row['post_code'] = $row_new['post_code'];
        	        }
        	        else{
        	             $statement_new = "SELECT rating,manager_name,profile_image,
        	             building_number,street_number,town,city,post_code FROM tbl_guest WHERE id = '".$row['guest_id']."'";
            	         $result_new = $connection->query($statement_new);
            	         $row_new = $result_new->fetch_assoc();
            	         $row['guest_name'] = $row_new['manager_name'];
            	         $row['rating'] = $row_new['rating'];
            	         $row['profile_image'] = BASE_URL.'uploads/guest_images/profile_image/'.$row_new['profile_image'];
                         $row['address'] = $row['address'];
            	         $row['building_number'] = $row['building_number'];
            	         $row['street_number'] = $row['street_address'];
            	         $row['town'] = $row['town'];
            	         $row['city'] = $row['city'];
            	         $row['post_code'] = $row['post_code'];
        	        }
        	         
        	         $a[] = $row;
        	    }
        	    $respon = ['success' => true, 'message' => 'Data successfully retrieved','data'=>$a];
        	}
        	
        	else{
        	    $respon = ['success' => false, 'message' => 'No data found'];
        	}
        }
        else{
              $respon = ['success'=> false,'message'=> 'Authorization refused'];
        }
    	
          return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
      });

/********************************************************************************/

/********************************************************************************/


    $app->post("/artist/booking_details" , function( $request, $response, $args){
            $data = $request->getParsedBody();
        	$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
        	$connection = connect_db();
            $connection->query("SET NAMES 'utf8'");
        if($udata){
            $booking_id=  $data['booking_id'];
            $statement= "SELECT * FROM tbl_booking WHERE booking_id = '".$booking_id."'";
            $result = $connection->query($statement);
            $a = array();

        	if($result->num_rows > 0){
        	    while($row = $result->fetch_assoc()){
        	        if($row['guest_id'] == ''){
        	         $statement_new = "SELECT venue_name,profile_image,venue_manager_name,alt_manager_name,address
        	         ,building_number,street_number,town,city,post_code,number_of_rooms,venue_bio,venue_facilities_available FROM tbl_venue WHERE id = '".$row['venue_id']."'";
        	         $result_new = $connection->query($statement_new);
        	         $row_new = $result_new->fetch_assoc();
        	         $row['venue_name'] = $row_new['venue_name'];
        	         $row['profile_image'] = BASE_URL.'uploads/venue_images/profile_image/'.$row_new['profile_image'];
        	         $row['venue_manager_name'] = $row_new['venue_manager_name'];
        	         $row['alt_venue_manager_name'] = $row_new['alt_manager_name'];
        	         $row['about'] = $row_new['venue_bio'];
        	         $row['room'] = $row_new['number_of_rooms'];
        	         $row['facilities_available'] = $row_new['venue_facilities_available'];
        	         $row['description'] = $row['description'];
        	         $row['address'] = $row_new['address'];
        	         $row['building_number'] = $row_new['building_number'];
        	         $row['street_number'] = $row_new['street_number'];
        	         $row['town'] = $row_new['town'];
        	         $row['city'] = $row_new['city'];
        	         $row['post_code'] = $row_new['post_code'];
        	         
        	         $detail= "SELECT * FROM tbl_booking_quotation WHERE booking_id = '".$booking_id."'";
                     $booking = $connection->query($detail);
                     $booking_detail = $booking->fetch_assoc();
                     $row['venue_counter_offer']= $booking_detail['venue_counter_offer'];
                     $row['artist_quote_offer']= $booking_detail['artist_quote_offer'];
        	        }
        	        else{
        	         $statement_new = "SELECT username,profile_image,guest_bio,manager_name,
        	         facilities_available FROM tbl_guest WHERE id = '".$row['guest_id']."'";
        	         $result_new = $connection->query($statement_new);
        	         $row_new = $result_new->fetch_assoc();
        	         $row['username'] = $row_new['username'];
        	         $row['profile_image'] = BASE_URL.'uploads/guest_images/profile_image/'.$row_new['profile_image'];
        	         $row['manager_name'] = $row_new['manager_name'];
        	         $row['username'] = $row_new['username'];
        	         $row['about'] = $row_new['guest_bio'];
        	         
        	         $detail= "SELECT * FROM tbl_booking_quotation WHERE booking_id = '".$booking_id."'";
                     $booking = $connection->query($detail);
                     $booking_detail = $booking->fetch_assoc();
                     $row['guest_counter_offer']= $booking_detail['guest_counter_offer'];
                     $row['artist_quote_offer']= $booking_detail['artist_quote_offer'];
                     $row['description'] = $row['description'];
                     $row['address'] = $row['address'];
        	         $row['building_number'] = $row['building_number'];
        	         $row['street_number'] = $row['street_address'];
        	         $row['town'] = $row['town'];
        	         $row['city'] = $row['city'];
        	         $row['post_code'] = $row['post_code'];
        	        }
        	         $a[] = $row;
        	    }
        	    $respon = ['success' => true, 'message' => 'Data successfully retrieved','data'=>$a];
        	}
        	
        	else{
        	    $respon = ['success' => false, 'message' => 'No data found'];
        	}
        }
    else{
        $respon = ['success'=> false,'message'=> 'Authorization refused'];
        }
          return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
      });

/********************************************************************************/

/******************************************************************************/

    $app->post("/artist/fetch_room_descriptions",function($request,$response,$args){
      $data = $request->getParsedBody();
      $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
      $connection = connect_db();
      $venue_id = $data['venue_id'];
      if($udata){
       $a = array();
       $statement = "SELECT id,venue_id,room_image,room_name,room_size,room_hold,carry_equipment,power,changing_facility FROM tbl_room_detailing WHERE venue_id = '".$venue_id."' and status = 1";
       $result = $connection->query($statement);
       if($result->num_rows > 0){
         while($row = $result->fetch_assoc()){
           $row['room_image'] = BASE_URL.'uploads/venue_images/room_detailing_image/'.$row['room_image'];
           $a[] = $row;
         }
         $respon = ['success'=> true,'message'=>'Data successfully retrieved','data'=>$a];
       }
       else{
         $respon = ['success'=> false,'message'=> 'NO data found'];
       }
     }
     else{
       $respon = ['success'=> false,'message'=> 'Authorization refused'];
     }
     return $response->withStatus(200)
     ->withHeader("Content-Type", "application/json")
     ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });

/********************************************************************************/

/********************************************************************************/


    $app->post("/artist/venue_fetch_multiple_images" , function( $request, $response, $args){
      $data = $request->getParsedBody();
      $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
      $connection = connect_db();
      $connection->query("SET NAMES 'utf8'");
      if($udata){
          $venue_id = $data['venue_id'];
          $statement = "SELECT image_path FROM tbl_image WHERE venue_id = '".$venue_id."'";
          $result=$connection->query($statement);
          $multiple_images = array();
        
          if($result->num_rows>0){
           while($row = $result->fetch_assoc()){
             $row['image_path'] = BASE_URL.'uploads/venue_images/multiple_image/'.$row['image_path'];
             $multiple_images[] = $row['image_path'];
           }
           $respon = ['success' => true, 'multiple_images' =>$multiple_images];
         }
        
         else{
           $respon = ['success' => false, 'message' => 'No data found'];
         }
      }
    else{
        $respon = ['success'=> false,'message'=> 'Authorization refused'];
    }
    
     return $response->withStatus(200)
     ->withHeader("Content-Type", "application/json")
     ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });

/********************************************************************************/

/********************************************************************************/


    $app->post("/artist/guest_fetch_multiple_images" , function( $request, $response, $args){
      $data = $request->getParsedBody();
      $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
      $connection = connect_db();
      $connection->query("SET NAMES 'utf8'");
      if($udata){
          $guest_id = $data['guest_id'];
          $statement = "SELECT image_path FROM tbl_image WHERE guest_id = '".$guest_id."'";
          $result=$connection->query($statement);
          $multiple_images = array();
        
          if($result->num_rows>0){
           while($row = $result->fetch_assoc()){
             $row['image_path'] = BASE_URL.'uploads/guest_images/multiple_image/'.$row['image_path'];
             $multiple_images[] = $row['image_path'];
           }
           $respon = ['success' => true, 'multiple_images' =>$multiple_images];
         }
        
         else{
           $respon = ['success' => false, 'message' => 'No data found'];
         }
      }
    else{
        $respon = ['success'=> false,'message'=> 'Authorization refused'];
    }
    
     return $response->withStatus(200)
     ->withHeader("Content-Type", "application/json")
     ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });

/********************************************************************************/



/********************************************************************************/


    $app->post("/artist/gig_counter" , function( $request, $response, $args){
      $data = $request->getParsedBody();
      //$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
      $connection = connect_db();
      $connection->query("SET NAMES 'utf8'");
      $booking_id = $data['booking_id'];
      $quote_price = $data['quote_price'];
      $type_of_user = $data['type_of_user'];
      
       $artist_id = getFieldWhere('artist_id','tbl_booking','booking_id',$booking_id);
       $artist_name = getFieldWhere('artist_name','tbl_artist','id',$artist_id);
       $artist_device_id = getFieldWhere('device_id','tbl_artist','id',$artist_id);
       $artist_device_type = getFieldWhere('device_type','tbl_artist','id',$artist_id);
       $device_id = $artist_device_id;
       $device_type = $artist_device_type;
       
       
      
      if($type_of_user == 1){
         $title ="Quotation";
         $statement = "INSERT INTO tbl_booking_quotation (booking_id,artist_quote_offer,counter_status) values('$booking_id','$quote_price','1')";
         $result = $connection->query($statement);
         
         $statement_new = "UPDATE tbl_booking SET quote_price='$quote_price',status = '1' WHERE booking_id = '".$booking_id."' ";
         $result_new = $connection->query($statement_new);
         if($result){
             $statement_new2 ="SELECT * FROM tbl_booking WHERE booking_id = '".$booking_id."'";
             $result2 = $connection->query($statement_new2);
             if($result2->num_rows > 0 ){
                 $row2 = $result2->fetch_assoc();
                 if($row2['venue_id'] != ''){
                     $statement_venue_data = "SELECT id,venue_name,device_type,device_id FROM tbl_venue WHERE id='".$row2['venue_id']."' ";
                     $venue_data_result =  $connection->query($statement_venue_data);
                     $venue_data = $venue_data_result->fetch_assoc();
                     $venue_name =  $venue_data['venue_name'];
                     $venue_device_type = $venue_data['device_type'];
                     $venue_device_id =  $venue_data['device_id'];
                     
                      /*$venue_id = getFieldWhere('venue_id','tbl_booking','booking_id',$booking_id);
                      $venue_name = getFieldWhere('venue_name','tbl_venue','id',$venue_id);
                      $venue_device_type = getFieldWhere('device_type','tbl_venue','id',$venue_id);
                      $venue_device_id = getFieldWhere('device_id ','tbl_venue','id',$venue_id);*/
                 }
                 if($row2['guest_id'] != ''){
                     $statement_guest_data = "SELECT id,manager_name,device_type,device_id FROM tbl_guest WHERE id='".$row2['guest_id']."' ";
                     $guest_data_result =  $connection->query($statement_guest_data);
                     $guest_data = $guest_data_result->fetch_assoc();
                     $guest_name =  $guest_data['manager_name'];
                     $guest_device_type = $guest_data['device_type'];
                     $guest_device_id =  $guest_data['device_id'];
                      /*$guest_id = getFieldWhere('guest_id','tbl_guest','booking_id',$booking_id);
                      $guest_name = getFieldWhere('manager_name','tbl_guest','id',$guest_id);
                      $guest_device_type = getFieldWhere('device_type','tbl_guest','id',$guest_id);
                      $guest_device_id = getFieldWhere('device_id ','tbl_guest','id',$guest_id);*/
                 
                 }
             }
            $body = 'null';
            if(!empty($venue_name) && !empty($venue_device_type) && !empty($venue_device_id)){
               $body = "$venue_name You have got quotation of  £$quote_price"; 
               $device_type = $venue_device_type;
               $device_id = $venue_device_id;
            }
             
            if(!empty($guest_name) && !empty($guest_device_type) && !empty($guest_device_id)){
               $body = "$guest_name You have got quotation of  £$quote_price";
               $device_type = $guest_device_type;
               $device_id = $guest_device_id;
            }
            gig_counter_nofication($device_id,$device_type,$quote_price,$title,$body,$booking_id);
            
             $respon = ['success'=> true,'message'=> 'Data successfully inserted'];
         }
         else{
              $respon = ['success'=> false,'message'=> 'Some error occurred'];
         }
      }
      if($type_of_user == 2){
          $title ="Counter offer";
          $statement = "UPDATE tbl_booking_quotation SET venue_counter_offer = '$quote_price' ,counter_status= '2' WHERE booking_id = '".$booking_id."'";
          $result = $connection->query($statement);
           $statement_new = "UPDATE tbl_booking SET quote_price='$quote_price',status = '2' WHERE booking_id = '".$booking_id."' ";
         $result_new = $connection->query($statement_new);
          if($result){
             $body = "$artist_name You Have got counter offer of £$quote_price";
             gig_counter_nofication($device_id,$device_type,$quote_price,$title,$body,$booking_id);
             $respon = ['success'=> true,'message'=> 'Data successfully inserted'];
          }
          else{
              $respon = ['success'=> false,'message'=> 'Some error occurred'];
         }
      }
      if($type_of_user == 3){
          $title ="Counter offer";
          $statement = "UPDATE tbl_booking_quotation SET guest_counter_offer = '$quote_price' , counter_status= '2' WHERE booking_id ='".$booking_id."'";
          $result = $connection->query($statement);
           $statement_new = "UPDATE tbl_booking SET quote_price='$quote_price',status = '2' WHERE booking_id = '".$booking_id."' ";
         $result_new = $connection->query($statement_new);
          if($result){
              
            $body = "$artist_name You have got counter offer of £$quote_price";
            gig_counter_nofication($device_id,$device_type,$quote_price,$title,$body,$booking_id);
             
             $respon = ['success'=> true,'message'=> 'Data successfully inserted'];
         }
         else{
              $respon = ['success'=> false,'message'=> 'Some error occurred'];
         }
      }
    
     return $response->withStatus(200)
     ->withHeader("Content-Type", "application/json")
     ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });

/********************************************************************************/


/********************************************************************************/


    $app->post("/artist/accept_decline" , function( $request, $response, $args){
      $data = $request->getParsedBody();
      $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);

      $connection = connect_db();
      $connection->query("SET NAMES 'utf8'");
      $booking_id = $data['booking_id'];
      $quote_price = $data['quote_price'];
      $status = $data['status'];
      $sbuject = "Booking status";
      $venue_id = getFieldWhere('venue_id','tbl_booking','booking_id',$booking_id);
      $guest_id = getFieldWhere('guest_id','tbl_booking','booking_id',$booking_id);
      $booking_date = getFieldWhere('booking_date','tbl_booking','booking_id',$booking_id);
      $artist_name = getFieldWhere('artist_name','tbl_artist','id',$udata['id']);
      
      
      $title ="Booking Status";
      if($venue_id != ''){
        $venue_name = getFieldWhere('venue_name','tbl_venue','id',$venue_id);
        $venue_device_type = getFieldWhere('device_type','tbl_venue','id',$venue_id);
        $venue_device_id = getFieldWhere('device_id','tbl_venue','id',$venue_id);
        $venue_email = getFieldWhere('email','tbl_venue','id',$venue_id);
        $device_id = $venue_device_id;
        $device_type = $venue_device_type;
        
        if($status==3){
          $body = "Hello $venue_name , Your booking is accepted"; 
          $booking_id = $booking_id;
          send_booking_status_email($udata['artist_email'],$artist_name,$venue_name,$booking_date,$sbuject,$booking_id);
          send_booking_status_email($venue_email,$venue_name,$artist_name,$booking_date,$sbuject,$booking_id);
        }
      
        if($status==4){
         $body = "Hello $venue_name , Your booking is declined from the artist side";
        }
      
        if($status==5){
         $body = "Hello $venue_name , Your booking is completed";  
        }
        
        if($status==7){
         $body = "Hello $venue_name , Your booking is canceled from the artist side";  
        }
        
      }
      
      if($guest_id != ''){
        $guest_name = getFieldWhere('manager_name','tbl_guest','id',$guest_id);
        $guest_device_type = getFieldWhere('device_type','tbl_guest','id',$guest_id);
        $guest_device_id = getFieldWhere('device_id','tbl_guest','id',$guest_id);
        $guest_email = getFieldWhere('email','tbl_guest','id',$guest_id);
        $device_id = $guest_device_id;
        $device_type = $guest_device_type;
              
        if($status==3){
          $body = "Hello $guest_name , Your booking is accepted";
          send_booking_status_email($udata['artist_email'],$artist_name,$guest_name,$booking_date,$sbuject,$booking_id);
          send_booking_status_email($guest_email,$guest_name,$artist_name,$booking_date,$sbuject,$booking_id);
        }
      
        if($status==4){
         $body = "Hello $guest_name , Your booking is canceled from the artist side";  
        }
      
        if($status==5){
         $body = "Hello $guest_name , Your booking is completed";  
        }
        
        if($status==7){
         $body = "Hello $venue_name , Your booking is canceled from the artist side";  
        }
      }
      
      $whr='';
      if(!empty($quote_price)){
          $whr.="quote_price='$quote_price',";
      }
     
      if($udata){
          if($status){
            $statement = "UPDATE tbl_booking SET ".$whr." status = $status WHERE booking_id = '".$booking_id."' ";
            $result = $connection->query($statement);
            if($result){
                $respon = ['success'=> true,'message'=> 'Data Successfully Inserted'];
                gig_counter_nofication($device_id,$device_type,$quote_price,$title,$body,$booking_id); 
            }
            else{
                $respon = ['success'=> false,'message'=> 'Some error occured'];
            }
          }
      }
      else{
            $respon = ['success'=> false,'message'=> 'Authorization refused'];
      }
     return $response->withStatus(200)
     ->withHeader("Content-Type", "application/json")
     ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });

/********************************************************************************/

/********************************************************************************/


    $app->post("/artist/status_update" , function( $request, $response, $args){
          $data = $request->getParsedBody();
          $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
          $connection = connect_db();
          $connection->query("SET NAMES 'utf8'");
          $status = $data['status'];
          $booking_id = $data['booking_id'];
          $body = "TEST";
          $title = "TEST";
          if($udata){
          $statement = "SELECT tbl_booking.venue_id,tbl_booking.booking_id,tbl_venue.device_type,tbl_venue.id,tbl_venue.device_id
            FROM tbl_booking
            LEFT JOIN tbl_venue
            ON tbl_booking.venue_id = tbl_venue.id
            WHERE tbl_booking.booking_id = '".$booking_id."'";
            
          $result = $connection->query($statement);
          $row =  $result->fetch_assoc();
          
          $success = update_booking_status($status,$booking_id,$row['device_type'],$row['device_id'],$body,$title);
          
          if($success == true){
            $respon = ['success'=> true,'message'=> 'Data successfully inserted'];  
          }
          else{
            $respon = ['success'=> false,'message'=> 'Some error occured'];  
          }
          
      }
      else{
            $respon = ['success'=> false,'message'=> 'Authorization refused'];
        }
    
     return $response->withStatus(200)
     ->withHeader("Content-Type", "application/json")
     ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });

/********************************************************************************/

/********************************************************************************/
      $app->post("/artist/get_status" , function( $request, $response, $args){
            $data = $request->getParsedBody();
            $connection = connect_db();
            $connection->query("SET NAMES 'utf8'");
            $booking_id = $data['booking_id'];
            $statement = "SELECT status FROM tbl_booking WHERE booking_id = '".$booking_id."'";
            $result=$connection->query($statement);

        	if($result->num_rows>0){
        	    $row = $result->fetch_assoc();
        	  $respon = ['success' => true,'message' => 'Data successfully retrieved', 'data' =>$row];
        	}
        	
        	else{
        	    $respon = ['success' => false, 'message' => 'No data found'];
        	}
    	
         return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
      });

/********************************************************************************/

/********************************************************************************/
  $app->get("/artist/booking_list", function( $request, $response, $args){
          $data = $request->getParsedBody();
          $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
          $connection = connect_db();
          $connection->query("SET NAMES 'utf8'");
          $booking_data = array();
          if($udata){
            $statement ="SELECT * FROM tbl_booking where artist_id = '".$udata['id']."' and ((status = 7 or status = 3) and transaction_id !='NULL')";
            $result = $connection->query($statement);
            if($result->num_rows > 0 ){
               while($row = $result->fetch_assoc()){
                   $booking_data[] = $row['booking_date'];
               }
               $respon = ['success' => true, 'message' => 'Data successfully retrived','booking_data' => $booking_data];
            }
            else{
        	    $respon = ['success' => false, 'message' => 'No data found'];
        	}
            
          }
          else{
            $respon = ['success'=> false,'message'=> 'Authorization refused'];
           }
        
        return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
  });

/********************************************************************************/

/********************************************************************************/
  $app->post("/artist/review", function( $request, $response, $args){
          $data = $request->getParsedBody();
          $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
          $connection = connect_db();
          $connection->query("SET NAMES 'utf8'");
          
          
          $guest_id =   $data['guest_id'];
          $venue_id =  $data['venue_id'];
          $artist_id =  $data['artist_id'];
          $booking_id = $data['booking_id'];
          $comments = $data['comments'];
          $rating= $data['rating'];
          $rating_2= $data['rating_2'];
          $rating_3= $data['rating_3'];
          
          if($udata){
            if($venue_id != ''){  
                $statement = 'INSERT INTO tbl_reviews SET artist_id="'.$artist_id.'",venue_id = "'.$venue_id.'",comments="'.$comments.'",rating = "'.$rating.'",
                rating_2="'.$rating_2.'",rating_3="'.$rating_3.'",booking_id = "'.$booking_id.'"';
                // $statement = "INSERT INTO tbl_reviews SET artist_id='$artist_id',venue_id = '$venue_id',comments = '$comments' ,rating = '$rating',rating_2='$rating_2',
                // rating_3='$rating_3',booking_id = '$booking_id'";
                $result = $connection->query($statement);
                
                if($connection->insert_id){
                  $statement = "SELECT * FROM tbl_reviews WHERE venue_id = '$venue_id'";
                  $result_rating = $connection->query($statement);
                  if($result_rating->num_rows >0){
                            $total_rating = 0;
                            $ave_rating = 0 ;
                        while($rating_data = $result_rating->fetch_assoc()){
                            $total_rating =  $total_rating + $rating_data['rating']+$rating_data['rating_2']+$rating_data['rating_3'];
                            $ave_rating = $total_rating /($result_rating->num_rows * 3);
                        } 
                    $ave_rating = round($ave_rating,2);
                      }
                       $insert_statement = "UPDATE tbl_venue SET rating ='".$ave_rating."' WHERE id= '$venue_id'";
                       $insert_result = $connection->query($insert_statement);
                }
            }
            if($guest_id != ''){
                $statement = 'INSERT INTO tbl_reviews SET artist_id="'.$artist_id.'",guest_id = "'.$guest_id.'",comments="'.$comments.'",rating = "'.$rating.'",
                rating_2="'.$rating_2.'",rating_3="'.$rating_3.'",booking_id = "'.$booking_id.'"';
                // $statement = "INSERT INTO tbl_reviews SET artist_id='$artist_id',guest_id = '$guest_id',comments = '$comments' ,rating = '$rating',rating_2='$rating_2',
                // rating_3='$rating_3',booking_id = '$booking_id'";
                $result = $connection->query($statement);
                
                if($connection->insert_id){
                      $statement = "SELECT * FROM tbl_reviews WHERE guest_id = '$guest_id'";
                      $result_rating = $connection->query($statement);
                      if($result_rating->num_rows >0){
                                $total_rating = 0;
                                $ave_rating = 0 ;
                            while($rating_data = $result_rating->fetch_assoc()){
                                $total_rating =  $total_rating + $rating_data['rating']+$rating_data['rating_2']+$rating_data['rating_3'];
                                $ave_rating = $total_rating /($result_rating->num_rows * 3);
                            } 
                        $ave_rating = round($ave_rating,2);
                      }
                       $insert_statement = "UPDATE tbl_guest SET rating ='".$ave_rating."' WHERE id= '$guest_id'";
                       $insert_result = $connection->query($insert_statement);
                 }
             
            }
            
            if($result){
               $respon = ['success' => true, 'message' => 'Successfully inserted'];
            }
            else{
        	    $respon = ['success' => false, 'message' => 'Error occured'];
        	}
          }
          else{
            $respon = ['success'=> false,'message'=> 'Authorization refused'];
           }
        
        return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
  });

/********************************************************************************/

/********************************************************************************/
  $app->get("/artist/get_review", function( $request, $response, $args){
          $data = $request->getParsedBody();
          $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
          $connection = connect_db();
          $connection->query("SET NAMES 'utf8'");
          
          if($udata){
             $statement = "SELECT * FROM tbl_artist_reviews WHERE artist_id = '".$udata['id']."'";
             $result = $connection->query($statement);
             $a = array();
             if($result->num_rows > 0){
                 while($row = $result->fetch_assoc()){
                        $row['date'] = date("d-m-Y", strtotime($row['added_at']));
                        $row['time'] = date("H:i:s", strtotime($row['added_at']));
                    if($row['venue_id'] != ''){
                            $venue_name = getFieldWhere('venue_name','tbl_venue','id',$row['venue_id']);
                            $venue_profile_image = getFieldWhere('profile_image','tbl_venue','id',$row['venue_id']);
                            $row['venue_name'] = $venue_name;
                            $row['venue_profile_image'] = BASE_URL.'uploads/venue_images/profile_image/'.$venue_profile_image;
                    } 
                    if($row['guest_id'] != ''){
                            $guest_name = getFieldWhere('manager_name','tbl_guest','id',$row['guest_id']);
                            $guest_profile_image = getFieldWhere('profile_image','tbl_guest','id',$row['guest_id']);
                            $row['guest_name'] = $guest_name;
                            $row['guest_profile_image'] = BASE_URL.'uploads/guest_images/profile_image/'.$guest_profile_image;
                           
                    }
                    $a[] = $row;  
                 }
                $respon = ['success' => true, 'message' => 'Data retrieved','rating_data'=> $a]; 
            }
             
             else{
                 $respon = ['success' => false, 'message' => 'No data found']; 
             }
          }
          else{
            $respon = ['success'=> false,'message'=> 'Authorization refused'];
           }
        
        return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
  });

/********************************************************************************/

/********************************************************************************/
  $app->post("/artist/guest_venue_get_review", function( $request, $response, $args){
          $data = $request->getParsedBody();
          $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
          $connection = connect_db();
          $connection->query("SET NAMES 'utf8'");
          $venue_id = $data['venue_id'];
          $guest_id = $data['guest_id'];
          
          if($venue_id == ''){
              $statement = "SELECT * FROM tbl_reviews WHERE guest_id = '".$guest_id."'";
          }
          else{
              $statement = "SELECT * FROM tbl_reviews WHERE venue_id = '".$venue_id."'";
          }
          $result = $connection->query($statement);
          $a = array();
          if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                        $row['date'] = date("d-m-Y", strtotime($row['added_at']));
                        $row['time'] = date("H:i:s", strtotime($row['added_at']));
                    if($row['artist_id'] != ''){
                            $artist_name = getFieldWhere('artist_name','tbl_artist','id',$row['artist_id']);
                            $artist_profile_image = getFieldWhere('profile_image','tbl_artist','id',$row['artist_id']);
                            $row['artist_name'] = $artist_name;
                            //$newDate = date("d/m/y,h:i a", strtotime($row['added_at']));
                            $row['date'] = date("d-m-Y", strtotime($row['added_at']));
                            $row['time'] = date("H:i:s", strtotime($row['added_at']));
                            $row['artist_profile_image'] = BASE_URL.'uploads/artist_profile/'.$artist_profile_image;
                    } 
                    if($row['venue_id'] != ''){
                            $venue_name = getFieldWhere('venue_name','tbl_venue','id',$row['venue_id']);
                            $venue_profile_image = getFieldWhere('profile_image','tbl_venue','id',$row['venue_id']);
                            $row['venue_name'] = $venue_name;
                            $row['venue_profile_image'] = BASE_URL.'uploads/venue_images/profile_image/'.$venue_profile_image;
                    } 
                    if($row['guest_id'] != ''){
                            $guest_name = getFieldWhere('manager_name','tbl_guest','id',$row['guest_id']);
                            $guest_profile_image = getFieldWhere('profile_image','tbl_guest','id',$row['guest_id']);
                            $row['guest_name'] = $guest_name;
                            $row['guest_profile_image'] = BASE_URL.'uploads/guest_images/profile_image/'.$guest_profile_image;
                           
                    }
                    $a[] = $row;  
                 }
                $respon = ['success' => true, 'message' => 'Data retrieved','rating_data'=> $a]; 
            }
             
             else{
                 $respon = ['success' => false, 'message' => 'No data found']; 
             }
         return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
  });

/********************************************************************************/


/********************************************************************************/
  $app->post("/artist/paypal_detail", function( $request, $response, $args){
          $data = $request->getParsedBody();
          $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
          $connection = connect_db();
          $connection->query("SET NAMES 'utf8'");
          
          $paypal_email   =   $data['paypal_email'];
          $account_name   =   $data['account_name'];
          $account_number =   $data['account_number'];
          $bank_sort_code =   $data['bank_sort_code'];
          
          if($udata){
            $statement = "UPDATE tbl_artist SET paypal_email ='$paypal_email',account_name ='$account_name',account_number ='$account_number',bank_sort_code ='$bank_sort_code'
            WHERE id = '".$udata['id']."'";
            $result = $connection->query($statement);
            if($result){
               $respon = ['success' => true, 'message' => 'Successfully inserted'];
            }
            else{
        	    $respon = ['success' => false, 'message' => 'Data can not be inserted'];
        	}
          }
          else{
            $respon = ['success'=> false,'message'=> 'Authorization refused'];
           }
        
        return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
  });

/********************************************************************************/

/********************************************************************************/
  $app->get("/artist/get_paypal_detail", function( $request, $response, $args){
          $data = $request->getParsedBody();
          $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
          $connection = connect_db();
          $connection->query("SET NAMES 'utf8'");
          if($udata){
            $statement = "SELECT id,paypal_email,account_name,account_number,bank_sort_code FROM tbl_artist WHERE id = '".$udata['id']."'";
            $result = $connection->query($statement);
            if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                $respon = ['success' => true, 'message' => 'Data successfully retrieved','paypal_detail'=>$row]; 
            }
            
            else{
        	    $respon = ['success' => false, 'message' => 'No data found'];
        	 }
          }
          else{
            $respon = ['success'=> false,'message'=> 'Authorization refused'];
           }
        
        return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
  });

/********************************************************************************/


/********************************************************************************/
  $app->get("/artist/artist_booking_count", function( $request, $response, $args){
          $data = $request->getParsedBody();
          $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
          $connection = connect_db();
          $connection->query("SET NAMES 'utf8'");
          if($udata){
            $statement = "SELECT  * FROM tbl_booking  WHERE artist_id = '".$udata['id']."' and status ='0'";
            $result = $connection->query($statement);
            if($result->num_rows > 0){
                  $data = $result->num_rows;
                 $respon = ['success' => true, 'message' => 'Data successfully retrieved','total_bookings'=>$data];
            }

            else{
        	    $respon = ['success' => false, 'message' => 'No data found'];
        	}
          }
          else{
            $respon = ['success'=> false,'message'=> 'Authorization refused'];
           }
        
        return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
  });

/********************************************************************************/

/********************************************************************************/


    $app->post("/artist/bookings_by_status" , function( $request, $response, $args){
            $data = $request->getParsedBody();
        	$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
        	$connection = connect_db();
            $connection->query("SET NAMES 'utf8'");
            $booking_date = $data['booking_date'];
            if($udata){
                $statement= "SELECT * FROM tbl_booking WHERE artist_id = '".$udata['id']."'  AND booking_date = '$booking_date' ORDER BY c_date DESC";
                $result = $connection->query($statement);
                $a = array();
        	if($result->num_rows > 0){
        	    while($row = $result->fetch_assoc()){
        	        if($row['guest_id'] == ''){
            	         $statement_new = "SELECT rating,venue_name,profile_image,venue_manager_name,alt_manager_name,address
            	         ,building_number,street_number,town,city,post_code FROM tbl_venue WHERE id = '".$row['venue_id']."'";
            	         $result_new = $connection->query($statement_new);
            	         $row_new = $result_new->fetch_assoc();
            	         $row['venue_name'] = $row_new['venue_name'];
            	         $row['rating'] = $row_new['rating'];
            	         $row['profile_image'] = BASE_URL.'uploads/venue_images/profile_image/'.$row_new['profile_image'];
            	         $row['venue_manager_name'] = $row_new['venue_manager_name'];
            	         $row['address'] = $row_new['address'];
            	         $row['building_number'] = $row_new['building_number'];
            	         $row['street_number'] = $row_new['street_number'];
            	         $row['town'] = $row_new['town'];
            	         $row['city'] = $row_new['city'];
            	         $row['post_code'] = $row_new['post_code'];
        	        }
        	        else{
        	             $statement_new = "SELECT rating,manager_name,profile_image,
        	             building_number,street_number,town,city,post_code FROM tbl_guest WHERE id = '".$row['guest_id']."'";
            	         $result_new = $connection->query($statement_new);
            	         $row_new = $result_new->fetch_assoc();
            	         $row['guest_name'] = $row_new['manager_name'];
            	         $row['rating'] = $row_new['rating'];
            	         $row['profile_image'] = BASE_URL.'uploads/guest_images/profile_image/'.$row_new['profile_image'];
                         $row['address'] = $row['address'];
            	         $row['building_number'] = $row['building_number'];
            	         $row['street_number'] = $row['street_address'];
            	         $row['town'] = $row['town'];
            	         $row['city'] = $row['city'];
            	         $row['post_code'] = $row['post_code'];
        	        }
        	         
        	         $a[] = $row;
        	    }
        	    $respon = ['success' => true, 'message' => 'Data successfully retrieved','data'=>$a];
        	}
        	
        	else{
        	    $respon = ['success' => false, 'message' => 'No data found'];
        	}
        }
        else{
              $respon = ['success'=> false,'message'=> 'Authorization refused'];
        }
    	
          return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
      });


/********************************************************************************/


/********************************************************************************/

    $app->post('/artist/update_profile', function ($request,$response, $args){
            $data = $request->getParsedBody();
        	$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
           	$connection = connect_db();
            $connection->query("SET NAMES 'utf8'");
            $artist_name = sanitizeString($data['artist_name']);
            $artist_email = sanitizeString($data['artist_email']);
            $artist_bio = sanitizeString(ucwords($data['artist_bio']));
            $artist_contact = sanitizeString($data['artist_contact']);
            $category_id = sanitizeString(ucwords($data['category_id']));
            $category_name = sanitizeString(ucwords($data['category_name']));
            $artist_age= sanitizeString($data['artist_age']);
            $artist_price = sanitizeString($data['artist_price']);
            $artist_travel = sanitizeString($data['artist_travel']);
            $artist_equipment = sanitizeString($data['artist_equipment']);
            $ip = sanitizeString($request->getServerParam('REMOTE_ADDR'));
            $building_number = sanitizeString($data['building_number']);
            $street_number = sanitizeString($data['street_number']);
            $town = sanitizeString($data['town']);
            $city = sanitizeString($data['city']);
            $post_code = sanitizeString($data['post_code']);
          $files = $request->getUploadedFiles();
          $directory  =   dirname(__FILE__).'/../../uploads/artist_profile/';
          if($udata){
            $statement_artist = "SELECT token,profile_image,user_name FROM tbl_artist where id='".$udata['id']."'";
            $result_artist = $connection->query($statement_artist);
            if($result_artist->num_rows > 0){
                $row = $result_artist->fetch_assoc();
                //print_r($row);die;
                $image_path  =   BASE_URL.'uploads/artist_profile/'.$row['profile_image'];
                $token = $row['token'];
                $user_name= $row['user_name'];
            }
        if(!empty($files)){
            $pic = $files['pic'];
        		if(!empty($pic) && $pic->getError() === UPLOAD_ERR_OK) {
        			$extension = pathinfo($pic->getClientFilename(), PATHINFO_EXTENSION);
        			$filename = substr(hash('sha256', mt_rand() . microtime()), 0, 20).'_'.'pic'.'.'.strtolower($extension);
        			$v = $directory.$filename;
        			$pic->moveTo($v);
        			$profile = '../uploads/images/'.$filename;
        			$profile = basename($filename);
        			if (file_exists($image_path)){
                        unlink($image_path);
                    }
          }
        else{
        	$respon = ["success" => false, 'message' => "Fail to upload file"];

    	    }
        }
                      
            else{
                $profile='';
            }
            $check_mail_statement = $connection->query("SELECT * FROM tbl_artist WHERE artist_email ='$artist_email' and id != '".$udata['id']."'");
            if($check_mail_statement->num_rows > 0){
                $respon = ['success'=>false,'message'=>'Email already exists'];
                  return $response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
            }
            // print_r($check_mail);
            // die;
            if($profile===''){
                $token = generate_token($udata['id'],$user_name,$artist_email);
                $result= $connection->query('UPDATE tbl_artist SET token = "'.$token.'",artist_name = "'.$artist_name.'",artist_email = "'.$artist_email.'",
                artist_contact= "'.$artist_contact.'",artist_cat_id= "'.$category_id.'",artist_category= "'.$category_name.'",
                building_number= "'.$building_number.'",street_number= "'.$street_number.'",town= "'.$town.'",city= "'.$city.'",post_code= "'.$post_code.'",
                artist_age= "'.$artist_age.'",artist_bio= "'.$artist_bio.'",artist_price= "'.$artist_price.'",artist_travel= "'.$artist_travel.'",
                artist_equipment= "'.$artist_equipment.'",ip= "'.$ip.'"  WHERE id= "'.$udata['id'].'"');
            }   
            else{
            $token = generate_token($udata['id'],$user_name,$artist_email);
            $result= $connection->query('UPDATE tbl_artist SET token ="'.$token.'" ,artist_name = "'.$artist_name.'",artist_email = "'.$artist_email.'",
            artist_contact= "'.$artist_contact.'",artist_cat_id= "'.$category_id.'",artist_category= "'.$category_name.'",artist_cat_id= "'.$category_id.'",
            profile_image="'.$profile.'",building_number= "'.$building_number.'",street_number= "'.$street_number.'",town= "'.$town.'",city= "'.$city.'",
            post_code= "'.$post_code.'",artist_age= "'.$artist_age.'",artist_bio= "'.$artist_bio.'",artist_price= "'.$artist_price.'",
            artist_travel= "'.$artist_travel.'",artist_equipment= "'.$artist_equipment.'",ip= "'.$ip.'" WHERE id= "'.$udata['id'].'"');
            }
                if($result == 1){
                    $connection->query('UPDATE tbl_artist_categories SET category_id ="'.$category_id.'",category_name = "'.$category_name.'" WHERE artist_id="'.$udata['id'].'"');
            	   //$connection->query("INSERT INTO tbl_artist_categories(artist_id,category_id,category_name) VALUES('".$udata['id']."','$category_id','$category_name')");
                		   $respon = ['success' => true, 'message' => 'Successfully updated','token'=>$token];
                }
                else{
                            $respon = ['success'=>false,'message'=>'Data could not be inserted'];
                }
            
                      
            }
                else{
                $respon = ['success'=> false,'message'=> 'Authorization refused'];
        }   
            return $response->withStatus(200)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
      });

      
/********************************************************************************/



/********************************************************************************/


    $app->post("/artist/artist_booking_cancel" , function( $request, $response, $args){
      $data = $request->getParsedBody();
      $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);

      $connection = connect_db();
      $connection->query("SET NAMES 'utf8'");
      $booking_id = $data['booking_id'];
      $status = $data['status'];
      $reason_of_cancel = $data['reason_of_cancel'];
      $sbuject = "Booking Canceled";
      $venue_id = getFieldWhere('venue_id','tbl_booking','booking_id',$booking_id);
      $guest_id = getFieldWhere('guest_id','tbl_booking','booking_id',$booking_id);
      $booking_date = getFieldWhere('booking_date','tbl_booking','booking_id',$booking_id);
      $artist_name = getFieldWhere('artist_name','tbl_artist','id',$udata['id']);
      
      $title ="Booking Canceled";
      if($venue_id != ''){
        $venue_name = getFieldWhere('venue_name','tbl_venue','id',$venue_id);
        $venue_device_type = getFieldWhere('device_type','tbl_venue','id',$venue_id);
        $venue_device_id = getFieldWhere('device_id','tbl_venue','id',$venue_id);
        $venue_email = getFieldWhere('email','tbl_venue','id',$venue_id);
        $device_id = $venue_device_id;
        $device_type = $venue_device_type;
        
        if($status==7){
          $body = "Hello $venue_name , Your booking is Canceled from the artist side";
          $booking_id = $booking_id;
          //send_booking_status_email($udata['artist_email'],$artist_name,$venue_name,$booking_date,$sbuject,$booking_id);
          send_booking_cancellation_email($venue_email,$venue_name,$artist_name,$booking_date,$sbuject,$booking_id);
        }
      }
      
      if($guest_id != ''){
        $guest_name = getFieldWhere('manager_name','tbl_guest','id',$guest_id);
        $guest_device_type = getFieldWhere('device_type','tbl_guest','id',$guest_id);
        $guest_device_id = getFieldWhere('device_id','tbl_guest','id',$guest_id);
        $guest_email = getFieldWhere('email','tbl_guest','id',$guest_id);
        $device_id = $guest_device_id;
        $device_type = $guest_device_type;
              
        if($status==7){
            $body = "Hello $guest_name , Your booking is Canceled from the artist side";  
          //send_booking_status_email($udata['artist_email'],$artist_name,$guest_name,$booking_date,$sbuject,$booking_id);
          send_booking_cancellation_email($guest_email,$guest_name,$artist_name,$booking_date,$sbuject,$booking_id);
        }
      }
      if($udata){
          if($status){
            $statement = 'UPDATE tbl_booking SET status = "'.$status.'",reason_of_cancel="'.$reason_of_cancel.'" WHERE booking_id = "'.$booking_id.'" ';
            // $statement = "UPDATE tbl_booking SET status = '".$status."',reason_of_cancel='".$reason_of_cancel."' WHERE booking_id = '".$booking_id."' ';
            $result = $connection->query($statement);
            if($result){
                $respon = ['success'=> true,'message'=> 'Data successfully inserted'];
                booking_canceled_nofication($device_id,$device_type,$reason_of_cancel,$title,$body,$booking_id); 
            }
            else{
                $respon = ['success'=> false,'message'=> 'Some error occured'];
            }
          }
      }
      else{
            $respon = ['success'=> false,'message'=> 'Authorization refused'];
      }
    
     return $response->withStatus(200)
     ->withHeader("Content-Type", "application/json")
     ->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
    });


/********************************************************************************/


/********************************************************************************/
  $app->post("/artist/check_device_id", function( $request, $response, $args){
          $data = $request->getParsedBody();
          $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
          $connection = connect_db();
          $connection->query("SET NAMES 'utf8'");
          $device_id = $data['device_id'];
          if($udata){
            $statement = "SELECT * FROM tbl_artist WHERE id= '".$udata['id']."'";
            $result = $connection->query($statement);
            if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                if($row['device_id'] === $device_id){
                    $respon = ['success' => true, 'message' => 'You can proceed'];
                }
                else{
        	       $respon = ['success' => false, 'message' => 'Session Expired']; 
        	    }
            }
          }
          else{
            $respon = ['success'=> false,'message'=> 'Authorization refused'];
           }
        
        return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
  });

/********************************************************************************/

/********************************************************************************/
        $app->post("/artist/chat_listing_notification", function( $request, $response, $args){
            $data = $request->getParsedBody();
            //$udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
            $connection = connect_db();
            $artist_id = $data['artist_id'];
            $venue_id =  $data['venue_id'];
            $guest_id =  $data['guest_id'];
            $sent_to =  $data['sent_to'];
            $message =  $data['message'];
            $booking_id =  $data['booking_id'];
            $message =  $data['message'];
            $name =  $data['name'];
            //$result = array();
            
            if($sent_to == '1'){
             $artist_device_id = getFieldWhere('device_id','tbl_artist','id',$artist_id);
             $artist_device_type = getFieldWhere('device_type','tbl_artist','id',$artist_id);
             if($artist_device_type == 1){
                $send_notification = push_notification_android_chat($artist_device_id,$name,$message,$booking_id,$artist_id,$venue_id,$guest_id);
              }
              else{
                $send_notification = push_notification_ios_chat($artist_device_id,$name,$message,$booking_id,$artist_id,$venue_id,$guest_id);  
              }
            }
            else if($sent_to == '2'){
              $venue_device_id = getFieldWhere('device_id','tbl_venue','id',$venue_id);
               $venue_device_type = getFieldWhere('device_type','tbl_venue','id',$venue_id);
              if($venue_device_type == 1){
                $send_notification = push_notification_android_chat($venue_device_id,$name,$message,$booking_id,$artist_id,$venue_id,$guest_id);
              }
              else{
                $send_notification = push_notification_ios_chat($venue_device_id,$name,$message,$booking_id,$artist_id,$venue_id,$guest_id);  
              }
              
            }
            else if($sent_to == '3'){
              $guest_device_id = getFieldWhere('device_id','tbl_guest','id',$guest_id);
              $guest_device_type = getFieldWhere('device_type','tbl_guest','id',$guest_id);
              
              if($guest_device_type == 1){
                $send_notification = push_notification_android_chat($guest_device_id,$name,$message,$booking_id,$artist_id,$venue_id,$guest_id);
              }
              else{
                $send_notification = push_notification_ios_chat($guest_device_id,$name,$message,$booking_id,$artist_id,$venue_id,$guest_id);  
              }
            }
            
            if($send_notification){
                   $respon = ['success' => true, 'message' => 'Successfully Sent'];
            }
            else{
            	    $respon = ['success' => false, 'message' => 'Failed to sent notification'];
            }
            
            return $response->withStatus(200)
        	->withHeader("Content-Type", "application/json")
        	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
      });

/********************************************************************************/


/********************************************************************************/
  $app->post("/artist/logout", function( $request, $response, $args){
          $data = $request->getParsedBody();
          $udata = validate_artist('tbl_artist',$request->getHeaders("Authorization")['HTTP_AUTHORIZATION'][0]);
          $connection = connect_db();
          $connection->query("SET NAMES 'utf8'");
          if($udata){
            $statement = "UPDATE tbl_artist SET device_id ='NULL' WHERE id = '".$udata['id']."'";
            $result = $connection->query($statement);
            if($result){
               $respon = ['success' => true, 'message' => 'Successfully logout'];
            }
            else{
        	    $respon = ['success' => false, 'message' => 'No data found'];
        	}
          }
          else{
            $respon = ['success'=> false,'message'=> 'Authorization refused'];
           }
        
        return $response->withStatus(200)
    	->withHeader("Content-Type", "application/json")
    	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
  });

/********************************************************************************/

$app->post("/artist/saveappleSignIndata",function($request, $response, $args){
    $data = $request->getParsedBody();
    $connection = connect_db();
    $connection->query("SET NAMES 'utf8'");
    $user_id = $data['userId'];
    $query = "select * from tbl_apple_signin where userid = '$user_id'";
    $result = $connection->query($query);
    if($result->num_rows > 0){
        $statement = "update tbl_apple_signin set email = '".$data['email']."'";
        $update = $connection->query($statement);
        $respon = ['success' => true, 'message' => 'Data updated successfully'];
    } else {
        $statement = "insert into tbl_apple_signin set email = '".$data['email']."', userid = '".$user_id."'";
        $save = $connection->query($statement);
        $respon = ['success' => true, 'message' => 'Data saved successfully'];
    }
    
    return $response->withStatus(200)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
});

/********************************************************************************/

$app->post("/artist/getAppleSignInData",function($request, $response, $args){
    $data = $request->getParsedBody();
    $connection = connect_db();
    $connection->query("SET NAMES 'utf8'");
    $user_id = $data['userId'];
    $query = "select * from tbl_apple_signin where userid = '$user_id'";
    $result = $connection->query($query);
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $respon = ['success' => true, 'message' => 'Data successfully retrieved','data'=>$row];
    } else {
        $respon = ['success' => false, 'message' => 'No data found'];
    }
    return $response->withStatus(200)
	->withHeader("Content-Type", "application/json")
	->write(json_encode($respon, JSON_UNESCAPED_SLASHES));
});
    
?>