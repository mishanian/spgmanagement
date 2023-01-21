<?php
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=report.xls");
header("Pragma: no-cache");
header("Expires: 0");
include_once("analyze_getRental.php");
?>