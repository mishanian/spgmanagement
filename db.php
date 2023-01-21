<?
$host = 'localhost';
$user = 'spgmgmt_admin';
$pass = 'no*_Qk+I0KwC';
$dbname   = 'spgmgmt_db';
$dsn = "mysql:host=$host;dbname=$dbname;charset=UTF8";
$opt = [
PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES   => false,
];
$db="";
try {
    global $db;
$db = new PDO($dsn, $user, $pass, $opt);
$errorInfo = $db->errorInfo();
if (isset($errorInfo[2])) {
    $error = $errorInfo[2];
}else{
//   echo "<p>Connection successful.</p>";
}
} catch (Exception $e) {
    $error = $e->getMessage();
    echo "<p>Error: $error</p>";
}
//echo "DB is loaded";
?>