<?php

namespace PHPMaker2023\spgmanagement;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


use \PDO;
use \Vendor;
use \Tenant;

// die(var_dump($_SESSION['UserLevel']));

function SelectRecords($field_name)
{
    $CurrentTableObject = CurrentTable();
    ////////////////////////////////////////  Start of Admin ///////////////////////////////////////////////
    if ($_SESSION['admin_id']) {
        $newFilter = "company_id=" . $_SESSION['company_id'];
        // echo "Table=" . $CurrentTableObject->TableName . " field_name=$field_name<br>";
        return $newFilter;
    }
    ////////////////////////////////////////  End of Admin ///////////////////////////////////////////////
    global $DB_con;
    $root = dirname(__DIR__);
    include_once($root . '/../pdo/dbconfig.php');
    include_once($root . '/../pdo/Class.Vendor.php');
    include_once($root . '/../pdo/Class.Tenant.php');

    $SelectSql = "SET @@session.group_concat_max_len = 1048576;";
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
    ////////////////////////////////////////  Start of Super Admin ///////////////////////////////////////////////

    if (CurrentUserLevel() == -1) {
        $newFilter = "true";
    } else {

        ////////////////////////////////////////  End of Super Admin ///////////////////////////////////////////////
        ////////////////////////////////////////  Start of Property Manager ///////////////////////////////////////////////
        $newFilter = " ";
        if (!empty($_SESSION["company_id"])) {
            $EmpSql = "select GROUP_CONCAT(DISTINCT employee_id SEPARATOR ', ') AS Company_Employees from employee_infos where company_id=" . $_SESSION["company_id"];
            $statement = $DB_con->prepare($EmpSql);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $Company_Employees = $result[0]["Company_Employees"];
        }
        if (in_array(CurrentUserLevel(), $_SESSION['ManagerLevelIDs']) || in_array(CurrentUserLevel(), array(10, 16, 11, 18, 19, 21, 26))) { //|| CurrentUserLevel()==14
            $newFilter = " false";

            //var_dump(CurrentMasterTable());
            //var_dump(CurrentDetailTable());


            $EmpPer = getEmployeePermission($_SESSION['employee_id']);
            $building_ids = $EmpPer['building_ids'];
            $SearchEmployees = $_SESSION['employee_id'];

            if ($EmpPer['admin_id'] == 1) {
                $SearchEmployees = $Company_Employees;
            }
            //       echo "TableName=".$CurrentTableObject->TableName." field_name=$field_name<br>";
            //        die($CurrentTableObject->TableName);
            if (!empty($CurrentTableObject->TableName)) {
                switch ($CurrentTableObject->TableName) {
                    case "company_infos":
                        $newFilter = "id=" . $_SESSION['company_id'];
                        break;
                    case "vendor_infos":
                        $newFilter = "company_id=" . $_SESSION['company_id'];
                        break;
                    case "holding_infos":
                    case 'bulletins':
                    case "employee_infos":
                        $newFilter = "employee_id in (" . $SearchEmployees . ")";

                        if ($EmpPer['s_em_v_ac'] == 1 || $EmpPer['admin_id'] == 1) {
                            $newFilter = "employee_id in(" . $Company_Employees . ") AND active_id=1 ";
                        }

                        if ($EmpPer['s_em_v_al'] == 1 || $EmpPer['admin_id'] == 1) {
                            $newFilter = "employee_id in(" . $Company_Employees . ")  ";
                        }

                        if ($EmpPer['admin_id'] == 1) {
                            $newFilter = "employee_id in (" . $Company_Employees . ")";
                        }

                        break;

                    case 'building_infos':
                    case "floor_infos":
                    case 'apartment_infos':
                    case "parking_unit_infos":
                    case "storage_unit_infos":
                    case "appliance_infos":
                    case "equipment_infos":
                    case "rental_payments":

                        $newFilter = "building_id in (select building_id from building_infos where employee_id in (" . $SearchEmployees . "))";
                        if (!empty($building_ids)) {
                            $newFilter .= "   or building_id in (" . $building_ids . ")";
                        }
                        if ($EmpPer['s_de_v_al'] == 1 || $EmpPer['s_bl_v_al'] == 1 || $EmpPer['admin_id'] == 1) {
                            //    $newFilter = "employee_id in(" . $Company_Employees . ") ";
                            $newFilter = "building_id in (select building_id from building_infos where employee_id in (" . $Company_Employees . "))";
                            //       die($EmpPer['s_de_v_al']."=".$EmpPer['s_bl_v_al']."-".$EmpPer['admin_id']."-".$newFilter);
                        }
                        break;
                    case "owner_infos":
                        if ($EmpPer['s_ow_v_al'] == 1) {
                            $SearchEmployeesOwners = $Company_Employees;
                        } else {
                            $SearchEmployeesOwners = $SearchEmployees;
                        }
                        $newFilter = " owner_id in (select owner_id from owner_infos where employee_id in (" . $SearchEmployeesOwners . "))";
                        break;
                    case "lease_infos":
                        if ($EmpPer['s_le_v_al'] == 1) {
                            $SearchEmployeesLeases = $Company_Employees;
                        } else {
                            $SearchEmployeesLeases = $SearchEmployees;
                        }
                        $newFilter = " employee_id in (" . $SearchEmployeesLeases . ")";
                        break;
                    case "lease_payments":
                        $newFilter = "building_id in (select building_id from building_infos where employee_id in (" . $SearchEmployees . "))";
                        if ($EmpPer['s_le_v_py'] != 1 || $EmpPer['admin_id'] == 1) {
                            $WhereUpPay = " true";
                        } else {
                            $WhereUpPay = " true or invoice_type_id=2";
                        }
                        $newFilter = $newFilter . " $WhereUpPay";
                        //                die($newFilter);
                        break;

                    case "view_tenant_infos":
                    case "view_tenant_statement":
                    case "tenant_infos":
                        $newFilter = "employee_id in (" . $SearchEmployees . ")";

                        if ($EmpPer['s_tt_v_ac'] == 1 || $EmpPer['admin_id'] == 1) {
                            $SearchEmployees = $Company_Employees;
                            $newFilter = "employee_id in(" . $Company_Employees . ") AND active_id=1 ";
                        }

                        if ($EmpPer['s_tt_v_al'] == 1 || $EmpPer['admin_id'] == 1) {
                            $SearchEmployees = $Company_Employees;
                            $newFilter = "employee_id in(" . $Company_Employees . ")  ";
                        }

                        if ($EmpPer['admin_id'] == 1) {
                            $SearchEmployees = $Company_Employees;
                            $newFilter = "employee_id in (" . $Company_Employees . ")";
                        }

                        break;


                        //            case "floor_infos":
                        //            case "parking_unit_infos":
                        //            case "storage_unit_infos":
                        //                $newFilter = "building_id in (select building_id from building_infos where employee_id in (" . $SearchEmployees . "))";
                        //                break;


                    case "request_infos_old":
                        $AssignToSQL = "SELECT GROUP_CONCAT(DISTINCT request_id SEPARATOR ', ') AS request_ids FROM request_communications WHERE assign_employee_id=" . $_SESSION["UserID"];
                        $statement = $DB_con->prepare($AssignToSQL);
                        $statement->execute();
                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                        $request_ids = $result[0]["request_ids"];
                        $newFilter = "employee_id in ($SearchEmployees)";
                        if (!empty($request_ids) && $field_name == "") {
                            $newFilter .= " or id in ($request_ids)";
                        }
                        break;

                    case "view_questions_and_visits":
                        $newFilter = "company_id =" . $_SESSION['company_id'];
                        break;
                    case 'project_infos':
                    case 'contract_infos':
                    case "invoice_infos":
                    case "payment_infos":
                        if ($CurrentTableObject->TableName == "project_infos" && !empty($_GET['vendor_id'])) {
                            $vendor_id = $_GET['vendor_id'];
                            $DB_vendor = new \Vendor($DB_con);
                            $project_ids = $DB_vendor->getVendorProjects($vendor_id);
                            if (!empty($project_ids)) {
                                $newFilter = " project_id in (" . implode(",", $project_ids) . ") ";
                            }
                        } else {
                            //$newFilter = "employee_id in ($SearchEmployees)";
                            $newFilter = "company_id =" . $_SESSION['company_id'];
                            $SQL = "select GROUP_CONCAT(DISTINCT vendor_id SEPARATOR ', ') AS vendor_ids from vendor_infos where company_id=" . $_SESSION["company_id"];
                            $VendorOfCompany = ExecuteRow($SQL);
                            //var_dump($VendorOfCompany);
                            if (!empty($VendorOfCompany[0])) {
                                $VendorOfCompanyfilter = " employee_id in (" . implode(",", $VendorOfCompany) . ") ";
                                //$newFilter = ($newFilter <> "") ? "( $newFilter  OR  $VendorOfCompanyfilter )" : $VendorOfCompanyfilter;
                            }
                        }
                        break;

                    case "attachment_infos":
                        /*
                $newFilter = "employee_id in ($SearchEmployees)";
                $sql="select GROUP_CONCAT(DISTINCT vendor_id SEPARATOR ', ') AS vendor_ids from vendor_infos where company_id=" . $_SESSION["company_id"];
                $VendorOfCompany=ExecuteRow($sql);
                if (!empty($VendorOfCompany[0])){
                    $VendorOfCompanyfilter=" employee_id in (".implode(",",$VendorOfCompany).") ";
                    $newFilter = ($newFilter <> "") ? "( $newFilter  OR $VendorOfCompanyfilter )" : $VendorOfCompanyfilter;
                }
                */
                        $newFilter = "company_id =" . $_SESSION['company_id'];
                        break;

                    case "view_project_vendor":
                        $newFilter = "employee_id in ($SearchEmployees)";
                        break;

                    case "view_vendor_projects":
                        $newFilter = " vendor_id=" . $_GET['fk_vendor_id']; //$vendor_id";
                        //die();
                        break;
                    case "paintcode_infos":
                    case "deposits":
                    default:
                        $newFilter = "employee_id in ($SearchEmployees)";
                        break;
                }
            }

            if ($EmpPer['s_tt_v_ac'] == 1 || $EmpPer['s_tt_v_al'] == 1 || $EmpPer['admin_id'] == 1) {
                $SearchEmployees = $Company_Employees;
            }

            switch ($field_name) {
                case "company_id":
                    $newFilter = "id=" . $_SESSION['company_id'];
                    break;
                case "building_id":
                case "apartment_id":
                case "floor_id":
                    $newFilter = " building_id in (select building_id from building_infos where company_id=" . $_SESSION['company_id'] . ")"; //employee_id in (" . $SearchEmployees . ")) ";
                    //
                    if (!empty($building_ids)) {
                        $newFilter .= " or building_id in (" . $building_ids . ")";
                    }
                    if ($EmpPer['s_de_v_al'] == 1 || $EmpPer['s_bl_v_al'] == 1 || $EmpPer['admin_id'] == 1) {
                        $newFilter = " building_id in (select building_id from building_infos where company_id=" . $_SESSION['company_id'] . ")"; // employee_id in (" . $Company_Employees . ")) ";
                    }
                    //           echo "$EmpPer['s_tt_v_ac'] == 1  || $EmpPer['s_tt_v_al'] == 1 || $EmpPer['admin_id']".$newFilter."<br>";
                    // echo ("newFilter=$newFilter");
                    break;
                case "owner_id":
                    if ($EmpPer['s_ow_v_al'] == 1) {
                        $SearchEmployeesOwners = $Company_Employees;
                    } else {
                        $SearchEmployeesOwners = $SearchEmployees;
                    }
                    $newFilter = " owner_id in (select owner_id from owner_infos where employee_id in (" . $SearchEmployeesOwners . "))";
                    if (!empty($building_ids)) {
                        $newFilter .= " or owner_id in (select owner_id from building_infos where building_id in (" . $building_ids . "))";
                    }
                    break;
                case "tenant_id":

                    if (!empty($CurrentTableObject->TableName) && $CurrentTableObject->TableName == "deposits" && !empty($_GET['fk_id'])) {
                        $lease_id = $_GET['fk_id'];
                        $DB_tenant = new Tenant($DB_con);
                        $lease_tenant_ids = $DB_tenant->getTenantForLeaseId($lease_id);
                        $newFilter = "tenant_id in (" . implode(",", $lease_tenant_ids) . ")";
                    } else {
                        $newFilter = "employee_id in ($SearchEmployees)";
                    }
                    break;
                case "view_tenant_statement":
                    $newFilter = "employee_id in ($SearchEmployees)";
                    break;

                case "lease_payments":

                    if ($EmpPer['s_le_v_py'] != 1 || $EmpPer['admin_id'] == 1 || $EmpPer['s_le_v_py'] != 1) {
                        $WhereUpPay = " true";
                    } else {
                        $WhereUpPay = " true or invoice_type_id=2";
                    }
                    $newFilter = " $WhereUpPay";
                    //                die($newFilter);
                    break;
                case "employee_id":
                    $newFilter = "employee_id in ($SearchEmployees)";
                    break;

                case "proposal_infos":
                case "contract_infos":
                case "invoice_infos":
                case "payment_infos":
                case "attachment_infos":
                    $newFilter = "employee_id in ($SearchEmployees)";
                    $sql = "select GROUP_CONCAT(DISTINCT vendor_id SEPARATOR ', ') AS vendor_ids from vendor_infos where company_id=" . $_SESSION["company_id"];
                    $VendorOfCompany = ExecuteRow($sql);
                    if (!empty($VendorOfCompany)) {
                        $VendorOfCompanyfilter = " employee_id in (" . implode(",", $VendorOfCompany) . ") ";
                        $newFilter = ($newFilter <> "") ? "( $newFilter  OR $VendorOfCompanyfilter )" : $VendorOfCompanyfilter;
                    }
                    break;
            }
            //            die($CurrentTableObject->TableName);
            //echo $newFilter."<br>";
        }

        ////////////////////////////////////////  End of Property Manager ///////////////////////////////////////////////

        ////////////////////////////////////////  Start of Tenant ///////////////////////////////////////////////

        if (CurrentUserLevel() == 5) {
            $newFilter = " false";
            $tenant_id = $_SESSION['tenant_id'];
            list($lease_id, $building_id, $floor_id, $apartment_id, $employee_id, $tenant_name) = getTenantDetail($tenant_id);

            if (!empty($CurrentTableObject->TableName)) {
                switch ($CurrentTableObject->TableName) {
                    case "building_infos":
                        if (!empty($building_id)) {
                            $newFilter = " building_id in (" . $building_id . ")";
                        }
                        break;

                    case "apartment_infos":
                        if (!empty($apartment_id)) {
                            $newFilter = " apartment_id=" . $apartment_id;
                        }
                        break;

                    case "floor_infos":
                        if (!empty($floor_id)) {
                            $newFilter = " floor_id=" . $floor_id;
                        }
                        break;


                    case "parking_unit_infos":
                        if (!empty($parking_id)) {
                            $newFilter = " parking_id=" . $parking_id;
                        }
                        break;


                    case "storage_unit_infos":
                        if (!empty($storage_id)) {
                            $newFilter = " storage_id=" . $storage_id;
                        }
                        break;


                    case "lease_infos":
                        if (!empty($lease_id)) {
                            $newFilter = " id=" . $lease_id;
                        }
                        break;


                    case "deposits":
                        if (!empty($tenant_id)) {
                            $newFilter = " tenant_id=" . $tenant_id;
                        }
                        break;


                    case "tenant_infos":
                        $newFilter = " tenant_id in (" . $tenant_id . ")";
                        break;

                    case "request_infos_old":
                        $newFilter = " tenant_ids in (" . $tenant_id . ") ";
                        break;
                }
            }
            switch ($field_name) {
                case "building_id":
                    $newFilter = "building_id=" . $building_id;
                    break;
                case "apartment_id":
                    $newFilter = "apartment_id=" . $apartment_id;
                    break;
                case "floor_id":
                    $newFilter = "floor_id=" . $floor_id;
                    break;
                case "tenant_id":
                    $newFilter = "tenant_id=" . $tenant_id;
                    break;
                case "tenant_ids":
                    $newFilter = "FIND_IN_SET ('" . $tenant_id . "',tenant_ids)";
                    break;
                case "lease_id":
                    $newFilter = "FIND_IN_SET ('" . $tenant_id . "',tenant_ids)";
                    break;
                case "lease_payments":
                    if (!empty($lease_id)) {
                        $newFilter = " lease_id=" . $lease_id;
                    }
                    break;
                case "holding_id":
                    $newFilter = " employee_id=" . $lease_id;
                    break;
            }
        }

        ////////////////////////////////////////  End of Tenant ///////////////////////////////////////////////


        ////////////////////////////////////////  Start of Owner ///////////////////////////////////////////////

        if (CurrentUserLevel() == 2) {
            $newFilter = " false";
            $SearchOwners = $_SESSION['owner_id'];
            //echo ($CurrentTableObject->TableName." ".$field_name);
            if (!empty($CurrentTableObject->TableName)) {
                switch ($CurrentTableObject->TableName) {
                    case "building_infos":
                        $newFilter = "building_id in (select building_id from building_infos where owner_id in (" . $SearchOwners . "))";
                        break;
                    case "floor_infos":
                        $newFilter = "building_id in (select building_id from building_infos where owner_id in (" . $SearchOwners . "))";
                        break;
                    case "apartment_infos":
                        $newFilter = "building_id in (select building_id from building_infos where owner_id in (" . $SearchOwners . "))";
                        break;
                    case "lease_infos":
                        $newFilter = "building_id in (select building_id from building_infos where owner_id in (" . $SearchOwners . "))";
                        break;
                    case "request_infos_old":
                        $newFilter = "building_id in (select building_id from building_infos where owner_id in (" . $SearchOwners . "))";
                        break;
                    case "rental_payments":
                        $newFilter = "building_id in (select building_id from building_infos where owner_id in (" . $SearchOwners . "))";
                        break;
                    case "owner_infos":
                        $newFilter = "owner_id in (" . $SearchOwners . ")";
                        break;
                    default:
                        //         $newFilter = "owner_id in ($SearchOwners)";
                        break;
                }
            }
            switch ($field_name) {
                case "building_id":
                    $newFilter = "building_id in (select building_id from building_infos where owner_id in (" . $SearchOwners . "))";
                    break;
                case "floor_id":
                    $newFilter = "building_id in (select building_id from building_infos where owner_id in (" . $SearchOwners . "))";
                    break;
                case "owner_id":
                    $newFilter = "owner_id in (" . $SearchOwners . ")";
                    break;
                case "tenant_id":
                    $newFilter = "tenant_id in (select tenant_id from lease_infos where building_id in (select building_id from building_infos where owner_id in (" . $SearchOwners . ")))";
                    break;
            }
        }


        ////////////////////////////////////////  Start of Owner ///////////////////////////////////////////////




        ////////////////////////////////////////  Start of Vendor ///////////////////////////////////////////////

        if (CurrentUserLevel() == 14) {
            $DB_vendor = new \Vendor($DB_con);
            $newFilter = " false";
            $vendor_id = $_SESSION['employee_id'];

            $project_ids = $DB_vendor->getVendorProjects($vendor_id);
            //echo(print_r($CurrentTableObject));
            if (empty($field_name)) {
                switch ($CurrentTableObject->TableName) {
                    case "view_project_vendors":
                        $newFilter = " FIND_IN_SET($vendor_id, vendor_ids)";
                        break;
                    case "view_vendor_projects":
                        $newFilter = " vendor_id=$vendor_id";
                        break;
                    case "project_infos":
                        $project_id = Page("project_infos")->project_id->CurrentValue;
                        if (!empty($project_id)) {
                            $newFilter = " project_id=$project_id and  vendor_id=" . $_SESSION['vendor_id'];
                        } else {
                            $newFilter = " project_id in (" . implode(",", $project_ids) . ") or  employee_id = $vendor_id"; //" vendor_id=".$_SESSION['vendor_id'];
                        }

                        // if (!empty($project_ids)) {
                        //     $newFilter = " project_id in (" . implode(",", $project_ids) . ") or  employee_id = $vendor_id";
                        //  }
                        break;
                    case "proposal_infos":
                    case "contract_infos":
                        //var_dump(Page("invoice_infos")->invoice_id);

                        $newFilter = " vendor_id=" . $_SESSION['vendor_id'];
                        //$newFilter="true";
                        //  echo(var_dump($CurrentTableObject->DetailPages->Items["MyDetailTable"]));
                        //   echo(var_dump($CurrentTableObject));
                        //     echo "<hr>";
                        break;
                    case "payment_infos":
                    case "view_shared_payment_infos":
                        $invoice_id = Page("invoice_infos")->invoice_id->CurrentValue;
                        if (!empty($invoice_id)) {
                            $newFilter = " invoice_id=$invoice_id and material_by_owner=1";
                        } else {
                            $newFilter = " vendor_id=" . $_SESSION['vendor_id'];
                        }
                        break;
                    case "invoice_infos":
                        $contract_id = Page("contract_infos")->contract_id->CurrentValue;
                        if (!empty($contract_id)) {
                            $newFilter = " contract_id=$contract_id and material_by_owner=1";
                        } else {
                            $newFilter = " vendor_id=" . $_SESSION['vendor_id'];
                        }
                        //$newFilter = " vendor_id in(" . $_SESSION['vendor_id'].") or (contract_id=$contract_id)";
                        //
                        // $newFilter = " ";
                        break;
                    case "attachment_infos":
                        $invoice_id = Page("invoice_infos")->invoice_id->CurrentValue;
                        $contract_id = Page("contract_infos")->contract_id->CurrentValue;
                        // die( "<hr>$invoice_id-$contract_id<br>");
                        if (!empty($invoice_id)) {
                            $newFilter = " invoice_id=$invoice_id";
                        } elseif (!empty($contract_id)) {
                            $newFilter = " contract_id=$contract_id";
                        } else {
                            $newFilter = " vendor_id=" . $_SESSION['vendor_id'];
                        }
                        //$newFilter=" ";
                        break;
                    case "vendor_infos":
                        $newFilter = " vendor_id = $vendor_id";
                        break;
                    case 'company_infos':
                        $newFilter = " id = " . $_SESSION['company_id'];

                        break;
                    default:
                        $newFilter = " employee_id = $vendor_id";
                        //   $newFilter = " vendor_id = $vendor_id";
                        break;
                }
                //  die($newFilter);
            } else {
                switch ($field_name) {
                    case "project_id":
                        $newFilter = " project_id in (" . implode(",", $project_ids) . ") or  employee_id = $vendor_id";
                        break;
                    case "contract_id":
                    case "vendor_id":
                        $vendor_ids = implode(",", $DB_vendor->getOtherVendors($vendor_id));
                        //$newFilter = " vendor_id in ($vendor_id)";
                        $newFilter = " vendor_id in ($vendor_ids)"; // $vendor_id";
                        break;
                        //                case "attachment_infos":
                        //                    $newFilter="aa";
                        //                    break;
                    case "owner_vendor_id":
                        $vendor_ids = implode(",", $DB_vendor->getOtherVendors($vendor_id));
                        $newFilter = " owner_vendor_id in ($vendor_ids)"; // $vendor_id";
                        break;
                    case 'company_id':
                        $newFilter = " id = " . $_SESSION['company_id'];
                        break;
                    default:
                        $newFilter = " employee_id = $vendor_id";
                        break;
                }
            }
        }

        ////////////////////////////////////////  End of Vendor ///////////////////////////////////////////////


        ////////////////////////////////////////  Start of Front Desk Office ///////////////////////////////////////////////

        if (CurrentUserLevel() == 8) {
            //$newFilter = " false";
            switch ($CurrentTableObject->TableName) {
                case "payment_infos":
                    $newFilter = " payment_action_id in (6)"; //Checkpickup
                    break;
            }
            switch ($field_name) {
                case "project_id":
                case "contract_id":
                case "vendor_id":
                    $newFilter = "employee_id in ($SearchEmployees)";
                    $VendorOfCompany = ExecuteRow("select GROUP_CONCAT(DISTINCT vendor_id SEPARATOR ', ') AS vendor_ids from vendor_infos where company_id=" . $_SESSION["company_id"]);
                    if (!empty($VendorOfCompany)) {
                        $VendorOfCompanyfilter = " employee_id in (" . implode(",", $VendorOfCompany) . ") ";
                        $newFilter = ($newFilter <> "") ? "( $newFilter  OR $VendorOfCompanyfilter )" : $VendorOfCompanyfilter;
                    }
                    break;
            }
        }

        ////////////////////////////////////////  Start of Front Desk Office ///////////////////////////////////////////////

        ////////////////////////////////////////  Start of Collector ///////////////////////////////////////////////

        if (CurrentUserLevel() == 17) {
            //$newFilter = " false";
            switch ($CurrentTableObject->TableName) {
                case "rental_payments":
                    $newFilter = " paidornot_status =2"; //Checkpickup
                    break;
            }
            switch ($field_name) {
                case "building_id":
                case "tenant_id":
                case "apartment_id":
                    $newFilter = "employee_id in ($Company_Employees)";
                    break;
            }
        }

        ////////////////////////////////////////  Start of Front Desk Office ///////////////////////////////////////////////
        ///
        ///
    }
    //      echo("Current Table=" . $CurrentTableObject->TableName . " newFilter=$newFilter<br>");
    return $newFilter;
}


