<?php

class Analyze
{
    private $db;
    private $crud;

    /**
     * Analyze constructor.
     * @param $DB_con
     */
    public function __construct($DB_con)
    {
        $this->db = $DB_con;
        $this->crud = new Crud($DB_con);
    }

    public function getBdName($id)
    {
        $this->crud->query("SELECT building_name FROM building_infos where building_id=$id and company_id=".$_SESSION['company_id']);
     //   echo("SELECT building_name FROM building_infos where building_id=$id <br>");
        return $this->crud->resultField();
    }

    public function getTenantName($id)
    {
        $this->crud->query("SELECT full_name FROM tenant_infos where tenant_id=$id and company_id=".$_SESSION['company_id']);
        return $this->crud->resultField();
    }

    public function getAllBdIdNameByCompany($company_id)
    {
        $this->crud->query("SELECT building_id,building_name FROM building_infos where company_id=$company_id "); // For make sure only shows for the company of employee
        return $this->crud->resultSet();
    }

    public function getAllUnitsIdNameByCompany($company_id)
    {
        $this->crud->query("SELECT apartment_id, unit_number FROM apartment_infos where company_id=$company_id ");
        return $this->crud->resultSet();
    }

    public function getAllUnitsIdNameByBid($company_id, $building_id)
    {
        if (!empty($building_id)) {
            $this->crud->query("SELECT apartment_id, unit_number FROM apartment_infos where company_id=$company_id and building_id in ($building_id)");
            return $this->crud->resultSet();
        }
    }

    public function getUnitNumber($apartment_id)
    {
        $this->crud->query("SELECT  unit_number FROM apartment_infos where apartment_id=$apartment_id ");
        return $this->crud->resultField();
    }

    public function getApartmentInfo($apartment_id)
    {
        $this->crud->query("SELECT * FROM apartment_infos where apartment_id=$apartment_id");
        return $this->crud->resultSet();
    }

    public function getAllPaymentMethods()
    {
        $this->crud->query("SELECT  id,name FROM payment_methods ");
        return $this->crud->resultSet();
    }

    public function getPaymentMethod($id)
    {
        $this->crud->query("SELECT  name FROM payment_methods where id=$id ");
        return $this->crud->resultField();
    }

    public function getAllPaymentTypes()
    {
        $this->crud->query("SELECT  id,name FROM payment_types ");
        return $this->crud->resultSet();
    }

    public function getProvince()
    {
        $this->crud->query("SELECT  id,name FROM provinces ");
        return $this->crud->resultSet();
    }

    public function getPaymentType($id)
    {
        $this->crud->query("SELECT  id,name FROM payment_types WHERE id=$id");
        return $this->crud->resultField();
    }

    public function getRentalPayments($company_id)
    {
        $this->crud->query("SELECT  * FROM rental_payments WHERE paid>0 AND due_date BETWEEN '" . date("Y-m-1") . "' AND '" . date("Y-m-t") . "'  AND company_id=$company_id order by due_date");
        return $this->crud->resultSet();
    }

    public function getAllSizeTypeNames()
    {
        $this->crud->query("SELECT  id,name FROM size_types ");
        return $this->crud->resultSet();
    }

    public function getSizeTypeName($id)
    {
        $this->crud->query("SELECT  name FROM size_types where id=$id ");
        return $this->crud->resultField();
    }

    public function getRentalPaymentParams($company_id, $building_id, $apartment_id, $payment_method_id, $payment_type_id, $year_due_date, $month_due_date, $province_id, $size_type_id)
    {
        if ($building_id != 0) {
            $WhereBuilding = "building_id in ( $building_id )";
        } else {
            $WhereBuilding = " true ";
        }
        if ($apartment_id != 0) {
            $WhereApartment = "apartment_id in (  $apartment_id )";
        } else {
            $WhereApartment = " true ";
        }
        if ($payment_method_id != 0) {
            $WherePM = "payment_method_id in (  $payment_method_id )";
        } else {
            $WherePM = " true ";
        }
        if ($payment_type_id != 0) {
            $WherePT = "payment_type_id in (  $payment_type_id )";
        } else {
            $WherePT = " true ";
        }
        if ($year_due_date != 0) {
            $WhereYear = "year_due_date in (  $year_due_date )";
        } else {
            $WhereYear = " true ";
        }
        if ($month_due_date != 0) {
            $WhereMonth = "month_due_date in (  $month_due_date )";
        } else {
            $WhereMonth = " true ";
        }
        if ($province_id != 0) {
            $WhereProv = "province_id in (  $province_id )";
        } else {
            $WhereProv = " true ";
        }
        if ($size_type_id != 0) {
            $WhereSize = "size_type_id in (  $size_type_id )";
        } else {
            $WhereSize = " true ";
        }
        $sql = "SELECT  * FROM rental_payments WHERE $WhereBuilding  AND company_id=$company_id AND $WhereApartment   AND $WherePM AND $WherePT AND $WhereYear AND $WhereMonth AND $WhereProv AND $WhereSize order by due_date";
        //echo $sql;
        $this->crud->query($sql);
        return $this->crud->resultSet();
    }

