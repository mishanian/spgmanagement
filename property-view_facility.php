<div class="row">
    <div class="col-sm-12">
        <div class="facilities_content">
            <div class="detail-title" style="text-align: left">
                <h2 class="title-left"> <?php echo $DB_snapshot->echot("Building Facilities"); ?></h2>
            </div>
            <?php
                foreach ($DB_building->getAllFacilities($building_id) as $facility) {
                    ?>
                    <em><?php echo $facility; ?></em>
                <?php } ?>
        </div>
        <div class="clearfix">&nbsp;</div>
    </div>
</div>