<?php

function push_notification_android($user_device_id,$title,$body,$image){
    define('API_ACCESS_KEY','AIzaSyCYcPl7NeHruTXpn41abj3FeMrwWp-oQcs');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    
    $notification = [
         'option' => 'notification'
    ];
    $extraNotificationData = [
        'title' => $title,
        'priority' => 'high',
        'body' => $body,
        'image' => $image,
        'option' => 'notification'
        ];
    $fcmNotification = [
        //'registration_ids' => $tokenList, //multple token array
        'to'        => $user_device_id,
        'notification' => $notification,
        'data' => $extraNotificationData
    ];
    $headers1 = [
        'Authorization: key=' .API_ACCESS_KEY,
        'Content-Type: application/json'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}


function push_notification_ios($user_device_id,$title,$body,$image){
    define('API_ACCESS_KEY','AIzaSyCYcPl7NeHruTXpn41abj3FeMrwWp-oQcs');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        
        $notification = array(
            'title' => $title, 
            'body' => $body,
            'image' => $image,
            'sound' => 'default'
        );
        $data = array(
            "option"=> "notification",
            );
        $arrayToSend = array(
            'to' => $user_device_id, 
            'notification' => $notification,
            'data' => $data
            );
    $headers1 = [
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$fcmUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}


function getCategoryTree($cat_id,$array){
	  //$array = array();
	  $array[]=$cat_id;
	  $parent=getParent($cat_id);
	  if($parent!=0){
		  $array[]=$parent;
		 return( getCategoryTree($parent,$array)); 
		   
	  }else{
	  	  $tree='';
	  	  
		  if($array!=''){
		  $array=array_unique($array);
		  $array=array_reverse($array);
		  foreach($array as $key=>$val){
			  $tree= $tree.getMainCategory($val)." > ";
		  }
		  return( substr( $tree,0,-2));
		  }else{
			  return( 'Main Category');
			  }
	  }
	  		
}


function getDatesFromRange($start, $end, $format = 'd-m-Y') { 
      
    // Declare an empty array 
    $array = array(); 
      
    // Variable that store the date interval 
    // of period 1 day 
    $interval = new DateInterval('P1D'); 
  
    $realEnd = new DateTime($end); 
    $realEnd->add($interval); 
  
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd); 
  
    // Use loop to store date into array 
    foreach($period as $date) {                  
        $array[] = $date->format($format);  
    } 
  
    // Return the array elements 
    return $array; 
} 

function getCategoryIDTree($cat_id,$array){
	  $array[]=$cat_id;
	  $parent=getParent($cat_id);
	  if($parent!=0){
		  $array[]=$parent;
	     return( getCategoryIDTree($parent,$array)); 
		   
	  }else{
		  $tree='';
		  if($array!=''){
		  $array=array_unique($array);
		  $array=array_reverse($array);
		  //print_r($array);
		  foreach($array as $key=>$val){
			  $tree= $tree.$val.",";
		  }
		  return( substr( $tree,0,-1));
		  }else{
			  return('Main Category');
			  }
	  }
	  		
}


function getCategoryArray($cat_id,$array){
	  $array[]=$cat_id;
	  $parent=getParent($cat_id);
	  if($parent!=0){
		  $array[]=$parent;
	    return(  getCategoryArray($parent,$array)); 
		   
	  }else{
		  
		  $array=array_unique($array);
		  $array=array_reverse($array);
		  return($array);
	  }
	  		
}
function getMainParent($cat_id){
	$arr=getCategoryArray($cat_id,$array=array());
	return ($arr[0]); 		
}
function getParent($pid){
	$sql=$GLOBALS['obj']->query("select parent_id from  tbl_maincategory where id='$pid'");
	$result=mysqli_fetch_assoc($sql);
	return ($result['parent_id']);
}
function getParent_slug($parent_id){
	$sql=$GLOBALS['obj']->query("select slug from  tbl_maincategory where id='$parent_id'");
	$result=mysqli_fetch_assoc($sql);
	return ($result['slug']);
}
function getParentname($p_id){
	$sql=$GLOBALS['obj']->query("select maincategory from  tbl_maincategory where id='$p_id'");
	$result=mysqli_fetch_assoc($sql);
	return ($result['maincategory']);
}
function getgrandParent($p_id){
	$sql=$GLOBALS['obj']->query("select maincategory from  tbl_maincategory where id='$p_id'");
	$result=mysqli_fetch_assoc($sql);
	return ($result['maincategory']);
}
function getMainCategory($catid){
	$sql=$GLOBALS['obj']->query("select maincategory from  tbl_maincategory where id='$catid'");
	$result=mysqli_fetch_assoc($sql);
	return ($result['maincategory']);
}

function getOrderByUserId($user_id){
	$sql=$GLOBALS['obj']->query("select id from  tbl_order where user_id='$user_id'");
	$result=mysqli_num_rows($sql);
	return ($result);
}

function getDeliveryEstimate($cat_id){
	
	$sql=$GLOBALS['obj']->query("select min_days,max_days from  tbl_deliveryestimate where cat_id='$cat_id'");
	$rs=mysqli_fetch_assoc($sql);
	if($rs[min_days]==0 && $rs[max_days]==0 ){
	$str='';
	}else{
	$str="Typically delivered in ";
	if($rs[max_days]!=0){
	$str.=$rs[min_days]." - ".$rs[max_days];
	}else{
	$str.=$rs[min_days];	
	}
	$str.=" Business Days";	
	}	
	
	return ($str);
}
function CalculateOrderTime($order_date){
	$order_time='';
	$diff = abs(time() - strtotime($order_date));
	$years = floor($diff / (365*60*60*24));
	$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	$hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 -$days*60*60*24)/ (60*60));
	$minutes = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 -$days*60*60*24-$hours*60*60)/ (60));
	$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 -$days*60*60*24-$hours*60*60-$minutes*60));
	if($years>0){$order_time.=$years." Years ";}
	if($months>0){$order_time.=$months." Months ";}
	if($days>0){$order_time.=$days." Days ";}
	if($hours>0){$order_time.=$hours." Hours ";}
	if($minutes>0){$order_time.=$minutes." Min ";}
	if($seconds>0){$order_time.=$seconds." Sec ";}

	$order_time.="  Ago ";
	return($order_time);
}
function getRewardPoints($user_id){
	
	$pointArr=$GLOBALS['obj']->query("select sum(reward_point) as credit from tbl_reward_history where user_id='".$user_id."' and type='Cr'");
	$rs=mysqli_fetch_object($pointArr);
	$total_cr=$rs->credit;
	$pointArr=$GLOBALS['obj']->query("select sum(reward_point) as debit from tbl_reward_history where user_id='".$user_id."' and type='Dr'");
	$rs=mysqli_fetch_object($pointArr);
	$total_dr=$rs->debit;
	$current_points=$total_cr-$total_dr; 
	return($current_points);
}
function generateCouponCode() {
$chars = "ABCDEFGHJKLMNOPQRSRTUVWXYZ123456789";
srand((double)microtime()*1000000);
$i = 0;
$randno = '' ;

while ($i < 6) {
$num = rand() % 33;
$tmp = substr($chars, $num, 1);
$randno = $randno . $tmp;
$i++;
}
return strtoupper($randno);
}

