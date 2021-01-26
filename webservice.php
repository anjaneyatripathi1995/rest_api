<?php
include ('include/config.php');
include ('include/functions.php');
include ('include/simpleimage.php');

$url = "http://php.frcoder.in/notification/webservice.php";

// ===================Customer APIs Start==============================
if ($_REQUEST['otpVerification'] == 1)
{
    $mode = 'otpVerification';
}

if ($_REQUEST['user_register'] == 1)
{
    $mode = 'user_register';
}

if ($_REQUEST['update_profile'] == 1)
{
    $mode = 'update_profile';
}
if ($_REQUEST['banner_list'] == 1)
{
    $mode = 'banner_list';
}

if ($_REQUEST['category_list'] == 1)
{
    $mode = 'category_list';
}

if ($_REQUEST['check_user'] == 1)
{
    $mode = 'check_user';
}

if ($_REQUEST['notification_list'] == 1)
{
    $mode = 'notification_list';
}

if ($_REQUEST['make_read'])
{
    $mode = 'make-read';
}

if($_REQUEST['make_favourite']){
    $mode = 'make-favourite';
}

if($_REQUEST['get_all_favourite']){
    $mode = 'get-all-favourite';
}

if($_REQUEST['unread_notification_count']){
    $mode = 'unread-notification-count';
}

if($_REQUEST['user_category_list']){
    $mode = 'user-category-list';
}

if ($_REQUEST['update_user_category']) {
	$mode = 'update-user-category';
}

switch ($mode)
{

        ################# OTP Verification Login #####################################
        
    case "otpVerification":

        $phone_number = $obj->escapestring($_REQUEST['phone_number']);
        $otp = SendSMS($phone_number);
        if ($otp)
        {
            $data['success'] = 1;
            $data['otp'] = $otp;
            $data['message'] = "Otp Sent Successfully";
        }
        else
        {
            $data['success'] = 0;
            $data['message'] = "OTP not sent! Sever issue";
        }

        echo json_encode($data);
    break;
        #################### Verification End ############################
        #################### Check User ############################
        
    case "check_user":
        $number = $obj->escapestring($_REQUEST['number']);
        $resultUser = (object)array();
        $uArr = $obj->query("select * from $tbl_user where number='$number'", -1);
        if ($obj->numRows($uArr) > 0)
        {
            $resultUser = $obj->fetchNextObject($uArr);
            $resultUser->image = SITE_URL . '/upload_images/user/thumb/' . $resultUser->image;
            $data['success'] = 1;
            $data['user_details'] = $resultUser;
            $data['message'] = "User already Exist";
        }
        else
        {
            $data['success'] = 0;
            $data['user_details'] = $resultUser;
            $data['message'] = "User Does Not Exist";
        }
        echo json_encode($data);
    break;

        #################### Check User End ############################
        #################### User Registration ############################
        
    case "user_register":
        $number = $obj->escapestring($_REQUEST['number']);
        $gender = $obj->escapestring($_REQUEST['gender']);
        $firstname = $obj->escapestring($_REQUEST['firstname']);
        $lastname = $obj->escapestring($_REQUEST['lastname']);
        $category = $obj->escapestring($_REQUEST['category']);
        $dob = $obj->escapestring($_REQUEST['dob']);
        $email = $obj->escapestring($_REQUEST['email']);
        $address = $obj->escapestring($_REQUEST['address']);
        $device_id = $obj->escapestring($_REQUEST['device_id']);
        $device_type = $obj->escapestring($_REQUEST['device_type']);
        
        $complete_status = $obj->escapestring($_REQUEST['complete_status']);
        $obj->query("insert  into $tbl_user set number='$number',gender='$gender',firstname='$firstname',lastname='$lastname',category='$category',dob='$dob',email='$email',address='$address',complete_status='$complete_status',device_type='$device_type',device_id='$device_id'", -1);
        $last_id = $obj
            ->link->insert_id;
        $uArr = $obj->query("Select * from $tbl_user where id='$last_id'", -1);
        $resultUser = $obj->fetchNextObject($uArr);
        if ($resultUser)
        {
            $resultUser->image = SITE_URL . '/upload_images/user/thumb/' . $resultUser->image;
            $data['success'] = 1;
            $data['user_details'] = $resultUser;
            $data['message'] = "Data Successfully Retrieved";
        }
        else
        {
            $data['success'] = 0;
            $data['message'] = "some error occurred";
        }
        echo json_encode($data);
    break;

        #################### End User Registration #############################
        #################### Update Profile ############################
        
    case "update_profile":

        $number = $obj->escapestring($_REQUEST['number']);
        $gender = $obj->escapestring($_REQUEST['gender']);
        $firstname = $obj->escapestring($_REQUEST['firstname']);
        $lastname = $obj->escapestring($_REQUEST['lastname']);
        $dob = $obj->escapestring($_REQUEST['dob']);
        $email = $obj->escapestring($_REQUEST['email']);
        $address = $obj->escapestring($_REQUEST['address']);
        $id = $obj->escapestring($_REQUEST['id']);
        // print_r($_FILES); die;
        if ($number)
        {
            if ($_FILES['image']['size'] > 0 && $_FILES['image']['error'] == '')
            {
                $img = time() . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], "upload_images/user/thumb/" . $img);
            }
            $queryTest = $obj->query("Update $tbl_user set gender='$gender',firstname='$firstname',lastname='$lastname',dob='$dob',email='$email',address='$address',image='$img', number='$number' where id='$id'", $debug = -1);

            $sql = $obj->query("select * from $tbl_user where id='$id'",$debug=-1);
            if($obj->numRows($sql) > 0){
                $record = $obj->fetchNextObject($sql);
                $record->image = SITE_URL . "/upload_images/user/thumb/" . $img;
                $a[] = $record; 
            }



            $data['success'] = 1;
            $data['data'] = $a;
            $data['message'] = "User Profile updated successfully!";

        }
        else
        {
            $data['success'] = 0;
            $data['message'] = "Please fill required fields !";
        }

        echo json_encode($data);
    break;


        #################### End Update Profile ############################
        ###################### Slider List ################################
        
    case "banner_list";
    $whr = '';

    if ($_REQUEST['category_id'])
    {
        $whr .= ' and cat_id=' . $_REQUEST['category_id'];
    }

    $uArr = $obj->query("select * from $tbl_banner where status=1 " . $whr, $debug = - 1);
    if ($obj->numRows($uArr) > 0)
    {
        while ($resultUser = $obj->fetchNextObject($uArr))
        {

            $data1['id'] = stripslashes($resultUser->id);
            $data1['title'] = stripslashes($resultUser->title);
            if ($resultUser->photo != '')
            {
                $data1['logo'] = SITE_URL . "upload_images/banner/big/" . $resultUser->photo;
            }
            else
            {
                $data1['logo'] = "No Available";
            }
            $data1['status'] = stripslashes($resultUser->status);
            $data['success'] = 1;
            $data['data'][] = $data1;
        }
    }
    else
    {
        $data['success'] = 0;
        $data['message'] = "No record found!";
    }
    echo json_encode($data);