/////// Insert Owners, Employee, etc
function InsertEntry(&$rsnew)
{
    //   die("emp=".$_SESSION['employee_id']." company_id=".$_SESSION['company_id']);
    global $DB_con;
    if (!empty($_SESSION['employee_id'])) {
        $employee_id = $_SESSION['employee_id'];
        $rsnew['employee_id'] = $employee_id;
    }

    if (!empty($_SESSION['company_id'])) {
        $company_id = $_SESSION['company_id'];
        $rsnew['company_id'] = $company_id;
    }

    if (!empty($_SESSION['owner_id'])) {
        $owner_id = $_SESSION['owner_id'];
        $rsnew['owner_id'] = $owner_id;
    }

    if (!empty($_SESSION['top_employee_id'])) {
        $employee_id = $_SESSION['top_employee_id'];
        $rsnew['employee_id'] = $employee_id;
    }

    if (!empty($_SESSION['manager_id'])) {
        $manager_id = $_SESSION['manager_id'];
        $rsnew['manager_id'] = $manager_id;
    }

    if (CurrentUserLevel() == 14) {
        $DB_vendor = new \Vendor($DB_con);
        $vendor_id = $_SESSION['vendor_id'];
        $rsnew['vendor_id'] = $vendor_id;
        $rsnew['company_id'] = $DB_vendor->getVendorCompany($vendor_id);
    }
    //    die(var_dump($rsnew));
}


