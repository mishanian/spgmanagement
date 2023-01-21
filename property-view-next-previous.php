<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
    <div class="next-prev-block clearfix">
        <div class="prev-box pull-left">
            <div class="media">
                <div class="media-left"><a
                        href="property-view.php?pass_bd_id=<?php echo $previous_building_id = $DB_building->getPreviousBdId($building_id, $company_id); ?>">
                        <img src="<?php echo $DB_building->getFeaturePic($previous_building_id) ?>"
                            class="media-object next-prev-pic" alt="image"> </a></div>
                <div class="media-body media-middle">
                    <h3 class="media-heading"><a
                            href="property-view.php?pass_bd_id=<?php echo $previous_building_id; ?>"><i
                                class="fa fa-angle-left"></i>
                            <?php echo $DB_snapshot->echot("PREVIOUS PROPERTY"); ?>
                        </a></h3>
                    <h4><?php echo $DB_building->getPreviousBdName($building_id, $company_id); ?></h4>
                </div>
            </div>
        </div>
        <div class="next-box pull-right">
            <div class="media">
                <div class="media-body media-middle text-right">
                    <h3 class="media-heading">
                        <a
                            href="property-view.php?pass_bd_id=<?php echo $next_building_id = $DB_building->getNextBdId($building_id, $company_id); ?>">
                            <?php echo $DB_snapshot->echot("NEXT PROPERTY"); ?> <i class="fa fa-angle-right"></i></a>
                    </h3>
                    <h4><?php echo $DB_building->getNextBdName($building_id, $company_id); ?></h4>
                </div>
                <div class="media-right"><a href="property-view.php?pass_bd_id=<?php echo $next_building_id; ?>"> <img
                            src="<?php echo $DB_building->getFeaturePic($next_building_id); ?>"
                            class="media-object next-prev-pic" alt="image"> </a></div>
            </div>
        </div>
    </div>
</div>