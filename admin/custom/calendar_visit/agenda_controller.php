<?php include("../../../pdo/dbconfig.php");
include_once("../../../pdo/Class.Calendar.php");
$DB_calendar = new Calendar($DB_con);

?>
<?php
session_start();
$employee_id = $_SESSION["employee_id"];
$time_limit = date('Y-m-d', strtotime('today + 3 year'));
$out = array();

$result_office = array();
$result_maintenance = array();
$results_visit = array();

//office events
//$result_office = $DB_calendar->get_personal_office_maintenance_events("office",$employee_id);
foreach ($DB_calendar->get_personal_office_maintenance_events("office", $employee_id) as $row) {
    $event_id = $row['id'];
    $event_name = $row['event_name'];
    $event_date = $row['event_date'];
    $event_frequency = $row['event_frequency'];
    $event_frequency_type = $row['event_frequency_type'];
    $event_type = $row['event_type'];
    $contact = $row["contact_number"];
    $userName = "N/A";
    $companyName = "N/A";

    if (!empty($row["event_created_by_user_id"])) {
        $userId = $row["event_created_by_user_id"];
        $userName = $DB_employee->getEmployeeName($userId);
        $userCompanyId =  $DB_employee->getCompanyId($userId);
        $companyName = $DB_company->getName($userCompanyId);
    }

    $row['contact'] = $contact;
    $row['username'] = $userName;
    $row['company'] = $companyName;
    $row['event_date'] = date_format(date_create($row['event_date']), "Y-m-d");

    array_push($result_office, $row);
}


//maintenance events
foreach ($DB_calendar->get_personal_office_maintenance_events("maintenance", $employee_id) as $row) {
    $event_id = $row['id'];
    $event_name = $row['event_name'];
    $event_date = $row['event_date'];
    $event_frequency = $row['event_frequency'];
    $event_frequency_type = $row['event_frequency_type'];
    $event_type = $row['event_type'];

    $userName = "N/A";
    $companyName = "N/A";

    if (!empty($row["event_created_by_user_id"])) {
        $userId = $row["event_created_by_user_id"];
        $userName = $DB_employee->getEmployeeName($userId);
        $userCompanyId =  $DB_employee->getCompanyId($userId);
        $companyName = $DB_company->getName($userCompanyId);
    }

    $row['contact'] = $contact;
    $row['username'] = $userName;
    $row['company'] = $companyName;
    $row['event_date'] = date_format(date_create($row['event_date']), "Y-m-d");

    array_push($result_maintenance, $row);
}


//visit events
foreach ($DB_calendar->get_personal_visit_events_by_employee($employee_id) as $row) {
    $event_id = $row['id'];
    $event_name = $row['event_name'];
    $result = $DB_calendar->get_all_bookings($event_id);

    $userName = "N/A";
    $companyName = "N/A";

    if (!empty($row["event_created_by_user_id"])) {
        $userId = $row["event_created_by_user_id"];
        $userName = $DB_employee->getEmployeeName($userId);
        $userCompanyId =  $DB_employee->getCompanyId($userId);
        $companyName = $DB_company->getName($userCompanyId);
    }


    $row['contact'] = $contact;
    $row['username'] = $userName;
    $row['company'] = $companyName;

    array_push($results_visit, $row);
}

$output = array_merge($result_office, $result_maintenance);
$data["data"] = $output;
echo  json_encode($data);
//echo json_encode($out);