function getTenantDetail($tenant_id)
{
    include_once($root . '/../pdo/dbconfig.php');
    $SelectSqlTenant = "SELECT APP.apartment_id , BLD.employee_id, BLD.building_id building_id, LI.tenant_ids, floor_id, LI.id as lease_id, TT.full_name as tenant_name  FROM lease_infos LI
LEFT JOIN apartment_infos APP ON LI.apartment_id=APP.apartment_id
LEFT JOIN building_infos BLD ON BLD.building_id=APP.building_id
LEFT JOIN view_tenant_infos TT ON TT.tenant_id in (LI.tenant_ids)
WHERE FIND_IN_SET ('" . $tenant_id . "',tenant_ids)  AND CURRENT_DATE BETWEEN LI.start_date AND LI.end_date";
    //		echo "SelectSql=$SelectSqlTenant<br>";
    $row = ExecuteRow($SelectSqlTenant);
    $building_id = $row['building_id'];
    $apartment_id = $row['apartment_id'];
    $employee_id = $row['employee_id'];
    $tenant_name = $row['tenant_name'];
    $floor_id = $row['floor_id'];
    if (!empty($row['parking_id'])) {
        $parking_id = $row['parking_id'];
    }
    if (!empty($row['storage_id'])) {
        $storage_id = $row['storage_id'];
    }
    $lease_id = $row['lease_id'];
    return array($lease_id, $building_id, $floor_id, $apartment_id, $employee_id, $tenant_name);
}


