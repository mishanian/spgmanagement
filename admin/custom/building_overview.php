<?php
  $building_id = $_GET['building_id'];

?>

<html lang="en">
<head>
  <link rel="stylesheet" href="../bootstrap3/css/bootstrap.css">
  <link rel="stylesheet" href="building_overview_css.css">
</head>

<body>
  <div class="container">

    <!-- Nav tabs -->
    <div class="col-sm-12 col-md-4">
      <ul class="nav nav-pills" role="tablist">
        <li role="presentation" class="active"><a href="#units" aria-controls="units" role="tab" data-toggle="tab">Units</a></li>
        <li role="presentation"><a href="#parking_spots" aria-controls="parking_spots" role="tab" data-toggle="tab">Parking</a></li>
        <li role="presentation"><a href="#storage" aria-controls="storage" role="tab" data-toggle="tab">Storage</a></li>
      </ul>
    </div>

    <div class="hidden-sm col-md-8" style="padding:15px;">
      <span class="category-box category-be-repaired"></span><span class="box-text">To Be Repaired</span>
      <span class="category-box category-under-repair"></span><span class="box-text">Unread Repair</span>
      <span class="category-box category-be-accessed"></span><span class="box-text">To Be Accessed</span>
      <span class="category-box category-upcoming-vacancy"></span><span class="box-text">Upcoming Vacancy</span>
      <span class="category-box category-vacant"></span><span class="box-text">Vacant</span>
      <span class="category-box category-occupied"></span><span class="box-text">Occupied</span>
    </div>

    <div class="col-sm-12 col-md-12"><hr></div>
    <!-- tab contents -->
    <div class="tab-content">
      <!-- panel:units -->
      <div role="tabpanel" class="tab-pane active" id="units">
        <div class="col-sm-12 col-md-12">
        <?php
        include_once("../../pdo/dbconfig.php");
        $floor_info = $DB_building->getFloorsForOneBuilding($building_id);

        foreach ($floor_info as $floor) {
            $floor_id = $floor['floor_id'];
            $floor_name = $floor['floor_name'];

            echo '<div class="unit-line">';

            $units_info = $DB_building->getUnitsForOneFloor($floor_id);
            $units_count = sizeof($units_info);
            $width = round(99.5/$units_count,2).'%';

            foreach ($units_info as $unit) {
                $apartment_id = $unit['apartment_id'];
                $unit_number = $unit['unit_number'];
                $monthly_price = $unit['monthly_price'];
                $apartment_status = $unit['apartment_status'];

                //lease info
                $lease_start = $unit['lease_start'];
                $lease_end = $unit['lease_end'];
                $tenant_ids = $unit['tenant_ids'];

                //tenant info
                $tenants_array = explode(',',$tenant_ids);
                $html_element_id = 'unit-box-'.$apartment_id;

                //unit box style class
                $unit_box_style = '';

                if($apartment_status == 1){
                  $unit_box_style = 'unit-be-accessed';
                }
                elseif($apartment_status == 2){
                    $unit_box_style = 'unit-be-repaired';
                }
                elseif ($apartment_status == 3){
                  $unit_box_style = 'unit-under-repair';
                }
                elseif($apartment_status == 4){
                    $unit_box_style = 'unit-under-ready';
                }
                elseif($apartment_status == 5){
                    $unit_box_style = 'unit-vacant';
                }
                elseif($apartment_status == 6){
                  $unit_box_style = 'unit-occupied';
                }
                elseif($apartment_status == 7){
                    $unit_box_style = 'unit-pending-renew';
                }
                else{    //8
                    $unit_box_style = 'unit-vacant';
                }

                ?>
                <a class="units-box <?= $unit_box_style ?>" id="<?= $html_element_id ?>" href="#" style="width:<?= $width ?>" data-toggle="popover" data-container="body" data-placement="top" data-trigger="focus">
                  <div class="box-text-unit"><?= $unit_number ?></div>
                  <div class="box-text-price"><?= $monthly_price ?></div>
                </a>


            <?php
            }

            echo '</div>';   // end of unit-line
        }
        ?>

        </div>
      </div>

      <!-- panel: parking_spots -->
      <div role="tabpanel" class="tab-pane" id="parking_spots">
        //...ppppp
      </div>
      <!-- panel:storage -->
      <div role="tabpanel" class="tab-pane" id="storage">
        <div style="width: 200px;">
          <div style=" font-size: 14px;border-bottom:1px solid #444444; padding-top: 5px;">LEASE</div>
          <div style=" font-size: 13px;">Lease Start:</div>
          <div style=" font-size: 13px;">Lease End:</div>
          <div style=" font-size: 13px;"><a>Lease Details</a></div>
          <div style=" font-size: 14px;border-bottom:1px solid #444444;">TENANTS</div>
          <div style=" font-size: 13px;"><a>Lisa</a></div>
          <div style=" font-size: 13px;"><a>Dunda</a></div>
          <div style=" font-size: 14px;border-bottom:1px solid #444444;">MORE OPERATIONS</div>
          <div style=" font-size: 13px;"><a>Buildings Details</a></div>
          <div style=" font-size: 13px;"><a>Units Details</a></div>
        </div>
      </div>

    </div><!-- end tab contents -->


  </div>

<script src="../bootstrap3/js/jquery-1.11.3.min.js"></script>
<script src="../bootstrap3/js/bootstrap.min.js"></script>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    $(function () {
        $('[data-toggle="popover"]').popover();
    });
</script>
</body>
</html>