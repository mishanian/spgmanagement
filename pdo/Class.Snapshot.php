<?php
// namespace PHPMaker2023\spgmanagement;
// use \Crud;
class Snapshot
{

    private $crud;
    private $lang3Words; // French
    private $lang2Words; // Chinese
    private $langKeyIds;

    public function __construct($DB_con)
    {
        $this->crud = new Crud($DB_con);

        $langsKeyId = array();
        $this->lang2Words = array();
        $this->lang3Words = array();

        foreach ($this->getLanguages() as $language) {
            $this->langsKeyId[$language["id"]] = $language["name"];
        }

        // Read the language translationn files from the server - french and chinese
        $this->readTranslationFile("fr");
        $this->readTranslationFile("ch");
    }

    //end of constructor

    public function readTranslationFile($language = "en")
    {

        if ($language == "en") {
            return true;
        }

        $fileName = __DIR__ . "/../translate_$language.xml";

        $translation = simplexml_load_file($fileName);

        foreach ($translation->translation as $translation) {
            $string = (string) strval($translation["string"]);
            $value = (string) strval($translation["value"]);
            if ($language == "fr") {
                $this->lang2Words[$string] = $value;
            } else if ($language == "ch") {
                $this->lang3Words[$string] = $value;
            }
        }
    }