function showTenant($tenant_ids)
{
    $tenant_ids = explode(",", $tenant_ids ?? '');
    if (count($tenant_ids)) {
        $ViewValue = "";
        $TenantNames = "";
        foreach ($tenant_ids as $tenant_id) {
            if (!empty($tenant_id)) {
                $tenant_name = ExecuteScalar("select full_name from tenant_infos where tenant_id=" . $tenant_id);
            }
            if ($ViewValue != "") {
                $ViewValue .= " <br> ";
            }
            if ($TenantNames != "") {
                $TenantNames .= " <br> ";
            }
            if (!empty($tenant_name)) {
                $TenantNames .= $tenant_name;
                $ViewValue .= "<a href='viewtenantinfosview?showdetail=&tenant_id=$tenant_id'>$tenant_name</a>";
            }
        }
    }
    return $ViewValue;
}

function showTenantContact($tenant_ids)
{
    $tenant_ids = explode(",", $tenant_ids ?? '');
    if (count($tenant_ids)) {
        $ViewValue = "";
        $TenantNames = "";
        foreach ($tenant_ids as $tenant_id) {
            if (!empty($tenant_id)) {
                list($tenant_name, $email, $mobile) = ExecuteRow("select full_name, email, mobile from tenant_infos where tenant_id=" . $tenant_id);
                if (empty($email)) {
                    $email = "";
                }
                if (empty($mobile)) {
                    $mobile = "";
                }
            }
            if ($ViewValue != "") {
                $ViewValue .= " <br> ";
            }
            if ($TenantNames != "") {
                $TenantNames .= " <br> ";
            }
            if (!empty($tenant_name)) {
                $TenantNames .= $tenant_name;
                $ViewValue .= "<a href='viewtenantinfosview?showdetail=&tenant_id=$tenant_id'>$tenant_name (  $mobile, $email )</a>";
            }
        }
    }
    return $ViewValue;
}