    public function getBuildingsIncome($company_id,$building_id)
    {
        if ($building_id != 0) {
            $WhereBuilding = "building_id in ( $building_id )";
        } else {
            $WhereBuilding = " true ";
        }
        $this->crud->query("SELECT  building_name,sum(paid) as paid FROM rental_payments WHERE  $WhereBuilding AND paid>0 AND year_due_date in (".date("Y").") AND month_due_date in (".date("m").")  AND company_id=$company_id group by building_name order by building_name ");
        //echo "SELECT  building_name,sum(paid) as paid FROM rental_payments WHERE  $WhereBuilding AND paid>0 AND year_due_date in (".date("Y").") AND month_due_date in (".date("m").")  AND company_id=$company_id group by building_name order by building_name ";
        return $this->crud->resultSet();
    }


/*
     public function getBuildingsMonthlyExpense($company_id,$building_ids)
    {
        if ($building_ids != 0) {
            $WhereBuilding = "building_id in ( $building_ids )";
        } else {
            $WhereBuilding = " true ";
        }
        $Sql="SELECT building_id,SUM(amount) AS amount, MONTH(payment_date) AS month_bill_date , YEAR(payment_date) AS year_bill_date ,company_id FROM bill_payment WHERE   company_id=$company_id AND $WhereBuilding  AND is_void=0 AND is_signed=1 GROUP BY building_id,MONTH(payment_date),YEAR(payment_date)";
        $this->crud->query($Sql);
       //      echo $Sql;
        return $this->crud->resultSet();
    }
 */

    public function getBuildingsMonthlyIncome($company_id,$building_id)
    {
        if ($building_id != 0) {
            $WhereBuilding = "building_id in ( $building_id )";
        } else {
            $WhereBuilding = " true ";
        }
        $sql="SELECT  building_id, building_name,month_due_date, year_due_date, sum(paid) as paid FROM rental_payments WHERE  $WhereBuilding AND paidornot_status=1 AND company_id=$company_id group by building_id, year_due_date, month_due_date  order by building_name,year_due_date, month_due_date ";
        $this->crud->query($sql);
        //echo $sql;
        return $this->crud->resultSet();
    }
//    ***********************************************************

    public function getExpenseDetails($company_id)
    {
        $this->crud->query("SELECT  * FROM bills_details WHERE amount>0  AND company_id=$company_id order by due_date");
        return $this->crud->resultSet();
    }

    public function getAllVendorNames()
    {
        $this->crud->query("SELECT  vendor_id,company_name FROM vendor_infos where company_id=".$_SESSION['company_id']);
        return $this->crud->resultSet();
    }

    public function getMemo()
    {
        $this->crud->query("SELECT  description FROM bill_details ");
        return $this->crud->resultField();
    }


    public function getRequestTypes($request_id)
    {
        $this->crud->query("SELECT name FROM request_types WHERE id = $request_id");
        return $this->crud->resultField();
    }

    public function getBillDate()
    {
        $this->crud->query("SELECT  invoice_date FROM invoice_infos");
        return $this->crud->resultField();
    }

    public function getOnlineShipping($request_id)
    {
        $this->crud->query("SELECT  material_detail FROM request_infos WHERE id = $request_id");
        die("SELECT  material_detail FROM request_infos WHERE id = $request_id");
        $material_detail = $this->crud->resultField();
        $material_detail = json_decode($material_detail);
        if (count($material_detail) > 0) {
            $material_online_store_id = $material_detail[0]->material_online_store_id;
            $this->crud->query("SELECT name FROM online_stores WHERE id = $material_online_store_id");
            $material_online_store = $this->crud->resultField();
        } else {
            $material_online_store = "";
        }
        return $material_online_store;
    }

    public function getOnlineStores()
    {
        $this->crud->query("SELECT  id,name FROM online_stores ");
        return $this->crud->resultSet();
    }

    public function getVendorInfos($vendor_id)
    {
        $this->crud->query("SELECT  vendor_id,vendor_type_id,company_name from vendor_infos where vendor_id = $vendor_id");
        return $this->crud->resultSet();
    }

    public function getVendorTypeName($vendor_type_id)
    {
        $this->crud->query("SELECT  name from vendor_types where id = $vendor_type_id");
        return $this->crud->resultField();
    }

    public function getAllVendorTypeNames()
    {
        $this->crud->query("SELECT * from vendor_types");
        return $this->crud->resultSet();
    }

    public function getRequestInfos($request_id)
    {
        $this->crud->query("SELECT request_type_id FROM request_infos where id = $request_id");
        return $this->crud->resultField();
    }

    public function getAllRequestTypes()
    {
        $this->crud->query("SELECT id,name FROM request_types");
        return $this->crud->resultSet();
    }

    public function getRequestTypeName($request_type_id)
    {
        $this->crud->query("SELECT name FROM request_types where id = $request_type_id");
        return $this->crud->resultField();
    }

