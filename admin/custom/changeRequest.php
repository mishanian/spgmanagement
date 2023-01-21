<?
session_start();
include '../../pdo/dbconfig.php';
$Crud=new CRUD($DB_con);
if (!empty($_GET['id'])) {
    $sql = "update view_shared_payment_infos set is_party1_approved=null,is_party2_approved=null,shared_ask_date=CURDATE()  WHERE id=" . $_GET['id'];
    $Crud->query($sql);
    $Crud->execute();
    $employee_id=$_SESSION['employee_id'];
    $sql="insert into payment_history (payment_id, employee_id, action_type_id, create_dt) VALUES (".$_GET['id'].", ".$employee_id.", 13, '".date("Y-m-d h:i:s")."')";
 //   echo($sql);
    $Crud->query($sql);
    $Crud->execute();
}else{
    echo "No Id";
}
    ?>