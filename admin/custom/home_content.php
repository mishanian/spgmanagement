<?php
// namespace PHPMaker2023\spgmanagement;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$CUL = PHPMaker2023\spgmanagement\CurrentUserLevel();

//if(empty($_SESSION['UserID']) || empty($_SESSION['company_id']) ){header("Location: logout.php"); }//&& $CUL!="-1"
if (!empty($_SESSION['search_vendor_id']) || !empty($_SESSION['search_vendor_name'])) {
    unset($_SESSION['search_vendor_id']);
    unset($_SESSION['search_vendor_name']);
}

if ((isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'changepassword') !== false)) {
    header("Location: logout");
}
switch ($CUL) {
    case -1: // super admin
        break;
    case 1: // employee
    case 8: // Office Clerk
    case 7: // agent
    case 23: // Project Manager
    case 24: // Project and Property Manager
        include_once("homepage/home_content_employees.php");
        echo "<hr><h3>Level=$CUL</h3>";
        break;
    case 14: //vendor
        include_once("home_content_vendors.php");
        break;
    case 5: // tenant
        include_once("tenant_portal/home.php");
        break;
    case 10:
        include_once("home_content_accountants.php");
        break;
        // case 7:
        //     include_once("homepage/home_content_employees.php"); //agent
        //     break;
    case 11:
        include_once("home_content_handyman.php");
        break;
    case 18:
        include_once("home_content_junitor.php");
        break;
    case 21:
        include_once("home_content_adv.php");
        break;
        // case 24:
        //     include_once("homepage/home_content_employees.php");
        //     //include_once("home_content_projectproperty.php");
        //     break;
    case 25: // Credit check
        include_once("home_content_creditcheck.php");
        break;
    default:
        // include_once("home_content_employees.php");
        // include_once("homepage/home_content_employees.php");
        echo "Please contact supprt with your detail information (level=$CUL) on techsupport@spgmanagement.com ";
}
// die("ul=".$_SESSION['UserLevel']);

?><style>
h3 {
    font-size: 1.3125rem !important;
}
</style>