function generateOTPCode() {
$chars = "0123456789";
srand((double)microtime()*1000000);
$i = 0;
$randno = '' ;

while ($i < 4) {
$num = rand() % 33;
$tmp = substr($chars, $num, 1);
$randno = $randno . $tmp;
$i++;
}
return $randno;
}

function getYouTubeVideo($url){
$a=explode('v=',$url);
$b=explode('&',$a[1]);
return ("http://www.youtube.com/embed/".$b[0]);
}
function generateSlug($name){
	$newurl=str_replace(" - "," ",$name);
	$newurl=str_replace("&","",$newurl);
	$newurl=str_replace(","," ",$newurl);
	$myurl=str_replace("--","-",str_replace("%","",str_replace(" ","-",str_replace("-"," ",trim(str_replace("/"," ",str_replace(".","",$newurl)))))));
	return $myurl=strtolower($myurl);
}

function dateDiff ($d1, $d2) {
  return round(abs(strtotime($d1)-strtotime($d2))/86400);
}

function generateSlug1($name,$tbl,$id){
	$newurl=str_replace(" - "," ",$name);
	$newurl=str_replace("&","",$newurl);
	$newurl=str_replace(","," ",$newurl);
	$myurl=str_replace("--","-",str_replace("%","",str_replace(" ","-",str_replace("-"," ",trim(str_replace("/"," ",str_replace(".","",$newurl)))))));
	$myurl=strtolower($myurl);
	$query=$GLOBALS['obj']->query("select id from $tbl where slug='$myurl' ");
	if(mysqli_num_rows($query)>0){
        $myurl=$myurl.$id;
		$GLOBALS['obj']->query("update $tbl set  slug='$myurl'  where id='$id' ");
	}else{
		$GLOBALS['obj']->query("update $tbl set  slug='$myurl'  where id='$id' ");
	 }
	
}
function buildURL($url){
	$newurl=str_replace(" - "," ",$url);
	$myurl=str_replace("&","",str_replace("--","-",str_replace("%","",str_replace(" ","-",str_replace("-"," ",trim(str_replace("/"," ",str_replace(",","",str_replace(".","",$newurl)))))))));
	$myurl = str_replace("--","-",$myurl);
	return stripslashes(strtolower($myurl));
}