break;

    ######################### End Slider List #################################################
    

    ###################### Category List ################################
    
case "category_list";
// $whr = '';

$user_id = $obj->escapestring($_REQUEST['user_id']);
// if ($_REQUEST['category_id'])
// {
//     $whr .= ' and cat_id=' . $_REQUEST['category_id'];
// }

// $uArr = $obj->query("select * from $tbl_category where status=1 " . $whr, $debug = - 1);

if(!empty($user_id)){
    $uArr = $obj->query("select category from tbl_users where id='$user_id'", $debug = -1);
    $record = $obj->fetchNextObject($uArr);
        $sql = $obj->query("select * from tbl_category where id in ($record->category)",$debug=-1); 
    } 
    else 
    {
        $sql = $obj->query("select * from tbl_category where status =1",$debug=-1);
    }

if ($obj->numRows($sql) > 0)
{
    while ($resultUser = $obj->fetchNextObject($sql))
    {

        $data1['id'] = stripslashes($resultUser->id);
        $data1['category_name'] = stripslashes($resultUser->category_name);
        if ($resultUser->image != '')
        {
            $data1['logo'] = SITE_URL . "upload_images/category/thumb/" . $resultUser->image;
        }
        else
        {
            $data1['logo'] = "No Available";
        }
        $data1['status'] = stripslashes($resultUser->status);
        $data['success'] = 1;
        $data['data'][] = $data1;
    }
}
else
{
    $data['success'] = 0;
    $data['message'] = "No record found!";
}
echo json_encode($data);
break;

    ######################### End Category List #################################################
    
