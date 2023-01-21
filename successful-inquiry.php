<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>
        <?php
        include_once ("pdo/dbconfig.php");
        include_once ('pdo/Class.Company.php');
        $DB_company = new Company($DB_con);
        $company_id = $_SESSION['company_id'];
        $building_id = $_GET['building_id'];
        echo $DB_company->getWebTitle($company_id);?></title>
    <!--    <base href="/" />-->
    <!--Meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="5;URL=property-view.php?pass_bd_id=<?php echo $building_id;?>">
    <?php include_once("links-for-html.php") ?>
</head>
<body>
<?php
include_once("header.php");
include_once("search-bar.php");
?>
<section id="section-body">
    <div class="container">
        <div class="page-title breadcrumb-top">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                        <li><a href="map-listing.php">Property</a></li>
                        <li><a href="property-view.php?pass_bd_id=<?php echo $building_id;?>"><?php echo $DB_building->getBdName($building_id);?></a></li>
                        <li class="active">Inquiry</li>
                    </ol>
                    <div class="page-title-left">
                        <h2>Submit an Inquiry</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div id="content-area" class="contact-area">
                    <div class="white-block">
                        <div class="row">
                            <legend>Congratulations! </legend>
                            <p>Your have successfully submitted your inquiry.</p>
                            <p>Our agents will answer your question as soon as possible.</p>
                            <p>You will be navigated to the building page in 5 seconds.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once("footer.php"); ?>
</body>
</html>