function parseInput($val) {
	return mysqli_real_escape_string(stripslashes($val));
}
function encryptPassword($val) {
	return sha1($val);
}
function getAdminEmail(){
$sql=$GLOBALS['obj']->query("select email from tbl_setting  where id=1");
$result=mysqli_fetch_assoc($sql);
return ($result['email']);
}
function getFieldWhere1($filed,$tbl,$where,$id){	
	$sql=$GLOBALS['obj']->query("select $filed as field from $tbl  where $where='".$id."'");
	$result=mysqli_fetch_assoc($sql);
	return (stripslashes($result['field']));	
}
function getFieldWhere($filed,$tbl,$where,$id){	
	//echo "select $filed as field from $tbl  where $where='".$id."'";
	$sql=$GLOBALS['obj']->query("select $filed as field from $tbl  where $where='".$id."'",$debug=-1);
	$result=mysqli_fetch_assoc($sql);
	return (stripslashes($result['field']));	
}

function getTotalCredit($user_id){
	$sql=$GLOBALS['obj']->query("select sum(credit_points) as tot_credit from tbl_credit_points  where user_id='".$user_id."'");
	$result=mysqli_fetch_assoc($sql);
	return (stripslashes($result['tot_credit']));
} 

function getTotalWallet($user_id){
	$sql=$GLOBALS['obj']->query("select sum(credit_amount) as tot_credit from tbl_credit  where user_id='".$user_id."' and type='Cr' and (t_type='Wallet' OR t_type='Order' OR t_type='Registration') ",$debug=-1);
	$result=mysqli_fetch_assoc($sql);

	$sql1=$GLOBALS['obj']->query("select sum(credit_amount) as tot_dcredit from tbl_credit  where user_id='".$user_id."' and type='Dr' and (t_type='Wallet'  OR t_type='Order' OR t_type='Registration') ");
	$result1=mysqli_fetch_assoc($sql1);

	return ($result['tot_credit']-$result1['tot_dcredit']);
}


function getTotalGiftWallet($user_id){
	$sql=$GLOBALS['obj']->query("select sum(credit_amount) as tot_credit from tbl_credit  where user_id='".$user_id."' and type='Cr' and (t_type='Referral' OR t_type='Voucher') ",$debug=-1);
	$result=mysqli_fetch_assoc($sql);

	$sql1=$GLOBALS['obj']->query("select sum(credit_amount) as tot_dcredit from tbl_credit  where user_id='".$user_id."' and type='Dr' and (t_type='Referral' OR t_type='Voucher') ");
	$result1=mysqli_fetch_assoc($sql1);

	return ($result['tot_credit']-$result1['tot_dcredit']);
}


