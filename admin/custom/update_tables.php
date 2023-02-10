<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(120);
function update_tables($id, $field_name)
{

	$$field_name = $id;
	//   die("field_name=$field_name id=$id lease_id=$lease_id apartment_id=$apartment_id");
	//    if (session_status() == PHP_SESSION_NONE) {
	//        session_start();
	//    }
	if (strpos(getcwd(), "custom") == false) {
		$path = "../pdo/";
	} else {
		$path = "../../pdo/";
	}
	$file = $path . 'dbconfig.php';

	include($file);

	$where = " and true";
	$whereApp = " and true";
	$WhereDate = " and CURDATE()<=LI.start_date";
	if (!empty($lease_id)) {
		$where = " and LI.lease_id=" . $lease_id;
		$whereApp = " and true";
	} else {
		$where = " and true ";
	}
	if (!empty($apartment_id)) {
		$where = " and LI.apartment_id=" . $apartment_id;
		$whereApp = " and APP.apartment_id=" . $apartment_id;
	} else {
		$where = " and true ";
	}
	//    $where .= $WhereDate;

	// 1 Active
	$Sql[] = "UPDATE lease_infos LI SET lease_status_id=1 WHERE CURDATE() BETWEEN start_date AND end_date AND lease_status_id NOT IN (4,5,6,7,9,10,12) AND CURDATE() BETWEEN start_date AND DATE_SUB(end_date, INTERVAL
(SELECT renewal_notification_day FROM company_infos WHERE id=(SELECT company_id FROM employee_infos EMP WHERE EMP.employee_id=LI.employee_id))-1 DAY)" . $where . ";";

	// 1 Active
	$Sql[] = "UPDATE lease_infos LI SET lease_status_id=1 WHERE CURDATE() BETWEEN start_date AND end_date AND lease_status_id NOT IN (4,5,6,9,10,12)" . $where . ";";

	// 11 Upcoming
	//	$Sql[] = "UPDATE lease_infos LI SET lease_status_id=2 WHERE CURDATE()<start_date AND lease_status_id NOT IN (4,5,6)" . $where.";"; // Query when there was no Upcoming - renewed
	$Sql[] = "UPDATE lease_infos LI SET lease_status_id=11 WHERE CURDATE()<start_date AND lease_status_id NOT IN (4,5,6,12)" . $where . ";";

	// 2 Upcoming Renewed
	$Sql[] = "UPDATE lease_infos SET lease_status_id = 2 WHERE id IN (SELECT * FROM (SELECT lease_renewed_id FROM lease_infos WHERE lease_renewed_id IS NOT NULL) AS X) AND lease_status_id NOT IN (1,3,4,5,6,12) AND CURDATE() < start_date;";

	// 7 Pending Renewal
	$Sql[] = "UPDATE lease_infos LI SET lease_status_id=7 WHERE lease_status_id NOT IN  (3,4,5,6,8,9,10,12) AND lease_status_id=1 AND CURDATE() BETWEEN DATE_SUB(end_date, INTERVAL
(SELECT renewal_notification_day FROM company_infos WHERE id = (SELECT company_id FROM employee_infos EMP WHERE EMP.employee_id=LI.employee_id)) DAY) AND end_date ;";

	// 9 - Active - Non Renewal
	//    $Sql[] = "UPDATE lease_infos LI SET lease_status_id=9 WHERE renewal=3 AND CURDATE() BETWEEN start_date AND end_date AND lease_status_id NOT IN (4,5,6,10)" . $where.";";

	// 8 Active - Auto Renewal
	$Sql[] = "UPDATE lease_infos LI SET lease_status_id=8 WHERE NOW() >= DATE_ADD(renewal_notice_date, INTERVAL 30 DAY) AND lease_status_id NOT IN (9,3,4,5,6,12) " . $where . ";";

	// 3 Expired
	$Sql[] = "UPDATE lease_infos LI SET lease_status_id=3 WHERE CURDATE()>end_date AND lease_status_id NOT IN (4,5,6,12)" . $where . ";";
	// 4,5,6 Cancelled, Manually Terminated, Terminated


	//all of renovation
	$Sql[] = "UPDATE apartment_infos APP SET apartment_status=renovation_status WHERE renovation_status<4" . $whereApp . ";";

	//vacant
	//     $Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
	// SET apartment_status=5 WHERE  apartment_status != 8 and renovation_status=4 AND (SELECT COUNT(*) FROM lease_infos LI WHERE CURDATE()<end_date AND lease_status_id IN(1,7) AND APP.apartment_id=LI.apartment_id)=0 " . $where.";";
	$Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
SET apartment_status=5 WHERE apartment_status NOT IN (6,8) and (SELECT COUNT(*) FROM lease_infos LI WHERE start_date > CURDATE() + INTERVAL 30 DAY AND lease_status_id IN(1,7) AND APP.apartment_id=LI.apartment_id) = 0 " . $where . ";";

	//UpComing Vacany
	//    $Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
	//SET apartment_status=8 WHERE renovation_status=4 AND renewal=3 AND CURDATE() BETWEEN start_date AND end_date AND APP.apartment_id=LI.apartment_id" . $where.";";

	$Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
