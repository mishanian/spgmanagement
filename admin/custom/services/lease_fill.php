<?php
$employee_id = $_SESSION['UserID'];
include_once('../pdo/dbconfig.php');
include_once('../pdo/Class.Services.php');
$DB_services = new Services($DB_con);

$employee_company_info    = $DB_services->get_employee_company_info($employee_id);
$default_lessor_name      = $employee_company_info['name'];
$default_lessor_email     = $employee_company_info['contact_email'];
$default_lessor_telephone = $employee_company_info['phone'];
$default_lessor_fax       = $employee_company_info['fax'];
$default_lessor_city      = $employee_company_info['city'];
$default_lessor_address   = $employee_company_info['address1'] . ', ' . $employee_company_info['address2'];
$default_postal_code      = $employee_company_info['postal_code'];

//lease address
$building_address_options = $DB_services->get_building_addresses_by_employee($employee_id);

?>
<link rel="stylesheet" href="custom/services/css/lease_style.css">
<link rel="stylesheet" href="custom/services/css/form-control.css">
<link href="custom/services/css/bootstrap-datepicker3.standalone.css" rel="stylesheet" type="text/css" />
<script src="custom/services/js/bootstrap-datepicker.js"></script>

<style>
input[type=text] {
    min-width: 0px !important;
}
</style>
<div class="container card">
    <form action="custom/services/lease_generator.php" class="form-horizontal" method="post" id="lease_form">
        <!-- lessor 1 -->
        <legend id="lessor">LESSOR</legend>
        <div class="form-group row">
            <label for="name_lessor1" class="col-md-1 control-label">*Name</label>
            <div class="col-md-4">
                <input type="text" id="name_lessor1" name="name_lessor1" placeholder="name of lessor"
                    class="form-control" required value="<?php echo $default_lessor_name; ?>">
            </div>
            <label for="email_lessor1" class="col-md-1 col-md-offset-1 control-label">*Email</label>
            <div class="col-md-4">
                <input type="email" id="email_lessor1" name="email_lessor1" placeholder="email of lessor"
                    class="form-control" required value="<?php echo $default_lessor_email; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="address_lessor1" class="col-md-1 control-label">*Address</label>
            <div class="col-md-10">
                <input type="text" id="address_lessor1" name="address_lessor1" placeholder="No. / Street / Apt."
                    class="form-control" required value="<?php echo $default_lessor_address; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="municipality_lessor1" class="col-md-1 control-label">*City</label>
            <div class="col-md-4">
                <input type="text" id="municipality_lessor1" name="municipality_lessor1" class="form-control"
                    placeholder="Municipality" required value="<?php echo $default_lessor_city; ?>">
            </div>
            <label for="postcode_lessor1" class="col-md-2  control-label">*Postal code</label>
            <div class="col-md-4">
                <input type="text" class="form-control" id="postcode_lessor1" name="postcode_lessor1"
                    placeholder="post code" required value="<?php echo $default_postal_code; ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="telephone_lessor1" class="col-md-1 control-label">*Telephone</label>
            <div class="col-md-4">
                <input type="text" id="telephone_lessor1" name="telephone_lessor1" class="form-control"
                    placeholder="Telephone No." required value="<?php echo $default_lessor_telephone; ?>">
            </div>
            <label for="otherphone_lessor1" class="col-md-2 control-label">Ohter Telephone</label>
            <div class="col-md-4">
                <input type="text" class="form-control" id="otherphone_lessor1" name="otherphone_lessor1"
                    placeholder="Other Telephone No." value="<?php echo $default_lessor_fax ?>">
            </div>
        </div>
        <!-- lessor 2 -->
        <div class="checkbox" data-toggle="collapse" data-target="#lessor2">
            <label>
                <input type="checkbox" name="second_lessor">The second lessor
            </label>
        </div>

        <div class="collapse" id="lessor2">
            <div class="card">
                <div class="form-group row">
                    <label for="name_lessor2" class="col-md-1 control-label">*Name</label>
                    <div class="col-md-4">
                        <input type="text" id="name_lessor2" name="name_lessor2" placeholder="name of lessor"
                            class="form-control">
                    </div>
                    <label for="email_lessor2" class="col-md-1 col-md-offset-1 control-label">*Email</label>
                    <div class="col-md-4">
                        <input type="email" id="email_lessor2" name="email_lessor2" placeholder="email of lessor"
                            class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_lessor2" class="col-md-1 control-label">*Address</label>
                    <div class="col-md-10">
                        <input type="text" id="address_lessor2" name="address_lessor2" placeholder="No. / Street / Apt."
                            class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="municipality_lessor2" class="col-md-1 control-label">City</label>
                    <div class="col-md-4">
                        <input type="text" id="municipality_lessor2" name="municipality_lessor2" class="form-control"
                            placeholder="Municipality">
                    </div>
                    <label for="postcode_lessor2" class="col-md-2  control-label">*Postal code</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="postcode_lessor2" name="postcode_lessor2"
                            placeholder="post code">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="telephone_lessor2" class="col-md-1 control-label">*Telephone</label>
                    <div class="col-md-4">
                        <input type="text" id="telephone_lessor2" name="telephone_lessor2" class="form-control"
                            placeholder="Telephone No.">
                    </div>
                    <label for="otherphone_lessor2" class="col-md-2 control-label">Ohter Telephone</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="otherphone_lessor2" name="otherphone_lessor2"
                            placeholder="Other Telephone No.">
                    </div>
                </div>
            </div>
        </div>
        <!-- lessee1 -->
        <legend class="big_title" id="lessee">LESSEE</legend>
        <div class="form-group row">
            <label for="name_lessee1" class="col-md-1 control-label">*Name</label>
            <div class="col-md-4">
                <input type="text" id="name_lessee1" name="name_lessee1" placeholder="name of lessee"
                    class="form-control" required>
            </div>
            <label for="email_lessee1" class="col-md-1 col-md-offset-1 control-label">*Email</label>
            <div class="col-md-4">
                <input type="email" id="email_lessee1" name="email_lessee1" placeholder="email of lessee"
                    class="form-control" required>
            </div>
        </div>
        <div class="form-group row">
            <label for="address_lessee1" class="col-md-1 control-label">Address</label>
            <div class="col-md-10">
                <input type="text" id="address_lessee1" name="address_lessee1" placeholder="No. / Street / Apt."
                    class="form-control" required>
            </div>
        </div>
        <div class="form-group row">
            <label for="municipality_lessee1" class="col-md-1 control-label">City</label>
            <div class="col-md-4">
                <input type="text" id="municipality_lessee1" name="municipality_lessee1" class="form-control"
                    placeholder="Municipality" required>
            </div>
            <label for="postcode_lessee1" class="col-md-2  control-label">*Postal code</label>
            <div class="col-md-4">
                <input type="text" class="form-control" id="postcode_lessee1" name="postcode_lessee1"
                    placeholder="post code" required>
            </div>
        </div>
        <div class="form-group row">
            <label for="telephone_lessee1" class="col-md-1 control-label">*Telephone</label>
            <div class="col-md-4">
                <input type="text" id="telephone_lessee1" name="telephone_lessee1" class="form-control"
                    placeholder="Telephone No." required>
            </div>
            <label for="otherphone_lessee1" class="col-md-2 control-label">Ohter Telephone</label>
            <div class="col-md-4">
                <input type="text" class="form-control" id="otherphone_lessee1" name="otherphone_lessee1"
                    placeholder="Other Telephone No.">
            </div>
        </div>
        <!-- lessee2 -->
        <div class="checkbox" data-toggle="collapse" data-target="#lessee2">
            <label>
                <input type="checkbox" name="second_lessee">The second lessee
            </label>
        </div>

        <div class="collapse" id="lessee2">
            <div class="card">
                <div class="form-group row">
                    <label for="name_lessee2" class="col-md-1 control-label">*Name</label>
                    <div class="col-md-4">
                        <input type="text" id="name_lessee2" name="name_lessee2" placeholder="name of lessee"
                            class="form-control">
                    </div>
                    <label for="email_lessee2" class="col-md-1 col-md-offset-1 control-label">*Email</label>
                    <div class="col-md-4">
                        <input type="email" id="email_lessee2" name="email_lessee2" placeholder="email of lessee"
                            class="form-control">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="address_lessee2" class="col-md-1 control-label">*Address</label>
                    <div class="col-md-10">
                        <input type="text" id="address_lessee2" name="address_lessee2" placeholder="No. / Street / Apt."
                            class="form-control">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="municipality_lessee2" class="col-md-1 control-label">City</label>
                    <div class="col-md-4">
                        <input type="text" id="municipality_lessee2" name="municipality_lessee2" class="form-control"
                            placeholder="Municipality">
                    </div>
                    <label for="postcode_lessee2" class="col-md-2  control-label">*Postal code</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="postcode_lessee2" name="postcode_lessee2"
                            placeholder="post code">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="telephone_lessee2" class="col-md-1 control-label">*Telephone</label>
                    <div class="col-md-4">
                        <input type="text" id="telephone_lessee2" name="telephone_lessee2" class="form-control"
                            placeholder="Telephone No.">
                    </div>
                    <label for="otherphone_lessee2" class="col-md-2 control-label">Ohter Telephone</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="otherphone_lessee2" name="otherphone_lessee2"
                            placeholder="Other Telephone No.">
                    </div>
                </div>
            </div>
        </div>

        <!-- part B -->
        <legend class="big_title" id="description">DESCRIPTION AND DESTINATION OF LEASED DWELLING,ACCESSORIES AND
            DEPENDENCIES
        </legend>
        <div class="form-group row">
            <label for="address_l" class="col-md-1 control-label">*Adresss</label>
            <div class="col-md-6">
                <select id="address_l" name="address_lease" class="form-control">
                    <?php
                    foreach ($building_address_options as $one) {
                        echo '<option value="' . $one["building_id"] . '">' . $one["address"] . '</option>';
                    }
                    ?>
                </select>
            </div>

            <label for="apart_l" class="col-md-1 control-label">*Apartment</label>
            <input type="hidden" id="apartment_name" name="apartment_name">
            <div class="col-md-3">
                <select id="apart_l" name="apartment_lease" class="form-control">

                </select>
            </div>
        </div>

        <div class="form-group row">
            <label for="municipality_l" class="col-md-1 control-label">*City</label>
            <div class="col-md-2">
                <input type="text" id="municipality_l" name="municipality_lease" class="form-control"
                    placeholder="Municipality">
            </div>

            <label for="postcode_l" class="col-md-2 control-label">*Postal code</label>
            <div class="col-md-2">
                <input type="text" id="postcode_l" name="postcode_lease" class="form-control" placeholder="Postal code">
            </div>

            <label for="no_rooms" class="col-md-2 control-label">*Number of rooms</label>
            <div class="col-md-2">
                <input type="text" id="no_rooms" name="no_rooms" class="form-control" placeholder="No. of rooms">
            </div>
        </div>

        <div class="form-group row">
            <p class="col-md-5 col-md-offset-1 criteria">The dwelling is leased for ressidential purpose only.</p>
            <div class="col-md-1 radio">
                <label><input type="radio" name="resident_only" value="1">Yes</label>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon">
                        <input type="radio" name="resident_only" value="0">&nbsp;No</span>
                    <input type="text" class="form-control" name="usage_lease"
                        placeholder="professional actvities,commercial actvities" style="width: 100% !important;">
                </div>
            </div>
        </div>

        <div class="form-group row">
            <p class="col-md-6 col-md-offset-1 criteria">The dwelling is located in a unit under divided
                co-ownership.</p>
            <div class="col-md-1 radio">
                <label><input type="radio" name="divided_co_ownership" value="1">Yes</label>
            </div>
            <div class="col-md-1 radio">
                <label><input type="radio" name="divided_co_ownership" value="0">No</label>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-2 col-md-offset-1">
                <div class="checkbox">
                    <label><input type="checkbox" class="criteria" name="outdoor_parking">Outdoor parking</label>
                </div>
            </div>
            <label for="outdoor_parking_num" class="col-md-2 control-label">Number of places</label>
            <div class="col-md-2">
                <input type="text" placeholder="number of places" class="form-control" id="outdoor_parking_num"
                    name="outdoor_parking_num">
            </div>
            <label for="outdoor_parking_ps" class="col-md-2 control-label">parking spaces</label>
            <div class="col-md-2">
                <input type="text" placeholder="Parking spaces" class="form-control" id="outdoor_parking_ps"
                    name="outdoor_parking_ps">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-2 col-md-offset-1">
                <div class="checkbox">
                    <label><input type="checkbox" class="criteria" name="indoor_parking">Indoor parking</label>
                </div>
            </div>
            <label for="indoor_parking_num" class="col-md-2 control-label">Number of places</label>
            <div class="col-md-2">
                <input type="text" placeholder="number of places" class="form-control" id="indoor_parking_num"
                    name="indoor_parking_num">
            </div>
            <label for="indoor_parking_ps" class="col-md-2 control-label">parking spaces</label>
            <div class="col-md-2">
                <input type="text" placeholder="Parking spaces" class="form-control" id="indoor_parking_ps"
                    name="indoor_parking_ps">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-10 col-md-offset-1">
                <div class="input-group">
                    <span class="input-group-addon">
                        <input type="checkbox" name="locker">&nbsp;Locker or storage space
                    </span>
                    <input type="text" class="form-control" name="locker_spf" style="width: 100% !important;">
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label class="control-label col-md-3 col-md-offset-1">Other accessories and dependencies</label>
            <div class="col-md-7">
                <input type="text" class="form-control" name="other_accessory">
            </div>
        </div>

        <div class="form-group row">
            <p class="col-md-4 col-md-offset-1 criteria">Furniture is leased and included in the rent.</p>
            <div class="col-md-1 radio">
                <label><input type="radio" name="furniture_include" id="furniture_include_1" value="1">Yes</label>
            </div>
            <div class="col-md-1 radio">
                <label><input type="radio" name="furniture_include" id="furniture_include_2" value="0">No</label>
            </div>
        </div>

        <!-- appliances -->
        <div class="checkbox">
            <label><input type="checkbox" data-toggle="collapse" data-target="#appliances"
                    name="appliances">Appliances</label>
        </div>

        <div class="collapse" id="appliances">
            <div class="form-group row">
                <div class="col-md-4">
                    <div class="checkbox">
                        <label class="control-label"><input type="checkbox" name="stove">Stove</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="checkbox">
                        <label class="control-label"><input type="checkbox" name="microwave">Microwave oven</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="checkbox">
                        <label class="control-label"><input type="checkbox" name="dishwasher">Dishwasher</label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-4">
                    <div class="checkbox">
                        <label class="control-label"><input type="checkbox" name="refrigerator">Refrigerator</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="checkbox">
                        <label class="control-label"><input type="checkbox" name="washer">Washer</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="checkbox">
                        <label class="control-label"><input type="checkbox" name="dryer">Dryer</label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon"><input type="checkbox" name="table">&nbsp;Tables</span>
                        <input type="text" class="form-control" placeholder="Number" name="table_num">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon"><input type="checkbox" name="chair">&nbsp;Chairs</span>
                        <input type="text" class="form-control" placeholder="Number" name="chair_num">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon"><input type="checkbox" name="chest">&nbsp;Chests</span>
                        <input type="text" class="form-control" placeholder="Number" name="chest_num">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon"><input type="checkbox" name="couch">&nbsp;Couches</span>
                        <input type="text" class="form-control" placeholder="Number" name="couch_num">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon"><input type="checkbox" name="armchair">&nbsp;Armchairs</span>
                        <input type="text" class="form-control" placeholder="Number" name="armchair_num">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon"><input type="checkbox" name="bed">&nbsp;beds</span>
                        <input type="text" class="form-control" placeholder="Number" name="bed_num">
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon">Others</span>
                        <input type="text" class="form-control" placeholder="Appliances / Numbers"
                            name="appliance_other">
                    </div>
                </div>
            </div>
        </div>

        <!-- Term of lease -->
        <legend class="big_title" id="term">TERM OF LEASE</legend>
        <div class="form-group row">
            <p class="col-md-3 col-md-offset-1 criteria">The term of the lease is</p>
            <div class="col-md-1 radio">
                <label><input type="radio" name="term_type" value="1" checked="checked">Fixed</label>
            </div>
            <div class="col-md-1 radio">
                <label><input type="radio" name="term_type" value="0">Indeterminate</label>
            </div>
        </div>

        <div class="form-group row">
            <label for="date_start" style="margin: 0 15px 0 112px">Term:&nbsp;&nbsp;&nbsp;&nbsp;From</label>
            <input type="text" class="text-field date_input" id="date_start" name="lease_date_start"
                placeholder="YYYY-MM-DD">
            <label for="date_end" style="margin:0 15px">To</label>
            <input type="text" class="text-field date_input" id="date_end" name="lease_date_end"
                placeholder="YYYY-MM-DD">
        </div>

        <!-- D:rent -->
        <legend class="big_title" id="rent">RENT</legend>
        <div class="form-group row">
            <div class="col-md-3 col-md-offset-1">
                <div class="input-group">
                    <span class="input-group-addon">The rent $</span>
                    <input type="text" class="form-control" name="pure_rent">
                </div>
            </div>
            <div class="col-md-2" style="padding-left: 0px">
                <div class="col-md-6 radio">
                    <label><input type="radio" name="pure_rent_period" value="1">Monthly</label>
                </div>
                <div class="col-md-5 radio">
                    <label><input type="radio" name="pure_rent_period" value="0">Weekly</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon">The total cost of service $</span>
                    <input type="text" class="form-control" name="cost_service" style="width: 100%!important;">
                </div>
            </div>
            <div class="col-md-2" style="padding-left: 0px">
                <div class="col-md-6 radio">
                    <label><input type="radio" name="cost_service_period" value="1">Monthly</label>
                </div>
                <div class="col-md-5 radio">
                    <label><input type="radio" name="cost_service_period" value="0">Weekly</label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3 col-md-offset-1">
                <div class="input-group">
                    <span class="input-group-addon">The total rent $</span>
                    <input type="text" class="form-control" name="rent_total" style="width: 100%!important;">
                </div>
            </div>
            <div class="col-md-2" style="padding-left: 0px">
                <div class="col-md-6 radio">
                    <label><input type="radio" name="rent_total_period" value="1">Monthly</label>
                </div>
                <div class="col-md-5 radio">
                    <label><input type="radio" name="rent_total_period" value="0">Weekly</label>
                </div>
            </div>
            <div class="col-md-5">
                <span class="col-md-9" style="padding: 6px 0px">The lessee is a beneficiary of a rent subsidy
                    program.</span>
                <div class="col-md-3" style="padding-left: 0px">
                    <div class="col-md-6 radio">
                        <label><input type="radio" name="subsidy_program" value="1">Yes</label>
                    </div>
                    <div class="col-md-6 radio">
                        <label><input type="radio" name="subsidy_program" value="0">No</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-5 col-md-offset-1">
                <label>THE FIRST PAYMENT</label>
                <div class="row">
                    <div class="col-md-5">
                        <p class="control-label">The rent will be paid on</p>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control date_input" name="rent_pay_date"
                            placeholder="YYYY-MM-DD">
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <label>OTHER PAYMENT PERIOD</label>
                <div class="row">
                    <div class="col-md-7">
                        <p class="control-label">The rent will be paid on the 1st day</p>
                    </div>
                    <div class="col-md-5">
                        <div class="col-md-6 radio">
                            <label><input type="radio" name="rent_pay_time" value="1">Monthly</label>
                        </div>
                        <div class="col-md-6 radio">
                            <label><input type="radio" name="rent_pay_time" value="0">Weekly</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-md-offset-1 col-md-2 control-label">METHOD OF PAYMENT</label>
            <div class="checkbox col-md-3">
                <label><input type="checkbox" name="pay_method[]" value="1">Cash</label>
                <label><input type="checkbox" name="pay_method[]" value="2">Cheque</label>
                <label><input type="checkbox" name="pay_method[]" value="3">Bank transfer</label>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-addon"><input type="checkbox" name="pay_method[]"
                            value="0">&nbsp;Other</span>
                    <input type="text" class="form-control" name="pay_method_other" style="width: 100%!important;">
                </div>
            </div>
        </div>
        <div class="form-group row">
            <p class="criteria col-md-5 col-md-offset-1" style="padding-left: 22px">The lessee agrees to give postdated
                cheques for the lease.</p>
            <div class="col-md-4">
                <div class="col-md-2 radio">
                    <label><input type="radio" name="postdated_cheque" value="1">Yes</label>
                </div>
                <div class="col-md-2 radio">
                    <label><input type="radio" name="postdated_cheque" value="0">No</label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="pay_place" class="col-md-offset-1 col-md-2 control-label">The place of payment</label>
            <div class="col-md-7">
                <input type="text" class="form-control" id="pay_place" name="pay_place">
            </div>
        </div>


        <!-- services and conditions -->
        <legend class="big_title" id="service">SERVICES AND CONDITIONS</legend>
        <div class="form-group row">
            <label class="control-label col-md-3">By-laws of the immovable</label>
            <div class="col-md-3">
                <input type="text" class="form-control date_input" name="immovable_date" placeholder="YYYY-MM-DD">
            </div>
            <div class="checkbox col-md-6">
                <label><input type="checkbox" name="immovable_before">Before entering into the lease</label>
            </div>
        </div>
        <label class="middle_title">The work and repair (The work and repairs to be done by lessor and the timetable for
            performing)</label>
        <div class="form-group row">
            <p class="control-label col-md-2 col-md-offset-1">Before the dwelling</p>
            <div class="col-md-8">
                <input type="text" class="form-control" name="before_work">
            </div>
        </div>
        <div class="form-group row">
            <p class="control-label col-md-2 col-md-offset-1">During the lease</p>
            <div class="col-md-8">
                <input type="text" class="form-control" name="during_work">
            </div>
        </div>

        <div class="form-group row">
            <label class=" col-md-2 control-label col-md-offset-1">Janitorial Services</label>
            <div class="col-md-7">
                <input type="text" class="form-control" name="janitorial_service">
            </div>
            <div class="checkbox col-md-1">
                <label><input type="checkbox" data-toggle="collapse" data-target="#janitor"
                        name="if_janitor">Janitor</label>
            </div>
        </div>

        <div class="collapse" id="janitor">
            <div class="card card-custom" style="padding-right: 60px">
                <div class="form-group row">
                    <label class="col-md-2 control-label">Name</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="janitor_name">
                    </div>
                    <label class="col-md-2 control-label">Email</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="janitor_email">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 control-label">Telephone_1</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="janitor_tele1">
                    </div>
                    <label class="col-md-2 control-label">Telephone_2</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="janitor_tele2">
                    </div>
                </div>
            </div>
        </div>

        <label class="middle_title">Services,Taxes,Consumption costs</label>
        <div class="form-group row">
            <div class="col-md-5 col-md-offset-1">
                <p class="criteria col-md-7">The heating of the dwelling</p>
                <div class="radio col-md-2">
                    <label><input type="radio" name="service_heating" id="service_heating_1" value="1">Lessor</label>
                </div>
                <div class="radio col-md-2">
                    <label><input type="radio" name="service_heating" id="service_heating_2" value="2">Lessee</label>
                </div>
            </div>

            <div class="col-md-5">
                <p class="criteria col-md-8">Water consumption for the dwelling</p>
                <div class="radio col-md-2">
                    <label><input type="radio" name="water_consumption" id="water_consumption_1"
                            value="1">Lessor</label>
                </div>
                <div class="radio col-md-2">
                    <label><input type="radio" name="water_consumption" id="water_consumption_2"
                            value="2">Lessee</label>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-5 col-md-offset-1">
                <p class="criteria col-md-7 text-center">Gas</p>
                <div class="radio col-md-2">
                    <label><input type="radio" name="gas" id="gas_1" value="1">Lessor</label>
                </div>
                <div class="radio col-md-2">
                    <label><input type="radio" name="gas" id="gas_2" value="2">Lessee</label>
                </div>
            </div>

            <div class="col-md-5">
                <p class="criteria col-md-8 text-center">Electricity</p>
                <div class="radio col-md-2">
                    <label><input type="radio" name="electricity" id="electricity_1" value="1">Lessor</label>
                </div>
                <div class="radio col-md-2">
                    <label><input type="radio" name="electricity" id="electricity_2" value="2">Lessee</label>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-5 col-md-offset-1">
                <p class="criteria col-md-7 text-center">Hot water heater</p>
                <div class="radio col-md-2">
                    <label><input type="radio" name="hot_water_heater" id="hot_water_heater_1" value="1">Lessor</label>
                </div>
                <div class="radio col-md-2">
                    <label><input type="radio" name="hot_water_heater" id="hot_water_heater_2" value="2">Lessee</label>
                </div>
            </div>

            <div class="col-md-5">
                <p class="criteria col-md-8 text-center">Hot water</p>
                <div class="radio col-md-2">
                    <label><input type="radio" name="hot_water" id="hot_water_1" value="1">Lessor</label>
                </div>
                <div class="radio col-md-2">
                    <label><input type="radio" name="hot_water" id="hot_water_2" value="2">Lessee</label>
                </div>
            </div>
        </div>

        <label class="middle_title">Snow and ice removal</label>
        <div class="form-group row">
            <div class="col-md-5 col-md-offset-1">
                <p class="criteria col-md-7 text-center">Parking area</p>
                <div class="radio col-md-2">
                    <label><input type="radio" name="sonwrm_parking" id="sonwrm_parking_1" value="1">Lessor</label>
                </div>
                <div class="radio col-md-2">
                    <label><input type="radio" name="sonwrm_parking" id="sonwrm_parking_2" value="2">Lessee</label>
                </div>
            </div>

            <div class="col-md-5">
                <p class="criteria col-md-8 text-center">Balcony</p>
                <div class="radio col-md-2">
                    <label><input type="radio" name="snowrm_balcony" id="snowrm_balcony_1" value="1">Lessor</label>
                </div>
                <div class="radio col-md-2">
                    <label><input type="radio" name="snowrm_balcony" id="snowrm_balcony_2" value="2">Lessee</label>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-5 col-md-offset-1">
                <p class="criteria col-md-7 text-center">Entrance,walkway,driveway</p>
                <div class="radio col-md-2">
                    <label><input type="radio" name="snowrm_entrance" id="snowrm_entrance_1" value="1">Lessor</label>
                </div>
                <div class="radio col-md-2">
                    <label><input type="radio" name="snowrm_entrance" id="snowrm_entrance_2" value="2">Lessee</label>
                </div>
            </div>

            <div class="col-md-5">
                <p class="criteria col-md-8 text-center">Stars</p>
                <div class="radio col-md-2">
                    <label><input type="radio" name="snowrm_stars" id="snowrm_stars_1" value="1">Lessor</label>
                </div>
                <div class="radio col-md-2">
                    <label><input type="radio" name="snowrm_stars" id="snowrm_stars_2" value="2">Lessee</label>
                </div>
            </div>
        </div>

        <label class="middle_title">Conditions</label>
        <div class="form-group row">
            <p class="criteria col-md-5 col-md-offset-1" style="padding-left: 22px">The lessee has a right of access to
                the land.</p>
            <div class="col-md-2">
                <div class="col-md-5 radio">
                    <label><input type="radio" name="right_access_land" id="right_access_land_1" value="1">Yes</label>
                </div>
                <div class="col-md-5 radio">
                    <label><input type="radio" name="right_access_land" id="right_access_land_0" value="0">No</label>
                </div>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Specify" name="right_access_land_spf">
            </div>
        </div>

        <div class="form-group row">
            <p class="criteria col-md-5 col-md-offset-1" style="padding-left: 22px">The lessee has a right to keep one
                or more animals.</p>
            <div class="col-md-2">
                <div class="col-md-5 radio">
                    <label><input type="radio" name="right_keep_animals" id="right_keep_animals_1" value="1">Yes</label>
                </div>
                <div class="col-md-5 radio">
                    <label><input type="radio" name="right_keep_animals" id="right_keep_animals_0" value="0">No</label>
                </div>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Specify" name="right_keep_animals_spf">
            </div>
        </div>

        <div class="form-group row">
            <p class="criteria col-md-4 col-md-offset-1" style="padding-left: 22px">Other services,Conditions and
                restrictions : </p>
            <div class="col-md-6">
                <input type="text" class="form-control" name="other_services">
            </div>
        </div>


        <!-- F: Restrictions .. -->
        <legend class="big_title" id="restriction">RESTRICTIONS ON THE RIGHT TO HAVE THE RENT FIXED AND THE LEASE
            MODIFIED
        </legend>
        <div class="from-group">
            <div class="col-md-offset-1">
                <div class="radio">
                    <input type="radio" name="restriction_immovable" id="restriction_immovable_1" value="1">The dwelling
                    is
                    located in
                    an immovable
                    erected five
                    years ago or
                    less.
                </div>
            </div>
        </div>
        <div class="from-group">
            <div class="col-md-offset-1">
                <div class="radio">
                    <input type="radio" name="restriction_immovable" id="restriction_immovable_2" value="2">The dewlling
                    is
                    located in
                    an immovable
                    whose used
                    for
                    residential
                    purpose
                    results from
                    a change of
                    destination
                    that was
                    made
                    five years
                    ago or less.

                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-4 col-md-offset-1" style="padding-top: 15px">The immovable became ready for habitation
                on</label>
            <div class="col-md-3" style="padding-top: 10px">
                <input type="text" class="form-control date_input" name="restriction_immovable_date"
                    placeholder="YYYY-MM-DD">
            </div>
        </div>


        <!-- G : notices  -->
        <legend id="notice">NOTICE TO A NEW LESSEE OR A SUBLESSEE</legend>
        <div class="form-group row">
            <p class="col-md-9 control-label">The lowest rent paid for the dwelling during the 12 monthes preceding the
                beginning of the lease, or the rent during the period :</p>
            <div class="col-md-2">
                <input type="Number" class="form-control" name="lowest_rent">
            </div>
        </div>

        <div class="form-group row">
            <div class="checkbox col-md-2 col-md-offset-1">
                <input type="checkbox" name="lowest_rent_period" value="1">Per month
            </div>
            <div class="checkbox col-md-2">
                <input type="checkbox" name="lowest_rent_period" value="2">Per week
            </div>
            <div class="input-group col-md-3">
                <span class="input-group-addon"><input type="checkbox" name="lowest_rent_period"
                        value="0">&nbsp;Other</span>
                <input type="text" class="form-control" name="lowest_rent_period_other" style="width: 100%!important;">
            </div>
        </div>
        <div class="form-group row">
            <p class="criteria col-md-8 col-md-offset-1" style="padding-left: 22px">The property leased,the services
                offered by the lessor and the
                conditions of your lease are the
                same.</p>
            <div class="col-md-3">
                <div class="col-md-3 radio">
                    <label><input type="radio" name="condition_same" id="condition_same_1" value="1">Yes</label>
                </div>
                <div class="col-md-3 radio">
                    <label><input type="radio" name="condition_same" id="condition_same_0" value="0"
                            data-toggle="collapse" data-target="#condition_no_same">No</label>
                </div>
            </div>
        </div>

        <div class="collapse" id="condition_no_same">
            <div class="form-group row">
                <p class="control-label col-md-2">The changes :</p>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="condition_changes">
                </div>
            </div>
        </div>

        <div class="form-group form-main">
            <div class="col-md-3 col-md-offset-2">
                <button type="submit" class="btn btn-success btn-lg btn-block">Submit</button>
            </div>
            <div class="col-md-3 col-md-offset-1">
                <button type="reset" class="btn btn-warning btn-lg btn-block">Reset</button>
            </div>
        </div>
    </form>