function getTotalIncomingCredit($team_id){
	$sql=$GLOBALS['obj']->query("select sum(amount) as tot_credit from tbl_cash where type='Cr' and team_id='".$team_id."' group by team_id",$debug=-1);
	$result=mysqli_fetch_assoc($sql);
	return $result['tot_credit'];
}


function getTotalOutgoingCredit($team_id){
	$sql=$GLOBALS['obj']->query("select sum(amount) as tot_dcredit from tbl_cash where type='Dr' and team_id='".$team_id."' group by team_id",$debug=-1);
	$result=mysqli_fetch_assoc($sql);
	return $result['tot_dcredit'];
}

function getTotalDigitalCredit($team_id){
	$sql=$GLOBALS['obj']->query("select sum(amount) as tot_credit from tbl_digital where type='Cr' and team_id='".$team_id."' group by team_id",$debug=-1);
	$result=mysqli_fetch_assoc($sql);
	return $result['tot_credit'];
}

function getTotalDigitalDebit($team_id){
	$sql=$GLOBALS['obj']->query("select sum(amount) as tot_dcredit from tbl_digital where type='Dr' and team_id='".$team_id."' group by team_id",$debug=-1);
	$result=mysqli_fetch_assoc($sql);
	return $result['tot_dcredit'];
}



function getTotalFixedCredit($cat_id){
	$sql=$GLOBALS['obj']->query("select sum(credit_amount) as tot_credit from tbl_cashcredit where type='Cr' and cat_id='".$cat_id."' group by cat_id",$debug=-1);
	$result=mysqli_fetch_assoc($sql);

	return $result['tot_credit'];
}


function getTotalFixeddebit($cat_id){
	$sql=$GLOBALS['obj']->query("select sum(credit_amount) as tot_dcredit from tbl_cashcredit where type='Dr' and cat_id='".$cat_id."' group by cat_id",$debug=-1);
	$result=mysqli_fetch_assoc($sql);

	return $result['tot_dcredit'];
}


function getTotalQty($product_id,$price_id){
	$sql=$GLOBALS['obj']->query("select sum(totqty) as tot_credit from tbl_stock  where price_id='".$price_id."' and product_id='".$product_id."' and type='Cr'",$debug=-1);
	$result=mysqli_fetch_assoc($sql);

	$sql1=$GLOBALS['obj']->query("select sum(totqty) as tot_dcredit from tbl_stock  where price_id='".$price_id."' and product_id='".$product_id."' and type='Dr'");
	$result1=mysqli_fetch_assoc($sql1);

	return ($result['tot_credit']-$result1['tot_dcredit']);
}

function getTotalSuspenseWallet($user_id){
	$sql=$GLOBALS['obj']->query("select referral_amount from tbl_user  where id='".$user_id."'",$debug=-1);
	$result=mysqli_fetch_assoc($sql);
	$response=$result['referral_amount'];
	if($response=='')
	{
		$response=0;
	}
	return $response;
}  

function getCategory($cat_id){
	//echo "select * from tbl_category  where category_id='".$cat_id."' and rootCategory=0";
$query="select * from tbl_category  where category_id='".$cat_id."' and rootCategory=0";
$sql=$GLOBALS['obj']->query($query);
$result=mysqli_fetch_assoc($sql);
//$result=$GLOBALS['obj']->fetchNextObject($sql);
return (stripslashes($result['categoryName']));
}

function getProductNameWithSize($id,$size){
$query = "select a.product_name,b.size,c.name from tbl_product as a JOIN tbl_productprice as b ON a.id=b.product_id JOIN tbl_unit as c on c.id=b.unit_id where a.id='$id' and b.id='$size'";

$sql=$GLOBALS['obj']->query($query);
$result=mysqli_fetch_assoc($sql);
$name=$result['product_name']."-".$result['size']."-".$result['name'];
return $name;
}

