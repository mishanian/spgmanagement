<?
$host = 'localhost:3306';
$user = 'iliveinx_admin';
$pass = 'mZf@0v%AoE^m';
$db   = 'iliveinx_property';
$dsn = "mysql:host=$host;dbname=$db;";
$opt = [
PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
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
?>