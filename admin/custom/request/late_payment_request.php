<?php

/**
 * This php file is to auto generate the system request for 'late payment' and send notification to tenants
 * User: t.e.chen
 * Date: 2018-01-29
 */
require_once('../../pdo/dbconfig.php');
include_once("../../pdo/Class.Request.php");
$DB_request = new Request($DB_con);

$late_payment_list = $DB_request->get_late_payment_list();

// works on 3rd every month
if (date('j') == '3') {
    foreach ($late_payment_list as $row) {
        $building_id = $row['building_id'];
        $apartment_id = $row['apartment_id'];
        $tenant_ids = explode(',', $row['tenant_ids']);
        $current = date('Y-m-d H:i:s');

        // add one system request into request module
        $request_id = $DB_request->add_request_info($building_id, $apartment_id, 'apartment', null, 46, 1, null, null, null, 'The Rental Payment is Overdue', $current, null, 0);
        // add communication for the request
        $DB_request->add_one_communication($request_id, null, 'SYSTEM : The Rental Payment is Overdue', $current, 1);

        # add assignees for the request
        $assignee_queue = array();

        // add related tenants
        foreach ($tenant_ids as $one) {
            $DB_request->add_request_assignee($request_id, $one, '2000-01-01 00:00:00', 1, 1, 1);
        }

        //add the responsible employees according to the building_id and the request_type_id
        //which methods will be used to notify default employees.
        $notify_default_by_email = 0;
        $notify_default_by_sms = 0;
        $notify_default_by_voice = 0;
        $notify_methods = explode(",", $DB_request->get_default_ntf_methods($building_id, 46)['ntf_methods']);
        if (in_array("1", $notify_methods)) {
            $notify_default_by_email = 1;
        }
        if (in_array("2", $notify_methods)) {
            $notify_default_by_sms = 1;
        }
        if (in_array("3", $notify_methods)) {
            $notify_default_by_voice = 1;
        }


        //add administrator
        $administrators = $DB_request->get_administrator_for_building($building_id);
        foreach ($administrators as $admin) {
            $employee_id = $admin['employee_id'];
            if (!in_array($employee_id, $notification_queue)) {
                $DB_request->add_request_assignee($request_id, $employee_id, '2000-01-01 00:00:00', $notify_default_by_email, $notify_default_by_sms, $notify_default_by_voice);
                array_push($notification_queue, $employee_id);
            }
        }


        //todo: include administrator
        $responsible_employees = $DB_request->get_default_employees($building_id, 46);
        foreach ($responsible_employees as $employee) {
            $responsible_employee_id = $employee['employee_id'];
            $DB_request->add_request_assignee($request_id, $responsible_employee_id, '2000-01-01 00:00:00', $notify_default_by_email, $notify_default_by_sms, $notify_default_by_voice);
        }
    }
}


// works on everyday, include 3rd
require "sendSMSEmail.php";
require "Class.SendSMS.php";

foreach ($late_payment_list as $row) {
    $building_id = $row['building_id'];
    $building_info = $DB_request->get_building_info($building_id);
    $building_name = $building_info['building_name'];
    $building_address = $building_info['address'];

    $apartment_id = $row['apartment_id'];

    $tenant_ids = explode(',', $row['tenant_ids']);

    //send the notification to the tenants
    foreach ($tenant_ids as $tenant_id) {
        $tenant_info = $DB_request->get_user_info($tenant_id);
        $full_name = $tenant_info['full_name'];
        $phone_number = $tenant_info['mobile'];
        $email = $tenant_info['email'];
        //send the sms
        $content = "Dear " . $full_name . ",\nYour rental payment for unit in " . $building_name . " is overdue, Please pay your rental payment as soon as possible\n-- spgmanagement.com";
        $phone_number = $this->formalize_telephone($phone_number);
        //SendSMS($phone_number,$content);
    }
}

?>

<?php
function formalize_telephone($original_tele)
{
    $formal_tele = trim($original_tele);
    $formal_tele = str_replace(' ', '', $formal_tele);
    $formal_tele = str_replace('-', '', $formal_tele);
    if (strlen($formal_tele) == 10)
        $formal_tele = '1' . $formal_tele;
    return $formal_tele;
}
?>