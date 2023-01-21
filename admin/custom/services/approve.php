<?php
  $service_type = $_GET['service_type'];

if($service_type == "credit_check"){
?>
<div class="container">
  <div class="col-md-12 col-sm-12" style="margin:40px 30px">
    <div class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1">
      <img src="custom/services/imgs/approve.png" style="max-height: 150px; max-width: 150px;">
    </div>
    <div class="col-md-8 col-sm-8" style="margin-top: 40px;">
      <h2 style="color: #7CCD7C">We have received your credit check request !</h2>
    </div>
  </div>

  <div class="col-md-12 col-sm-12">
    <label class="control-label" style="font-size:20px; text-align:left; margin: 10px 20px;">Thank you for using our services !<br> To complete this credit check, we also need the details personal information from your potential tenants. Your tenant will receive a email to fill the information what we need, please guide your tenant to fill the form and complete the credit check.</label>
  </div>
</div>
<?php
} elseif($service_type == "lease_signing"){
?>
<div class="container">
  <div class="col-md-12 col-sm-12" style="margin:40px 30px">
    <div class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1">
      <img src="custom/services/imgs/approve.png" style="max-height: 150px; max-width: 150px;">
    </div>
    <div class="col-md-8 col-sm-8" style="margin-top: 40px;">
      <h2 style="color: #7CCD7C">We have received your lease singing request !</h2>
    </div>
  </div>

  <div class="col-md-12 col-sm-12">
    <label class="control-label" style="font-size:20px; text-align:left; margin: 10px 20px;">Thank you for using our services !<br>The information will be used to form a pdf lease soon. The electronic version lease will be sent to both email of lessor and emails of all lessees via docsign. please inform them to confirm the lease and sign their name.</label>
  </div>
</div>
<?php } ?>
