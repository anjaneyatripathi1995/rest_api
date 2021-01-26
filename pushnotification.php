<?php

function push_notification_android_booking($regid,$body,$title,$booking_id){
    define('API_ACCESS_KEY','AAAA0MvOY0k:APA91bGx71jFBZSLWvxoeYaWdF4R1lzi_hhYRTgaYgf5J1qAC_yMXwDMU17fKY4SuxiNYw0tlS-9f-6ArRRgkEkZO5ioxrQVYGv7DdK3IelG5Ehrdr-06iaRwRD5zwrSbtagMeMxC-Ln');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    
    $notification = [
         'option' => 'artist_booking'
    ];
    $extraNotificationData = [
        'title' =>$title,
        'body' => $body,
        'booking_id'=> $booking_id,
        'option' => 'artist_booking'
        ];
    $fcmNotification = [
        //'registration_ids' => $tokenList, //multple token array
        'to'        => $regid,
        'notification' => $notification,
        'data' => $extraNotificationData
    ];
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}

function push_notification_ios_booking($regid,$body,$title,$booking_id){
    define('API_ACCESS_KEY','AAAA0MvOY0k:APA91bGx71jFBZSLWvxoeYaWdF4R1lzi_hhYRTgaYgf5J1qAC_yMXwDMU17fKY4SuxiNYw0tlS-9f-6ArRRgkEkZO5ioxrQVYGv7DdK3IelG5Ehrdr-06iaRwRD5zwrSbtagMeMxC-Ln');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        
        $notification = array(
            'title' => $title, 
            'body' => $body,
            'sound' => 'default'
        );
        $data = array(
            "option"=> "Booking status",
            'booking_id' => $booking_id
            );
        $arrayToSend = array(
            'to' => $regid, 
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


function push_notification_android_status_update($regid,$body,$title,$booking_id){
    define('API_ACCESS_KEY','AAAA0MvOY0k:APA91bGx71jFBZSLWvxoeYaWdF4R1lzi_hhYRTgaYgf5J1qAC_yMXwDMU17fKY4SuxiNYw0tlS-9f-6ArRRgkEkZO5ioxrQVYGv7DdK3IelG5Ehrdr-06iaRwRD5zwrSbtagMeMxC-Ln');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    
    $notification = [
         'option' => 'artist_booking'
    ];
    $extraNotificationData = [
        'title' =>$title,
        'body' => $body,
        'booking_id'=> $booking_id,
        'option' => 'artist_booking'
        ];
    $fcmNotification = [
        //'registration_ids' => $tokenList, //multple token array
        'to'        => $regid,
        'notification' => $notification,
        'data' => $extraNotificationData
    ];
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}


function push_notification_ios_status_update($regid,$body,$title,$booking_id){
    define('API_ACCESS_KEY','AAAA0MvOY0k:APA91bGx71jFBZSLWvxoeYaWdF4R1lzi_hhYRTgaYgf5J1qAC_yMXwDMU17fKY4SuxiNYw0tlS-9f-6ArRRgkEkZO5ioxrQVYGv7DdK3IelG5Ehrdr-06iaRwRD5zwrSbtagMeMxC-Ln');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        
        $notification = array(
            'title' => $title, 
            'body' => $body,
            'sound' => 'default'
        );
        $data = array(
            "option"=> "Booking status",
            'booking_id' => $booking_id
            );
        $arrayToSend = array(
            'to' => $regid, 
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


function push_notification_android_gig_counter($regid,$quote_price,$title,$body,$booking_id){
    define('API_ACCESS_KEY','AAAA0MvOY0k:APA91bGx71jFBZSLWvxoeYaWdF4R1lzi_hhYRTgaYgf5J1qAC_yMXwDMU17fKY4SuxiNYw0tlS-9f-6ArRRgkEkZO5ioxrQVYGv7DdK3IelG5Ehrdr-06iaRwRD5zwrSbtagMeMxC-Ln');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    $notification = [
         'option' => 'Booking status'
    ];
    $extraNotificationData = [
        'title' =>$title,
        'body' => $body,
        'booking_id'=> $booking_id,
        'quote_price'=> $quote_price,
        'option' => 'Booking status'
        ];
    $fcmNotification = [
        //'registration_ids' => $tokenList, //multple token array
        'to'        => $regid,
        'notification' => $notification,
        'data' => $extraNotificationData
    ];
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    $result = curl_exec($ch);
    curl_close($ch);
    //echo $result;
    return $result;
}

function push_notification_ios_gig_counter($regid,$quote_price,$title,$body,$booking_id){
    define('API_ACCESS_KEY','AAAA0MvOY0k:APA91bGx71jFBZSLWvxoeYaWdF4R1lzi_hhYRTgaYgf5J1qAC_yMXwDMU17fKY4SuxiNYw0tlS-9f-6ArRRgkEkZO5ioxrQVYGv7DdK3IelG5Ehrdr-06iaRwRD5zwrSbtagMeMxC-Ln');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        
        $notification = array(
            'title' => $title, 
            'body' => $body,
            'sound' => 'default'
        );
        $data = array(
            "option"=> 'Booking status',
            'booking_id' => $booking_id,
            'quote_price'=>$quote_price,
            );
        $arrayToSend = array(
            'to' => $regid, 
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


function push_notification_android_booking_canceled($regid,$reason_of_cancel,$title,$body,$booking_id){
    define('API_ACCESS_KEY','AAAA0MvOY0k:APA91bGx71jFBZSLWvxoeYaWdF4R1lzi_hhYRTgaYgf5J1qAC_yMXwDMU17fKY4SuxiNYw0tlS-9f-6ArRRgkEkZO5ioxrQVYGv7DdK3IelG5Ehrdr-06iaRwRD5zwrSbtagMeMxC-Ln');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    $notification = [
         'option' => 'Booking status'
    ];
    $extraNotificationData = [
        'title' =>$title,
        'body' => $body,
        'booking_id'=> $booking_id,
        'reason'=> $reason_of_cancel,
        'option' => 'Booking status'
        ];
    $fcmNotification = [
        //'registration_ids' => $tokenList, //multple token array
        'to'        => $regid,
        'notification' => $notification,
        'data' => $extraNotificationData
    ];
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    $result = curl_exec($ch);
    curl_close($ch);
    //echo $result;
    return $result;
}

function push_notification_ios_booking_canceled($regid,$reason_of_cancel,$title,$body,$booking_id){
    define('API_ACCESS_KEY','AAAA0MvOY0k:APA91bGx71jFBZSLWvxoeYaWdF4R1lzi_hhYRTgaYgf5J1qAC_yMXwDMU17fKY4SuxiNYw0tlS-9f-6ArRRgkEkZO5ioxrQVYGv7DdK3IelG5Ehrdr-06iaRwRD5zwrSbtagMeMxC-Ln');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        
        $notification = array(
            'title' => $title, 
            'body' => $body,
            'sound' => 'default'
        );
        $data = array(
            "option"=> 'Booking status',
            'booking_id' => $booking_id,
            'reason'=>$reason_of_cancel,
            );
        $arrayToSend = array(
            'to' => $regid, 
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


function push_notification_android_chat($regid,$name,$message,$booking_id,$artist_id,$venue_id,$guest_id){
    define('API_ACCESS_KEY','AAAA0MvOY0k:APA91bGx71jFBZSLWvxoeYaWdF4R1lzi_hhYRTgaYgf5J1qAC_yMXwDMU17fKY4SuxiNYw0tlS-9f-6ArRRgkEkZO5ioxrQVYGv7DdK3IelG5Ehrdr-06iaRwRD5zwrSbtagMeMxC-Ln');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    $notification = [
         'option' => 'chat'
    ];
    $extraNotificationData = [
        'title' => $name,
        'body' => $message,
        'booking_id'=>$booking_id,
        'artist_id'=>$artist_id,
        'venue_id'=>$venue_id,
        'guest_id'=>$guest_id,
        'name'=>$name,
        'option' => 'chat'
        ];
    $fcmNotification = [
        'to'        => $regid,
        'notification' => $notification,
        'data' => $extraNotificationData
    ];
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    $result = curl_exec($ch);
    curl_close($ch);
    //echo $result;
    return $result;
}

function push_notification_ios_chat($regid,$name,$message,$booking_id,$artist_id,$venue_id,$guest_id){
    define('API_ACCESS_KEY','AAAA0MvOY0k:APA91bGx71jFBZSLWvxoeYaWdF4R1lzi_hhYRTgaYgf5J1qAC_yMXwDMU17fKY4SuxiNYw0tlS-9f-6ArRRgkEkZO5ioxrQVYGv7DdK3IelG5Ehrdr-06iaRwRD5zwrSbtagMeMxC-Ln');
    $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        
        $notification = array(
            'title' => $name,
            'body' => $message,
            'booking_id'=>$booking_id,
            'artist_id'=>$artist_id,
            'venue_id'=>$venue_id,
            'guest_id'=>$guest_id,
            'name'=>$name,
            'sound' => 'default'
        );
        $data = array(
            'option' => 'chat',
            'booking_id'=>$booking_id,
            'artist_id'=>$artist_id,
            'venue_id'=>$venue_id,
            'guest_id'=>$guest_id,
            'name'=>$name,
            );
        $arrayToSend = array(
            'to' => $regid, 
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

?>