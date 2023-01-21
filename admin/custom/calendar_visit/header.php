<!--/**-->
<!-- * Created by PhpStorm.-->
<!-- * User: Fish-->
<!-- * Date: 2017/7/5-->
<!-- * Time: 下午2:24-->
<!-- */-->

<button class="btn scrolltop-btn back-top"><i class="fa fa-angle-up"></i></button>
<!--<div class="modal fade" id="pop-login" tabindex="-1" role="dialog">-->
<!--  <div class="modal-dialog modal-sm">-->
<!--    <div class="modal-content">-->
<!--      <div class="modal-header">-->
<!--        <ul class="login-tabs">-->
<!--          <li class="active">Login</li>-->
<!--          <li>Register</li>-->
<!--        </ul>-->
<!--        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-close"></i></button>-->
<!--      </div>-->
<!--      <div class="modal-body login-block">-->
<!--        <div class="tab-content">-->
<!--          <div class="tab-pane fade in active">-->
<!--            <div class="message">-->
<!--              <p class="error text-danger"><i class="fa fa-close"></i> You are not Logedin</p>-->
<!--              <p class="success text-success"><i class="fa fa-check"></i> You are not Logedin</p>-->
<!--            </div>-->
<!--            <form>-->
<!--              <div class="form-group field-group">-->
<!--                <div class="input-user input-icon">-->
<!--                  <input type="text" placeholder="Username">-->
<!--                </div>-->
<!--                <div class="input-pass input-icon">-->
<!--                  <input type="password" placeholder="Password">-->
<!--                </div>-->
<!--              </div>-->
<!--              <div class="forget-block clearfix">-->
<!--                <div class="form-group pull-left">-->
<!--                  <div class="checkbox">-->
<!--                    <label>-->
<!--                      <input type="checkbox">-->
<!--                      Remember me </label>-->
<!--                  </div>-->
<!--                </div>-->
<!--                <div class="form-group pull-right"> <a href="#" data-toggle="modal" data-dismiss="modal" data-target="#pop-reset-pass">I forgot username and password</a> </div>-->
<!--              </div>-->
<!--              <button type="submit" class="btn btn-primary btn-block">Login</button>-->
<!--            </form>-->
<!--            <hr>-->
<!--            <a href="#" class="btn btn-social btn-bg-facebook btn-block"><i class="fa fa-facebook"></i> login with facebook</a> <a href="#" class="btn btn-social btn-bg-linkedin btn-block"><i class="fa fa-linkedin"></i> login with linkedin</a> <a href="#" class="btn btn-social btn-bg-google-plus btn-block"><i class="fa fa-google-plus"></i> login with Google</a> </div>-->
<!--          <div class="tab-pane fade">-->
<!--            <form>-->
<!--              <div class="form-group field-group">-->
<!--                <div class="input-user input-icon">-->
<!--                  <input type="text" placeholder="Username">-->
<!--                </div>-->
<!--                <div class="input-email input-icon">-->
<!--                  <input type="email" placeholder="Email">-->
<!--                </div>-->
<!--              </div>-->
<!--              <div class="form-group">-->
<!--                <div class="checkbox">-->
<!--                  <label>-->
<!--                    <input type="checkbox">-->
<!--                    I agree with your <a href="#">Terms & Conditions</a>. </label>-->
<!--                </div>-->
<!--              </div>-->
<!--              <button type="submit" class="btn btn-primary btn-block">Register</button>-->
<!--            </form>-->
<!--            <hr>-->
<!--            <a href="#" class="btn btn-social btn-bg-facebook btn-block"><i class="fa fa-facebook"></i> login with facebook</a> <a href="#" class="btn btn-social btn-bg-linkedin btn-block"><i class="fa fa-linkedin"></i> login with linkedin</a> <a href="#" class="btn btn-social btn-bg-google-plus btn-block"><i class="fa fa-google-plus"></i> login with Google</a>-->
<!--          </div>-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--</div>-->
<!--<div class="modal fade" id="pop-reset-pass" tabindex="-1" role="dialog">-->
<!--  <div class="modal-dialog modal-sm">-->
<!--    <div class="modal-content">-->
<!--      <div class="modal-header">-->
<!--        <ul class="login-tabs">-->
<!--          <li class="active">Reset Password</li>-->
<!--        </ul>-->
<!--        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-close"></i></button>-->
<!--      </div>-->
<!--      <div class="modal-body">-->
<!--        <p>Please enter your username or email address. You will receive a link to create a new password via email.</p>-->
<!--        <form>-->
<!--          <div class="form-group">-->
<!--            <div class="input-user input-icon">-->
<!--              <input placeholder="Enter your username or email" class="form-control">-->
<!--            </div>-->
<!--          </div>-->
<!--          <button class="btn btn-primary btn-block">Get new password</button>-->
<!--        </form>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--</div>-->

<!--start header section header v1-->
<?php
include_once("pdo/dbconfig.php");
$employee_id = 1;
$company_id = $DB_employee->getCompanyId($employee_id);
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
                    <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
                    <li><a href="map-listing.php?search=0"><i class="fa fa-list"></i> Listing</a></li>
                    <li><a href="about-us.php?employee_id=1"><i class="fa fa-info"></i> About Us</a></li>
                    <li><a href="contact-us.php?employee_id=1"><i class="fa fa-envelope"></i> Conatct Us</a></li>
                </ul>
            </nav>
        </div>
        <div class="header-right">
            <div class="user"> <a href="admin/login" data-target="#pop-login">Sign In / Register</a>
                <!--data-toggle="modal"-->
                <div class="languages">
                    <select class="selectpicker" style="display: none;">
                        <option>English</option>
                        <option>French</option>
                        <option>Chinese </option>
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
        <div class="header-logo"> <a href="index.php"><img src="images/logo-white.png" alt="logo"></a> </div>
        <div class="header-user">
            <ul class="account-action">
                <li> <span class="user-icon"><i class="fa fa-user"></i></span>
                    <div class="account-dropdown">
                        <ul>
                            <li> <a href="add-new-property.html"> <i class="fa fa-plus-circle"></i>Creat Listing</a>
                            </li>
                            <li> <a href="#" data-toggle="modal" data-target="#pop-login"> <i class="fa fa-user"></i>
                                    Log in / Register </a></li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!--end header section header v1-->