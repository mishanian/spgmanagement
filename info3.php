<?php
$time1 = microtime(true);

 phpinfo();
echo"<pre>";
print_r($_SERVER);
echo "</pre>";
$time2 = microtime(true);
echo 'script execution time: ' . ($time2 - $time1);
?>Date:<?=date("Y-m-d H:i:s");