function getProductListingName($pname){
	$pname=stripslashes(trim($pname));
	/*$spos=strpos($pname," ");
	$pfname=substr($pname,0,$spos);
	$plname=substr($pname,$spos);
$pname="<span>".$pfname."</span><br>".$plname;*/
if(strlen($pname)>25){
	$pname=substr($pname,0,25)."..";
return($pname);
}
return($pname);
	
} 
function getSubcategory($cat_id){
$sql=$GLOBALS['obj']->query("select * from tbl_category  where category_id='".$cat_id."' and rootCategory!=0");
$result=mysqli_fetch_assoc($sql);
return (stripslashes($result['categoryName']));
} 

function getFAQCategory($cat_id){
$sql=$GLOBALS['obj']->query("select * from tbl_faqcategory  where id='".$cat_id."'");
$result=mysqli_fetch_assoc($sql);
return (stripslashes($result['faqcategory']));	
}
function getUser($uid){
$sql=$GLOBALS['obj']->query("select uname from tbl_user  where id='".$uid."'");
$result=mysqli_fetch_assoc($sql);
return (stripslashes(ucfirst($result['uname'])));
} 
 
function getContent($title) {
$sql=$GLOBALS['obj']->query("select * from tbl_content where title='$title' ");
$result=mysqli_fetch_assoc($sql);
return (stripslashes($result['content']));
}
function getField1($filed,$table,$id) {
	
$sql=$GLOBALS['obj']->query("select $filed as field from $table where  category_id='$id' ");
$result=mysqli_fetch_assoc($sql);
return (stripslashes($result['field']));
}

function getField($filed,$table,$id) {
	
$sql=$GLOBALS['obj']->query("select $filed as field from $table where id='$id'",$debug=-1);
$result=mysqli_fetch_assoc($sql);
return (stripslashes($result['field']));
}

function getcompany($filed,$table,$id) {
	
$sql=$GLOBALS['obj']->query("select $filed as field from $table where company_id='$id' ");
$result=mysqli_fetch_assoc($sql);
return (stripslashes($result['field']));
}
function clearCache() {
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
}
function redirect($url) {
	header("location:$url");
	exit();
}
function validateAdminSession() {
	if(trim($_SESSION["sess_admin_id"])=="" && trim($_SESSION["sess_admin_logged"])!="true") {
		$_SESSION["sess_msg"] = "Session is expire. Please login again to continue";
		redirect("login.php");
	}
}
function showSessionMsg() {
	if(trim($_SESSION["sess_msg"])) {
		echo $_SESSION["sess_msg"];
		$_SESSION["sess_msg"] = "";
	}
}
function validate_user()
{
	if($_SESSION['sess_user_id']=='')
	{
		ms_redirect("index.php");
	}
}
function validate_admin()
{
	if($_SESSION['sess_admin_id']=='')
	{
		ms_redirect("index.php?back=$_SERVER[REQUEST_URI]");
	}
}
function ms_redirect($file, $exit=true, $sess_msg='')
{
	header("Location: $file");
	exit();
	
}
function sort_arrows($column){
	global $_SERVER;
	return '<A HREF="'.$_SERVER['PHP_SELF'].get_qry_str(array('order_by','order_by2'), array($column,'asc')).'"><IMG SRC="images/white_up.gif" BORDER="0"></A> <A HREF="'.$_SERVER['PHP_SELF'].get_qry_str(array('order_by','order_by2'), array($column,'desc')).'"><IMG SRC="images/white_down.gif" BORDER="0"></A>';
}
function sort_arrows1($column){
	global $_SERVER;
	return '<A HREF="'.$_SERVER['PHP_SELF'].get_qry_str(array('order_by','order_by2'), array($column,'asc')).'"><IMG SRC="admin/images/white_up.gif" BORDER="0"></A> <A HREF="'.$_SERVER['PHP_SELF'].get_qry_str(array('order_by','order_by2'), array($column,'desc')).'"><IMG SRC="admin/images/white_down.gif" BORDER="0"></A>';
}