case 'notification_list':

    $cdate = date('d-m-Y');
    // if(!empty($_REQUEST['user_id'])){
    //     $sql = $obj->query("select * from tbl_notification where display_user='1' and start_date >= '$cdate' and user_id= '".$obj->escapestring($_REQUEST['user_id'])."'",$debug=-1);
    // } elseif(!empty($_REQUEST['category'])) {
    //     $sql = $obj->query("select * from tbl_notification where display_user='1' and start_date >= '$cdate' and category_id='".$obj->escapestring($_REQUEST['category'])."'",$debug=-1);
    // } elseif(!empty($_REQUEST['gender'])){
    //     $sql = $obj->query("select * from tbl_notification where display_user='1' and start_date >= '$cdate' and gender='".$obj->escapestring($_REQUEST['gender'])."'",$debug=-1);
    // } elseif(!empty($_REQUEST['group_id'])){
    //     $sql = $obj->query("select * from tbl_notification where display_user='1' and start_date >= '$cdate' and group_id='".$obj->escapestring($_REQUEST['group_id'])."'",$debug=-1);
    // }else {
    // $sql = $obj->query("select * from tbl_notification where display_user='1' and start_date >= '$cdate' and status=1",$debug=-1);
    // }
    if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['category'])){
        $sql = $obj->query("select * from tbl_notification where display_user='1' and user_id= '" . $obj->escapestring($_REQUEST['user_id']) . "' and category_id = '".$obj->escapestring($_REQUEST['category'])."' order by id desc", $debug = - 1);
    } elseif (!empty($_REQUEST['user_id']))
    {
        $get_group = $obj->query("select * from tbl_group where status=1",$debug=-1);
        while ($group_data = $obj->fetchNextObject($get_group)) {    
            $users_id = explode(",",$group_data->user_id);
        if (in_array($_REQUEST['user_id'],$users_id)) {
            $group_id = $group_data->id;
        } 
        }
        $whr = "";
        if($group_id!= '')
        {
            $whr = " and group_id='$group_id'";
        }
        $sql = $obj->query("select * from tbl_notification where display_user='1' and user_id= '" . $obj->escapestring($_REQUEST['user_id']) . "' order by id desc", $debug = -1);
    }
    elseif (!empty($_REQUEST['category']))
    {
        $sql = $obj->query("select * from tbl_notification where display_user='1' and category_id='" . $obj->escapestring($_REQUEST['category']) . "' order by id desc", $debug = -1);
    }
    elseif (!empty($_REQUEST['gender']))
    {
        $sql = $obj->query("select * from tbl_notification where display_user='1' and gender='" . $obj->escapestring($_REQUEST['gender']) . "' order by id desc", $debug = -1);
    }
    elseif (!empty($_REQUEST['group_id']))
    {
        $sql = $obj->query("select * from tbl_notification where display_user='1' and group_id='" . $obj->escapestring($_REQUEST['group_id']) . "' order by id desc", $debug = -1);
    }
    else
    {
        $sql = $obj->query("select * from tbl_notification where display_user='1' and status=1 order by id desc", $debug = -1);
    }

    if ($obj->numRows($sql) > 0)
    {

        while ($record = $obj->fetchNextObject($sql))
        {
            $start = $record->start_date;

            $end = $record->end_date;
            // print_r($start);
            //$period = new DatePeriod(new DateTime($start) , new DateInterval('P1D') , new DateTime($end));

            $Date = getDatesFromRange($start, $end); 
            //  print_r($Date);
            
            // echo $cdate;
            //  die;
            if(in_array($cdate,$Date)){
                $first_name = getFieldWhere('firstname','tbl_users','id',$record->user_id);
                $last_name = getFieldWhere('lastname','tbl_users','id',$record->user_id);
                $record->name = $first_name." ".$last_name;
                if(!empty($record->photo)){
                    $record->image = SITE_URL . '/upload_images/banner/' . $record->photo;
                    $record->big = SITE_URL . '/upload_images/banner/big/' . $record->photo;
                    $record->thumb = SITE_URL . '/upload_images/banner/thumb/' . $record->photo;
                    $record->tiny = SITE_URL . '/upload_images/banner/tiny/' . $record->photo;
                    $record->content = trim(strip_tags($record->content));
                }
                $a[] = $record;
            } 

        }
        // die;
        if(!empty($a) && count($a)>0){
            
            $data['success'] = 1;
            $data['message'] = "Notification list";
            $data['result'] = $a;
            
        }else{
             $data['success'] = 0;
             $data['message'] = "No notification found";
        }

        
    }
    else
    {
       $data['success'] = 0;
       $data['message'] = "No notification found";
    }

    echo json_encode($data);

