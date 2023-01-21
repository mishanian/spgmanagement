<?
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
$apartment_id=$_GET['ap_id'];
$JsonArr=array();
if(!empty($apartment_id)) {
    $SelectSqlTenant = "SELECT APP.apartment_id , unit_number, BLD.building_id, LI.tenant_ids, floor_id, LI.id as lease_id  FROM lease_infos LI
INNER JOIN apartment_infos APP ON LI.apartment_id=APP.apartment_id
INNER JOIN building_infos BLD ON BLD.building_id=APP.building_id
WHERE APP.apartment_id=$apartment_id AND LI.lease_status_id=1";
//echo "SelectSql=$SelectSqlTenant<br>";
    $statement = $DB_con->prepare($SelectSqlTenant);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    $row = $result[0];
    foreach ($row as $key => $value) {
        $$key = $value;
    }
//    $JsonArr=array("lease_id"=>$lease_id, "building_id"=>$building_id, "floor_id"=>$floor_id, "apartment_id"=>$apartment_id, "tenant_ids"=>$tenant_ids);
 //   echo  json_encode( array("lease_id"=>$lease_id, "building_id"=>$building_id, "floor_id"=>$floor_id, "apartment_id"=>$apartment_id, "tenant_ids"=>$tenant_ids));

    $SelectSqlTenant = "SELECT tenant_id, full_name  FROM tenant_infos WHERE tenant_id in($tenant_ids)";
//   echo $SelectSqlTenant."<br>";
    $statement = $DB_con->prepare($SelectSqlTenant);
    $statement->execute();
    $i=0;
    foreach ($result = $statement->fetchAll(PDO::FETCH_ASSOC) as $row){
 //       $JsonArr["tenants"][$i++] = array($row['id'] => $row['full_name']);
        $JsonArr[$row['tenant_id']] = $row['full_name'];
  //      echo $row['id']."<br>";
    }
    echo  json_encode($JsonArr);
    
}
?>