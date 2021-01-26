<?php   include('include/config.php');
        require("src/config/PHPMailer/PHPMailerAutoload.php");
        $today_date = date('Y-m-d');
        //$today_date = '2019-09-13';
        $statement1 = "SELECT count(id) AS total_artist FROM tbl_artist WHERE DATE(added_at) ='$today_date'";
        $result1 = $connection->query($statement1);
        if($result1->num_rows > 0){
            $row1 = $result1->fetch_assoc();
            $total_artist = $row1['total_artist'];
        }
        
        $statement2 ="SELECT count(id) AS total_venue FROM tbl_venue WHERE DATE(added_at) ='$today_date'";
        $result2 = $connection->query($statement2);
        if($result2->num_rows > 0){
            $row2 = $result2->fetch_assoc();
            $total_venue = $row2['total_venue'];
        }
        
        
        $statement3 = "SELECT count(id) AS total_guest FROM tbl_guest WHERE DATE(added_at) ='$today_date'";
        $result3 = $connection->query($statement3);
        if($result3->num_rows > 0){
            $row3 = $result3->fetch_assoc();
            $total_guest = $row3['total_guest'];
        }
  
    $msg= '<table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;width:100%" width="100%">
                   <tbody>
                      <tr>
                         <td align="center" bgcolor="#F2F2F2" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0" valign="top">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width: 400px;max-width:600px">
                               <tbody>
                                  <tr>
                                     <td align="center" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:6.25%;padding-right:6.25%;width:87.5%;padding-top:25px;padding-bottom:25px" valign="top">
                                        <div style="display:none;overflow:hidden;opacity:0;font-size:1px;line-height:1px;height:0;max-height:0;max-width:0;color:#f2f2f2"> (Bookme)Daily Report</div>
                                        <a href="'.BASE_URL.'" style="text-decoration:none"><img alt="Logo" border="0" hspace="0" src="'.BASE_URL.'admin/assets/admin/images/logo.png" style="color:#808080;font-size:10px;margin:0;padding:0;outline:none;text-decoration:none;border:none;display:block" title="Infothrive Logo" vspace="0" width="200">
                                        </a>
                                     </td>
                                  </tr>
                               </tbody>
                            </table>
                            <table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0;padding:0;width: 400px;max-width:600px">
                               <tbody>
                                  <tr>
                                     <td style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-left:4.25%;padding-right:4.25%;width:87.5%;font-size:15px;font-weight:400;line-height:180%;padding-top:25px;padding-bottom:0px;color:#072a5a;font-family:\'Open Sans\',sans-serif" valign="top" align="center">
                                        <h3>Daily Registration Artist,Venue,Guest</h3>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="center" bgcolor="#FFFFFF" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-top:5px" valign="top">
                                        <div style="padding:10px 0 10px 0;margin:0">
                                           <a href="javscript:void(0)">
                                              <div style="width: 100%;line-height:0px">
                                                 <span style="color:#000;font-family:\'Open Sans\',sans-serif;font-size: 17px;font-weight: 700;letter-spacing: 0.5px;font-weight:700">Total Artist :- '.$total_artist.'</span>
                                              </div>
                                           </a>
                                        </div>
                                        <div style="clear:both"></div>
                                     </td>
                                  </tr>
                                  <tr>
                                     <td align="center" bgcolor="#FFFFFF" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-top:5px" valign="top">
                                        <div style="padding:20px 0 10px 0;margin:0">
                                           <a href="javscript:void(0)">
                                              <div style="width: 100%;line-height:0px">
                                                 <span style="color:#000;font-family:\'Open Sans\',sans-serif;font-size: 17px;font-weight: 700;letter-spacing: 0.5px;font-weight:700">Total Venue :- '.$total_venue.'</span>
                                              </div>
                                           </a>
                                        </div>
                                        <div style="clear:both"></div>
                                     </td>
                                  </tr>
                                  
                                  <tr>
                                     <td align="center" bgcolor="#FFFFFF" style="border-collapse:collapse;border-spacing:0;margin:0;padding:0;padding-top:5px" valign="top">
                                        <div style="padding:10px 0 10px 0;margin:0">
                                           <a href="javscript:void(0)">
                                              <div style="width: 100%;padding-bottom:40px">
                                                 <span style="color:#000;font-family:\'Open Sans\',sans-serif;font-size: 17px;font-weight: 700;letter-spacing: 0.5px;font-weight:700">Total Guest :- '.$total_guest.'</span>
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
        $mail->AddAddress('anjaneye.frcoder@gmail.com');
        $mail->Subject= 'Daily Registration Report';
        $mail->Body = $msg;
        $mail->AltBody = $msg;
        if(!$mail->Send()) {
          return false;
        }
        else {
		 return true;
       }
        
?>