break;
    ######################################################
    

    
case 'make-read':
    
    $user_id = $obj->escapestring($_REQUEST['user_id']);
    $msg_id = $obj->escapestring($_REQUEST['message_id']);
    $sql = $obj->query("update tbl_notification set status = '2' where id ='" .$msg_id. "' and user_id ='".$user_id."'", $debug = -1);
    if ($sql)
    {
        $data['success'] = 1;
        $data['message'] = "Message marked read";
    }
    else
    {
        $data['success'] = 0;
        $data['message'] = "Message not marked read";
    }

    echo json_encode($data);

break;

########################################################

case 'make-favourite':
$user_id = $obj->escapestring($_REQUEST['user_id']);
$notification_id = $obj->escapestring($_REQUEST['not_id']);
$favourite = $obj->escapestring($_REQUEST['favourite']);
$sql = $obj->query("update tbl_notification set favourite ='$favourite' where id = '$notification_id' and user_id ='$user_id'",$debug=-1);
if($sql){
    $query = $obj->query("select * from tbl_notification where id='$notification_id'",$debug=-1);
    if($obj->numRows($query) > 0){
        $record = $obj->fetchNextObject($query);
        $first_name = getFieldWhere('firstname','tbl_users','id',$record->user_id);
        $last_name = getFieldWhere('lastname','tbl_users','id',$record->user_id);
        $record->name = $first_name." ".$last_name;
        if(!empty($record->photo)){
            $record->image = SITE_URL . '/upload_images/banner/' . $record->photo;
            $record->big = SITE_URL . '/upload_images/banner/big/' . $record->photo;
            $record->thumb = SITE_URL . '/upload_images/banner/thumb/' . $record->photo;
            $record->tiny = SITE_URL . '/upload_images/banner/tiny/' . $record->photo;
            $record->content = trim(strip_tags($record->content));
        }
        $a[] = $record;
} 
$data['success'] = 1;
$data['notification_detail'] = $a;
$data['message'] = "Favourite updated successfully";

} else {
    $data['success'] = 0;
    $data['message'] = "Failed to update favourite";
}
echo json_encode($data);

break;

##############################################################

######################Get all favourite notification##############

case 'get-all-favourite':

$user_id = $obj->escapestring($_REQUEST['user_id']);

$sql = $obj->query("select * from tbl_notification where user_id ='$user_id' and favourite = '1'",$debug=-1);
if($obj->numRows($sql) > 0){
    while($record = $obj->fetchNextObject($sql)){
        $first_name = getFieldWhere('firstname','tbl_users','id',$record->user_id);
        $last_name = getFieldWhere('lastname','tbl_users','id',$record->user_id);
        $record->name = $first_name." ".$last_name;
        if(!empty($record->photo)){
            $record->image = SITE_URL . '/upload_images/banner/' . $record->photo;
            $record->big = SITE_URL . '/upload_images/banner/big/' . $record->photo;
            $record->thumb = SITE_URL . '/upload_images/banner/thumb/' . $record->photo;
            $record->tiny = SITE_URL . '/upload_images/banner/tiny/' . $record->photo;
            $record->content = trim(strip_tags($record->content));
        }
        $a[] = $record;   
    }
    $data['success'] = 1;
    $data['message'] = "Favourite notification list";
    $data['notification_list'] = $a; 
} else {
    $data['success'] = 0;
    $data['message'] = "No favourite notification";
}
echo json_encode($data);
break;
############################################################################

#######################Unread notification count############################

case 'unread-notification-count':

$user_id  = $obj->escapestring($_REQUEST['user_id']);