</div>

<script src="custom/services/js/uncheckable_radio.js"></script>
<script>
$(function() {
    $('#address_l').trigger('change');
});

var apartments_in_selected_building = null;

//------------- actions after lease address selection --------------

$('#address_l').on('change', function() {
    var building_id = $('#address_l').val();

    $.ajax({
        type: "post",
        url: "custom/services/services_controller.php",
        data: {
            action: "auto_fill_building_info",
            building_id: building_id,
        },
        dataType: "json",
        async: false,
        success: function(result) {
            if (result.code === 1) {
                $('#apart_l').empty();
                apartments_in_selected_building = result.data.apartments;

                for (var i = 0; i < apartments_in_selected_building.length; i++) {
                    $('#apart_l').append('<option value="' + apartments_in_selected_building[i]
                        .apartment_id + '">' + apartments_in_selected_building[i].unit_number +
                        '</option>');
                }

                $('#municipality_l').val(result.data.city);
                $('#postcode_l').val(result.data.postal_code);
            }
        },
        error: function(result) {
            console.log("Network Error:" + result);
        }
    });

});


//-------------- actions after lease apartment selection --------------

$('#apart_l').on('change', function() {
    var apartment_id = $('#apart_l').val();
    for (var i = 0; i < apartments_in_selected_building.length; i++) {
        if (apartments_in_selected_building[i].apartment_id == apartment_id) {
            $('#no_rooms').val(apartments_in_selected_building[i].rooms);
            $('#apartment_name').val(apartments_in_selected_building[i].unit_number);
            break;
        }
    }
});
</script>

<script>
$('.date_input').datepicker({
    format: 'y-MM-dd'
});
</script>