SET apartment_status=8 WHERE LI.lease_status_id in (7) AND CURDATE() BETWEEN start_date AND end_date AND APP.apartment_id=LI.apartment_id" . $where . ";";


	$Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
	SET apartment_status=9 WHERE LI.lease_status_id in (9) AND CURDATE() BETWEEN start_date AND end_date AND APP.apartment_id=LI.apartment_id and renewal=0" . $where . ";";


	$Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
SET apartment_status=6 WHERE lease_status_id IN (1,11,8,10);";

	//Pending Renewal
	$Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
SET apartment_status=7 WHERE renovation_status=4 AND lease_status_id=7 AND APP.apartment_id=LI.apartment_id" . $where . ";";


	/* Start Available Date */

	// 1Active , 2 Renewed-Upcoming, 3 Expired, 4 Cancelled, 5 Manually Terminated, 6 Terminated, 7 Active, 8 Active - Auto Renewal, 9 Active - Non Renewal, 10 - Active - Renewed, 11 Upcoming Lease
	//Available Date  - Pending Renewal
	//	$Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
	//SET APP.available_date=DATE_ADD(move_out_date, INTERVAL 1 DAY) WHERE lease_status_id = 7  AND CURDATE() BETWEEN start_date AND end_date " . $where.";";
	//die(var_dump($Sql));


	// Set to Vacant if there is no active lease
	$Sql[] = "UPDATE apartment_infos AP2 SET AP2.apartment_status=5 , available_date=CONCAT(YEAR(CURDATE()), '-01-01')
WHERE AP2.apartment_id NOT IN
(SELECT AP.apartment_id FROM (SELECT * FROM apartment_infos) AS AP LEFT JOIN lease_infos LI ON AP.apartment_id=LI.apartment_id WHERE lease_status_id NOT IN(3,4,5,6));";


	//Available Date if there is no lease (VACANT)
	$Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
SET APP.available_date=CONCAT(YEAR(CURDATE()), '-01-01') where apartment_status=5";

	//Available Date
	$Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
SET APP.available_date=DATE_ADD(terminate_date, INTERVAL 1 DAY) WHERE lease_status_id IN (5,6) AND CURDATE() BETWEEN start_date AND end_date AND LI.start_date=(SELECT MAX(start_date) FROM lease_infos WHERE apartment_id=APP.apartment_id) " . $where . ";";

	//Available Date
	$Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
SET APP.available_date=DATE_ADD(move_out_date, INTERVAL 1 DAY) WHERE lease_status_id IN (1,2,3,4)  AND
LI.start_date=(SELECT MAX(start_date) FROM lease_infos WHERE apartment_id=APP.apartment_id AND lease_status_id NOT IN (5,6,7,8,9,11)) " . $where . ";"; //AND CURDATE() BETWEEN start_date AND end_date


	//Available Date
	$Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
SET APP.available_date=DATE_ADD(move_out_date, INTERVAL 1 DAY) WHERE lease_status_id IN (7,8,9,11)  AND
LI.start_date=(SELECT MAX(start_date) FROM lease_infos WHERE apartment_id=APP.apartment_id AND lease_status_id NOT IN (1,2,3,4,5,6)) " . $where . ";"; //AND CURDATE() BETWEEN start_date AND end_date


	//Add Lease info to the apartment
	$Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
SET APP.current_lease_status_id=LI.lease_status_id, APP.current_lease_id=LI.id WHERE lease_status_id IN (3,4,5,6)  AND
LI.start_date=(SELECT MAX(start_date) FROM lease_infos WHERE apartment_id=APP.apartment_id AND lease_status_id IN (3,4,5,6)) " . $where . ";";

	//Add Lease info to the apartment
	$Sql[] = "UPDATE apartment_infos APP LEFT JOIN lease_infos LI ON APP.apartment_id=LI.apartment_id
SET APP.current_lease_status_id=LI.lease_status_id, APP.current_lease_id=LI.id WHERE lease_status_id IN (1,2,7,8,9,10,11)  AND
LI.start_date=(SELECT MAX(start_date) FROM lease_infos WHERE apartment_id=APP.apartment_id AND lease_status_id IN (1,2,7,8,9,10,11)) " . $where . ";"; //AND CURDATE() BETWEEN start_date AND end_date


	foreach ($Sql as $SelectSql) {
		// echo "<p>".htmlentities($SelectSql)."</p>\n";
		$statement = $DB_con->prepare($SelectSql);
		$result = $statement->execute();
		//          echo "<p> Numbe of rows affected ".$statement->rowCount();
		//		echo "</p><hr>";
	}
	//    echo "<!--Tables are updated.-->";
	if (!empty($lease_id)) {
		//       echo "lease_id=$lease_id";
	}
	if (!empty($apartment_id)) {
		//       echo "apartment_id=$apartment_id";
	}
	$DB_con = null;
	//die();
}

update_tables("", "");