$sql = $obj->query("select * from tbl_notification where user_id ='$user_id' and status ='1' and display_user=1 order by id desc", $debug=-1);
if ($obj->numRows($sql) > 0) {
    $data['count'] = $obj->numRows($sql);
    while($record = $obj->fetchNextObject($sql)){
        $first_name = getFieldWhere('firstname','tbl_users','id',$record->user_id);
        $last_name = getFieldWhere('lastname','tbl_users','id',$record->user_id);
        $record->name = $first_name." ".$last_name;
        $record->image = SITE_URL . '/upload_images/banner/' . $record->photo;
        $record->big = SITE_URL . '/upload_images/banner/big/' . $record->photo;
        $record->thumb = SITE_URL . '/upload_images/banner/thumb/' . $record->photo;
        $record->tiny = SITE_URL . '/upload_images/banner/tiny/' . $record->photo;
        $record->content = trim(strip_tags($record->content));
        $a[] = $record;
    }
    
    $data['success'] = 1;
    $data['message'] = "Unread notification list";
    $data['notification_list'] = $a;
} else {
    $data['success'] = 0;
    $data['message'] = "No notification found";
}
echo json_encode($data);
break;

############################################################################


###########################User category list##############################
case 'user-category-list';

$user_id = $obj->escapestring($_REQUEST['user_id']);

$sql = $obj->query("select category from tbl_users where id = '$user_id'",$debug=-1);
$record = $obj->fetchNextObject($sql);
$category = $obj->query("select * from tbl_category",$debug=-1);
if(!empty($record->category)){
$user_category = explode(",",$record->category);
$category = $obj->query("select * from tbl_category",$debug=-1);
while($category_list = $obj->fetchNextObject($category)){
    if(in_array($category_list->id,$user_category))
    {
    	$category_list->user_selected = "true";
    } else {
    	$category_list->user_selected = "false";
    }
    $category_list->big = SITE_URL."upload_images/category/big/".$category_list->image;
    $category_list->thumb = SITE_URL."upload_images/category/thumb/".$category_list->image;
    $a[] = $category_list;
}	

$data['success'] = 1;
$data['message'] = "User category list"; 	
$data['user_category'] = $a;
} else {
while($category_list = $obj->fetchNextObject($category)){
   	$category_list->user_selected = "false";
    $category_list->big = SITE_URL."upload_images/category/big/".$category_list->image;
    $category_list->thumb = SITE_URL."upload_images/category/thumb/".$category_list->image;
    $a[] = $category_list;
}	
$data['success'] = 1;
$data['message'] = "Category list";
$data['user_category'] = $a;
}

echo json_encode($data); 
break;
############################################################

####################Update user category####################

case 'update-user-category':

$user_id = $obj->escapestring($_REQUEST['user_id']);
$category = $obj->escapestring($_REQUEST['category']);


$sql = $obj->query("update tbl_users set category='$category' where id='$user_id'",$debug=-1);
if($sql){
$sql = $obj->query("select category from tbl_users where id = '$user_id'",$debug=-1);
$record = $obj->fetchNextObject($sql);
$category = $obj->query("select * from tbl_category",$debug=-1);
if(!empty($record->category)){
$user_category = explode(",",$record->category);
$category = $obj->query("select * from tbl_category",$debug=-1);
while($category_list = $obj->fetchNextObject($category)){
    if(in_array($category_list->id,$user_category))
    {
    	$category_list->user_selected = "true";
    } else {
    	$category_list->user_selected = "false";
    }
    $category_list->big = SITE_URL."upload_images/category/big/".$category_list->image;
    $category_list->thumb = SITE_URL."upload_images/category/thumb/".$category_list->image;
    $a[] = $category_list;
}	

$data['success'] = 1;
$data['message'] = "Category updated successfully"; 	
$data['user_category'] = $a;
} else {
while($category_list = $obj->fetchNextObject($category)){
   	$category_list->user_selected = "false";
    $category_list->big = SITE_URL."upload_images/category/big/".$category_list->image;
    $category_list->thumb = SITE_URL."upload_images/category/thumb/".$category_list->image;
    $a[] = $category_list;
}	
$data['success'] = 1;
$data['message'] = "Category list";
$data['user_category'] = $a;
}
} else {
	while($category_list = $obj->fetchNextObject($category)){
   	$category_list->user_selected = "false";
    $category_list->big = SITE_URL."upload_images/category/big/".$category_list->image;
    $category_list->thumb = SITE_URL."upload_images/category/thumb/".$category_list->image;
    $a[] = $category_list;
}	
$data['success'] = 1;
$data['message'] = "Category list";	
$data['user_category'] = $a;
}
echo json_encode($data);
break;
###############################################################



}