    public function getLanguages()
    {
        try {
            $this->crud->query("SELECT * FROM languages");
            $row = $this->crud->resultSet();
            return $row;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function echot($word)
    {
        $word = trim($word);

        if (isset($_SESSION["lang_set"])) {
            $langSetId = $_SESSION["lang_set"];
        }

        if (isset($_SESSION["ilivein_LanguageId"])) {
            switch ($_SESSION["ilivein_LanguageId"]) {
                case "zh-CN":
                    // Chinese
                    $languageSelected = 3;
                    break;
                case "fr-ca":
                    // French
                    $languageSelected = 2;
                    break;
                default:
                    $languageSelected = 1;
                    // English
                    break;
            }
            $langSetId = $languageSelected;
        }

        if (isset($langSetId)) {
            $langVariable = "lang" . $langSetId . "Words"; // Setting the name for the Variable Variable
            if (!isset($this->$langVariable)) { // Words Language Variable for the language that is set in the session
                return $word;
            }
            foreach ($this->$langVariable as $requestedWord => $langword) { // $langword is the word from the selected language in the session to match with the word that needs to be printed $word
                if (strcasecmp(trim($requestedWord), $word) == 0) {
                    return $langword;
                }
            }
            return $word;
        } else {
            // Return the english word as no other language is set by the user in the frontend
            return $word;
        }
    }

    public function getOwnerNum($id, $role)
    {
        if (PHPMaker2023\spgmanagement\CurrentUserLevel() != -1) {
            $Role_ID = " " . $role . "_id=$id";
        } else {
            $Role_ID = " true";
        }
        try {
            $this->crud->query("SELECT * FROM owner_infos WHERE $Role_ID");
            $row = $this->crud->resultSet();
            $num = $this->crud->rowCount();
            return $num;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getBuildingNum($id, $role)
    {
        if (PHPMaker2023\spgmanagement\CurrentUserLevel() != -1) {
            $Role_ID = " " . $role . "_id=$id";
        } else {
            $Role_ID = " true";
        }
        try {
            $this->crud->query("SELECT * FROM building_infos WHERE $Role_ID");
            $row = $this->crud->resultSet();
            $num = $this->crud->rowCount();
            return $num;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getUnitNum($id, $role)
    {
        if (PHPMaker2023\spgmanagement\CurrentUserLevel() != -1) {
            $Role_ID = " " . $role . "_id=$id";
        } else {
            $Role_ID = " true";
        }
        try {
            $this->crud->query("SELECT * FROM apartment_infos WHERE $Role_ID");
            $row = $this->crud->resultSet();
            $num = $this->crud->rowCount();
            return $num;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getLeaseNum($id, $role)
    {
        if (PHPMaker2023\spgmanagement\CurrentUserLevel() != -1) {
            $Role_ID = " " . $role . "_id=$id";
        } else {
            $Role_ID = " true";
        }

        try {
            $this->crud->query("SELECT * FROM lease_infos WHERE  $Role_ID"); //            lease_status_id in (1,7,8,9)
            $row = $this->crud->resultSet();
            $num = $this->crud->rowCount();
            return $num;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getManagerNum($id, $role)
    {
        if (PHPMaker2023\spgmanagement\CurrentUserLevel() != -1) {
            $Role_ID = " " . $role . "_id=$id";
        } else {
            $Role_ID = " true";
        }
        if (PHPMaker2023\spgmanagement\CurrentUserLevel() == 2) {
            $Role_ID = " false";
        } //Owner
        try {
            $this->crud->query("SELECT * FROM employee_infos WHERE company_id in (select company_id from employee_infos where $Role_ID)");
            $this->crud->bind('$id', $id);
            $row = $this->crud->resultSet();
            $num = $this->crud->rowCount();
            return $num;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getTenantNum($id, $role)
    {
        if (PHPMaker2023\spgmanagement\CurrentUserLevel() != -1) {
            $Role_ID = " " . $role . "_id=$id";
        } else {
            $Role_ID = " true";
        }
        try {
            $this->crud->query("SELECT * FROM view_tenant_infos WHERE $Role_ID"); //            active_id=1

            $row = $this->crud->resultSet();
            $num = $this->crud->rowCount();
            return $num;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getLatePaymentNum($id, $role)
    {
        if (PHPMaker2023\spgmanagement\CurrentUserLevel() != -1) {
            $Role_ID = " " . $role . "_id=$id";
        } else {
            $Role_ID = " true";
        }
        try {

            $today = date('Y-m-d', strtotime('today'));

            $this->crud->query("SELECT * FROM rental_payments WHERE outstanding > 0 AND due_date >= '" . date("Y-m-1") . "' AND due_date <='" . date("Y-m-t") . "' AND $Role_ID");
            //			$this->crud->bind(':today', $today);
            $row = $this->crud->resultSet();
            $num = $this->crud->rowCount();

            return $num;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getUnpaidPaymentNum($id, $role)
    {
        if (PHPMaker2023\spgmanagement\CurrentUserLevel() != -1) {
            $Role_ID = " " . $role . "_id=$id";
        } else {
            $Role_ID = " true";
        }
        try {
            date_default_timezone_set('America/New_York');
            $today = date('Y-m-d', strtotime('today'));
            //echo "SELECT * FROM rental_payments WHERE outstanding > 0 AND due_date <= '".date("Y-m-t")."'  AND $Role_ID";
            $this->crud->query("SELECT * FROM rental_payments WHERE payment_status_id=1 AND due_date <= '" . date("Y-m-t") . "'  AND $Role_ID");
            //			$this->crud->bind(':today', $today);
            $row = $this->crud->resultSet();
            $num = $this->crud->rowCount();

            return $num;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getUnreadRequestNum($id, $role)
    {
        if (PHPMaker2023\spgmanagement\CurrentUserLevel() != -1) {
            $Role_ID = " " . $role . "_id=$id";
        } else {
            $Role_ID = " true";
        }

        try {
            $this->crud->query("SELECT count(*) FROM request_assignees WHERE user_id = :user_id AND request_assignees.last_access_time < ( ifnull((SELECT request_communications.entry_date FROM request_communications WHERE request_communications.request_id = request_assignees.request_id ORDER BY request_communications.entry_date DESC limit 1),(SELECT request_infos.entry_datetime FROM request_infos WHERE request_infos.id = request_assignees.request_id )))");
            $this->crud->bind("user_id", $id);
            return $this->crud->resultSingle()['count(*)'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getOpenRequestNum($id, $role)
    {
        if (PHPMaker2023\spgmanagement\CurrentUserLevel() != -1) {
            $Role_ID = " " . $role . "_id=$id";
        } else {
            $Role_ID = " true";
        }
        return 0;
    }

    public function getPendingRenewalNum($id, $role)
    {
        try {
            $today = date('Y-m-d', strtotime('today'));
            $time_limit = date('Y-m-d', strtotime("+30 days"));
            if (PHPMaker2023\spgmanagement\CurrentUserLevel() != -1) {
                $Role_ID = " " . $role . "_id=$id";
            } else {
                $Role_ID = " true";
            }
            $this->crud->query("SELECT * FROM lease_infos WHERE $Role_ID AND lease_renewed_id is NULL AND end_date >= :today AND end_date <= :time_limit");
            $this->crud->bind(':today', $today);
            $this->crud->bind(':time_limit', $time_limit);
            $row = $this->crud->resultSet();
            $num = $this->crud->rowCount();
            return $num;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getPotentialTenantNum($id, $role)
    {
        if ($role == "owner") {
            $role = "employee";
        }
        try {
            if (PHPMaker2023\spgmanagement\CurrentUserLevel() != -1) {
                $Role_ID = " " . $role . "_id=$id";
            } else {
                $Role_ID = " true";
            }
            $this->crud->query("SELECT * FROM view_questions_and_visits WHERE $Role_ID and last_followup_type_id = 1");
            $row = $this->crud->resultSet();
            $num = $this->crud->rowCount();
            return $num;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //-------------------------------- 2018.01.05 (Tann) recent request infos in home page -------------------------------

    public function get_current_bulletins($user_id)
    {
        try {
            $this->crud->query("SELECT bulletins.id AS bulletin_id, message_title FROM bulletins WHERE find_in_set(bulletins.building_id,(SELECT building_ids FROM employee_infos WHERE employee_id = :employee_id)) LIMIT 10");
            $this->crud->bind(":employee_id", $user_id);
            return $this->crud->resultSet();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


    public function get_recent_request($user_id)
    {
        try {
            $this->crud->query("SELECT request_infos.id,request_types.name AS request_type,request_status.name as request_status,message,entry_datetime AS created_time,
                                        (if(request_infos.status_id IN (3,4,14,15,16,17,18,19,20,21,22),'closed','open')) AS issue_status,
                                        (SELECT building_name FROM building_infos WHERE building_infos.building_id = request_infos.building_id) AS building_name,
                                        (SELECT issue_past_after_days FROM building_infos WHERE building_infos.building_id = request_infos.building_id) AS issue_past_after_days,
                                        (ifnull((SELECT entry_date FROM request_communications WHERE request_id = request_infos.id  AND if_seen_by_tenant = 1 ORDER BY entry_date DESC limit 1),entry_datetime )) AS last_update_time
                                        FROM request_infos,request_types, request_status WHERE request_infos.id IN ( SELECT request_id  FROM request_assignees WHERE user_id = :user_id)
                                        AND request_types.id = request_infos.request_type_id
                                        AND request_status.id = request_infos.status_id
                                        ORDER BY request_infos.entry_datetime  DESC LIMIT 30");
            $this->crud->bind(":user_id", $user_id);

            $rows = $this->crud->resultSet();

            $recent_rows = array();
            $count = 0;

            foreach ($rows as $r) {
                if ($count >= 10)
                    break;

                $issue_status = $r['issue_status'];
                //$issue_past_after_days = $r['issue_past_after_days'];
                //$last_update_time = date('Y-m-d', strtotime($r['last_update_time']));
                //$time_flag = strtotime("$last_update_time + $issue_past_after_days day"); //timestamp for past issue list

                if ($issue_status == 'closed')
                    continue;

                array_push($recent_rows, $r);
                $count += 1;
            }

            return $recent_rows;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // Language translation in Frontend

    public function getNoOfUnReadAtt()
    {
        try {
            $this->crud->query("SELECT count(*) FROM attachment_infos where is_read=0"); //" and assign_employee_id=".$_SESSION['employee_id']);
            $NoOfUnReadAtt = $this->crud->resultField();
            return $NoOfUnReadAtt;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}