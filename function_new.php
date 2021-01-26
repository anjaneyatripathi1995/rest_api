<?php
 use Firebase\JWT\JWT;
 use Tuupola\Base62;
 require("PHPMailer/PHPMailerAutoload.php");
 use Square\SquareClient;
use Square\LocationsApi;
use Square\Exceptions\ApiException;
use Square\Http\ApiResponse;
use Square\Models\Money;
use Square\Environment;
use Square\Models\CreatePaymentRequest;
 date_default_timezone_set('Asia/Kolkata');
 
    function sanitizeString($var){
        $connection = connect_db();
        $var = trim($var);
        //$var = strip_tags($var);
        //$var = htmlentities($var);
        //$var = stripslashes($var);
        $var = $connection->real_escape_string($var);
        return $var;
    }

    //--------------Password Hashing-----------------
    function hashedPassword($plainPassword){
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }
    
    function verifyHashedPassword($plainPassword, $hashedPassword){
        return password_verify($plainPassword, $hashedPassword) ? true : false;
    }

    //-----------------------------------------------------
    function make_slug_of_string($string){
         $slug = preg_replace('/[^a-z0-9-]+/', '-', trim(strtolower($string)));
         return $slug;
    }

    //--------functions for jwt--------------------------------
    function generate_token($user_id, $user_name, $user_email){
        $now = new DateTime();
        //$future = new DateTime("now +2 hours");
        $future = new DateTime("+43200 minutes");
        $jti = (new Base62)->encode(random_bytes(16));
        $secret = "@3524#$%4654ythfDG$%^&*Sn*(41t)";
        
            $payload = [
             "iat" => $now->getTimeStamp(),
             "exp" => $future->getTimeStamp(),
             "jti" => $jti,
             'data' => [
              'userId'   => $user_id,
              'Name' => $user_name,
              'Email' => $user_email,
            ] ];
            $token = JWT::encode($payload, $secret, "HS256");
            return $token;
        }

        function decode_token($data) {
         $data = trim(str_replace('Bearer', '', $data));
         return JWT::decode($data, "@3524#$%4654ythfDG$%^&*Sn*(41t)", array('HS256'));
        }


        function hashedString() {
         $sal = '#*c5c^d9!@fz';
         return hash("sha512", uniqid(rand(), true) . $sal);
        }

    //-------------resize image-----------------------------
    function fn_resize_image($image_resource_id,$width,$height, $type) {
           $target_width =400;
           $target_height =($height/$width)*$target_width;
           $target_layer=imagecreatetruecolor($target_width,$target_height);
           if($type==='png' || $type==='gif'){
             $background = imagecolorallocate($target_layer , 0, 0, 0);
             imagecolortransparent($target_layer, $background);
             imagealphablending($target_layer, false);
             imagesavealpha($target_layer, true);
    } 
    imagecopyresampled($target_layer,$image_resource_id,0,0,0,0,$target_width,$target_height, $width,$height);
    
    return $target_layer;
    }
    
   //--------------------------Signup sign-in section-------------------

    function validate_artist($tbl_artist,$token){
      $decoded = decode_token($token);
      $connection = connect_db();
      $result = $connection->query("SELECT id, artist_email FROM tbl_artist WHERE id = '".$decoded->data->userId."' AND artist_email = '".$decoded->data->Email."' ");
      $row = $result->fetch_assoc();
    if($row){
       return $row;
    }
    else {
      return false;
     }
     $connection = null;
    }

    function validate_venue($tbl_venue,$token){
      $decoded = decode_token($token);
      $connection = connect_db();
      $result = $connection->query("SELECT id, email FROM tbl_venue WHERE id = '".$decoded->data->userId."' AND email = '".$decoded->data->Email."' ");
      $row = $result->fetch_assoc();
      if($row){
        return $row;
      }
      else {
        return false;
      }
      $connection = null;
    }

    function validate_guest($tblable,$token){
      $decoded = decode_token($token);
      $connection = connect_db();
      $result = $connection->query("SELECT id, email FROM tbl_guest WHERE id = '".$decoded->data->userId."' AND email = '".$decoded->data->Email."' ");
      $row = $result->fetch_assoc();
      if($row){
        return $row;
      }
      else {
        return false;
      }
      $connection = null;
    }

    //--------------------Encode Decode function--------------

    function encrypt_decrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'sdrfg3246576@#%$&$^%gfdf';
        $secret_iv = 'sdrfg3246576@#%$&$^%gfdfsdfgh43567';
        $key = hash('sha256', $secret_key);
        
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
        	$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        	$output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
        	$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }
   //-----------------------Forget Password Email--------------------------

    function randomPassword() {
      $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
      $pass = array(); //remember to declare $pass as an array
      $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
      for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
        }
     return implode($pass); //turn the array into a string
    }
   
    function send_sginup_greeting_mail($mailID){
        $msg= '<table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;width:100%" width="100%">
                   <tbody>
                      <tr>
                         <td align="center" bgcolor="#F2F2F2" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0" valign="top">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width: 90%;">
                               <tbody>
                                  <tr>
                                     <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px;padding-bottom:25px" valign="top">
                                        <div style="display:none;overflow:hidden;opacity:0;font-size:1px;line-height:1px;height:0;max-height:0;max-width:0;color:#f2f2f2">Thank you for registering</div>
                                        <a href="'.BASE_URL.'" style="text-decoration:none"><img alt="Logo" border="0" hspace="0" src="'.BASE_URL.'admin/assets/admin/images/logo.png" style="color:#808080;font-size:10px;margin:0;padding:0;outline:none;text-decoration:none;border:none;display:block" title="Infothrive Logo" vspace="0" width="200">
                                        </a>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width: 90%;">
                               <tbody>
                                  <tr>
                                     <td style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:100%;font-size:15px;font-weight:400;line-height:0;padding-top:25px;padding-bottom:0px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top" align="left">
                                        <h3>Dear User,</h3>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" bgcolor="#FFFFFF" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-top:5px;padding-bottom: 30px;padding-left: 40px;" valign="top">
                                        <div style="padding:10px 0 10px 0;margin:0">
                                           <a href="javscript:void(0)">
                                              <div style="width: 100%;line-height:27px;">
                                                <span style="color:#000;font-family:\'Open Sans\',sans-serif;font-size: 14px;font-weight: 400;letter-spacing: 0.5px;font-weight:400">
                                                <strong>Thank you for registering and creating your BookMe profile (You’re in safe hands and we’re really looking forward to working with you).</strong><br><br> 
                                                <strong>Who are we?</strong><br><br>
                                                BookMe is a “Digital Dating Agent” for performing artists & venues across the world. 
                                                We’re harnessing the power of AI, analytics and automation to bring more artists to more venues than ever thought possible. <br><br> 
                                                Supporting performing artists to create & maintain Facebook style profiles for the consumption of date & genre search engines will ensure venues are selecting appropriate & desirable artists every time.<br><br>
                                                We’re investing in big resources to bring together brilliant minds, talented designers and leading industry insiders to deliver the most advanced booking platform in the world today.<br><br>
                                                We’re building the biggest & best community of performing artists on Earth, and we’re made up to hear that you’ve joined us.  Others may follow, but this is where it’s starting….<br><br><br>
                                                
                                                James & Woody<br>
                                                Co-Creators @ BookMe.</span>
                                              </div>
                                           </a>
                                        </div>
                                        <div style="clear:both"></div>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table border="0" cellpadding="0" cellspacing="0" align="center" width="600" style="border-collapse:collapse;border-spacing:0;padding:0;padding-top:25px;width:inherit;max-width:600px">
                               <tbody>
                                  <tr>
                                     <td align="center" valign="top" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px"></td>
                                  </tr>
                                  <tr></tr>
                               </tbody>
                            </table>
                         </td>
                      </tr>
                   </tbody>
                </table>';
        $mail=new PHPMailer;
        //$mail->IsSMTP();
        $mail->SMTPAuth=true; // enable SMTP authentication
        $mail->SMTPSecure="ssl";
        $mail->Host="smtp.gmail.com";
        $mail->Port= 465; // set the SMTP port
        $mail->Username="smtp.frcoder@gmail.com";
        $mail->Password="frcoder6581";
        $mail->From="neharika@frcoder.com";
        $mail->FromName="BOOKME";
        $mail->AddAddress($mailID);
        $mail->Subject= 'Thank you for registering and creating your BookMe profile';
        $mail->Body = $msg;
        $mail->AltBody = $msg;
        if(!$mail->Send()) {
          return false;
        }
        else {
		 return true;
       }
    }

    function send_forget_password_mail($mailID, $user_name, $subject, $password, $altBody){
        $msg= '<table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;width:100%" width="100%">
                   <tbody>
                      <tr>
                         <td align="center" bgcolor="#F2F2F2" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0" valign="top">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                               <tbody>
                                  <tr>
                                     <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px;padding-bottom:25px" valign="top">
                                        <div style="display:none;overflow:hidden;opacity:0;font-size:1px;line-height:1px;height:0;max-height:0;max-width:0;color:#f2f2f2"> (Bookme) Change your Password</div>
                                        <a href="'.BASE_URL.'" style="text-decoration:none"><img alt="Logo" border="0" hspace="0" src="'.BASE_URL.'admin/assets/admin/images/logo.png" style="color:#808080;font-size:10px;margin:0;padding:0;outline:none;text-decoration:none;border:none;display:block" title="Infothrive Logo" vspace="0" width="200">
                                        </a>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                               <tbody>
                                  <tr>
                                     <td style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:87.5%;font-size:15px;font-weight:400;line-height:180%;padding-top:25px;padding-bottom:0px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top" align="left">
                                        <h3>Dear User,</h3>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 87.5%;font-size: 15px;font-weight: 400;line-height: 115%;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>We\'ve received a request to Change the password for your Bookme account.</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 100%;font-size: 15px;font-weight: 400;line-height:180%;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>This is a temporary password you can change it after login with this password.</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="center" bgcolor="#FFFFFF" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-top:5px" valign="top">
                                        <div style="padding:10px 0 10px 0;margin:0 auto">
                                          <div style="width:220px;display:inline-block;margin:5px auto 0 auto;border-radius:5px;font:17px #ffff;text-align:center;line-height:40px">
                                             <span style="text-decoration:none;color:#fff;background:#ff3600f0;display:block;padding-left:2px;padding-right:2px;font-family:\'Open Sans\',sans-serif;font-size:15px;font-weight:700">'.$password.'</span>
                                          </div>
                                        </div>
                                        <div style="clear:both"></div>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:87.5%;font-size:15px;font-weight:400;line-height:180%;text-align:center;padding-top:25px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        </p>If you did not make this request, you may safely ignore this message. You can login with this password and change it according to you.</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="center" bgcolor="#FFFFFF" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-top:5px" valign="top">
                                        <span> <font color="#888888"> </font> 
                                        </span>
                                        <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                                           <tbody>
                                              <tr>
                                                 <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:15px" valign="top">
                                                    <hr align="center" noshade="" color="#f2f2f2" size="2" style="margin:10px 0;padding:0" width="100%">
                                                    <span> <font color="#888888"> </font> </span>
                                                 </td>
                                              </tr>
                                           </tbody>
                                        </table>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table border="0" cellpadding="0" cellspacing="0" align="center" width="600" style="border-collapse:collapse;border-spacing:0;padding:0;padding-top:25px;width:inherit;max-width:600px">
                               <tbody>
                                  <tr>
                                     <td align="center" valign="top" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px"></td>
                                  </tr>
                                  <tr></tr>
                               </tbody>
                            </table>
                         </td>
                      </tr>
                   </tbody>
                </table>';
        $mail=new PHPMailer;
        //$mail->IsSMTP();
        $mail->SMTPAuth=true; // enable SMTP authentication
        $mail->SMTPSecure="ssl";
        $mail->Host="smtp.gmail.com";
        $mail->Port= 465; // set the SMTP port
        $mail->Username="smtp.frcoder@gmail.com";
        $mail->Password="frcoder6581";
        $mail->From="neharika@frcoder.com";
        $mail->FromName="BookMe";
        $mail->AddAddress($mailID);
        $mail->Subject= $subject;
        $mail->Body = $msg;
        $mail->AltBody = $altBody;
        if(!$mail->Send()) {
          return false;
        }
        else {
		 return true;
       }
    }
    
    function send_forget_username_mail($mailID, $user_name, $subject, $username, $altBody){
        $msg= '<table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;width:100%" width="100%">
                   <tbody>
                      <tr>
                         <td align="center" bgcolor="#F2F2F2" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0" valign="top">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                               <tbody>
                                  <tr>
                                     <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px;padding-bottom:25px" valign="top">
                                        <div style="display:none;overflow:hidden;opacity:0;font-size:1px;line-height:1px;height:0;max-height:0;max-width:0;color:#f2f2f2"> (Bookme) Your Username</div>
                                        <a href="'.BASE_URL.'" style="text-decoration:none"><img alt="Logo" border="0" hspace="0" src="'.BASE_URL.'admin/assets/admin/images/logo.png" style="color:#808080;font-size:10px;margin:0;padding:0;outline:none;text-decoration:none;border:none;display:block" title="Infothrive Logo" vspace="0" width="200">
                                        </a>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                               <tbody>
                                  <tr>
                                     <td style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:87.5%;font-size:15px;font-weight:400;line-height:180%;padding-top:25px;padding-bottom:0px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top" align="left">
                                        <h3>Dear User,</h3>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 87.5%;font-size: 15px;font-weight: 400;line-height: 180%;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>We\'ve received a request for your Username of  Bookme account.</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 100%;font-size: 15px;font-weight: 400;line-height:180%;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>This is a your your name mentioned below you can login with this Username:-</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="center" bgcolor="#FFFFFF" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-top:5px" valign="top">
                                        <div style="padding:10px 0 10px 0;margin:0 auto">
                                           <a style="padding-top:25px;margin-right:20px;text-decoration:none" href="javscript:void(0)">
                                              <div style="width:220px;display:inline-block;margin:5px auto 0 auto;border-radius:5px;font:17px #ffff;text-align:center;line-height:40px">
                                                <span style="text-decoration:none;color:#000;background:#f2f2f2;border-radius: 5px;display:block;padding-left:2px;padding-right:2px;font-family:\'Open Sans\',sans-serif;font-size:15px;font-weight:700">'.$username.'</span>
                                              </div>
                                           </a>
                                        </div>
                                        <div style="clear:both"></div>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:87.5%;font-size:15px;font-weight:400;line-height:180%;text-align:center;padding-top:25px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        </p>If you did not make this request, you may safely ignore this message.Your account is safe...</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="center" bgcolor="#FFFFFF" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-top:5px" valign="top">
                                        <span> <font color="#888888"> </font> 
                                        </span>
                                        <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                                           <tbody>
                                              <tr>
                                                 <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:15px" valign="top">
                                                    <hr align="center" noshade="" color="#f2f2f2" size="2" style="margin:10px 0;padding:0" width="100%">
                                                    <span> <font color="#888888"> </font> </span>
                                                 </td>
                                              </tr>
                                           </tbody>
                                        </table>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table border="0" cellpadding="0" cellspacing="0" align="center" width="600" style="border-collapse:collapse;border-spacing:0;padding:0;padding-top:25px;width:inherit;max-width:600px">
                               <tbody>
                                  <tr>
                                     <td align="center" valign="top" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px"></td>
                                  </tr>
                                  <tr></tr>
                               </tbody>
                            </table>
                         </td>
                      </tr>
                   </tbody>
                </table>';
        $mail=new PHPMailer;
        //$mail->IsSMTP();
        $mail->SMTPAuth=true; // enable SMTP authentication
        $mail->SMTPSecure="ssl";
        $mail->Host="smtp.gmail.com";
        $mail->Port= 465; // set the SMTP port
        $mail->Username="smtp.frcoder@gmail.com";
        $mail->Password="frcoder6581";
        $mail->From="neharika@frcoder.com";
        $mail->FromName="BOOKME";
        $mail->AddAddress($mailID);
        $mail->Subject= $subject;
        $mail->Body = $msg;
        $mail->AltBody = $altBody;
        if(!$mail->Send()) {
          return false;
        }
        else {
		 return true;
       }
    }
    function send_booking_status_email($mailID,$name1,$name2,$booking_date,$subject,$booking_id){
        // $mail_message  = 'Dear ';
        // $mail_message .= "\n";
        // $mail_message .= $name1 ;
        // $mail_message .= "\n";
        // $mail_message .= ' your booking request for ';
        // $mail_message .=  $booking_date ;
        // $mail_message .= ' has been sent to ';
        // $mail_message .= $name2;
        // $mail_message .= ' with the Booking id ';
        // $mail_message .= $booking_id ;
        $msg= '<table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;width:100%" width="100%">
                   <tbody>
                      <tr>
                         <td align="center" bgcolor="#F2F2F2" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0" valign="top">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                               <tbody>
                                  <tr>
                                     <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px;padding-bottom:25px" valign="top">
                                        <div style="display:none;overflow:hidden;opacity:0;font-size:1px;line-height:1px;height:0;max-height:0;max-width:0;color:#f2f2f2"> (Bookme) Booking Status</div>
                                        <a href="'.BASE_URL.'" style="text-decoration:none"><img alt="Logo" border="0" hspace="0" src="'.BASE_URL.'admin/assets/admin/images/logo.png" style="color:#808080;font-size:10px;margin:0;padding:0;outline:none;text-decoration:none;border:none;display:block" title="Infothrive Logo" vspace="0" width="200">
                                        </a>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                               <tbody>
                                  <tr>
                                     <td style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:87.5%;font-size:15px;font-weight:400;line-height:180%;padding-top:25px;padding-bottom:0px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top" align="left">
                                        <h3>Dear '.$name1.',</h3>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 87.5%;font-size: 15px;font-weight: 400;line-height: 180%;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>Your booking request for '.$booking_date.'.</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 100%;font-size: 15px;font-weight: 400;line-height:180%;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>has been sent to '.$name2.'</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 100%;font-size: 15px;font-weight: 400;line-height:180%;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>with the Booking id '.$booking_id.'</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="center" bgcolor="#FFFFFF" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-top:5px" valign="top">
                                        <span> <font color="#888888"> </font> 
                                        </span>
                                        <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                                           <tbody>
                                              <tr>
                                                 <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:15px" valign="top">
                                                    <hr align="center" noshade="" color="#f2f2f2" size="2" style="margin:10px 0;padding:0" width="100%">
                                                    <span> <font color="#888888"> </font> </span>
                                                 </td>
                                              </tr>
                                           </tbody>
                                        </table>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table border="0" cellpadding="0" cellspacing="0" align="center" width="600" style="border-collapse:collapse;border-spacing:0;padding:0;padding-top:25px;width:inherit;max-width:600px">
                               <tbody>
                                  <tr>
                                     <td align="center" valign="top" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px"></td>
                                  </tr>
                                  <tr></tr>
                               </tbody>
                            </table>
                         </td>
                      </tr>
                   </tbody>
                </table>';
        $mail=new PHPMailer;
        //$mail->IsSMTP();
        $mail->SMTPAuth=true; // enable SMTP authentication
        $mail->SMTPSecure="ssl";
        $mail->Host="smtp.gmail.com";
        $mail->Port= 465; // set the SMTP port
        $mail->Username="smtp.frcoder@gmail.com";
        $mail->Password="frcoder6581";
        $mail->From="neharika@frcoder.com";
        $mail->FromName="BOOKME";
        $mail->AddAddress($mailID);
        $mail->Subject= $subject;
        $mail->Body = $msg;
        $mail->AltBody = $subject;
        $mail->SMTPDebug = 0;
        if(!$mail->Send()) {
          return false;
        }
        else {
		 return true;
       }
    }
    
    function send_booking_cancellation_email($mailID,$name1,$name2,$booking_date,$subject,$booking_id){
        // $mail_message  = 'Dear ';
        // $mail_message .= $name1 ;
        // $mail_message .= ' your booking request for ';
        // $mail_message .=  $booking_date ;
        // $mail_message .= ' has been sent to ';
        // $mail_message .= $name2;
        // $mail_message .= ' with the Booking id ';
        // $mail_message .= $booking_id ;
        // $mail_message .= ' and is awaiting a quote. ';
        $msg= '<table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;width:100%" width="100%">
                   <tbody>
                      <tr>
                         <td align="center" bgcolor="#F2F2F2" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0" valign="top">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                               <tbody>
                                  <tr>
                                     <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px;padding-bottom:25px" valign="top">
                                        <div style="display:none;overflow:hidden;opacity:0;font-size:1px;line-height:1px;height:0;max-height:0;max-width:0;color:#f2f2f2"> (Bookme) Your Username</div>
                                        <a href="'.BASE_URL.'" style="text-decoration:none"><img alt="Logo" border="0" hspace="0" src="'.BASE_URL.'admin/assets/admin/images/logo.png" style="color:#808080;font-size:10px;margin:0;padding:0;outline:none;text-decoration:none;border:none;display:block" title="Infothrive Logo" vspace="0" width="200">
                                        </a>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                               <tbody>
                                  <tr>
                                     <td style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:87.5%;font-size:15px;font-weight:400;line-height:180%;padding-top:25px;padding-bottom:0px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top" align="left">
                                        <h3>Dear '.$name1.',</h3>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 87.5%;font-size: 15px;font-weight: 400;line-height: 180%;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>Your booking request for date '.$booking_date.'</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 100%;font-size: 15px;font-weight: 400;line-height:180%;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>has been sent to:- '.$name2.'</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 100%;font-size: 15px;font-weight: 400;line-height:180%;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>with the Booking id:- '.$booking_id.'</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:87.5%;font-size:15px;font-weight:400;line-height:180%;text-align:center;padding-top:25px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        </p>and is awaiting a quote...</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="center" bgcolor="#FFFFFF" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-top:5px" valign="top">
                                        <span> <font color="#888888"> </font> 
                                        </span>
                                        <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                                           <tbody>
                                              <tr>
                                                 <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:15px" valign="top">
                                                    <hr align="center" noshade="" color="#f2f2f2" size="2" style="margin:10px 0;padding:0" width="100%">
                                                    <span> <font color="#888888"> </font> </span>
                                                 </td>
                                              </tr>
                                           </tbody>
                                        </table>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table border="0" cellpadding="0" cellspacing="0" align="center" width="600" style="border-collapse:collapse;border-spacing:0;padding:0;padding-top:25px;width:inherit;max-width:600px">
                               <tbody>
                                  <tr>
                                     <td align="center" valign="top" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px"></td>
                                  </tr>
                                  <tr></tr>
                               </tbody>
                            </table>
                         </td>
                      </tr>
                   </tbody>
                </table>';
                
        $mail=new PHPMailer;
        //$mail->IsSMTP();
        $mail->SMTPAuth=true; // enable SMTP authentication
        $mail->SMTPSecure="ssl";
        $mail->Host="smtp.gmail.com";
        $mail->Port= 465; // set the SMTP port
        $mail->Username="smtp.frcoder@gmail.com";
        $mail->Password="frcoder6581";
        $mail->From="neharika@frcoder.com";
        $mail->FromName="BOOKME";
        $mail->AddAddress($mailID);
        $mail->Subject= $subject;
        $mail->Body = $msg;
        $mail->AltBody = $subject;
        $mail->SMTPDebug = 0;
        if(!$mail->Send()) {
          return false;
        }
        else {
		 return true;
       }
    }
    
    function send_email_admin_issue_with_artist($artist_name,$artist_email,$artist_contact,$venue_name,$venue_email,$venue_contact,$issue){
        $msg= '<table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;width:100%" width="100%">
                   <tbody>
                      <tr>
                         <td align="center" bgcolor="#F2F2F2" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0" valign="top">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:100%;max-width:600px" width="600">
                               <tbody>
                                  <tr>
                                     <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px;padding-bottom:25px" valign="top">
                                        <div style="display:none;overflow:hidden;opacity:0;font-size:1px;line-height:1px;height:0;max-height:0;max-width:0;color:#f2f2f2"> (Bookme) Issue With Artist</div>
                                        <a href="'.BASE_URL.'" style="text-decoration:none"><img alt="Logo" border="0" hspace="0" src="'.BASE_URL.'admin/assets/admin/images/logo.png" style="color:#808080;font-size:10px;margin:0;padding:0;outline:none;text-decoration:none;border:none;display:block" title="Infothrive_ogo" vspace="0" width="200">
                                        </a>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:100%;max-width:600px" width="600">
                               <tbody>
                                  <tr>
                                     <td style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:100%;font-size:15px;font-weight:400;line-height:0;padding-top:25px;padding-bottom:0px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top" align="left">
                                        <h3>Dear Admin,</h3>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 100%;font-size: 15px;font-weight: 400;line-height: 0;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>Artist Name :- '.$artist_name.'</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 100%;font-size: 15px;font-weight: 400;line-height:0;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>Artist Email:- '.$artist_email.'</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 100%;font-size: 15px;font-weight: 400;line-height:0;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>Artist Contact:- '.$artist_contact.'</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:100%;font-size:15px;font-weight:400;line-height:0;padding-top:25px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        </p>Venue Name :- '.$venue_name.'</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:100%;font-size:15px;font-weight:400;line-height:0;padding-top:25px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        </p>Venue Email :- '.$venue_email.'</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:100%;font-size:15px;font-weight:400;padding-top:25px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        </p>Venue Contact :- '.$venue_contact.'</p>
                                     </td>
                                  </tr>

                                  <tr>
                                     <td align="left" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:100%;font-size:15px;font-weight:400;padding-top:25px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        </p>Problem :- '.$issue.'</p>
                                     </td>
                                  </tr>

                                  <tr>
                                     <td align="center" bgcolor="#FFFFFF" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-top:5px" valign="top">
                                        <span><font color="#888888"></font> 
                                        </span>
                                        <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:100%;max-width:600px" width="600">
                                           <tbody>
                                              <tr>
                                                 <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:100%;padding-top:15px" valign="top">
                                                    <hr align="center" noshade="" color="#f2f2f2" size="2" style="margin:10px 0;padding:0" width="100%">
                                                    <span> <font color="#888888"> </font> </span>
                                                 </td>
                                              </tr>
                                           </tbody>
                                        </table>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table border="0" cellpadding="0" cellspacing="0" align="center" width="600" style="border-collapse:collapse;border-spacing:0;padding:0;padding-top:25px;width:100%;max-width:600px">
                               <tbody>
                                  <tr>
                                     <td align="center" valign="top" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px"></td>
                                  </tr>
                                  <tr></tr>
                               </tbody>
                            </table>
                         </td>
                      </tr>
                   </tbody>
                </table>';
        $mailID ='anjaneye.frcoder@gmail.com';
        $subject ='Venue have an issue with artist';
        $mail=new PHPMailer;
        //$mail->IsSMTP();
        $mail->SMTPAuth=true; // enable SMTP authentication
        $mail->SMTPSecure="ssl";
        $mail->Host="smtp.gmail.com";
        $mail->Port= 465; // set the SMTP port
        $mail->Username="smtp.frcoder@gmail.com";
        $mail->Password="frcoder6581";
        $mail->From="neharika@frcoder.com";
        $mail->FromName="BOOKME";
        $mail->AddAddress($mailID);
        $mail->Subject= 'Venue Have issue with artist';
        $mail->Body = $msg;
        $mail->AltBody = $subject;
        $mail->SMTPDebug = 0;
        if(!$mail->Send()) {
          return false;
        }
        else {
		 return true;
       }
    }
    function send_email_artist_issue_with_artist($mailID,$issue,$artist_name){
        // $mail_message  = 'Dear ';
        // $mail_message .= $artist_name ;
        // $mail_message .= "\n";
        // $mail_message .= ' there is an issue as mentioned below ';
        // $mail_message .= "\n";
        // $mail_message .=  $issue ;
        // $mail_message .= "\n";
        // $mail_message .= ' and your payment is on hold till issue is not resolved. ';
        // $mail_message .= "\n";
        $msg= '<table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;width:100%" width="100%">
                   <tbody>
                      <tr>
                         <td align="center" bgcolor="#F2F2F2" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0" valign="top">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                               <tbody>
                                  <tr>
                                     <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px;padding-bottom:25px" valign="top">
                                        <div style="display:none;overflow:hidden;opacity:0;font-size:1px;line-height:1px;height:0;max-height:0;max-width:0;color:#f2f2f2"> (Bookme) Issue With Artist</div>
                                        <a href="'.BASE_URL.'" style="text-decoration:none"><img alt="Logo" border="0" hspace="0" src="'.BASE_URL.'admin/assets/admin/images/logo.png" style="color:#808080;font-size:10px;margin:0;padding:0;outline:none;text-decoration:none;border:none;display:block" title="Infothrive Logo" vspace="0" width="200">
                                        </a>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                               <tbody>
                                  <tr>
                                     <td style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:87.5%;font-size:15px;font-weight:400;line-height:180%;padding-top:25px;padding-bottom:0px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top" align="left">
                                        <h3>Dear '.$artist_name.',</h3>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 87.5%;font-size: 15px;font-weight: 400;line-height: 180%;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>there is an issue as mentioned below</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:87.5%;font-size:15px;font-weight:400;line-height:180%;text-align:center;padding-top:25px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        </p>Problem :- '.$issue.'</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="left" style="border-collapse: collapse;border-spacing: 0;margin: 0;padding: 0;padding-left: 4.25%;padding-right: 4.25%;width: 100%;font-size: 15px;font-weight: 400;line-height:180%;padding-top: 0px;padding-bottom: 0;color: #072a5a;font-family:\'Open Sans\',sans-serif" valign="top">
                                        <p>and your payment is on hold till issue is not resolved.</p>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="center" bgcolor="#FFFFFF" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-top:5px" valign="top">
                                        <span> <font color="#888888"> </font> 
                                        </span>
                                        <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width:inherit;max-width:600px" width="600">
                                           <tbody>
                                              <tr>
                                                 <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:15px" valign="top">
                                                    <hr align="center" noshade="" color="#f2f2f2" size="2" style="margin:10px 0;padding:0" width="100%">
                                                    <span> <font color="#888888"> </font> </span>
                                                 </td>
                                              </tr>
                                           </tbody>
                                        </table>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table border="0" cellpadding="0" cellspacing="0" align="center" width="600" style="border-collapse:collapse;border-spacing:0;padding:0;padding-top:25px;width:inherit;max-width:600px">
                               <tbody>
                                  <tr>
                                     <td align="center" valign="top" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px"></td>
                                  </tr>
                                  <tr></tr>
                               </tbody>
                            </table>
                         </td>
                      </tr>
                   </tbody>
                </table>';
        $subject ='There is issue in job completion';
        $mail=new PHPMailer;
        //$mail->IsSMTP();
        $mail->SMTPAuth=true; // enable SMTP authentication
        $mail->SMTPSecure="ssl";
        $mail->Host="smtp.gmail.com";
        $mail->Port= 465; // set the SMTP port
        $mail->Username="smtp.frcoder@gmail.com";
        $mail->Password="frcoder6581";
        $mail->From="neharika@frcoder.com";
        $mail->FromName="BOOKME";
        $mail->AddAddress($mailID);
        $mail->Subject= $subject;
        $mail->Body = $msg;
        $mail->AltBody = $subject;
        $mail->SMTPDebug = 0;
        if(!$mail->Send()) {
          return false;
        }
        else {
		 return true;
       }
    }



    function fetch_status_by_staus_id($status){
        $satus_text = 'NULL';
        if($status == 1){
            $satus_text= 'Quote Send';
        }
        
        if($status == 2){
            $satus_text= 'Accepted';
        }
        
        if($status == 3){
            $satus_text= 'Declined';
        }
        
        if($status == 4){
            $satus_text= 'Completed';
        }
        
        if($status == 5){
            $satus_text= 'Payment Done';
        }
    }
    function update_booking_status($status,$booking_id,$device_type,$device_id,$body,$title){
        $connection = connect_db();
        //$title = fetch_status_by_staus_id($status);
        $statement = "UPDATE tbl_booking SET status = '".$status."' WHERE booking_id = '".$booking_id."' ";
        $result = $connection->query($statement);
        if($result){
            if($device_type == 1){
                push_notification_android_status_update($device_id,$body,$title,$booking_id);
            }
            else{
                push_notification_ios_status_update($device_id,$body,$title,$booking_id);
            }
            
            return true;
        }
        else{
            return false;
        }
    }

    function gig_counter_nofication($device_id,$device_type,$quote_price,$title,$body,$booking_id){
        $connection = connect_db();
        if($device_type == 1){
                push_notification_android_gig_counter($device_id,$quote_price,$title,$body,$booking_id);
        }
        else{
                push_notification_ios_gig_counter($device_id,$quote_price,$title,$body,$booking_id);
        }
    
    }
    
    function booking_canceled_nofication($device_id,$device_type,$reason_of_cancel,$title,$body,$booking_id){
        $connection = connect_db();
        if($device_type == 1){
                push_notification_android_booking_canceled($device_id,$reason_of_cancel,$title,$body,$booking_id);
        }
        else{
                push_notification_ios_booking_canceled($device_id,$reason_of_cancel,$title,$body,$booking_id);
        }
    
    }


    function getFieldWhere($filed,$tbl,$where,$id){
        $connection = connect_db();
        $statement =  "SELECT $filed AS field FROM $tbl WHERE  $where = '".$id."'";
        $result = $connection->query($statement);
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            return (stripslashes($row['field']));   
        }
        else{
            return false;
        }
    }

    function getLatLong($address){
        if(!empty($address)){
            // print_r($address);
            // echo "<br>";
            //Formatted address
            $formattedAddr = str_replace(' ','+',$address);
            //Send request and receive json data by address
            $geocodeFromAddr = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBJFeFF4SPQbu6MNVzdAd4xfQhiGVgy1p4&address='.$formattedAddr.'&sensor=false'); 
            $output = json_decode($geocodeFromAddr);
           
             //Get latitude and longitute from json data
             if($output->status == "OK"){
              $data['latitude']  = $output->results['0']->geometry->location->lat; 
              $data['longitude'] = $output->results['0']->geometry->location->lng;
             }
             else{
              $data['latitude']  = 'NULL'; 
              $data['longitude'] = 'NULL';
             } 
            //Return latitude and longitude of the given address
            if(!empty($data)){
                return  $data;
            }else{
                return  false;
            }
        }
        else{
            return false;   
        }
    }
    
    function calculate_distance($lat1, $lon1, $lat2, $lon2, $unit) {
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
      return 0;
    }
    else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
          return ($miles * 1.609344);
        } else if ($unit == "N") {
          return ($miles * 0.8684);
        } else {
          return $miles;
        }
      }
    }
    
    /*function send_forget_password_mail($mailID, $user_name, $subject, $msg, $altBody){
     require("PHPMailer/PHPMailerAutoload.php");
     $mail = new PHPMailer;
     //Enabl SMTP debugging. 
     $mail->SMTPDebug = 1;                               
     //Set PHPMailer to use SMTP.
     $mail->isSMTP();            
     //Set SMTP host name                          
     $mail->Host = "smtp.gmail.com";
     //Set this to true if SMTP host requires authentication to send email
     $mail->SMTPAuth = true;                          
     //Provide username and password     
     $mail->Username = "smtp.frcoder@gmail.com";                 
     $mail->Password = "frcoder6581";                           
     //If SMTP requires TLS encryption then set it
     $mail->SMTPSecure = "ssl";                           
     //Set TCP port to connect to 
     $mail->Port = 465;                                   
     $mail->From = "neharika@frcoder.com";
     $mail->FromName = "Tinkerby Admin";
     $mail->addAddress($mailID, $user_name);
     $mail->isHTML(true);
     $mail->Subject = $subject;
     $mail->Body = $msg;
     $mail->AltBody = $user_name;
     if(!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
     } 
     else{
            echo "";
      }
    }*/
    
    
    // function base64_to_jpeg($base64_string, $output_file) {
    //     // open the output file for writing
    //     $ifp = fopen( $output_file, 'wb' ); 
    
    //     // split the string on commas
    //     // $data[ 0 ] == "data:image/png;base64"
    //     // $data[ 1 ] == <actual base64 string>
    //     $data = explode( ',', $base64_string );
    
    //     // we could add validation here with ensuring count( $data ) > 1
    //     fwrite( $ifp, base64_decode( $data[ 1 ] ) );
    
    //     // clean up the file resource
    //     fclose( $ifp ); 
    
    //     return $output_file; 
    // }



    /*function login_validate($tbl_artist,$credential, $pwd){
            $connection = connect_db();
            $result = $connection->query("SELECT * FROM tbl_artist WHERE user_name = '$credential' OR mobile = '$credential'");
            $row = $result->fetch_assoc();
            if($row){
             if(verifyHashedPassword($pwd, $row['password'])){
              return $row;
            } else {
             return false;
            }
            } else {
            return false;
            }
    }*/
    
    /*function calculate_distance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo){
      $theta = $longitudeFrom - $longitudeTo;
      $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $dist_num = number_format((float)($miles * 1.609344), 2, '.', '');
      return $dist_num*1000;
    }*/
    /*echo distance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
    echo distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
    echo distance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";*/
    
    function process_square_payment($nonce,$amount){
        $access_token = "EAAAEDhUBWJkqXNP3bHyE0TgqEAdFphGunNWaF5mBiyUpqjbTe92JZRNLDyA7DdU";
    	$client = new SquareClient([
    	  'accessToken' => $access_token,
    	  'environment' => Environment::PRODUCTION,
    	]);
    	$payments_api = $client->getPaymentsApi();
    
    	$money = new Money();
    	$money->setAmount($amount);
    	$money->setCurrency('GBP');
    	$create_payment_request = new CreatePaymentRequest($nonce, uniqid(), $money);
    	try {
    	  $response = $payments_api->createPayment($create_payment_request);
    	  if ($response->isError()) {
    	    $errors = $response->getErrors();
    	    $data['success'] = 0;
    	    $data['message'] = $errors[0]->getDetail();
    	    return $data;
    	    exit();
    	  }
    	  $response = json_decode($response->getBody());
    	  $data['success'] = 1;
    	  $data['response'] = $response->payment;
    	  return $data;
    	} catch (ApiException $e) {
    	  echo 'Caught exception!<br/>';
    	  exit();
    	}
    }