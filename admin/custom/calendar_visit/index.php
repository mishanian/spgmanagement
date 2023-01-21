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
                            <form action="admin/login" method="post">
                                <div class="form-group field-group">
                                    <div class="input-user input-icon">
                                        <input type="text" placeholder="Username" name="username" id="username">
                                    </div>
                                    <div class="input-pass input-icon">
                                        <input type="password" placeholder="Password" name="password" id="password">
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
    <header id="header-section" class="header-section-4 header-main nav-left hidden-sm hidden-xs navbar-fixed-top"
        data-sticky="0">
        <div class="container">
            <div class="header-left">
                <div class="logo"> <a href="index"> <img src="images/logo.jpg" alt="logo"> </a> </div>
                <nav class="navi main-nav">
                    <ul>
                        <li><a href="index"><i class="fa fa-home"></i> Home</a></li>
                        <li><a href="map-listing"><i class="fa fa-list"></i> Listing</a></li>
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
    <div class="header-mobile visible-sm visible-xs navbar-fixed-top ">
        <div class="container">
            <!--start mobile nav-->
            <div class="mobile-nav"> <span class="nav-trigger"><i class="fa fa-navicon"></i></span>
                <div class="nav-dropdown main-nav-dropdown"></div>
            </div>
            <!--end mobile nav-->
            <div class="header-logo"> <a href="index"><img src="images/logo-white.png" alt="logo"></a> </div>
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

    <div class="header-media">
        <div id="houzez-gmap-main">
            <!--<div class="mapPlaceholder">
      <div class="loader-ripple">
        <div></div>
        <div></div>
      </div>
    </div>-->
            <div id="map"></div>


            <div class="map-next-prev-actions">
                <button id="houzez-gmap-view-my" class="map-btn" type="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="true"><i class="fa fa-globe"></i> <span>My
                        location</span></button>
                <div id="markerlist" style="display:none;"></div>
                <!--<ul class="dropdown-menu" aria-labelledby="houzez-gmap-location">
        <li><a href="#" onclick="fave_change_map_type('roadmap')"><span>Roadmap</span></a></li>
        <li><a href="#" onclick="fave_change_map_type('satellite')"><span>Satelite</span></a></li>
        <li><a href="#" onclick="fave_change_map_type('hybrid')"><span>Hybrid</span></a></li>
        <li><a href="#" onclick="fave_change_map_type('terrain')"><span>Terrain</span></a></li>
      </ul>-->
                <!--<button id="houzez-gmap-prev" class="map-btn"><i class="fa fa-chevron-left"></i> <span>Prev</span></button>
        <button id="houzez-gmap-next" class="map-btn"><span>Next</span> <i class="fa fa-chevron-right"></i></button>-->
                <span id="houzez-gmap-full" class="map-btn"><i class="fa fa-arrows-alt"></i>
                    <span>Fullscreen</span></span>
            </div>
        </div>
    </div>
    <div class="search_box_bg">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div class="search-inner-wrap">
                        <h2 class="text-uppercase">Find your ideal home</h2>
                        <div>
                            <form>
                                <div class="row">
                                    <div class="col-sm-3 col-xs-12">
                                        <div class="form-group">
                                            <div class="search map-marker input-icon">
                                                <select class="selectpicker" data-live-search="false"
                                                    title="All Cities">
                                                    <option>City 1</option>
                                                    <option>City 2</option>
                                                    <option>City 3</option>
                                                    <option>City 4</option>
                                                    <option>City 5</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 col-xs-6">
                                        <div class="form-group">
                                            <select class="selectpicker" data-live-search="false" title="Any Size">
                                                <option>1 Bedroom</option>
                                                <option>2 Bedroom</option>
                                                <option>3 Bedroom</option>
                                                <option>4 Bedroom</option>
                                                <option>5 Bedroom and more</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 col-xs-6">
                                        <div class="form-group">
                                            <select class="selectpicker" data-live-search="false" title="Any Price">
                                                <option>$500 or less</option>
                                                <option>$501 or $800</option>
                                                <option>$801 or $1000</option>
                                                <option>$1001 or $1200</option>
                                                <option>$1201 or $1400</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 col-xs-6">
                                        <div class="form-group">
                                            <select class="selectpicker" data-live-search="false" title="Options">
                                                <option>Pets Allowed</option>
                                                <option>Furnished</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-6">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-block"><i
                                                    class="fa fa-search"></i> Search</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--start section page body-->
    <section id="section-body">

        <!--start carousel module-->
        <div class="houzez-module-main">
            <div class="houzez-module carousel-module">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="module-title-nav clearfix">
                                <div>
                                    <h2 class="text-uppercase">Featured Properties and Units</h2>
                                </div>
                                <div class="module-nav">
                                    <button class="btn btn-sm btn-crl-pprt-1-prev"><i
                                            class="fa fa-arrow-left"></i></button>
                                    <button class="btn btn-sm btn-crl-pprt-1-next"><i
                                            class="fa fa-arrow-right"></i></button>
                                    <a href="map-listing" class="btn btn-carousel btn-sm">All</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="row grid-row">
                                <div class="carousel properties-carousel-grid-1 slide-animated">
                                    <?php
                                    include_once("pdo/dbconfig.php");
                                    for ($i = 1; $i <= 6; $i++) {
                                        $apt_row = $DB_apt->getAptInfo($i);
                                    ?>
                                    <div class="item">
                                        <div class="item-wrap">
                                            <div class="property-item item-grid">
                                                <div class="figure-block">
                                                    <figure class="item-thumb">
                                                        <div class="label-wrap hide-on-list">
                                                            <div class="label-status label label-default">For Rent</div>
                                                        </div>
                                                        <span class="label-featured label label-success">Featured</span>
                                                        <div class="price hide-on-list">
                                                            <h3>$350,000</h3>
                                                            <p class="rant">$<?php echo $apt_row['monthly_price'] ?>/mo
                                                            </p>
                                                        </div>
                                                        <a href="property-view.html" class="hover-effect"> <img
                                                                src="images/listings/properties_01.jpg" alt="thumb">
                                                        </a>
                                                        <ul class="actions">
                                                            <li class="share-btn">
                                                                <div class="share_tooltip fade"> <a href="#"
                                                                        target="_blank"><i
                                                                            class="fa fa-facebook"></i></a> <a
                                                                        href="javascript:void(0);" target="_blank"><i
                                                                            class="fa fa-twitter"></i></a> <a
                                                                        href="javascript:void(0);" target="_blank"><i
                                                                            class="fa fa-google-plus"></i></a> <a
                                                                        href="javascript:void(0);" target="_blank"><i
                                                                            class="fa fa-pinterest"></i></a> </div>
                                                                <span data-toggle="tooltip" data-placement="top"
                                                                    title="share"><i class="fa fa-share-alt"></i></span>
                                                            </li>
                                                            <li> <span data-toggle="tooltip" data-placement="top"
                                                                    title="Favorite"> <i class="fa fa-heart-o"></i>
                                                                </span> </li>
                                                            <li> <span data-toggle="tooltip" data-placement="top"
                                                                    title="Photos (12)"> <i class="fa fa-camera"></i>
                                                                </span> </li>
                                                        </ul>
                                                    </figure>
                                                </div>
                                                <div class="item-body">
                                                    <div class="body-left">
                                                        <div class="info-row">
                                                            <h2 class="property-title">
                                                                <a href="property-view.html">
                                                                    <?php
                                                                        $building_id = $apt_row['building_id'];
                                                                        $building_row = $DB_building->getBdInfo($building_id);
                                                                        echo "<a href=''>{$building_row['building_name']}</a>"; ?>
                                                                </a>
                                                            </h2>
                                                            <h4 class="property-location">
                                                                <?php echo "{$building_row['address']}"; ?></h4>
                                                            <div class="rating"> <span class="bottom-ratings"><span
                                                                        class="fa fa-star-o"></span><span
                                                                        class="fa fa-star-o"></span><span
                                                                        class="fa fa-star-o"></span><span
                                                                        class="fa fa-star-o"></span><span
                                                                        class="fa fa-star-o"></span><span
                                                                        style="width: 70%" class="top-ratings"><span
                                                                            class="fa fa-star"></span><span
                                                                            class="fa fa-star"></span><span
                                                                            class="fa fa-star"></span><span
                                                                            class="fa fa-star"></span><span
                                                                            class="fa fa-star"></span></span></span>
                                                                <span class="star-text-right">15 Ratings</span>
                                                            </div>
                                                        </div>
                                                        <div class="table-list full-width info-row">
                                                            <div class="cell">
                                                                <div class="info-row amenities">
                                                                    <p>
                                                                        <i class="fa fa-bed"></i>&nbsp; <span>:
                                                                            <?php echo $apt_row['bedrooms'] ?></span>
                                                                        <i class="fa fa-bath"></i>&nbsp; <span>:
                                                                            <?php echo $apt_row['bath_rooms'] ?></span>
                                                                        <i class="fa fa-compress"></i>&nbsp; <span>Sqft:
                                                                            <?php echo $apt_row['area'] ?></span>
                                                                    </p>
                                                                    <p> <i class="fa fa-building-o"></i>&nbsp;Single
                                                                        Family Home</p>
                                                                </div>
                                                            </div>
                                                            <div class="cell">
                                                                <div class="phone"> <a href="property-view.html"
                                                                        class="btn btn-primary">Details <i
                                                                            class="fa fa-angle-right fa-right"></i></a>
                                                                    <p><a href="javascript:void(0);">+1 (786)
                                                                            225-0199</a></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <!--  <div class="item">
                  <div class="item-wrap">
                    <div class="property-item item-grid">
                      <div class="figure-block">
                        <figure class="item-thumb">
                          <div class="label-wrap hide-on-list">
                            <div class="label-status label label-default">For Rent</div>
                          </div>
                          <span class="label-featured label label-success">Featured</span>
                          <div class="price hide-on-list">
                            <h3>$350,000</h3>
                            <p class="rant">$21，000/mo</p>
                          </div>
                          <a href="property-view.html" class="hover-effect"> <img src="images/listings/properties_02.jpg" alt="thumb"> </a>
                          <ul class="actions">
                            <li class="share-btn">
                              <div class="share_tooltip fade"> <a href="#" target="_blank"><i class="fa fa-facebook"></i></a> <a href="javascript:void(0);" target="_blank"><i class="fa fa-twitter"></i></a> <a href="javascript:void(0);"  target="_blank"><i class="fa fa-google-plus"></i></a> <a href="javascript:void(0);" target="_blank"><i class="fa fa-pinterest"></i></a> </div>
                              <span data-toggle="tooltip" data-placement="top" title="share"><i class="fa fa-share-alt"></i></span> </li>
                            <li> <span data-toggle="tooltip" data-placement="top" title="Favorite"> <i class="fa fa-heart-o"></i> </span> </li>
                            <li> <span data-toggle="tooltip" data-placement="top" title="Photos (12)"> <i class="fa fa-camera"></i> </span> </li>
                          </ul>
                        </figure>
                      </div>
                      <div class="item-body">
                        <div class="body-left">
                          <div class="info-row">
                            <h2 class="property-title"><a href="property-view.html">Apartment Oceanview</a></h2>
                            <h4 class="property-location">7601 East Treasure Dr. Miami Beach, FL 33141</h4>
                            <div class="rating"> <span class="bottom-ratings"><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span style="width: 70%" class="top-ratings"><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span></span></span> <span class="star-text-right">15 Ratings</span> </div>
                          </div>
                          <div class="table-list full-width info-row">
                            <div class="cell">
                              <div class="info-row amenities">
                                <p>
                                    <i class="fa fa-bed"></i>&nbsp; <span>: 3</span>
                                    <i class="fa fa-bath"></i>&nbsp; <span>: 2</span>
                                    <i class="fa fa-compress"></i>&nbsp; <span>Sqft: 1,965</span>
                                </p>
                                <p> <i class="fa fa-building-o"></i>&nbsp;Single Family Home</p>
                              </div>
                            </div>
                            <div class="cell">
                              <div class="phone"> <a href="property-view.html" class="btn btn-primary">Details <i class="fa fa-angle-right fa-right"></i></a>
                                <p><a href="javascript:void(0);">+1 (786) 225-0199</a></p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="item-wrap">
                    <div class="property-item item-grid">
                      <div class="figure-block">
                        <figure class="item-thumb">
                          <div class="label-wrap hide-on-list">
                            <div class="label-status label label-default">For Rent</div>
                          </div>
                          <span class="label-featured label label-success">Featured</span>
                          <div class="price hide-on-list">
                            <h3>$350,000</h3>
                            <p class="rant">$21,000/mo</p>
                          </div>
                          <a href="property-view.html" class="hover-effect"> <img src="images/listings/properties_03.jpg" alt="thumb"> </a>
                          <ul class="actions">
                            <li class="share-btn">
                              <div class="share_tooltip fade"> <a href="#" target="_blank"><i class="fa fa-facebook"></i></a> <a href="javascript:void(0);" target="_blank"><i class="fa fa-twitter"></i></a> <a href="javascript:void(0);"  target="_blank"><i class="fa fa-google-plus"></i></a> <a href="javascript:void(0);" target="_blank"><i class="fa fa-pinterest"></i></a> </div>
                              <span data-toggle="tooltip" data-placement="top" title="share"><i class="fa fa-share-alt"></i></span> </li>
                            <li> <span data-toggle="tooltip" data-placement="top" title="Favorite"> <i class="fa fa-heart-o"></i> </span> </li>
                            <li> <span data-toggle="tooltip" data-placement="top" title="Photos (12)"> <i class="fa fa-camera"></i> </span> </li>
                          </ul>
                        </figure>
                      </div>
                      <div class="item-body">
                        <div class="body-left">
                          <div class="info-row">
                            <h2 class="property-title"><a href="property-view.html">Apartment Oceanview</a></h2>
                            <h4 class="property-location">7601 East Treasure Dr. Miami Beach, FL 33141</h4>
                            <div class="rating"> <span class="bottom-ratings"><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span style="width: 70%" class="top-ratings"><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span></span></span> <span class="star-text-right">15 Ratings</span> </div>
                          </div>
                          <div class="table-list full-width info-row">
                            <div class="cell">
                              <div class="info-row amenities">
                                <p> <i class="fa fa-bed"></i>&nbsp; <span>: 3</span><i class="fa fa-bath"></i>&nbsp; <span>: 2</span> <i class="fa fa-compress"></i>&nbsp; <span>Sqft: 1,965</span> </p>
                                <p> <i class="fa fa-building-o"></i>&nbsp;Single Family Home</p>
                              </div>
                            </div>
                            <div class="cell">
                              <div class="phone"> <a href="property-view.html" class="btn btn-primary">Details <i class="fa fa-angle-right fa-right"></i></a>
                                <p><a href="javascript:void(0);">+1 (786) 225-0199</a></p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="item-wrap">
                    <div class="property-item item-grid">
                      <div class="figure-block">
                        <figure class="item-thumb">
                          <div class="label-wrap hide-on-list">
                            <div class="label-status label label-default">For Rent</div>
                          </div>
                          <span class="label-featured label label-success">Featured</span>
                          <div class="price hide-on-list">
                            <h3>$350,000</h3>
                            <p class="rant">$21,000/mo</p>
                          </div>
                          <a href="property-view.html" class="hover-effect"> <img src="images/listings/properties_04.jpg" alt="thumb"> </a>
                          <ul class="actions">
                            <li class="share-btn">
                              <div class="share_tooltip fade"> <a href="#" target="_blank"><i class="fa fa-facebook"></i></a> <a href="javascript:void(0);" target="_blank"><i class="fa fa-twitter"></i></a> <a href="javascript:void(0);"  target="_blank"><i class="fa fa-google-plus"></i></a> <a href="javascript:void(0);" target="_blank"><i class="fa fa-pinterest"></i></a> </div>
                              <span data-toggle="tooltip" data-placement="top" title="share"><i class="fa fa-share-alt"></i></span> </li>
                            <li> <span data-toggle="tooltip" data-placement="top" title="Favorite"> <i class="fa fa-heart-o"></i> </span> </li>
                            <li> <span data-toggle="tooltip" data-placement="top" title="Photos (12)"> <i class="fa fa-camera"></i> </span> </li>
                          </ul>
                        </figure>
                      </div>
                      <div class="item-body">
                        <div class="body-left">
                          <div class="info-row">
                            <h2 class="property-title"><a href="property-view.html">Apartment Oceanview</a></h2>
                            <h4 class="property-location">7601 East Treasure Dr. Miami Beach, FL 33141</h4>
                            <div class="rating"> <span class="bottom-ratings"><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span style="width: 70%" class="top-ratings"><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span></span></span> <span class="star-text-right">15 Ratings</span> </div>
                          </div>
                          <div class="table-list full-width info-row">
                            <div class="cell">
                              <div class="info-row amenities">
                                <p> <i class="fa fa-bed"></i>&nbsp; <span>: 3</span><i class="fa fa-bath"></i>&nbsp; <span>: 2</span> <i class="fa fa-compress"></i>&nbsp; <span>Sqft: 1,965</span> </p>
                                <p> <i class="fa fa-building-o"></i>&nbsp;Single Family Home</p>
                              </div>
                            </div>
                            <div class="cell">
                              <div class="phone"> <a href="property-view.html" class="btn btn-primary">Details <i class="fa fa-angle-right fa-right"></i></a>
                                <p><a href="javascript:void(0);">+1 (786) 225-0199</a></p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="item-wrap">
                    <div class="property-item item-grid">
                      <div class="figure-block">
                        <figure class="item-thumb">
                          <div class="label-wrap hide-on-list">
                            <div class="label-status label label-default">For Rent</div>
                          </div>
                          <span class="label-featured label label-success">Featured</span>
                          <div class="price hide-on-list">
                            <h3>$350,000</h3>
                            <p class="rant">$21,000/mo</p>
                          </div>
                          <a href="property-view.html" class="hover-effect"> <img src="images/listings/properties_01.jpg" alt="thumb"> </a>
                          <ul class="actions">
                            <li class="share-btn">
                              <div class="share_tooltip fade"> <a href="#" target="_blank"><i class="fa fa-facebook"></i></a> <a href="javascript:void(0);" target="_blank"><i class="fa fa-twitter"></i></a> <a href="javascript:void(0);"  target="_blank"><i class="fa fa-google-plus"></i></a> <a href="javascript:void(0);" target="_blank"><i class="fa fa-pinterest"></i></a> </div>
                              <span data-toggle="tooltip" data-placement="top" title="share"><i class="fa fa-share-alt"></i></span> </li>
                            <li> <span data-toggle="tooltip" data-placement="top" title="Favorite"> <i class="fa fa-heart-o"></i> </span> </li>
                            <li> <span data-toggle="tooltip" data-placement="top" title="Photos (12)"> <i class="fa fa-camera"></i> </span> </li>
                          </ul>
                        </figure>
                      </div>
                      <div class="item-body">
                        <div class="body-left">
                          <div class="info-row">
                            <h2 class="property-title"><a href="property-view.html">Apartment Oceanview</a></h2>
                            <h4 class="property-location">7601 East Treasure Dr. Miami Beach, FL 33141</h4>
                            <div class="rating"> <span class="bottom-ratings"><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span class="fa fa-star-o"></span><span style="width: 70%" class="top-ratings"><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span><span class="fa fa-star"></span></span></span> <span class="star-text-right">15 Ratings</span> </div>
                          </div>
                          <div class="table-list full-width info-row">
                            <div class="cell">
                              <div class="info-row amenities">
                                <p> <i class="fa fa-bed"></i>&nbsp; <span>: 3</span><i class="fa fa-bath"></i>&nbsp; <span>: 2</span> <i class="fa fa-compress"></i>&nbsp; <span>Sqft: 1,965</span> </p>
                                <p> <i class="fa fa-building-o"></i>&nbsp;Single Family Home</p>
                              </div>
                            </div>
                            <div class="cell">
                              <div class="phone"> <a href="property-view.html" class="btn btn-primary">Details <i class="fa fa-angle-right fa-right"></i></a>
                                <p><a href="javascript:void(0);">+1 (786) 225-0199</a></p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>   -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--end carousel module-->

        <!--start location module-->
        <div class="houzez-module-main module-white-bg">
            <div class="houzez-module module-title text-center">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <h2 class="text-uppercase">Search by our categories</h2>
                            <h3 class="sub-heading">Book space in amazing locations across the world</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div id="location-modul" class="houzez-module location-module grid">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="location-block"> <a href="#">
                                    <figure> <img src="images/cat_grid_1.jpg" width="370" height="370" alt="Apartment">
                                        <figcaption class="location-fig-caption">
                                            <h3 class="heading">Apartment</h3>
                                            <p class="sub-heading">30 Properties</p>
                                        </figcaption>
                                    </figure>
                                </a> </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="location-block"> <a href="#">
                                    <figure> <img src="images/cat_grid_2.jpg" width="770" height="370" alt="Loft">
                                        <div class="location-fig-caption">
                                            <h3 class="heading">Loft</h3>
                                            <p class="sub-heading">1 Property</p>
                                        </div>
                                    </figure>
                                </a> </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="location-block"> <a href="#">
                                    <figure> <img src="images/cat_grid_3.jpg" width="770" height="370"
                                            alt="Single Family Home">
                                        <div class="location-fig-caption">
                                            <h3 class="heading">Single Family Home</h3>
                                            <p class="sub-heading">11 Properties</p>
                                        </div>
                                    </figure>
                                </a> </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="location-block"> <a href="#">
                                    <figure> <img src="images/cat_grid_4.jpg" width="370" height="370" alt="Villa">
                                        <div class="location-fig-caption">
                                            <h3 class="heading">Villa</h3>
                                            <p class="sub-heading">10 Properties</p>
                                        </div>
                                    </figure>
                                </a> </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end location module-->

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
    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0N5pbJN10Y1oYFRd0MJ_v2g8W2QT74JE%20&amp;callback=initMap">
    </script>
    <script type="text/javascript" src="js/modernizr.custom.js"></script>
    <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/owl.carousel.min.js"></script>
    <script type="text/javascript" src="js/jquery.matchHeight-min.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.js"></script>
    <script type="text/javascript" src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/masonry.pkgd.min.html"></script>
    <script type="text/javascript" src="js/jquery.nicescroll.js"></script>
    <script type="text/javascript" src="js/markerclusterer.js"></script>
    <script type="text/javascript" src="js/custom.js"></script>
    <!--<script>
        (function($){
            var theMap;
            function initMap() {

                /* Properties Array */
                var properties = [{
                    id: 294,
                    title: "Penthouse apartment",
                    lat: "40.6879438",
                    lng: "-73.94192980000003", bedrooms: "3",
                    address:"Quincy St, Brooklyn, NY, USA",
                    bathrooms:"2",
                    bedrooms:"3",
                    icon:"images/x2-apartment.png",
                    id:294,
                    images_count:7,
                    lat:"40.6879438",
                    lng:"-73.94192980000003",
                    price:"<span class='item-price'>$876,000</span><span class='item-sub-price'>$7,600&#47;sq ft</span>",
                    prop_meta:"<p><span>Beds: 3</span><span>Baths: 2</span><span>Sq Ft: 2560</span></p>",
                    retinaIcon:"images/x2-apartment.png",
                    thumbnail:"<img src='http://placehold.it/385x258' alt='thumb'>",
                     title:"Penthouse apartment",
                    type:"Apartment",
                    url:"/"
                },
                {
                    id: 285, title: "Confortable apartment",
                    lat: "40.72305619999999",
                    lng: "-74.03885300000002",
                    address:"Metro Plaza Dr, Jersey City, NJ 07302, USA",
                    bathrooms:"2",
                    bedrooms:"1",
                    icon:"images/x2-apartment.png",
                    id:285,
                    images_count:7,
                    lat:"40.72305619999999",
                    lng:"-74.03885300000002",
                    price:"<span class='item-price'>$3,700&#47;mo</span>",
                    prop_meta:"<p><span>Bed: 1</span><span>Baths: 2</span><span>Sq Ft: 1900</span></p>",
                    retinaIcon:"images/x2-apartment.png",
                    thumbnail:"<img src='http://placehold.it/385x258' alt='thumb'>",
                    title:"Confortable apartment",
                    type:"Apartment",
                    url:"/"
                }];


                var myLatLng = new google.maps.LatLng(properties[0].lat,properties[0].lng);

                var houzezMapOptions = {
                    zoom: 12,
                    maxZoom: 12,
                    center: myLatLng,
                    disableDefaultUI: true,
                    scrollwheel: false,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    scroll:{x:$(window).scrollLeft(),y:$(window).scrollTop()}
                };
                var theMap = new google.maps.Map(document.getElementById("houzez-listing-map"), houzezMapOptions);

                if (Modernizr.mq('only all and (max-width: 1000px)')) {
                    theMap.setOptions({'draggable': false});
                }

                var markers = new Array();
                var current_marker = 0;
                var visible;

                var offset=$(theMap.getDiv()).offset();
                theMap.panBy(((houzezMapOptions.scroll.x-offset.left)/3),((houzezMapOptions.scroll.y-offset.top)/3));
                google.maps.event.addDomListener(window, 'scroll', function(){
                    var scrollY=$(window).scrollTop(),
                            scrollX=$(window).scrollLeft(),
                            scroll=theMap.get('scroll');
                    if(scroll){
                        theMap.panBy(-((scroll.x-scrollX)/3),-((scroll.y-scrollY)/3));
                    }
                    theMap.set('scroll',{x:scrollX,y:scrollY});
                });

                var mapBounds = new google.maps.LatLngBounds();

                for( i = 0; i < properties.length; i++ ) {

                    var marker_url = properties[i].icon;
                    var marker_size = new google.maps.Size( 44, 56 );
                    if( window.devicePixelRatio > 1.5 ) {
                        if ( properties[i].retinaIcon ) {
                            marker_url = properties[i].retinaIcon;
                            marker_size = new google.maps.Size( 84, 106 );
                        }
                    }

                    var marker_icon = {
                        url: marker_url,
                        size: marker_size,
                        scaledSize: new google.maps.Size( 44, 56 ),
                        origin: new google.maps.Point( 0, 0 ),
                        anchor: new google.maps.Point( 7, 27 )
                    };

                    // Markers
                    markers[i] = new google.maps.Marker({
                        map: theMap,
                        draggable: false,
                        position: new google.maps.LatLng(properties[0].lat,properties[0].lng),
                        icon: marker_icon,
                        title: properties[i].title,
                        animation: google.maps.Animation.DROP,
                        visible: true
                    });

                    mapBounds.extend(markers[i].getPosition());

                    var infoBoxText = document.createElement("div");
                    infoBoxText.className = 'property-item item-grid map-info-box';
                    infoBoxText.innerHTML =
                            '<div class="figure-block">'+
                            '<figure class="item-thumb">'+
                            properties[i].is_featured +
                            '<div class="price hide-on-list">'+
                            properties[i].price +
                            '</div>'+
                            '<a href="'+properties[i].url+'" tabindex="0">'+
                            properties[i].thumbnail +
                            '</a>'+
                            '<figcaption class="thumb-caption cap-actions clearfix">'+
                            '<div class="pull-right">'+
                            '<span title="" data-placement="top" data-toggle="tooltip" data-original-title="Photos">'+
                            '<i class="fa fa-camera"></i> <span class="count">('+ properties[i].images_count +')</span>'+
                            '</span>'+
                            '</div>'+
                            '</figcaption>'+
                            '</figure>'+
                            '</div>' +
                            '<div class="item-body">' +
                            '<div class="body-left">' +
                            '<div class="info-row">' +
                            '<h2 class="property-title"><a href="'+properties[i].url+'">'+properties[i].title+'</a></h2>' +
                            '<h4 class="property-location">'+properties[i].full_address+'</h4>' +
                            '</div>' +
                            '<div class="table-list full-width info-row">' +
                            '<div class="cell">' +
                            '<div class="info-row amenities">' +
                            properties[i].prop_meta +
                            '<p>'+properties[i].type+'</p>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>';


                    var infoBoxOptions = {
                        content: infoBoxText,
                        disableAutoPan: true,
                        maxWidth: 0,
                        alignBottom: true,
                        pixelOffset: new google.maps.Size( -122, -48 ),
                        zIndex: null,
                        closeBoxMargin: "0 0 -16px -16px",
                        closeBoxURL: "images/close.png",
                        infoBoxClearance: new google.maps.Size( 1, 1 ),
                        isHidden: false,
                        pane: "floatPane",
                        enableEventPropagation: false
                    };

                    var infobox = new InfoBox( infoBoxOptions );

                    attachInfoBoxToMarker( theMap, markers[i], infobox );

                }

                if(  document.getElementById('listing-mapzoomin') ){
                    google.maps.event.addDomListener(document.getElementById('listing-mapzoomin'), 'click', function () {
                        var current= parseInt( theMap.getZoom(),10);
                        current++;
                        if(current > 20){
                            current = 20;
                        }
                        theMap.setZoom(current);
                    });
                }


                if(  document.getElementById('listing-mapzoomout') ){
                    google.maps.event.addDomListener(document.getElementById('listing-mapzoomout'), 'click', function () {
                        var current= parseInt( theMap.getZoom(),10);
                        current--;
                        if(current < 0){
                            current = 0;
                        }
                        theMap.setZoom(current);
                    });
                }

                // Marker Clusters
                //if( googlemap_pin_cluster != 'no' ) {
                var markerClustererOptions = {
                    ignoreHidden: true,
                    maxZoom: parseInt(10),
                    styles: [{
                        textColor: '#ffffff',
                        url: 'images/cluster-icon.png',
                        height: 48,
                        width: 48
                    }]
                };

                var markerClusterer = new MarkerClusterer(theMap, markers, markerClustererOptions);
                //}

                theMap.fitBounds(mapBounds);

                function attachInfoBoxToMarker( map, marker, infoBox ){
                    marker.addListener('click', function() {
                        var scale = Math.pow( 2, map.getZoom() );
                        var offsety = ( (100/scale) || 0 );
                        var projection = map.getProjection();
                        var markerPosition = marker.getPosition();
                        var markerScreenPosition = projection.fromLatLngToPoint( markerPosition );
                        var pointHalfScreenAbove = new google.maps.Point( markerScreenPosition.x, markerScreenPosition.y - offsety );
                        var aboveMarkerLatLng = projection.fromPointToLatLng( pointHalfScreenAbove );
                        map.setCenter( aboveMarkerLatLng );
                        infoBox.close();
                        infoBox.open( map, marker );
                    });
                }

                jQuery('#houzez-gmap-next').click(function(){
                    current_marker++;
                    if ( current_marker > markers.length ){
                        current_marker = 1;
                    }
                    while( markers[current_marker-1].visible===false ){
                        current_marker++;
                        if ( current_marker > markers.length ){
                            current_marker = 1;
                        }
                    }
                    if( theMap.getZoom() < 15 ){
                        theMap.setZoom(15);
                    }
                    google.maps.event.trigger( markers[current_marker-1], 'click' );
                });

                jQuery('#houzez-gmap-prev').click(function(){
                    current_marker--;
                    if (current_marker < 1){
                        current_marker = markers.length;
                    }
                    while( markers[current_marker-1].visible===false ){
                        current_marker--;
                        if ( current_marker > markers.length ){
                            current_marker = 1;
                        }
                    }
                    if( theMap.getZoom() <15 ){
                        theMap.setZoom(15);
                    }
                    google.maps.event.trigger( markers[current_marker-1], 'click');
                });


                fave_change_map_type = function(map_type){

                    if(map_type==='roadmap'){
                        theMap.setMapTypeId(google.maps.MapTypeId.ROADMAP);
                    }else if(map_type==='satellite'){
                        theMap.setMapTypeId(google.maps.MapTypeId.SATELLITE);
                    }else if(map_type==='hybrid'){
                        theMap.setMapTypeId(google.maps.MapTypeId.HYBRID);
                    }else if(map_type==='terrain'){
                        theMap.setMapTypeId(google.maps.MapTypeId.TERRAIN);
                    }
                    return false;
                };

                // Create the search box and link it to the UI element.
                //var input = document.getElementById('google-map-search');
                //var searchBox = new google.maps.places.SearchBox(input);
                //theMap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                // Bias the SearchBox results towards current map's viewport.
                /*theMap.addListener('bounds_changed', function() {
                    searchBox.setBounds(theMap.getBounds());
                });*/

                //var markers_location = [];
                // Listen for the event fired when the user selects a prediction and retrieve
                // more details for that place.
               /* searchBox.addListener('places_changed', function() {
                    var places = searchBox.getPlaces();

                    if (places.length == 0) {
                        return;
                    }

                    // Clear out the old markers.
                    markers_location.forEach(function(marker) {
                        marker.setMap(null);
                    });
                    markers_location = [];

                    // For each place, get the icon, name and location.
                    var bounds = new google.maps.LatLngBounds();
                    places.forEach(function(place) {
                        var icon = {
                            url: place.icon,
                            size: new google.maps.Size(71, 71),
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(17, 34),
                            scaledSize: new google.maps.Size(25, 25)
                        };

                        // Create a marker for each place.
                        markers_location.push(new google.maps.Marker({
                            map: theMap,
                            icon: icon,
                            title: place.name,
                            position: place.geometry.location
                        }));

                        if (place.geometry.viewport) {
                            // Only geocodes have viewport.
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                    });
                    theMap.fitBounds(bounds);
                });*/

                google.maps.event.addListenerOnce(theMap, 'tilesloaded', function() {
                    $('.mapPlaceholder').hide();
                });

            }

            google.maps.event.addDomListener( window, 'load', initMap );
        })(jQuery)
    </script>-->
    <script src="data.json"></script>
    <script src="speed_test.js"></script>

    <script>
    google.maps.event.addDomListener(window, 'load', speedTest.init);
    </script>
    <script type="text/javascript">
    $(document).ready(function() {
        $("#box").niceScroll({
            autohidemode: true
        })
    });
    </script>
    <script src="js/jquery.nicescroll.js" type="text/javascript"></script>

</body>

</html>