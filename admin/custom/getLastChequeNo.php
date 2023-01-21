<?
$cheque_no=0;
include '../../pdo/dbconfig.php';
$Crud=new CRUD($DB_con);
if (!empty($_GET['id'])) {
$sql = "select last_cheque_no from owner_accounts WHERE owner_account_id=" . $_GET['id'];
//die($sql);
//echo $sql;
$result=$Crud->query($sql);
    $cheque_no=$Crud->resultField()+1;
echo $cheque_no;
}
?>