function sort_arrows_front($column,$heading){
	global $_SERVER;
	return '<A HREF="'.$_SERVER['PHP_SELF'].get_qry_str(array('order_by','order_by2'), array($column,'asc')).'"><img src="images/sort_up.gif" alt="Sort Up" border="0" title="Sort Up"></A>&nbsp;'.$heading.'&nbsp;<A HREF="'.$_SERVER['PHP_SELF'].get_qry_str(array('order_by','order_by2'), array($column,'desc')).'"><img src="images/sort_down.gif" alt="Sort Down" border="0" title="Sort Down"></A>';
}
function sort_arrows_front1($column,$heading){
	global $_SERVER;
	return '<A HREF="'.$_SERVER['PHP_SELF'].get_qry_str(array('order_by','order_by2'), array($column,'asc')).'"><img src="admin/images/sort_up.gif" alt="Sort Up" border="0" title="Sort Up"></A>&nbsp;'.$heading.'&nbsp;<A HREF="'.$_SERVER['PHP_SELF'].get_qry_str(array('order_by','order_by2'), array($column,'desc')).'"><img src="admin/images/sort_down.gif" alt="Sort Down" border="0" title="Sort Down"></A>';
}


function get_qry_str($over_write_key = array(), $over_write_value= array())
{
	global $_GET;
	$m = $_GET;
	if(is_array($over_write_key)){
		$i=0;
		foreach($over_write_key as $key){
			$m[$key] = $over_write_value[$i];
			$i++;
		}
	}else{
		$m[$over_write_key] = $over_write_value;
	}
	$qry_str = qry_str($m);
	return $qry_str;
} 

function qry_str($arr, $skip = '')
{
	$s = "?";
	$i = 0;
	foreach($arr as $key => $value) {
		if ($key != $skip) {
			if(is_array($value)){
				foreach($value as $value2){
					if ($i == 0) {
						$s .= "$key%5B%5D=$value2";
					$i = 1;
					} else {
						$s .= "&$key%5B%5D=$value2";
					} 
				}		
			}else{
				if ($i == 0) {
					$s .= "$key=$value";
					$i = 1;
				} else {
					$s .= "&$key=$value";
				} 
			}
		} 
	} 
	return $s;
}



function genrateReferralCode($uid,$u_type) {

	$sql=$GLOBALS['obj']->query("select count(*) total from tbl_referral_code",$debug=-1);
	$result=mysqli_fetch_assoc($sql);
	//print_r($result);

	$number=$result['total'];	
	$totalNumber=$number+1;
	//$totalNumber=123453;	

	if($u_type=="1"){
		
		if($totalNumber<10){
			$code="GR000000".$totalNumber;
		}else if($totalNumber>=10 && $totalNumber<100){
			$code="GR00000".$totalNumber;			
		}else if($totalNumber>=100 && $totalNumber<1000){
			$code="GR0000".$totalNumber;			
		}else if($totalNumber>=1000 && $totalNumber<10000){
			$code="GR000".$totalNumber;			
		}else if($totalNumber>=10000 && $totalNumber<100000){
			$code="GR00".$totalNumber;			
		}else if($totalNumber>=100000 && $totalNumber<1000000){
			$code="GR0".$totalNumber;			
		}else if($totalNumber>=1000000 && $totalNumber<10000000){
			$code="GR".$totalNumber;			
		}


	}else if($u_type=="2"){
		
		if($totalNumber<10){
			$code="GR000000".$totalNumber;
		}else if($totalNumber>=10 && $totalNumber<100){
			$code="GR00000".$totalNumber;			
		}else if($totalNumber>=100 && $totalNumber<1000){
			$code="GR0000".$totalNumber;			
		}else if($totalNumber>=1000 && $totalNumber<10000){
			$code="GR000".$totalNumber;			
		}else if($totalNumber>=10000 && $totalNumber<100000){
			$code="GR00".$totalNumber;			
		}else if($totalNumber>=100000 && $totalNumber<1000000){
			$code="GR0".$totalNumber;			
		}else if($totalNumber>=1000000 && $totalNumber<10000000){
			$code="GR".$totalNumber;			
		}

	}		

	$sql1=$GLOBALS['obj']->query("select count(*) total from tbl_referral_code where referral_code='$code' and u_id='$uid'",$debug=-1);
	$result1=mysqli_fetch_assoc($sql1);
	//print_r($result1);

	if ($result1->total>0) {
			
		//$sql=$GLOBALS['obj']->query("insert into tbl_referral_code set u_id='$uid',referral_code='$code',u_type='$u_type'");
	}else{
		$sql=$GLOBALS['obj']->query("insert into tbl_referral_code set u_id='$uid',referral_code='$code',u_type='$u_type'");
	}
	
	return $code;
} 

