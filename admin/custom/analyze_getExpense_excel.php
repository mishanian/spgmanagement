<?php
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=report.xls");
header("Pragma: no-cache");
header("Expires: 0");
header("Content-type: text/html; charset: utf-8");
$export=1;
include_once("analyze_getExpenseDetail.php");
?>