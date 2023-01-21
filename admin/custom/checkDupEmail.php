<?
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
$SelectSql = "select count(*) as EmailExist from userlist where username='".$_GET['e']."'";
$statement = $DB_con->prepare($SelectSql);
$result=$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
echo $result[0]['EmailExist'];
//var_dump($result);
?>