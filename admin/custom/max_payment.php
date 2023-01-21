<? session_start();
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
$SelectSql = "SELECT outstanding FROM lease_payments where id=".$_GET['lpid'];
$statement = $DB_con->prepare($SelectSql);
$statement->execute();
$outstanding = $statement->fetchAll(PDO::FETCH_ASSOC);
echo outstanding;
?>