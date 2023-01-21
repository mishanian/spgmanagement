<?
include '../../pdo/dbconfig.php';
$Crud=new CRUD($DB_con);
if (!empty($_GET['id'])) {
    $sql = "UPDATE payment_infos SET is_signed=0,is_sent=0,is_printed=0,is_send_vendor=0,is_vendor_signed=0,is_pickup=0 WHERE id=" . $_GET['id'];
}elseif(!empty($_GET['fid'])){
    $sql = "UPDATE payment_infos SET is_pickup=1 WHERE id=" . $_GET['fid'];
}
//echo $sql;
$Crud->query($sql);
$Crud->execute();
?>