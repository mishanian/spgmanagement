<?php

include_once('../../../pdo/dbconfig.php');

if ($_POST['action'] == 'get_bulletin_info') {
    $bulletin_id = $_POST['bulletin_id'];
    $bulletin_info = $DB_request->get_bulletin_info($bulletin_id);

    $feedback = array();
    $feedback['bulletin_id'] = 1000000 + $bulletin_info['bulletin_id'];
    $feedback['building_name'] = $bulletin_info['building_name'];
    $feedback['issuer_name'] = $bulletin_info['issuer_name'];
    $feedback['issuer_telephone'] = $bulletin_info['issuer_telephone'];

    $create_time = $bulletin_info['create_time'];
    $issue_date = date('Y-m-d', strtotime($create_time));
    $issue_time = date('H:i:s', strtotime($create_time));

    $feedback['issue_date'] = $issue_date;
    $feedback['issue_time'] = $issue_time;
    $feedback['issue_to'] = $bulletin_info['issue_to'];
    $feedback['title'] = $bulletin_info['title'];
    $feedback['content'] = $bulletin_info['content'];

    echo json_encode($feedback);
}

if ($_POST['action'] == 'get_new_bulletin') {
    $user_id = $_POST['user_id'];
    $buildings = $DB_request->get_bulletin_buildings($user_id);

    echo json_encode($buildings);
}

if ($_POST['action'] == 'add_bulletin') {
    $building_id = $_POST['bulletinBuilding'];
    $employee_id = $_POST['reportUserId'];
    $create_time = date('Y-m-d H:i:s');
    $issue_from = parse_uk_date($_POST['bulletinFrom']);
    $issue_to = parse_uk_date($_POST['bulletinTo']);
    $title = $_POST['bulletinTitle'];
    $content = $_POST['bulletinContent'];
    if (!empty($_FILES['attachment'])) {
        $file_name = $_FILES['attachment']['name'];
        $file_tmp = $_FILES['attachment']['tmp_name'];
        // Check if file already exists

        $file_name = "bulletin" . "_" . date("Y-m-d-H-i-s") . "_" . $file_name;

        //    $file_ext=strtolower(end(explode('.',$_FILES['attachment']['name'])));
        move_uploaded_file($file_tmp, "../../files/requests/" . $file_name);
        $attachment = $file_name;
        //       echo "File Name=".$file_name;
    } else {
        $attachment = "";
    }
    //    die("attachment=".$attachment);
    $file_id = $DB_request->add_bulletin($building_id, $employee_id, $create_time, $issue_from, $issue_to, $title, $content, $attachment);
    //die("file_id=$file_id");

    // EMAIL TEMPLATE
    $secret = 'Bulletin';
    $title          = "Bulletin Notification";
    $subtitle       = $title;
    $body1          = $content;
    $body2          = "";
    $slug = md5($file_id . $secret);

    //echo $body2;


    $button_url     = "https://spgmanagement.com/admin/";
    $button_content = "View Notification";
    include_once("../sendSMSEmail.php");
    include_once "../../../pdo/Class.Template.php";
    $template       = new Template();

    $receivers = $DB_request->get_tenants_building($building_id);
    //    echo "<Pre>";
    //    var_dump($receivers);
    //    echo "</Pre>";
    foreach ($receivers as $receiver) {

        /* Tracker */
        $user_tracker_id = $receiver["tenant_id"];
        $user_tracker_email = $receiver["email"];
        $id = $file_id;
        $history_type_id = 1;
        $subject        = "Bulletin Notification from spgmanagement.com";
        $bodyFull = $body2 . "<img border='0' src='https://www.spgmanagement.com/admin/custom/email_tracker.php?u=$user_tracker_id&id=$id&h=$history_type_id&e=$user_tracker_email&s=$subject' width='1' height='1' alt='I Live In' >";
        /* End of Tracker */
        $history_type_id = 2; //Download Attach
        if (!empty($attachment)) {
            $bodyFull = $bodyFull . "<p align='center'><a href='https://www.spgmanagement.com/admin/custom/download_file.php?fid=$slug&u=$user_tracker_id&id=$id&h=$history_type_id&e=$user_tracker_email&s=$subject'>Click here to download the attachment</a></p>";
        }
        $email_template = $template->emailTemplate($title, $subtitle, $receiver["full_name"], $body1, $bodyFull, $button_url, $button_content);
        $bodyFull = "";

        SendEmail('info@mgmgmt.ca', 'Info - spgmanagement.com', $receiver["email"], $receiver["full_name"], $subject, $email_template);

        //  echo "Email Send to ".$receiver["email"]."<br>";

    }
}


if ($_POST['action'] == 'get_bulletin_reading_status') {
    $bulletin_id = $_POST['bulletin_id'];
    $readed_tenant_infos = $DB_request->get_bulletin_reading_status($bulletin_id);
    $feedback = array();

    $feedback['data_count'] = sizeof($readed_tenant_infos);

    $data_content = array();
    foreach ($readed_tenant_infos as $row) {
        $temp = array();
        $temp['full_name'] = $row['full_name'];
        $temp['username'] = $row['username'];
        $temp['last_login_time'] = $row['last_login_time'];
        array_push($data_content, $temp);
    }
    $feedback['data_content'] = $data_content;

    echo json_encode($feedback);
}


if ($_POST['action'] == 'close_bulletin') {
    $bulletin_id = intval($_POST['bulletin_id']);
    $result = $DB_request->close_bulletin($bulletin_id);
    $feedback['status'] = $result == true ? "success" : "failed";
    echo json_encode($feedback);
}



function parse_uk_date($uk_date)
{
    if (empty($uk_date)) {
        return '';
    }
    $date_arr = explode('/', $uk_date);
    return $date_arr[2] . $date_arr[1] . $date_arr[0];
}