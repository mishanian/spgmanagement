<!-- Left side section start-->
<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 table_tab">
    <div id="facilities" class="detail-features detail-block target-block">
        <?
        // if (!$isMobile) {
            include ('property-view-units.php');
        //}
        ?>
        <div class="clearfix">&nbsp;</div>
        <? 
            include_once ('property-view_facility.php');
        ?>
        <?
         if ($isMobile) {
         //   include ('property-view-units-mobiles.php');
        }
        ?>


        <? if (!$isMobile) { ?>
        <div class="detail-title HideOnMobile">
            <h2 class="title-left"><?php echo $DB_snapshot->echot("Building Description"); ?></h2>
        </div>

        <div class="HideOnMobile">
            <p><?php echo $DB_snapshot->echot($DB_building->getBdInfo($building_id)['public_comments']); ?></p>
        </div>

        <? include_once ('property-view-local-logic.php');?>
        <? } //if (! $isMobile ) {?>
        <!--<div id="facilities" -->
    </div>
</div>
<!-- Left side section end-->