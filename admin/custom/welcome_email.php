<? ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('../../pdo/Class.WelcomeNtf.php');
$user_id  = $_GET['id'];
$welcome_test = new WelcomeNtf($user_id);
//var_dump($welcome_test);
$welcome_test->send_welcome_email();