function DeliveryDateCal(){
	$TodayTime = date('h:i A',strtotime('-19 Minutes')); //return;
	$Sql = $GLOBALS['obj']->query("select * from tbl_bookingslot where status=1 and find_in_set('".date('l')."',booking_days) order by id asc",$debug=-1); //return;
	while($result = mysqli_fetch_assoc($Sql)){
		
		if(strtotime($result['booking_availble_from']) < strtotime($TodayTime) && strtotime($result['booking_availble_to']) > strtotime($TodayTime)){
			return $AvailbleTime =  $result['time_from']." - ".$result['time_to']; 
			
		}else{
			return $AvailbleTime ="02:00 PM - 04:00 PM";
		}
	}

}


function TotalInstockProduct($order_id){
	$Sql = $GLOBALS['obj']->query("select * from tbl_order_itmes where order_id='$order_id' and status=1",$debug=-1);
	$result = mysqli_num_rows($Sql);
	return $result;
}

function TotalOutstockProduct($order_id){
	$Sql = $GLOBALS['obj']->query("select * from tbl_order_itmes where order_id='$order_id' and status=0",$debug=-1);
	$result = mysqli_num_rows($Sql);
	return $result;
}

function TotalStockProduct($order_id){
	$Sql = $GLOBALS['obj']->query("select * from tbl_order_itmes where order_id='$order_id'",$debug=-1);
	$result = mysqli_num_rows($Sql);
	return $result;
}



