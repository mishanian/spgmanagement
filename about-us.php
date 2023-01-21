<?php 
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

include_once ('pdo/dbconfig.php');
include_once ('pdo/Class.Company.php');
$DB_company = new Company($DB_con);
include_once ('pdo/Class.Province.php');
$DB_province = new Province($DB_con);
include_once ('pdo/Class.SizeType.php');
$DB_size = new SizeType($DB_con);
include_once ('pdo/Class.Facility.php');
$DB_apt = new Facility($DB_con);
include_once ('pdo/Class.Building.php');
$DB_building = new Building($DB_con);
include_once ('pdo/Class.Apt.php');
$DB_apt = new Apt($DB_con); ?>
 
<?php include "subdomain_check.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<title><?php
    include_once ("pdo/dbconfig.php");
    $company_id = $_SESSION['company_id'];
    echo $DB_company->getWebTitle($company_id);?>
</title>
<!--<base href="/" />-->
<!--Meta tags-->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php include_once ("links-for-html.php")?>
</head>
<body>

<?php
include_once ("header.php");
include_once ("search-bar.php");
?>
<div class="header-media">
  <div class="banner-parallax" style="height: 140px;">
    <div class="banner-bg-wrap">
      <div class="banner-inner" style="background-image: url(images/abour-contact/inner-page-banner1.jpg);">

    </div>
  </div>
</div>
<!--start section page body-->
<section id="section-body">
  <div class="container">
    <div class="page-title breadcrumb-top">
      <div class="row">
        <div class="col-sm-12">
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-home"></i></a></li>
            <li class="active"><?php echo $DB_snapshot->echot("About SPG Canada"); ?></li>
          </ol>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 container-contentbar">
        <div class="page-main">
          <div class="article-detail">
            <h3><?php echo $DB_snapshot->echot("Our History"); ?></h3>
            <p><?php echo $DB_snapshot->echot($DB_company->getHistory($company_id));?></p>
            <hr>
            <h3><?php echo $DB_snapshot->echot("About us"); ?></h3>
            <p><?php echo $DB_snapshot->echot($DB_company->getAbout($company_id));?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!--end section page body-->

    <?php include_once ("footer.php");?>

<!--Start Scripts-->
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/modernizr.custom.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/owl.carousel.min.js"></script>
<script type="text/javascript" src="js/jquery.matchHeight-min.js"></script>
<script type="text/javascript" src="js/bootstrap-select.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.nicescroll.js"></script>
<script type="text/javascript" src="js/jquery.parallax-1.1.3.html"></script>
<script type="text/javascript" src="js/custom.js"></script>
</body>
</html>
