<?php
$time1 = microtime(true);
// session_save_path ("/tmp");
//echo exec('whoami')."<br>"; 
session_start();

echo "<pre>";
print_r($_SERVER);
echo "</pre>";
$time2 = microtime(true);
echo 'script execution time: ' . ($time2 - $time1);
?>Date:<?= date("Y-m-d H:i:s");?>
<? 
if( ini_get('allow_url_fopen') ) {
    echo('allow_url_fopen is enabled. file_get_contents should work well');
} else {
    echo('allow_url_fopen is disabled. file_get_contents would not work');
}

?>
<? phpinfo();?>