    public function getExpenseDetailParams($company_id, $building_id, $apartment_id, $online_shopping_id, $vendor_id, $vendor_type_id, $request_type_id)
    {
        if ($building_id != 0) {
            $WhereBuilding = "BD.building_id in ( $building_id )";
        } else {
            $WhereBuilding = " true ";
        }
        if ($apartment_id != 0) {
            $WhereApartment = "BD.apartment_id in (  $apartment_id )";
        } else {
            $WhereApartment = " true ";
        }
        if ($online_shopping_id != 0 && false) {
            $WhereOnlineshopping = "online_shopping_id in (  $online_shopping_id )";
        } else {
            $WhereOnlineshopping = " true ";
        }
        if ($vendor_id != 0) {
            $WhereVendor = "BL.vendor_id in (  $vendor_id )";
        } else {
            $WhereVendor = " true ";
        }
        if ($request_type_id != 0) {
            $WhereRequestTypeID = "RI.request_type_id in (  $request_type_id )";
        } else {
            $WhereRequestTypeID = " true ";
        }
        if ($vendor_type_id != 0) {
            $WhereVendorTypeID = "vendor_type_id in (  $vendor_type_id )";
        } else {
            $WhereVendorTypeID = " true ";
        }

        //$sql = "SELECT  BD.building_id,BD.apartment_id,invoice_date,BL.vendor_id,BL.request_id,BL.memo,BD.total,BL.grand_total,BD.amount, BL.paid_amount FROM invoice_infos BL WHERE  due_date BETWEEN '" . date("Y-m-1") . "' AND '" . date("Y-m-t") . "'  AND company_id=$company_id AND $WhereBuilding AND $WhereApartment   AND $WhereOnlineshopping AND $WhereVendor AND $WhereRequestTypeID AND $WhereVendorTypeID order by due_date"; //Left join request_infos RI ON BL.request_id = RI.id left join vendor_infos VI on BL.vendor_id = VI.vendor_id
      $sql="SELECT  building_id,apartment_id,invoice_date,vendor_id,request_id,memo,material_grand_total,labor_grand_total,amount,paid_amount FROM invoice_infos BL WHERE  due_date  BETWEEN '" . date("Y-m-1") . "' AND '" . date("Y-m-t") . "'  AND company_id=$company_id AND $WhereBuilding AND $WhereApartment   AND $WhereOnlineshopping AND $WhereVendor AND $WhereRequestTypeID AND $WhereVendorTypeID order by due_date";
     //   echo $sql;


        $this->crud->query($sql);
        return $this->crud->resultSet();
    }

    public function getBuildingsExpense($company_id,$building_ids)
    {
        if ($building_ids != 0) {
            $WhereBuilding = "BD.building_id in ( $building_ids )";
        } else {
            $WhereBuilding = " true ";
        }
        $Sql="SELECT  building_id,SUM(amount) AS amount, paid_amount FROM invoice_infos WHERE  invoice_date   BETWEEN '" . date("Y-m-1") . "' AND '" . date("Y-m-t") . "'  AND company_id=$company_id AND $WhereBuilding  group by building_id ";
        $this->crud->query($Sql);
    //    die("SELECT  BD.building_id,SUM(amount) AS amount FROM bills_details BD LEFT JOIN bills BL ON BL.invoice_id=BD.bill_id WHERE  bill_date   BETWEEN '" . date("Y-m-1") . "' AND '" . date("Y-m-t") . "'  AND company_id=$company_id group by building_id ");
        return $this->crud->resultSet();
    }
    public function getBuildingsMonthlyExpenseDetail($company_id,$building_ids) //Not Used
    {
        if ($building_ids != 0) {
            $WhereBuilding = "BD.building_id in ( $building_ids )";
        } else {
            $WhereBuilding = " true ";
        }
        $Sql="SELECT  BD.building_id,SUM(total) AS total, month(invoice_date)  as month_invoice_date, SUM(BL.paid_amount) as paid_amount FROM bills_details BD LEFT JOIN invoice_infos BL ON BL.invoice_id=BD.invoice_id WHERE  company_id=$company_id AND $WhereBuilding group by BD.building_id ";
        $this->crud->query($Sql);
 //      echo $Sql;
        return $this->crud->resultSet();
    }
    public function getBuildingsMonthlyExpense($company_id,$building_ids)
    {
        if ($building_ids != 0) {
            $WhereBuilding = "building_id in ( $building_ids )";
        } else {
            $WhereBuilding = " true ";
        }
        $Sql="SELECT building_id,SUM(amount) AS amount, MONTH(payment_date) AS month_invoice_date , YEAR(payment_date) AS year_invoice_date ,company_id FROM payment_infos WHERE   company_id=$company_id AND $WhereBuilding  AND is_void=0 AND is_signed=1 GROUP BY building_id,YEAR(payment_date),MONTH(payment_date)";
        $this->crud->query($Sql);
       //      echo $Sql;
        return $this->crud->resultSet();
    }
}