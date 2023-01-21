<!-- Right side section start-->
<div id="tab_top"  class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
    <div class="houzez-taber-body no_marpad_top">
        <div class="tab-content">
            <?php
            $default = '0';
            //foreach ($apt_rows = $DB_apt->getAptInfoInBuilding($building_id) as $apt_row) {
                $apt_count=count($apt_rows)-1;
                $apt_counter=-1;
                $apt_real_counter=-1;
            foreach ($apt_rows as $apt_row) {
                $apt_counter++;
                if ($DB_apt->isUnitShowed($apt_row['apartment_id'])) {
                    $apt_id = $apt_row['apartment_id'];
                    // Checking if the apartment is under repair
                    if (!$apt_row["front_force_list"]) {
                        if (in_array($apt_row['renovation_status'], array(2, 3))) {
                            continue;
                        }
                    }
                    $apt_real_counter++;
            ?>
            <div id="<?php echo "tabcontent_" . $apt_row["apartment_id"] ?>" class="<?=(!$detect->isMobile())?'tab-pane fade':'tab-pane fade ';?> 
                <?php if ($default == '0') {echo "active in";$default = '1';} ?>">

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="detail-bar">
                            <!--Unit Details section start-->
                            <div class="detail-list detail-block target-block">
                                <!-- <div class="detail-title">
                                    <h2 class="title-left"><?php echo $DB_snapshot->echot("Unit Details"); ?></h2>
                                </div> -->
                                <div class="row ">
                                    <div class="prop_details">
                                        <div class="col-lg-6">
                                            <strong><?php echo $DB_snapshot->echot("Unit Number"); ?></strong>
                                            <div class="col-lg-12">
                                                <span
                                                    style="font-size:24px;"><b><?php echo $DB_apt->getUnitNumber($apt_id); ?></b></span>

                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <a href="book-event.php?building_id=<?php echo $building_id; ?>&apt_id=<?php echo $apt_id; ?>"
                                                style="margin:5px" class="btn btn-success btn-sm"><?php echo $DB_snapshot->echot("Visit Appointment"); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-lg-12">
                                            <? if ($detect->isMobile()) {
                                            include 'property-view_unit_pics.php';
                                           } ?>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-lg-6 col-md-6">
                                            <strong><?php echo $DB_snapshot->echot("Monthly rent"); ?></strong>
                                            <span
                                                class="prop_details"><?php echo $DB_apt->getMonthlyPrice($apt_id, true) . "/" . $DB_snapshot->echot("Month"); ?>
                                                <!--em>(include water & heat)</em-->
                                            </span>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <strong><?php echo $DB_snapshot->echot("Size") ?></strong>
                                            <span class="prop_details"> <?php echo $DB_apt->getSize($apt_id); ?></span>
                                        </div>

                                        <div class="col-lg-6 col-md-6">
                                            <strong><?php echo $DB_snapshot->echot("Floor"); ?></strong>
                                            <span class="prop_details">
                                                <?php
                                                $floorName = $DB_apt->getFloorInfo($apt_id);
                                                echo str_replace("Floor", $DB_snapshot->echot("Floor"), $floorName);
                                                ?> </span>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <strong><?php echo $DB_snapshot->echot("Available Date"); ?></strong>
                                            <span class="prop_details"><?php
                                            if ($apt_row['apartment_status'] == '5') {
                                                echo $DB_snapshot->echot("Available Now!");
                                            } else {
                                                echo $apt_row['available_date'];
                                            } ?></span>
                                        </div>

                                        <?php if ($amenities = $DB_apt->getAmenities($apt_id)) { ?>
                                        <div class="col-lg-12 facilities_content">
                                            <strong><?php echo $DB_snapshot->echot("Amenities"); ?></strong>
                                            <p>
                                                <?php
                                                foreach ($amenities as $amenity) {?>
                                                <em><?php echo $DB_snapshot->echot($amenity); ?></em>
                                                <?php } ?>
                                            </p>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="row ">
                                        <div class="col-lg-12">

                                            <? if ($detect->isMobile()) {
                                                        include 'property-view_question.php';
                                                    //    echo "<br><br>";
                                                        } ?>
                                            <? if ($detect->isMobile() && $apt_row['floor_plan']) {
                                                        include 'property-view_floorplan.php';
                                                        } ?>
                                            <? if ($detect->isMobile()) {
                                                        include 'property-view_estimate.php';
                                                        } ?>


                                        </div>
                                    </div>
                                    <!--                      </div>-->
                                    <!--Unit Details section end-->

                                    <!--Unit Pics section start-->
                                    <!--                      <div class="additional_info detail-list detail-block target-block">-->
                                    <div class="row ">
                                        <div class="col-lg-12 text-center">
                                            <? if (!$detect->isMobile()) {
                                                include 'property-view_unit_pics.php';
                                            } ?>
                                            <? if (!$detect->isMobile() && $apt_row['floor_plan']) {
                                                include 'property-view_floorplan.php';
                                            } ?>
                                            <? if (!$detect->isMobile()) {
                                                include 'property-view_estimate.php';
                                            } ?>
                                            <? if (!$detect->isMobile()) {
                                                include 'property-view_question.php';
                                            } ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                    
                                        <div class="col-*-12 text-center">
                                        <? if($apt_real_counter!=0){?><button class="btn btn-lg prev_row" style="background-color:#0d47a1; color:blanchedalmond"> &lt; Previous Unit</button><? }?>
                                       
                                        <? if($apt_counter!=$apt_count){?><button class="btn btn-lg next_row" style="background-color:#0d47a1; color:blanchedalmond"> Next Unit &gt; </button><? }?>
                                        </div>
                                    </div>





                                    <? if( !empty($apt_row['public_comments']) ){?>
                                    <div style="clear:both;">&nbsp;</div>
                                    <div class="detail-title">
                                        <h2 class="title-left">
                                            <?php echo $DB_snapshot->echot("Unit Description"); ?></h2>
                                    </div>
                                    <div>
                                        <p><?php echo $apt_row['public_comments']; ?></p>
                                    </div>
                                    <? }?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- /div tab -->
                <?php
            } // if ($DB_apt->isUnitShowed($apt_row['apartment_id'])) {
        } //foreach ($apt_rows as $apt_row) { ?>   
        
        </div>
    </div>
</div>
    <!-- Right side section end-->