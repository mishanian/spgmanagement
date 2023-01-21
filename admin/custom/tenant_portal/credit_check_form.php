<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
    <title>CreditCheck</title>
    <!-- <link rel="icon" type="image/PNG" href="images/logo_spg.png" /> -->
    <link rel="stylesheet" type="text/css" href="css/credit_check_style.css">
    <link href="css/credit_check_bootstrap.css" rel="stylesheet">
    <style>
    #signature {
        width: 300px;
        height: 200px;
        border: 1px solid black;
    }
    </style>
</head>

<body>
    <?
    if (!empty($_GET['company_id'])) {
        $company_id = $_GET['company_id'];
    } else {
        $company_id = 9;
    } //SPG Canada
    include('../../../pdo/dbconfig.php');
    if (empty($_GET['l']) || $_GET['l'] == 1) {
        $lang = "en";
    } elseif ($_GET['l'] == 2) {
        $lang = "fr";
    } else {
        $lang = "ch";
    }

    $fields = resultArrayFields($DB_con, 'cc_field', "cc_$lang", 'creditcheck_lang', '');
    $provinces = resultArrayFields($DB_con, 'id', "province_short_code", 'provinces', '');
    $employees = resultArrayFields($DB_con, 'employee_id', "full_name", 'employee_infos', "active_id=1 and user_level in (1,8) and company_id=$company_id ");
    extract($fields);
    extract($provinces);
    // die(print_r($provinces));
    $provinces = array(1 => 'QC', 2 => 'ON', 3 => 'NS', 4 => 'NB', 5 => 'MB', 6 => 'BC', 7 => 'PE', 8 => 'SK', 9 => 'AB', 10 => 'NL');

    function resultArrayFields($DB_con, $field1, $field2, $table, $where)
    {
        if (empty($where)) {
            $where = 'true';
        }
        $arr = [];
        $query = "select $field1, $field2 from  $table where $where";
        $stmt = $DB_con->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            ${$row[$field1]} = $row[$field2];
            $arr[$row[$field1]] = $row[$field2];
        }
        return $arr;
    }


    ?>
    <div class="container">
        <form class="well form-horizontal" action="credit_check_action.php" method="post" id="investigation_form"
            enctype="multipart/form-data">
            <fieldset>

                <!-- Form Name -->

                <legend>
                    <!-- <img src="images/logo_spg.png" alt="logo" style="width:100px;height:80px;"> -->
                    <?= $Investigation ?>
                    <a class="language"
                        href="credit_check_form.php?l=1&c=<?= $company_id ?>">English&nbsp;&nbsp;&nbsp;&nbsp;</a>
                    <a class="language"
                        href="credit_check_form.php?l=2&c=<?= $company_id ?>">Français&nbsp;&nbsp;|&nbsp;&nbsp;</a>
                    <a class="language"
                        href="credit_check_form.php?l=3&c=<?= $company_id ?>">中文|&nbsp;&nbsp;|&nbsp;&nbsp;</a>

                </legend>
                <!--
                <div class="form-group">
                    <label class="col-md-12 control-label-info"><?= $TopAddress ?></label>
                </div>