function showParking($lease_id)
{
    $ViewValues = "";
    $ParkingNames = "";
    //echo("LeaseID=$lease_id<br>");
    if (!empty($lease_id)) {
        if (!empty($lease_id)) {
            $ParkingNames = ExecuteRows("select name from lease_parkings LP left JOIN parking_unit_infos PUI on LP.parking_id=PUI.parking_id where lease_id=" . $lease_id);
        }
        if (!empty($ParkingNames)) {
            foreach ($ParkingNames as $PK) {
                $ViewValues .= $PK['name'] . ", ";
            }
        }
        //if (!empty($ParkingNames) && count($ParkingNames['name'])>=1){$ViewValues=implode("<br>",$ParkingNames['name']);}
        //  if (!empty($ParkingNames) && count($ParkingNames==1)){$ViewValues=$ParkingNames;}
    }

    //           if (!empty($ParkingNames )){$ParkingNames .= $ParkingNames; $ViewValue .= "<a href='viewtenantinfosview?showdetail=&tenant_id=$tenant_id'>$tenant_name</a>";}
    return $ViewValues;
}

function showStorage($lease_id)
{
    $ViewValues = "";
    $StorageNames = "";
    //echo("LeaseID=$lease_id<br>");
    if (!empty($lease_id)) {
        if (!empty($lease_id)) {
            $StorageNames = ExecuteRows("select name from lease_storages LS left JOIN storage_unit_infos SUI on LS.storage_id=SUI.storage_id where lease_id=" . $lease_id);
        }
        if (!empty($StorageNames)) {
            foreach ($StorageNames as $PK) {
                $ViewValues .= $PK['name'] . ", ";
            }
        }
        //if (count($StorageNames==1)){$ViewValues=$StorageNames;}
    }

    //           if (!empty($ParkingNames )){$ParkingNames .= $ParkingNames; $ViewValue .= "<a href='viewtenantinfosview?showdetail=&tenant_id=$tenant_id'>$tenant_name</a>";}
    return $ViewValues;
}


