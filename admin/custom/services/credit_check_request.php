<?php
$employee_id = $_SESSION['UserID'];
include_once ('../pdo/dbconfig.php');
include_once('../pdo/Class.Services.php');
$DB_services = new Services($DB_con);
$employee_info = $DB_services->get_employee_id($employee_id);
$default_landlord_name = $employee_info['full_name'];
$default_landlord_email = $employee_info['email'];
$default_landlord_telephone = $employee_info['mobile'];
$employee_company_info = $DB_services->get_employee_company_info($employee_id);
$default_landlord_address = $employee_company_info['address2'].', '.$employee_company_info['address1'];
?>

<link rel="stylesheet" href="custom/services/css/form-control.css">
<div class="container">
  <form class="form-horizontal" action="custom/services/services_controller.php" method="post" id="investigation_form">
  <div class="card">
  <div class="card-header bg-light">
  <h4>Landlord</h4>
  </div>
  <div class="card-body">

  <div class="input-group mb-3">
  <label class="col-md-2 control-label">Name</label>
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
  </div>
  <input name="client_name" class="form-control" type="text" autocomplete="off" required value="<?php echo $default_landlord_name;?>">
</div>


<div class="input-group mb-3">
  <label class="col-md-2 control-label">E-Mail</label>
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1"><i class="fas fa-at"></i></span>
  </div>
  <input name="client_email" class="form-control" type="text" autocomplete="off" required value="<?php echo $default_landlord_email?>" pattern="[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$">
</div>
<div class="input-group mb-3">
  <label class="col-md-2 control-label">Telephone</label>
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1"><i class="fas fa-phone-alt"></i></span>
  </div>
  <input name="client_tel" class="form-control" type="text" autocomplete="off" required value="<?php echo $default_landlord_telephone;?>" pattern="[\+]\d{11}">
</div>

<div class="input-group mb-3">
  <label class="col-md-2 control-label">Address</label>
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1"><i class="fas fa-home"></i></span>
  </div>
  <input name="client_address" class="form-control" type="text" autocomplete="off" required value="<?php echo $default_landlord_address;?>">
</div>

  </div>
  </div>

  <div class="card">
  <div class="card-header bg-light">
  <h4>Potential Tenant</h4>
  </div>
  <div class="card-body">

  <div class="input-group mb-3">
  <label class="col-md-2 control-label">Name</label>
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
  </div>
  <input name="tenant_name" class="form-control" type="text" autocomplete="off" required>
</div>

<div class="input-group mb-3">
  <label class="col-md-2 control-label">E-Mail</label>
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1"><i class="fas fa-at"></i></span>
  </div>
  <input name="tenant_email" class="form-control" type="text" autocomplete="off" required pattern="[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$">
</div>
<div class="input-group mb-3">
  <label class="col-md-2 control-label">Telephone</label>
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1"><i class="fas fa-phone-alt"></i></span>
  </div>
  <input name="tenant_tel" class="form-control" type="text" autocomplete="off" required pattern="[\+]\d{11}">
</div>

<div class="input-group mb-3">
  <label class="col-md-2 control-label">Applying Address</label>
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1"><i class="fas fa-home"></i></span>
  </div>
  <input name="tenant_address" class="form-control" type="text" autocomplete="off" required>
</div>


<div class="input-group mb-3">
  <label class="col-md-2 control-label">Province</label>
  <div class="input-group-prepend">
    <span class="input-group-text" id="basic-addon1"><i class="fas fa-home"></i></span>
  </div>
  <select name="tenant_province" class="form-control" type="text" autocomplete="off" required>
            <option value="AB">AB</option>
            <option value="BC">BC</option>
            <option value="MB">MB</option>
            <option value="NB">NB</option>
            <option value="NL">NL</option>
            <option value="NS">NS</option>
            <option value="ON">ON</option>
            <option value="PE">PE</option>
            <option value="QC">QC</option>
            <option value="SK">SK</option>
            <option value="NT">NT</option>
            <option value="NU">NU</option>
            <option value="YT">YT</option>
          </select>
</div>


    <div class="form-group">
      <div class="col-md-9 col-md-offset-2" style="margin-top: 30px;">
        <label>
          <input name="term_check" type="checkbox" required>
          By clicking the submit bottom below, I agree and give the consent to SPG Canada of the following:</br>
          &nbsp;&nbsp;&nbsp;1. I authorize SPG Canada to contact the applicant directly on my behalf.</br>
          &nbsp;&nbsp;&nbsp;2. I have obtained the authorization from the applicant to release such information to SPG Canada.</br>
          &nbsp;&nbsp;&nbsp;3. I agree to pay the fee and give the mandate to SPG Canada to do the investigation on my behalf.
        </label>
      </div>
    </div>

    <!-- Button -->
    <div class="form-group">
      <div class="col-md-12 text-center" style="margin-top: 30px;">
        <button type="submit" class="btn btn-success btn-lg" name="credit_check_submit">Submit</button>
      </div>
    </div>
  </div>
  </form>
</div>
