<?php
ignore_user_abort(true);
set_time_limit(0);
$path = "../files/";
include_once ('../../pdo/dbconfig.php');
$secret = 'Bulletin';
include_once ('../../pdo/Class.Request.php');

$DB_Request=new Request($DB_con);

        $user_id=$_GET['u'];
        $history_type_id=$_GET['h'];
        $table_id=$_GET['id'];
        if(!empty($_GET['e'])){$user_email=$_GET['e'];}else{$user_email="";}
        if(!empty($_GET['s'])){$subject=$_GET['s'];}else{$subject="";}
        if(!empty($_GET['f'])){$file_name=$_GET['f'];}else{$file_name="";}
        if(!empty($_GET['c'])){$comments=$_GET['c'];}else{$comments="";}

include_once ('GetClientData.php');

$data = new GetDataPlugin();

$client_data=array($data->ip(),$data->os(),$data->browser(),$data->geo('country'),
    $data->geo('region'),$data->geo('continent'),$data->geo('city'),$data->agent(),$data->referer(),
    $data->height(),$data->width(),$data->javaenabled(),$data->cookieenabled(),$data->language(),$data->architecture(),$data->geo('logitude'),$data->geo('latitude'),$data->provetor(),$data->getdate());
//die(var_dump($client_data));
//echo "<br>IP               ".$data->ip();
//echo "<br>Operative System ".$data->os();
//echo "<br>Browser          ".$data->browser();
//echo "<br>Screen height    ".$data->height();
//echo "<br>Screen width     ".$data->width();
//echo "<br>Java enabled     ".$data->javaenabled();
//echo "<br>Cookie enabled   ".$data->cookieenabled();
//echo "<br>Language         ".$data->language();
//echo "<br>Architecture     ".$data->architecture();
//echo "<br>Device           ".$data->device();
//echo "<br>Country          ".$data->geo('country');
//echo "<br>Region           ".$data->geo('region');
//echo "<br>Continent        ".$data->geo('continent');
//echo "<br>City             ".$data->geo('city');
//echo "<br>Logitude         ".$data->geo('logitude');
//echo "<br>Latitude         ".$data->geo('latitude');
//echo "<br>Currency         ".$data->geo('currency');
//echo "<br>Provetor         ".$data->provetor();
//echo "<br>Agent            ".$data->agent();
//echo "<br>Referer          ".$data->referer();
//echo "<br>Date             ".$data->getdate();
$client_data = json_encode($client_data);
//        if(!empty($_GET['ip'])){$IP=$_GET['ip'];}else{$IP="";}
       $DB_Request->insertHistory($user_id,$history_type_id,$table_id,$user_email,$subject,$file_name,$comments,$client_data,'');

//Get the http URI to the image
$graphic_http = 'https://www.spgmanagement.com/images/blank.gif';

//Get the filesize of the image for headers
$filesize = filesize('../../images/blank.gif');

//Now actually output the image requested, while disregarding if the database was affected
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-type: image/png');
header('Cache-Control: private', false);
header('Content-Disposition: inline; filename="blank.gif"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . $filesize);
readfile($graphic_http);

//All done, get out!
exit;