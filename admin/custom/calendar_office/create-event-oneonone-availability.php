<!DOCTYPE html>
<html lang="en">

<head>
    <title>Commercial Property Management</title>
    <!--Meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap-select.css" rel="stylesheet" type="text/css" />
    <link href="css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="css/owl.carousel.css" rel="stylesheet" type="text/css" />
    <link href="css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="css/bootstrapValidator.min.css" rel="stylesheet" type="text/css" />
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <button class="btn scrolltop-btn back-top"><i class="fa fa-angle-up"></i></button>
    <div class="modal fade" id="pop-login" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <ul class="login-tabs">
                        <li class="active">Login</li>
                        <li>Register</li>
                    </ul>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i
                            class="fa fa-close"></i></button>
                </div>
                <div class="modal-body login-block">
                    <div class="tab-content">
                        <div class="tab-pane fade in active">
                            <div class="message">
                                <p class="error text-danger"><i class="fa fa-close"></i> You are not Logedin</p>
                                <p class="success text-success"><i class="fa fa-check"></i> You are not Logedin</p>
                            </div>
                            <form>
                                <div class="form-group field-group">
                                    <div class="input-user input-icon">
                                        <input type="text" placeholder="Username">
                                    </div>
                                    <div class="input-pass input-icon">
                                        <input type="password" placeholder="Password">
                                    </div>
                                </div>
                                <div class="forget-block clearfix">
                                    <div class="form-group pull-left">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox">
                                                Remember me </label>
                                        </div>
                                    </div>
                                    <div class="form-group pull-right"> <a href="#" data-toggle="modal"
                                            data-dismiss="modal" data-target="#pop-reset-pass">I forgot username and
                                            password</a> </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Login</button>
                            </form>
                            <hr>
                            <a href="#" class="btn btn-social btn-bg-facebook btn-block"><i class="fa fa-facebook"></i>
                                login with facebook</a> <a href="#" class="btn btn-social btn-bg-linkedin btn-block"><i
                                    class="fa fa-linkedin"></i> login with linkedin</a> <a href="#"
                                class="btn btn-social btn-bg-google-plus btn-block"><i class="fa fa-google-plus"></i>
                                login with Google</a>
                        </div>
                        <div class="tab-pane fade">
                            <form>
                                <div class="form-group field-group">
                                    <div class="input-user input-icon">
                                        <input type="text" placeholder="Username">
                                    </div>
                                    <div class="input-email input-icon">
                                        <input type="email" placeholder="Email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox">
                                            I agree with your <a href="#">Terms & Conditions</a>. </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Register</button>
                            </form>
                            <hr>
                            <a href="#" class="btn btn-social btn-bg-facebook btn-block"><i class="fa fa-facebook"></i>
                                login with facebook</a> <a href="#" class="btn btn-social btn-bg-linkedin btn-block"><i
                                    class="fa fa-linkedin"></i> login with linkedin</a> <a href="#"
                                class="btn btn-social btn-bg-google-plus btn-block"><i class="fa fa-google-plus"></i>
                                login with Google</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="pop-reset-pass" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <ul class="login-tabs">
                        <li class="active">Reset Password</li>
                    </ul>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i
                            class="fa fa-close"></i></button>
                </div>
                <div class="modal-body">
                    <p>Please enter your username or email address. You will receive a link to create a new password via
                        email.</p>
                    <form>
                        <div class="form-group">
                            <div class="input-user input-icon">
                                <input placeholder="Enter your username or email" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block">Get new password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--start header section header v1-->
    <header id="header-section" class="header-section-4 header-main nav-left hidden-sm hidden-xs" data-sticky="0">
        <div class="container">
            <div class="header-left">
                <div class="logo"> <a href="index.php"> <img src="images/logo.jpg" alt="logo"> </a> </div>
                <nav class="navi main-nav">
                    <ul>
                        <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
                        <li><a href="map-listing.php"><i class="fa fa-list"></i> Listing</a></li>
                        <li><a href="about-us.html"><i class="fa fa-info"></i> About Us</a></li>
                        <li><a href="contact-us.html"><i class="fa fa-envelope"></i> Conatct Us</a></li>
                    </ul>
                </nav>
            </div>
            <div class="header-right">
                <div class="user"> <a href="#" data-toggle="modal" data-target="#pop-login">Sign In / Register</a>
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
                                <li> <a href="#" data-toggle="modal" data-target="#pop-login"> <i
                                            class="fa fa-user"></i> Log in / Register </a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--end header section header v1-->

    <!--start advanced search section-->
    <div class="advanced-search advance-search-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <form>
                        <div class="form-group search-long">
                            <div class="search">
                                <div class="input-search input-icon">
                                    <input class="form-control" type="text" placeholder="Search for a place to stay?">
                                </div>
                                <select name="location" title="Location" class="selectpicker bs-select-hidden"
                                    data-live-search="false">
                                    <option value="">All Cities</option>
                                    <option value="chicago"> Chicago</option>
                                    <option value="los-angeles"> Los Angeles</option>
                                    <option value="miami"> Miami</option>
                                    <option value="new-york"> New York</option>
                                </select>
                                <select name="area" title="Area" class="selectpicker bs-select-hidden"
                                    data-live-search="false">
                                    <option value="">All Areas</option>
                                    <option value="beverly-hills"> Beverly Hills</option>
                                    <option value="brickell"> Brickell</option>
                                    <option value="brickyard"> Brickyard</option>
                                    <option value="bronx"> Bronx</option>
                                    <option value="brooklyn"> Brooklyn</option>
                                    <option value="coconut-grove"> Coconut Grove</option>
                                    <option value="downtown"> Downtown</option>
                                    <option value="eagle-rock"> Eagle Rock</option>
                                    <option value="englewood"> Englewood</option>
                                    <option value="hermosa"> Hermosa</option>
                                    <option value="hollywood"> Hollywood</option>
                                    <option value="lincoln-park"> Lincoln Park</option>
                                    <option value="manhattan"> Manhattan</option>
                                    <option value="midtown"> Midtown</option>
                                    <option value="queens"> Queens</option>
                                    <option value="westwood"> Westwood</option>
                                    <option value="wynwood"> Wynwood</option>
                                </select>
                                <div class="advance-btn-holder">
                                    <button class="advance-btn btn" type="button"><i class="fa fa-gear"></i>
                                        Advanced</button>
                                </div>
                            </div>
                            <div class="search-btn">
                                <button class="btn btn-secondary">Go</button>
                            </div>
                        </div>
                        <div class="advance-fields">
                            <div class="row">
                                <div class="col-sm-3 col-xs-6">
                                    <div class="form-group">
                                        <select class="selectpicker" data-live-search="true" title="Status">
                                            <option>Status 1</option>
                                            <option>Status 2</option>
                                            <option>Status 3</option>
                                            <option>Status 4</option>
                                            <option>Status 5</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-xs-6">
                                    <div class="form-group">
                                        <select class="selectpicker" data-live-search="true" title="Property Type">
                                            <option>Property Type 1</option>
                                            <option>Property Type 2</option>
                                            <option>Property Type 3</option>
                                            <option>Property Type 4</option>
                                            <option>Property Type 5</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-xs-6">
                                    <div class="form-group">
                                        <select class="selectpicker" data-live-search="true" title="Beds">
                                            <option>01</option>
                                            <option>02</option>
                                            <option>03</option>
                                            <option>04</option>
                                            <option>05</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-xs-6">
                                    <div class="form-group">
                                        <select class="selectpicker" data-live-search="true" title="Baths">
                                            <option>01</option>
                                            <option>02</option>
                                            <option>03</option>
                                            <option>04</option>
                                            <option>05</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-xs-6">
                                    <div class="form-group">
                                        <select class="selectpicker" data-live-search="true" title="Min Areas (Sqft)">
                                            <option>$100</option>
                                            <option>$100</option>
                                            <option>$100</option>
                                            <option>$100</option>
                                            <option>$100</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-xs-6">
                                    <div class="form-group">
                                        <select class="selectpicker" data-live-search="true" title="Max Areas (Sqft)">
                                            <option>$100</option>
                                            <option>$100</option>
                                            <option>$100</option>
                                            <option>$100</option>
                                            <option>$100</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="range-advanced-main">
                                        <div class="range-text">
                                            <input type="text" class="min-price-range-hidden range-input" readonly>
                                            <input type="text" class="max-price-range-hidden range-input" readonly>
                                            <p><span class="range-title">Price Range:</span> from <span
                                                    class="min-price-range"></span> to <span
                                                    class="max-price-range"></span></p>
                                        </div>
                                        <div class="range-wrap">
                                            <div class="price-range-advanced"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xs-12 features-list">
                                    <label class="advance-trigger text-uppercase title"><i
                                            class="fa fa-plus-square"></i> Other Features </label>
                                    <div class="clearfix"></div>
                                    <div class="field-expand">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option1">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option2">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option3">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option1">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option2">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option3">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option1">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option2">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option3">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option1">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option2">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option3">
                                            Feature </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="advanced-search-mobile visible-xs visible-sm">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <form>
                        <div class="single-search-wrap">
                            <div class="single-search-inner advance-btn">
                                <button class="table-cell text-left" type="button"><i class="fa fa-gear"></i></button>
                            </div>
                            <div class="single-search-inner single-search">
                                <input type="text" class="form-control table-cell" name="search" placeholder="Search">
                            </div>
                            <div class="single-search-inner single-seach-btn">
                                <button class="table-cell text-right" type="submit"><i
                                        class="fa fa-search"></i></button>
                            </div>
                        </div>
                        <div class="advance-fields">
                            <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <select class="selectpicker" data-live-search="false" title="All Cities">
                                            <option>City 1</option>
                                            <option>City 2</option>
                                            <option>City 3</option>
                                            <option>City 4</option>
                                            <option>City 5</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <select class="selectpicker" data-live-search="false" title="All Areas">
                                            <option>Area 1</option>
                                            <option>Area 2</option>
                                            <option>Area 3</option>
                                            <option>Area 4</option>
                                            <option>Area 5</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <select class="selectpicker" data-live-search="false" title="All Status">
                                            <option>Status 1</option>
                                            <option>Status 2</option>
                                            <option>Status 3</option>
                                            <option>Status 4</option>
                                            <option>Status 5</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <select class="selectpicker" data-live-search="false" title="All Types">
                                            <option>Type 1</option>
                                            <option>Type 2</option>
                                            <option>Type 3</option>
                                            <option>Type 4</option>
                                            <option>Type 5</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-btn">
                                                <button type="button" class="btn btn-number" disabled="disabled"
                                                    data-type="minus" data-field="count_beds"> <i
                                                        class="fa fa-minus"></i> </button>
                                            </span>
                                            <input type="text" name="count_beds" class="form-control input-number"
                                                value="1" data-min="1" data-max="10" placeholder="Beds">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-number" data-type="plus"
                                                    data-field="count_beds"> <i class="fa fa-plus"></i> </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-btn">
                                                <button type="button" class="btn btn-number" disabled="disabled"
                                                    data-type="minus" data-field="count_baths"> <i
                                                        class="fa fa-minus"></i> </button>
                                            </span>
                                            <input type="text" name="count_baths" class="form-control input-number"
                                                value="1" data-min="1" data-max="10" placeholder="Baths">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-number" data-type="plus"
                                                    data-field="count_baths"> <i class="fa fa-plus"></i> </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" value="" name="min-area"
                                            placeholder="Min Area (sqft)">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" value="" name="max-area"
                                            placeholder="Max Area (sqft)">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="range-advanced-main">
                                        <div class="range-text">
                                            <input type="text" class="min-price-range-hidden range-input" readonly>
                                            <input type="text" class="max-price-range-hidden range-input" readonly>
                                            <p><span class="range-title">Price Range:</span> from <span
                                                    class="min-price-range"></span> to <span
                                                    class="max-price-range"></span></p>
                                        </div>
                                        <div class="range-wrap">
                                            <div class="price-range-advanced"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <label class="advance-trigger"><i class="fa fa-plus-square"></i> Other Features
                                    </label>
                                </div>
                                <div class="col-sm-12 col-xs-12 features-list ">
                                    <div class="field-expand">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option1">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option2">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option3">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option1">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option2">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option3">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option1">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option2">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option3">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option1">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option2">
                                            Feature </label>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" value="option3">
                                            Feature </label>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <button type="submit" class="btn btn-secondary btn-block"><i
                                            class="fa fa-search pull-left"></i> Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <section id="section-body">
        <div class="container">
            <div class="page-title breadcrumb-top">
                <div class="row">
                    <div class="col-sm-12">
                        <ol class="breadcrumb">
                            <li><a href="index-2.html"><i class="fa fa-home"></i></a></li>
                            <li><a href="event-list.php">Event List</a></li>
                            <li class="active">Create an One-on-One Event</li>
                        </ol>
                        <div class="page-title-left">
                            <h2>Create an One-on-One Event</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div id="content-area" class="contact-area">
                        <div class="white-block">
                            <div class="row">

                                <form id="add-event-oneonone-availability-form" action="controller_event.php"
                                    method="post">

                                    <?php
                  $event_id = $_GET['event_id'];
                  ?>
                                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                                    <legend>Add Availability</legend>
                                    <div class="form-group col-md-6">
                                        <div class="col-md-6">
                                            <label class="control-label" for="availability_start">From</label>
                                            <input type="time" class="form-control" name="availability_start"
                                                id="availability_start">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label" for="availability_end">To</label>
                                            <input type="time" class="form-control" name="availability_end"
                                                id="availability_end">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="col-md-4 form-item-top-padding-1">
                                            <select name="availability_type" class="form-control" type="text"
                                                autocomplete="off">
                                                <option value="monday">Every Monday</option>
                                                <option value="tuesday">Every Tuesday</option>
                                                <option value="wednesday">Every Wednesday</option>
                                                <option value="thursday">Every Thursday</option>
                                                <option value="friday">Every Friday</option>
                                                <option value="specific">Specific Date</option>
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="control-label" for="availability_specific_date">Choose
                                                Specific Date</label>
                                            <input type="date" class="form-control" name="availability_specific_date"
                                                id="availability_specific_date" placeholder="dd-mm-yyyy"
                                                pattern="\d{1,2}-\d{1,2}-\d{4}">
                                        </div>
                                        <div class="col-md-3 form-item-top-padding-1">
                                            <button type="submit" class="btn btn-secondary event-form-button"
                                                name="add_event_oneonone_availability">Add</button>
                                        </div>
                                    </div>
                                </form>
                                <legend>Edit Availability</legend>
                                <?php
                include_once("pdo/dbconfig.php");

                if ($event_id != null) {
                  $result = $DB_calendar->get_event_availability($event_id);
                  foreach ($result as $row) {
                    $availability_id = $row['id'];
                    $availability_start = $row['availability_start'];
                    $availability_end = $row['availability_end'];
                    $availability_type = $row['availability_type'];
                    $availability_specific_date = $row['availability_specific_date'];

                    if ($availability_type == "specific") {
                      $availability_slot = $availability_specific_date;
                    } else if ($availability_type == "monday") {
                      $availability_slot = "Every Monday";
                    } else if ($availability_type == "tuesday") {
                      $availability_slot = "Every Tuesday";
                    } else if ($availability_type == "wednesday") {
                      $availability_slot = "Every Wednesday";
                    } else if ($availability_type == "thursday") {
                      $availability_slot = "Every Thursday";
                    } else if ($availability_type == "friday") {
                      $availability_slot = "Every Friday";
                    }
                ?>
                                <form action="controller_event.php" method="post">
                                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                                    <input type="hidden" name="availability_id"
                                        value="<?php echo $availability_id; ?>" />
                                    <div class="form-group col-md-6">
                                        <div class="col-md-6 form-item-top-padding-1">
                                            <input class="form-control" name="availability_type" id="availability_type"
                                                value="<?php echo $availability_slot ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label" for="availability_from">From</label>
                                            <input type="time" class="form-control" name="availability_start"
                                                id="availability_start" value="<?php echo $availability_start ?>">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="col-md-6">
                                            <label class="control-label" for="availability_to">To</label>
                                            <input type="time" class="form-control" name="availability_end"
                                                id="availability_end" value="<?php echo $availability_end ?>">
                                        </div>
                                        <div class="col-md-3 form-item-top-padding-1">
                                            <button type="submit" class="btn btn-secondary event-form-button"
                                                name="edit_event_oneonone_availability">Edit</button>
                                        </div>
                                        <div class="col-md-3 form-item-top-padding-1">
                                            <button type="submit" class="btn btn-secondary event-form-button"
                                                name="delete_event_oneonone_availability"
                                                onclick="return confirm('Are you sure to delete this slot?')">Delete</button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                  }
                }
                ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--end section page body-->

    <!--start footer section-->
    <footer class="footer-v2">
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="footer-widget widget-about">
                            <div class="widget-top">
                                <h3 class="widget-title">About Site</h3>
                            </div>
                            <div class="widget-body">
                                <p>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</p>
                                <p class="read"><a href="#">Read more <i class="fa fa-caret-right"></i></a></p>
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
                                    <li><i class="fa fa-location-arrow"></i> 121 King Street, Melbourne VIC 3000</li>
                                    <li><i class="fa fa-phone"></i> +1 (877) 987 3487</li>
                                    <li><i class="fa fa-envelope-o"></i> <a href="#">info@domainname.com</a></li>
                                </ul>
                                <p class="read"><a href="contact-us.html">Contact us <i
                                            class="fa fa-caret-right"></i></a></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="footer-widget widget-newsletter">
                            <div class="widget-top">
                                <h3 class="widget-title">Newsletter Subscribe</h3>
                            </div>
                            <div class="widget-body">
                                <form>
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
                                </form>
                                <p>It is a long established fact that a reader will be distracted by the readable
                                    content of a page when looking at its layout. </p>
                                <ul class="social">
                                    <li> <a href="#" class="btn-facebook"><i class="fa fa-facebook-square"></i></a>
                                    </li>
                                    <li> <a href="#" class="btn-twitter"><i class="fa fa-twitter-square"></i></a> </li>
                                    <li> <a href="#" class="btn-google-plus"><i
                                                class="fa fa-google-plus-square"></i></a> </li>
                                    <li> <a href="#" class="btn-linkedin"><i class="fa fa-linkedin-square"></i></a>
                                    </li>
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
                            <p>&copy; 2017 - All rights reserved</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="footer-col">
                            <div class="navi">
                                <ul id="footer-menu" class="">
                                    <li><a href="#">Privacy</a></li>
                                    <li><a href="#">Terms and Conditions</a></li>
                                    <li><a href="#">Contact</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <div class="footer-col foot-social">
                            <p> Follow us <a target="_blank" class="btn-facebook" href="https://facebook.com/"><i
                                        class="fa fa-facebook-square"></i></a> <a target="_blank" class="btn-twitter"
                                    href="https://twitter.com/"><i class="fa fa-twitter-square"></i></a> <a
                                    target="_blank" class="btn-linkedin" href="http://linkedin.com/"><i
                                        class="fa fa-linkedin-square"></i></a> <a target="_blank"
                                    class="btn-google-plus" href="http://google.com/"><i
                                        class="fa fa-google-plus-square"></i></a> <a target="_blank"
                                    class="btn-instagram" href="http://instagram.com/"><i
                                        class="fa fa-instagram"></i></a> </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!--end footer section-->

    <!--Start Scripts-->
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/modernizr.custom.js"></script>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/owl.carousel.min.js"></script>
    <script type="text/javascript" src="js/jquery.matchHeight-min.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js"></script>
    <script type="text/javascript" src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/masonry.pkgd.min.html"></script>
    <script type="text/javascript" src="js/jquery.nicescroll.js"></script>
    <script type="text/javascript" src="js/infobox.js"></script>
    <script type="text/javascript" src="js/markerclusterer.js"></script>
    <script type="text/javascript" src="js/custom.js"></script>

</body>

</html>