-->
                <input type="hidden" name="potential_id" value="<?php echo (isset($_GET["pt"])) ? $_GET["pt"] : 0; ?>">

                <legend><?= $DwellingID ?></legend>


                <div class="form-group">
                    <label class="col-md-2 control-label"><?= $ApplyingAddress ?></label>
                    <div class="col-md-5 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="dwelling_address" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $Apt ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="dwelling_apt" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-2 control-label"><?= $City ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="dwelling_city" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $Province ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <select name="dwelling_province" class="form-control" type="text" autocomplete="off">
                                <?
                                foreach ($provinces as $provinceId => $provinceName) {
                                    echo "<option value='$provinceId'";
                                    if ($provinceId == 1) {
                                        echo " selected ";
                                    }
                                    echo ">$provinceName</option>";
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $PostalCode ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="dwelling_postalcode" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>


                <legend><?= $ProspectiveTenant ?></legend>


                <div class="form-group">
                    <label class="col-md-2 control-label">*<?= $FamilyName ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="tenant_surname" class="form-control" type="text" autocomplete="off" required>
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $DriverID ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="tenant_driverid" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-10 control-label"><?= $TenantStatus ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <select name="tenant_status" class="form-control" type="text" autocomplete="off">
                                <option value="Married"><?= $Married ?></option>
                                <option value="Separated"><?= $Separated ?></option>
                                <option value="Single"><?= $Single ?></option>
                                <option value="Divorced"><?= $Divorced ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label">*<?= $FirstName ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="tenant_firstname" class="form-control" type="text" autocomplete="off" required>
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $PassportID ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="tenant_passportid" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $Nationality ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="tenant_nationality" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-2 control-label"><?= $Tel ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
                            <input name="tenant_tel" class="form-control" type="text" autocomplete="off" value="+1"
                                placeholder="+15141234567">
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $Gender ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <select name="tenant_gender" class="form-control" type="text" autocomplete="off">
                                <option value="2"><?= $Female ?></option>
                                <option value="1"><?= $Male ?></option>
                            </select>
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $SINNo ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="tenant_sinno" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-2 control-label">*<?= $DateofBirth ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group" data-provide="datepicker">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                            <input name="tenant_dateofbirth" id="tenant_dateofbirth" class="form-control" type="date"
                                autocomplete="off" required>
                        </div>
                    </div>
                    <label class="col-md-4 control-label">*<?= $EMail ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                            <input name="tenant_email" class="form-control" type="email" autocomplete="off"
                                data-error="The email address is invalid" required>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-2 control-label">*<?= $CurrentAddress ?></label>
                    <div class="col-md-5 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="tenant_address" class="form-control" type="text" autocomplete="off" required>
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $Apt ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="tenant_apt" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-2 control-label"><?= $City ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="tenant_city" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $Province ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <select name="tenant_province" class="form-control" type="text" autocomplete="off">
                                <?
                                foreach ($provinces as $provinceId => $provinceName) {
                                    echo "<option value='$provinceId'";
                                    if ($provinceId == 1) {
                                        echo " selected ";
                                    }
                                    echo ">$provinceName</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $PostalCode ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="tenant_postalcode" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label"><?= $HowLongLive ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="tenant_howlong" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-4 control-label"><?= $YourNameLease ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="tenant_onlease" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label"><?= $NameLandlord ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="tenant_landlord" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-4 control-label">*<?= $TelLandlord ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
                            <input name="tenant_tellandlord" class="form-control" type="text" autocomplete="off"
                                required>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label">*<?= $EndCurrentLeaseDate ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="tenant_endlease" id="tenant_endlease" class="form-control" type="date"
                                autocomplete="off" required>
                        </div>
                    </div>
                    <label class="col-md-4 control-label">*<?= $CurrentRent ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="tenant_rent" class="form-control" type="text" autocomplete="off" required>
                        </div>
                    </div>
                </div>



                <legend><?= $Dependents ?></legend>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?= $Number ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="dependent_number" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $Gender ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <select name="dependent_gender" class="form-control" type="text" autocomplete="off">
                                <option value="2"><?= $Female ?></option>
                                <option value="1"><?= $Male ?></option>
                            </select>
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $Age ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="dependent_age" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>


                <legend><?= $Employment ?></legend>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?= $NameEmployer ?></label>
                    <div class="col-md-5 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="employment_name" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $Tel ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
                            <input name="employment_tel" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?= $CompanyAddress ?></label>
                    <div class="col-md-5 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="employment_address" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $Unit ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="employment_unit" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?= $City ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="employment_city" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $Province ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <select name="employment_province" class="form-control" type="text" autocomplete="off">
                                <?
                                foreach ($provinces as $provinceId => $provinceName) {
                                    echo "<option value='$provinceId'";
                                    if ($provinceId == 1) {
                                        echo " selected ";
                                    }
                                    echo ">$provinceName</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <label class="col-md-2 control-label"><?= $PostalCode ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
                            <input name="employment_postalcode" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label"><?= $OccupationalTitle ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="employment_occupation" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-4 control-label"><?= $MonthlySalary ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="employment_salary" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label"><?= $HowLongWorked ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="employment_howlong" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-4 control-label"><?= $OtherResource ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="employment_other" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-9 control-label"><?= $PayStub ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <select name="employment_paystub" class="form-control" type="text" autocomplete="off">
                                <option value="Yes"><?= $Yes ?></option>
                                <option value="No"><?= $No ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <legend><?= $BankingTitle ?></legend>




                <div class="form-group">
                    <label class="col-md-11 " style="text-align:left!important"><I
                            class="span_banking"><?= $BankText ?></I></label>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label">*<?= $NameBank ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                            <input name="banking_name" class="form-control" type="text" autocomplete="off" required>
                        </div>
                    </div>
                    <label class="col-md-4 control-label">*<?= $BankBranchAddress ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                            <input name="banking_address" class="form-control" type="text" autocomplete="off" required>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label">*<?= $AccountNo ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                            <input name="banking_accountno" class="form-control" type="text" autocomplete="off"
                                required>
                        </div>
                    </div>
                    <label class="col-md-4 control-label">*<?= $TransitNumber ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                            <input name="banking_transit" class="form-control" type="text" autocomplete="off" required>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label">*<?= $InstitutionalNumber ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-globe"></i></span>
                            <input name="banking_institution" class="form-control" type="text" autocomplete="off"
                                required>
                        </div>
                    </div>
                    <label class="col-md-4 control-label"><?= $Tel ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
                            <input name="banking_tel" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label"><?= $CreditCardCompany ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
                            <input name="banking_company" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-4 control-label"><?= $CreditLimit ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
                            <input name="banking_limit" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label"><?= $Type ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <select name="banking_payment" class="form-control" type="text" autocomplete="off">
                                <option value="VISA">VISA</option>
                                <option value="Master Card">Master Card</option>
                                <option value="American Express">American Express</option>
                                <option value="VISA-Debit">VISA-Debit</option>
                            </select>
                        </div>
                    </div>
                    <label class="col-md-4 control-label"><?= $TypeInsurance ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <select name="banking_insurancetype" class="form-control" type="text" autocomplete="off">
                                <option value="Auto">Auto</option>
                                <option value="Home">Home</option>
                                <option value="Life">Life</option>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-4 control-label"><?= $InsuranceCompanyName ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
                            <input name="banking_insurance" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-4 control-label"><?= $PolicyNo ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
                            <input name="banking_policyno" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                </div>




                <legend>HydroQuebec</legend>

                <div class="form-group">

                    <label class="col-md-11 " style="text-align:left!important"><I
                            class="span_banking"><?= $HQText ?></I></label>
                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label"><?= $HydroQuebecAccount ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
                            <input name="banking_hydroquebec" class="form-control" type="text" autocomplete="off">
                        </div>
                    </div>
                    <label class="col-md-4 control-label"><?= $MovingDate ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input name="banking_movingdate" id="banking_movingdate" class="form-control" type="date"
                                autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-7 control-label"><?= $AccountActive ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <select name="banking_active" class="form-control" type="text" autocomplete="off">
                                <option value="Yes"><?= $Yes ?></option>
                                <option value="No"><?= $No ?></option>
                            </select>
                        </div>
                    </div>
                </div>


                <legend><?= $Questions ?></legend>


                <div class="form-group">
                    <label class="col-md-6 control-label"><?= $Crime ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <select name="question_crime" class="form-control" type="text" autocomplete="off">
                                <option value="Yes"><?= $Yes ?></option>
                                <option value="No"><?= $No ?></option>
                            </select>
                        </div>
                    </div>
                    <label class="col-md-6 control-label"><?= $Bankruptcy ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <select name="question_bankruptcy" class="form-control" type="text" autocomplete="off">
                                <option value="Yes"><?= $Yes ?></option>
                                <option value="No"><?= $No ?></option>
                            </select>
                        </div>
                    </div>
                </div>



                <div class="form-group">
                    <label class="col-md-7 control-label"><?= $RegieduLogement ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <select name="question_file" class="form-control" type="text" autocomplete="off">
                                <option value="Yes"><?= $Yes ?></option>
                                <option value="No"><?= $No ?></option>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-7 control-label"><?= $Declined ?></label>
                    <div class="col-md-2 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <select name="question_declined" class="form-control" type="text" autocomplete="off">
                                <option value="Yes"><?= $Yes ?></option>
                                <option value="No"><?= $No ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-7 control-label"><?= $CreditCardCompanyDeclined ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-credit-card"></i></span>
                            <input name="question_creditcompany" class="form-control" type="text">
                        </div>
                    </div>
                </div>





                <legend><?= $ProfileText ?></legend>




                <div class="form-group">




                    <label class="col-md-4 control-label">* <?= $BankStatement ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-upload"></i></span>
                            <input type='file' name='bank_statement_file' class="form-control-file" required>
                        </div>
                    </div>

                    <label class="col-md-4 control-label">* <?= $VoidCheck ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-upload"></i></span>
                            <input type='file' name='void_check_file' class="form-control-file" required>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label">* <?= $PhotoId1 ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-upload"></i></span>
                            <input type='file' name='photo1_file' class="form-control-file" required>
                        </div>
                    </div>
                    <label class="col-md-4 control-label">* <?= $PhotoId2 ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-upload"></i></span>
                            <input type='file' name='photo2_file' class="form-control-file" required>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label class="col-md-4 control-label"><?= $PaySlip ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-upload"></i></span>
                            <input type='file' name='pay_slip_file' class="form-control-file">
                        </div>
                    </div>

                    <label class="col-md-4 control-label"><?= $Agent ?></label>
                    <div class="col-md-4 inputGroupContainer">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <!-- <select name="agent_id" class="form-control" type="text" autocomplete="off">
              <?
                foreach ($employees as $employeeId => $employeeName) {
                    echo "<option value='$employeeId'";
                    // if ($employeeId==1){echo " selected ";}
                    echo ">$employeeName</option>";
                }
                ?>
       </select> -->
                            <input type="text" name="agent_name">
                        </div>
                    </div>

                </div>

                <!--
<div class="form-group">
  <label class="col-md-4 control-label"><?= $UserNameText ?></label>
  <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
      <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
      <input type="input" name="userName">

    </div>
  </div>

  <label class="col-md-4 control-label"><?= $UserPassText ?></label>
  <div class="col-md-4 inputGroupContainer">
    <div class="input-group">
      <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
      <input type="password" name="userPass">

    </div>
  </div>
</div>
-->

                <div class="row">
                    <div class="col-md-12">
                        <p><b>Note:</b> If you have made any payment, this payment will be considered as partial payment
                            of
                            the first month rent for the lease to be signed. This payment is not refundable if the
                            tenant
                            volunteerly refuse to sign the lease.</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div id="signature">
                            <canvas id="signature-pad" class="signature-pad" width="300px" height="200px"></canvas>
                        </div><br />
                        <input type="hidden" name="signatureData" id="signatureData">
                        <input class="btn btn-primary" type="submit" name="submit" value="Submit" />
                        <button class="btn btn-danger" type="button" onclick="window.signaturePad.clear();">Clear
                            Signature</button>
                    </div>

                    <div class="col-md-6 align-top" style="margin:50px; color:red; font-size:18pt;">
                        <?= $SignBox ?><br>
                        <span style="font-size:50pt">&lArr;</span>
                    </div>

                </div>

    </div>








    <!-- Success message -->
    <div class="alert alert-success" role="alert" id="success_message">Success <i
            class="glyphicon glyphicon-thumbs-up"></i> Thanks for contacting us, we will send you a document to your
        e-mail address for you to sign.</div>

    <!-- Button
        <div class="form-group">
          <label class="col-md-5 control-label"></label>
          <div class="col-md-7">
            <button type="submit" class="btn btn-warning btn-size" name="form_submit"><?= $SubmitText ?><span class="glyphicon glyphicon-send"></span></button>
          </div>
        </div>-->

    </fieldset>
    <input type="hidden" name="company_id" value="<?= $company_id ?>">
    </form>
    </div>
    </div><!-- /.container -->

    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/moment.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <!--
<script src="js/validation.js"></script>
-->
    <script>
    $(document).ready(function() {


        window.signaturePad = new SignaturePad(document.getElementById('signature-pad'));

        $('#investigation_form').submit(function() {
            const tenant_dateofbirth = document.getElementById("tenant_dateofbirth").value;
            const checkTenant_dateofbirth = moment(tenant_dateofbirth, 'YYYY-MM-DD', true).isValid();
            if (!checkTenant_dateofbirth) {
                alert("Please enter Date of Birth in correct format YYYY-MM-DD");
                document.getElementById("tenant_dateofbirth").focus();
                return false;
            }

            const tenant_endlease = document.getElementById("tenant_endlease").value;
            const checkTenant_endlease = moment(tenant_endlease, 'YYYY-MM-DD', true).isValid();
            if (!checkTenant_endlease) {
                alert("Please enter Lease End in correct format YYYY-MM-DD");
                document.getElementById("tenant_endlease").focus();
                return false;
            }

            const banking_movingdate = document.getElementById("banking_movingdate").value;
            if (banking_movingdate) {
                const checkBanking_movingdate = moment(banking_movingdate, 'YYYY-MM-DD', true)
                    .isValid();
                if (!checkBanking_movingdate) {
                    alert("Please enter Moving Date in correct format YYYY-MM-DD");
                    document.getElementById("banking_movingdate").focus();
                    return false;
                }
            }



            var data = window.signaturePad.toDataURL('image/png');
            $('#signatureData').val(data);

            if (window.signaturePad.isEmpty()) {
                alert('Please Sign below of the page');
                return false;
            }


        });
    })
    </script>
</body>

</html>