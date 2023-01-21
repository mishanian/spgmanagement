<button class="btn scrolltop-btn back-top"><i class="fa fa-angle-up"></i></button>

<!--start header section header v1-->
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
include_once('pdo/Class.Province.php');
$DB_province = new Province($DB_con);
include_once('pdo/Class.SizeType.php');
$DB_size = new SizeType($DB_con);
include_once('pdo/Class.Building.php');
$DB_building = new Building($DB_con);
include_once('pdo/Class.Snapshot.php');
$DB_snapshot = new Snapshot($DB_con);

$languages = array();

if (isset($DB_snapshot)) {
    $languages = $DB_snapshot->getLanguages();
}
?>
<header id="header-section" class="header-section-4 header-main nav-left hidden-sm hidden-xs" data-sticky="0">
    <div class="container">
        <div class="header-left">
            <div class="logo">
                <a href="index.php">
                    <img src="<?php echo $DB_company->getLogo($company_id); ?>" alt="logo" style="height: 80px">
                    <h1 class="logo"><?php echo $DB_company->getName($company_id); ?></h1>
                </a>
            </div>
            <nav class="navi main-nav">
                <ul>
                    <li><a href="index.php"><i class="fa fa-home"></i> <?php echo $DB_snapshot->echot("Home"); ?></a>
                    </li>
                    <li><a href="map-listing.php"><i class="fa fa-list"></i>
                            <?php echo $DB_snapshot->echot("Listing"); ?> </a></li>
                    <li><a href="about-us.php"><i class="fa fa-info"></i> <?php echo $DB_snapshot->echot("About Us"); ?>
                        </a></li>
                    <li><a href="contact-us.php"><i class="fa fa-envelope"></i>
                            <?php echo $DB_snapshot->echot("Contact Us"); ?></a></li>
                </ul>
            </nav>
        </div>
        <div class="header-right">
            <div class="user"> <a href="admin"
                    style="margin-right: 10px;"><?php echo ucfirst($DB_snapshot->echot("Sign In")); ?></a>/<a
                    href="admin/signupadd.php"
                    style="margin-left: 10px;"><?php echo $DB_snapshot->echot("Register"); ?></a>
                <!--data-toggle="modal"-->
                <div class="languages">
                    <select id="languageChanger" class="selectpicker" style="display:none;">
                        <?php
                        foreach ($languages as $language) {
                            if (isset($_SESSION["lang_set"]) && (intval($_SESSION["lang_set"]) === intval($language["id"]))) {
                                echo "<option selected='selected' value='" . $language["id"] . "'>" . $language["name"] . "</option>";
                            } else {
                                echo "<option value='" . $language["id"] . "'>" . $language["name"] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="header-mobile visible-sm visible-xs">
    <div class="container">
        <!--start mobile nav-->
        <div class="mobile-nav"> <span class="nav-trigger"><i class="fa fa-navicon"></i></span>
            <div class="nav-dropdown main-nav-dropdown"></div>
        </div>
        <!--end mobile nav-->
        <div class="header-logo">
            <a href="index.php">
                <img src="<?php echo $DB_company->getLogo($company_id); ?>" alt="logo">
                <h1 class="logo" style="vertical-align: middle; padding-left: 6px;">
                    <?php echo $DB_company->getName($company_id); ?></h1>
            </a>
        </div>
    </div>
</div>
<!--end header section header v1-->

<script>
window.onload = function() {
    var jQ = jQuery.noConflict();
    jQuery('document').ready(function() {
        jQ("#languageChanger").on("change", function() {
            var languageSelected = jQ(this).val();

            // Send a AJAX request to language controller and Set the language SESSION value to the language selected
            jQ.ajax({
                url: "language_controller.php",
                type: "POST",
                dataType: "json",
                data: {
                    ln: languageSelected,
                    request: 'lnChange'
                },
                success: function(response) {
                    if (response && response.result) {
                        console.log(response.value + " is set as the new language");
                        window.location.reload();
                    }
                }
            });

        });
    });
}
</script>