<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('pdo/dbconfig.php');
include_once('pdo/Class.Company.php');
$DB_company = new Company($DB_con);
include_once('pdo/Class.Apt.php');
$DB_apt = new Apt($DB_con);

include "subdomain_check.php";
if (isset($_GET['c'])) {
    $_SESSION['company_id'] = $_GET['c'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php
            include_once("pdo/dbconfig.php");
            $company_id = $_SESSION['company_id'];
            echo $DB_company->getWebTitle($company_id); ?></title>
    <!--    <base href="/" />-->
    <!--Meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include_once "links-for-html.php"; ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body>
    <?php
    include_once("header.php");
    include_once("search-bar.php");
    $building_id = $_GET['building_id'];
    $apt_id = $_GET['apt_id'];
    ?>
    <section id="section-body">
        <div class="container">
            <div class="page-title breadcrumb-top">
                <div class="row">
                    <div class="col-sm-12">
                        <ol class="breadcrumb">
                            <li><a href="index.php"><i class="fa fa-home"></i></a></li>
                            <li><a href="map-listing.php">Property</a></li>
                            <li><a
                                    href="property-view.php?pass_bd_id=<?php echo $building_id; ?>"><?php echo $DB_building->getBdName($building_id); ?></a>
                            </li>
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
                                <form id="inquiry-form" action="inquiry_controller.php" method="post">
                                    <!-- TODO the source_type_id-->
                                    <input type="hidden" id="building_id" name="building_id"
                                        value="<?php echo $building_id; ?>">
                                    <input type="hidden" id="apt_id" name="apt_id" value="<?php echo $apt_id; ?>">
                                    <?php
                                    if (isset($_GET['portal'])) {
                                    ?>
                                    <input type="hidden" id="from_portal" name="from_portal" value="true">
                                    <?php
                                    }
                                    ?>
                                    <legend>Please Fill in Your Information</legend>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6">
                                            <div style="clear:both;">&nbsp;</div>
                                            <div class="prop_details"><span>Apt.
                                                    <?php echo $DB_apt->getAptInfo($apt_id)['unit_number'] ?> in
                                                    <?php echo $DB_building->getBdInfo($building_id)['building_name']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6"><label>Your Name</label><input
                                                class="form-control" name="customer_name" id="customer_name" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6"><label>Your Email</label><input
                                                class="form-control" name="customer_email" id="customer_email" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6"><label>Your Telephone</label><input
                                                class="form-control" name="customer_phone" id="customer_phone" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12">
                                        <div class="book-event-col-1 col-md-6"><label>Your Question</label><textarea
                                                class="form-control" name="inquiry_content" rows="6"
                                                id="message"></textarea></div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6"><label>Please Check the Box</label>
                                            <div class="g-recaptcha"
                                                data-sitekey="6LeSFDEUAAAAAPSBwXLuph-qK5d4uksBUyD6_3un"></div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <div class="book-event-col-1 col-md-6"><button type="submit"
                                                class="btn btn-secondary event-form-button"
                                                name="submit_inquiry">Confirm & Submit</button></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--end section page body-->

    <!--start footer section-->
    <?php include_once("footer.php"); ?>
    <!--end footer section-->

    <!--Start Scripts-->
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/modernizr.custom.js"></script>
    <script type="text/javascript" src="js/moment.js"></script>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/owl.carousel.min.js"></script>
    <script type="text/javascript" src="js/jquery.matchHeight-min.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js"></script>
    <script type="text/javascript" src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/masonry.pkgd.min.js"></script>
    <script type="text/javascript" src="js/jquery.nicescroll.js"></script>
    <!-- <script type="text/javascript" src="js/infobox.js"></script> -->
    <script type="text/javascript" src="js/markerclusterer.js"></script>
    <script type="text/javascript" src="js/custom.js"></script>

</body>

</html>