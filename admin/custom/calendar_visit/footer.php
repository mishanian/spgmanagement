
<!--/**-->
<!-- * Created by PhpStorm.-->
<!-- * User: Fish-->
<!-- * Date: 2017/7/5-->
<!-- * Time: 下午2:48-->
<!-- */-->

<!--start footer section-->
<?php
include_once ("pdo/dbconfig.php");
$employee_id = 1;
$company_id = $DB_employee->getCompanyId($employee_id);
?>
<footer class="footer-v2">
  <div class="footer">
    <div class="container">
      <div class="row">
        <div class="col-sm-3">
          <div class="footer-widget widget-about">
            <div class="widget-top">
              <h3 class="widget-title">About Us</h3>
            </div>
            <div class="widget-body">
              <p class="read"><a href="about-us.php?employee_id=<?php echo $employee_id;?>">Read more <i class="fa fa-caret-right"></i></a></p>
            </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="footer-widget widget-contact">
            <div class="widget-top">
              <h3 class="widget-title">Contact Us</h3>
            </div>
            <div class="widget-body">
              <ul class="list-unstyled">
                <li><i class="fa fa-location-arrow"></i> <?php echo $DB_company->getAddress($company_id);?></li>
                <li><i class="fa fa-phone"></i> <?php echo $DB_employee->getEmployeeMobile($employee_id);?></li>
                <li><i class="fa fa-envelope-o"></i> <?php echo $DB_employee->getEmployeeEmail($employee_id);?></li>
              </ul>
              <p class="read"><a href="contact-us.php?employee_id=<?php echo $employee_id;?>">Contact us <i class="fa fa-caret-right"></i></a></p>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="footer-widget widget-newsletter">
            <!--div class="widget-top">
              <h3 class="widget-title">Newsletter Subscribe</h3>
            </div-->
            <div class="widget-body">
              <!--form>
                <div class="table-list">
                  <div class="form-group table-cell">
                    <div class="input-email input-icon">
                      <input class="form-control" placeholder="Enter your email">
                    </div>
                  </div>
                  <div class="table-cell">
                    <button class="btn btn-primary">Submit</button>
                  </div>
                </div>
              </form-->
<!--              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. </p>-->
              <h3 class="widget-title">Follow Us</h3>
              <ul class="social">
                <li> <a href="<?php $DB_company->getFacebook($company_id);?>" class="btn-facebook"><i class="fa fa-facebook-square"></i></a> </li>
                <li> <a href="<?php $DB_company->getTwitter($company_id);?>" class="btn-twitter"><i class="fa fa-twitter-square"></i></a> </li>
                <li> <a href="<?php $DB_company->getGooglePlus($company_id);?>" class="btn-google-plus"><i class="fa fa-google-plus-square"></i></a> </li>
                <li> <a href="<?php $DB_company->getLinkedIn($company_id);?>" class="btn-linkedin"><i class="fa fa-linkedin-square"></i></a> </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-md-3 col-sm-3">
          <div class="footer-col">
            <p>powered by ilivein</p>
            <p>&copy; 2017 - Beaver AIT Canada Inc. All rights reserved</p>
          </div>
        </div>
        <div class="col-md-6 col-sm-6">
          <div class="footer-col">
            <div class="navi">
              <!--ul id="footer-menu" class="">
                <li><a href="#">Privacy</a></li>
                <li><a href="#">Terms and Conditions</a></li>
                <li><a href="#">Contact</a></li>
              </ul-->
            </div>
          </div>
        </div>
        <!--div class="col-md-3 col-sm-3">
          <div class="footer-col foot-social">
            <p> Follow us <a target="_blank" class="btn-facebook" href="https://facebook.com/"><i class="fa fa-facebook-square"></i></a> <a target="_blank" class="btn-twitter" href="https://twitter.com/"><i class="fa fa-twitter-square"></i></a> <a target="_blank" class="btn-linkedin" href="http://linkedin.com/"><i class="fa fa-linkedin-square"></i></a> <a target="_blank" class="btn-google-plus" href="http://google.com/"><i class="fa fa-google-plus-square"></i></a> <a target="_blank" class="btn-instagram" href="http://instagram.com/"><i class="fa fa-instagram"></i></a> </p>
          </div>
        </div-->
      </div>
    </div>
  </div>
</footer>
<!--end footer section--> 