function getProcessingUserID(){
	$diffResult=array();
	$totalUser=array();
	$processUser=array();

	$Sql = $GLOBALS['obj']->query("select * from tbl_admin where department='2' and  FIND_IN_SET('9',designation)",$debug=-1);
	$result = mysqli_num_rows($Sql);
	
	if ($result>0) {
			
		while($line = mysqli_fetch_assoc($Sql))
		{
			
			$totalUser[] = $line['id'];	

			$uid=$line['id'];
			$Sql1 = $GLOBALS['obj']->query("select * from tbl_order_process_user where user_id='$uid' and  
				DATE(cdate) = CURRENT_DATE and type='1'",$debug=-1);
			$userResult = mysqli_fetch_assoc($Sql1);
			$processUser[] = $userResult['user_id'];	

		}

			$diffResult ='';
			$diffResult=array_diff($totalUser,$processUser);

			if (!empty($diffResult)) {
					// Insert  user
					$uid= array_shift($diffResult);
					$GLOBALS['obj']->query("insert into tbl_order_process_user set user_id='$uid',type='1'",$debug=-1);
					return $uid;


			} else {
				
				 $Sql1 = $GLOBALS['obj']->query("select user_id,total_order from tbl_order_process_user where DATE(cdate) = CURRENT_DATE and type='1' order by total_order ASC",$debug=-1);
				 $userResult1 = mysqli_fetch_assoc($Sql1);

				 $uid=$userResult1["user_id"];
				 $new_total_order=$userResult1['total_order']+1;	
				 $GLOBALS['obj']->query("update tbl_order_process_user set total_order='$new_total_order' where user_id='".$userResult1["user_id"]."' and type='1'",$debug=-1);

				 return $uid;
				
				
			}


	} else {

		return 0;
		
	}

}

function getSourcingUserID(){
	$diffResult=array();
	$totalUser=array();
	$processUser=array();

	$Sql = $GLOBALS['obj']->query("select * from tbl_admin where department='2' and  FIND_IN_SET('5',designation)",$debug=-1);
	$result = mysqli_num_rows($Sql);
	
	if ($result>0) {
			
		while($line = mysqli_fetch_assoc($Sql))
		{
			
			$totalUser[] = $line['id'];	

			$uid=$line['id'];
			$Sql1 = $GLOBALS['obj']->query("select * from tbl_order_process_user where user_id='$uid' and  
				DATE(cdate) = CURRENT_DATE and type='2'",$debug=-1);
			$userResult = mysqli_fetch_assoc($Sql1);
			$processUser[] = $userResult['user_id'];	

		}

			$diffResult ='';
			$diffResult=array_diff($totalUser,$processUser);

			if (!empty($diffResult)) {
					// Insert  user
					$uid= array_shift($diffResult);
					$GLOBALS['obj']->query("insert into tbl_order_process_user set user_id='$uid',type='2'",$debug=-1);
					return $uid;


			} else {
				
				 $Sql1 = $GLOBALS['obj']->query("select user_id,total_order from tbl_order_process_user where DATE(cdate) = CURRENT_DATE and type='2' order by total_order ASC",$debug=-1);
				 $userResult1 = mysqli_fetch_assoc($Sql1);

				 $uid=$userResult1["user_id"];
				 $new_total_order=$userResult1['total_order']+1;	
				 $GLOBALS['obj']->query("update tbl_order_process_user set total_order='$new_total_order' where user_id='".$userResult1["user_id"]."' and type='2'",$debug=-1);

				 return $uid;
				
				
			}


	} else {

		return 0;
		
	}

}


function getDeliveryUserID(){
	$diffResult=array();
	$totalUser=array();
	$processUser=array();

	$Sql = $GLOBALS['obj']->query("select * from tbl_admin where department='2' and  FIND_IN_SET('10',designation)",$debug=-1);
	$result = mysqli_num_rows($Sql);
	
	if ($result>0) {
			
		while($line = mysqli_fetch_assoc($Sql))
		{
			
			$totalUser[] = $line['id'];	

			$uid=$line['id'];
			$Sql1 = $GLOBALS['obj']->query("select * from tbl_order_process_user where user_id='$uid' and  
				DATE(cdate) = CURRENT_DATE and type='3'",$debug=-1);
			$userResult = mysqli_fetch_assoc($Sql1);
			$processUser[] = $userResult['user_id'];	

		}

			$diffResult ='';
			$diffResult=array_diff($totalUser,$processUser);

			if (!empty($diffResult)) {
					// Insert  user
					$uid= array_shift($diffResult);
					$GLOBALS['obj']->query("insert into tbl_order_process_user set user_id='$uid',type='3'",$debug=-1);
					return $uid;


			} else {
				
				 $Sql1 = $GLOBALS['obj']->query("select user_id,total_order from tbl_order_process_user where DATE(cdate) = CURRENT_DATE and type='3' order by total_order ASC",$debug=-1);
				 $userResult1 = mysqli_fetch_assoc($Sql1);

				 $uid=$userResult1["user_id"];
				 $new_total_order=$userResult1['total_order']+1;	
				 $GLOBALS['obj']->query("update tbl_order_process_user set total_order='$new_total_order' where user_id='".$userResult1["user_id"]."' and type='3'",$debug=-1);

				 return $uid;
				
				
			}


	} else {

		return 0;
		
	}

}

function getMRP($pid){	
	$sql=$GLOBALS['obj']->query("select mrp from tbl_store_price  where price_id='".$pid."' order by mrp asc limit 0,1");
	$result=mysqli_fetch_assoc($sql);
	return (stripslashes($result['mrp']));	
}


function SendSMS($mobile){
	$otp=rand(1000,9999);
    $_SESSION['otp']=$otp;
    $_SESSION['mobilenumber']=$mobile;

	$msg="Your verification code is ".$_SESSION['otp'];
	$mobile = "+91".$mobile;
	$otp =$_SESSION['otp'];

	$curl = curl_init();

	curl_setopt_array($curl, array(
	CURLOPT_URL => "http://control.msg91.com/api/sendotp.php?otp_length=4&authkey=295686Ayle8oxDsEX5d89f32d&message=$msg&sender=NOTIFICATION&mobile=$mobile&otp=$otp",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_SSL_VERIFYHOST => 0,
	CURLOPT_SSL_VERIFYPEER => 0,
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	return $otp;
}
?>
