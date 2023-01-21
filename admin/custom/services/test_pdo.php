<?php
/**
 * Created by PhpStorm.
 * User: t.e.chen
 * Date: 2018-02-13
 * Time: 15:34
 */
include "../../../pdo/Class.Test.php";

$db_test = new Test();

$user_info = $db_test->test_get_user_info(1);
echo $user_info['email'];
echo "<br>";
echo $db_test->get_repo_id();
echo "<br>";

echo "<hr`>";



$db_test_2 = new Test();
$user_info = $db_test->test_get_user_info(2);
echo $user_info['email'];
echo "<br>";
echo $db_test_2->get_repo_id();
echo "<br>";