function getInfo($id, $table_name, $fields)
{
    $SelectSqlInfo = "SELECT * from $table_name where id=$id";
    $row = ExecuteRow($SelectSqlInfo);
    //	print_r($row);
    foreach ($fields as $field) {
        echo "$id,$table_name $field " . $row[$field] . " -  $field <br>";
        $$field = $row[$field];
    }
    return $fields;
}

function getFullDetail($id, $field)
{
    if (!empty($id)) {
        if ($field == "building_id") {
            $SelectSql = " SELECT DISTINCT BLD.building_name, BLD.owner_id, BLD.employee_id from building_infos BLD where BLD.building_id=$id";
            //        echo $SelectSql."<br>\n";
            $row = ExecuteRow($SelectSql);
            return $row;
        }

        if ($field == "apartment_id") {
            $SelectSql = " SELECT DISTINCT building_name, unit_number, owner_id, employee_id,  lease_status_id, lease_id, tenant_ids, start_date, end_date , lease_amount
        from rental_payments  where lease_status_id in (1,2,7,8,9,10,11)  and apartment_id=$id";
            //      die("Sql=".$SelectSql."<br>\n");
            $row = ExecuteRow($SelectSql);
            return $row;
        }

        if ($field == "lease_id") {
            $SelectSql = "select DISTINCT building_name, unit_number, lease_amount
	     from lease_infos LI LEFT JOIN  building_infos BI ON BI.building_id=LI.building_id LEFT JOIN apartment_infos APP ON LI.apartment_id=APP.apartment_id where LI.id=" . $id;
            //        echo $SelectSql."<br>\n";
            $row = ExecuteRow($SelectSql);
            return $row;
        }
    }
    return [];
}


function getEmployeePermission($id)
{
    // die(var_dump($_SESSION['EmpPer']));
    return $_SESSION['EmpPer'];

    // global $DB_con;
    // if (!empty($id)) {
    //     $EmpSql = "select * from employee_infos where employee_id=" . $id;
    //     $statement = $DB_con->prepare($EmpSql);
    //     $statement->execute();
    //     $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    //     //        $row = ExecuteRow($EmpSql);
    //     //    print_r($result[0]);
    //     foreach ($result[0] as $key => $value) {
    //         //   echo "$key=$value<br>\n";
    //         if ((substr($key, 0, 2) == "s_" || $key == "admin_id" || $key == "building_ids")) {
    //             //                if ($value==null){$value=0;}
    //             $EmpPer[$key] = $value;
    //             //echo "$key=$value<br>\n";
    //         }
    //     }
    //     //        echo "<hr>";
    //     return $EmpPer;
    // }
}

function ChangeDateFormat($date)
{
    return implode('-', array_reverse(explode('/', $date ?? ''))); //Change dd/mm/YYYY to YYYY-mm-dd
}

function console_log($output, $with_script_tags = true)
{
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}