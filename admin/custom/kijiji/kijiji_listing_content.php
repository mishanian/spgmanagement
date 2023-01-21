<?php
include_once("../pdo/dbconfig.php");
include_once("../pdo/Class.Kijiji.php");
$DB_kijiji = new Kijiji($DB_con);
if (isset($_SESSION['UserLevel']) && $_SESSION['UserLevel'] == -1) {
    $user_level = -1;
} else {
    $employee_id = $_SESSION['employee_id'];
    $user_level = $DB_kijiji->get_admin_level($employee_id);
}

if($user_level != 1){
    echo('<div class="alert alert-danger alert-dismissible fade in" role="alert">
  <h4>Sorry ! You do not have accessibility for this module</h4>
  <p>If these funtions are important to you, please upgrade your accessibility to administrator level</p>
</div>');

}
else {

    //update to latest candidate / feeding infos
    $DB_kijiji->update_to_latest_feeding_listing($employee_id);

    $kijiji_settings = $DB_kijiji->get_kijiji_settings_by_employee($employee_id);
    $feed_by_price_priority_level = $kijiji_settings['feed_by_price'];
    $feed_by_price_priority_strategy = $kijiji_settings['feed_by_price_order_strategy'];
    $feed_by_value_priority_level = $kijiji_settings['feed_by_value'];
    $feed_by_value_priority_strategy = $kijiji_settings['feed_by_value_order_strategy'];
    $if_sort_randomly = $kijiji_settings['feed_carousel'];
    echo $employee_id;
    $slots_number = $DB_kijiji->get_available_slots_number($employee_id);

    $employee_name = $DB_kijiji->get_employee_name($employee_id);
    $candidate_number = $DB_kijiji->get_candidates_count($employee_id);
    if ($candidate_number == "") $candidate_number = 0;

    ?>
  <link rel="stylesheet" href="custom/kijiji/css/bootstrap-switch.css">
  <link rel="stylesheet" href="custom/kijiji/css/kijiji.css">
  <link rel="stylesheet" href="custom/kijiji/css/table_style.css">
  <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
  <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>


  <div class="container ">
    <form action="custom/kijiji/kijiji_controller.php" method="post">
      <input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>">
      <div class="row form-group">
        <div class="col-sm-12 col-md-7">
          <div class="row">
            <div class="col-sm-6 col-md-4">
              <div class="thumbnail">
                <div class="caption caption-text">
                  <h4>Feeding By Price</h4>
                  <div class="priority-module">
                    <p>Priority Level</p>
                    <label class="radio-inline">
                      <input type="radio" name="feed_by_price_priority_level" id="feed_by_price_priority_level_2" value="2" <?php if ($feed_by_price_priority_level == 2) echo "checked"; ?>> High
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="feed_by_price_priority_level" id="feed_by_price_priority_level_1" value="1" <?php if ($feed_by_price_priority_level == 1) echo "checked"; ?>> Low
                    </label>
                  </div>
                  <div class="order-module">
                    <p>Order Strategy</p>
                    <label class="radio-inline">
                      <input type="radio" name="feed_by_price_order_strategy" id="feed_by_price_order_strategy_ASC" value="ASC" <?php if ($feed_by_price_priority_strategy == "ASC") echo "checked"; ?>>ASC
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="feed_by_price_order_strategy" id="feed_by_price_order_strategy_DESC" value="DESC" <?php if ($feed_by_price_priority_strategy == 'DESC') echo "checked"; ?>>DESC
                    </label>
                  </div>

                  <div class="switch-margin">
                    <input type="checkbox" name="feed_by_price_switch" data-size="mini" data-on-color="primary" <?php if ($feed_by_price_priority_level > 0) echo "checked"; ?>/>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-md-4">
              <div class="thumbnail">
                <div class="caption caption-text">
                  <h4 title="Value = Price / Area">Feeding By Value</h4>
                  <div class="priority-module">
                    <p>Priority Level</p>
                    <label class="radio-inline">
                      <input type="radio" name="feed_by_value_priority_level" id="feed_by_value_priority_level_2" value="2" <?php if ($feed_by_value_priority_level == 2) echo "checked"; ?>> High
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="feed_by_value_priority_level" id="feed_by_value_priority_level_1" value="1" <?php if ($feed_by_value_priority_level == 1) echo "checked"; ?>> Low
                    </label>
                  </div>

                  <div class="order-module">
                    <p>Order Strategy</p>
                    <label class="radio-inline">
                      <input type="radio" name="feed_by_value_order_strategy" id="feed_by_value_order_strategy_ASC" value="ASC" <?php if ($feed_by_value_priority_strategy == "ASC") echo "checked"; ?>>ASC
                    </label>
                    <label class="radio-inline">
                      <input type="radio" name="feed_by_value_order_strategy" id="feed_by_value_order_strategy_DESC" value="DESC" <?php if ($feed_by_value_priority_strategy == "DESC") echo "checked"; ?>>DESC
                    </label>
                  </div>

                  <div class="switch-margin">
                    <input type="checkbox" name="feed_by_value_switch" data-size="mini" data-on-color="primary" <?php if ($feed_by_value_priority_level > 0) echo "checked"; ?>/>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-md-4">
              <div class="thumbnail">
                <div class="caption caption-text">
                  <h4>Carousel</h4>
                  <div class="priority-module">
                    <p>Under Carousel pattern,</p>
                    <p>Units in Candidate List</p>
                    <p>automatically selected</p>
                    <p>into Feeding List by turn</p>
                  </div>

                  <div class="switch-margin-carousel">
                    <input type="checkbox" name="carousel_switch" id="carousel_switch" data-size="mini" data-on-color="primary" <?php if ($if_sort_randomly == 1) echo "checked"; ?>/>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!--ads slots payment-->
        <div class="col-sm-12 col-md-5 ">
          <div class="panel panel-default ">
            <div class="panel-body" style="height: 206px;">
              <div class="col-sm-12 col-md-12">
                <h4 style="font-size: 14pt" class="pull-left">
                  <p style="padding-bottom: 20px;">Hello,&nbsp;<?php echo $employee_name; ?></p>
                  <p>You have<strong class="have-slots"><?php echo $slots_number; ?></strong>Advertisement slot(s) in Kijiji.</p>
                  <p>You are going to post<strong class="using-slots"><?php echo $candidate_number; ?></strong>Advertisement(s).
                  </p>
                </h4>
              </div>
              <div class="col-sm-12 col-md-12">
                <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#buy_form">Buy
                  More
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- alert window -->
      <div id="alert-window"></div>
    </form>

    <!-- feeding list -->
    <div class="row form-group">
      <div class="col-sm-12">
        <legend>Feeding List</legend>
        <div class="col-md-2 col-md-offset-10"><div class="category-box category-internal"></div><div class="box-text">Forced posting</div></div>
        <table class="table table-hover" id="feeding_list" style="background-color: white">
          <thead>
          <tr>
            <td class="col-md-1 text-center">City</td>
            <td class="col-md-2 text-center">Building</td>
            <td class="col-md-1 text-center">Unit</td>
            <td class="col-md-1 text-center">Unit Sub Type</td>
            <td class="col-md-1 text-center">Price</td>
            <td class="col-md-1 text-center">Medium</td>
            <td class="col-md-2 text-center">Search Keywords</td>
          </tr>
          </thead>
          <tbody>
          <?php
          $feeding_list = $DB_kijiji->get_feeding_info($employee_id);
          $feeding_list_count = 0;
          foreach ($feeding_list as $item){
              $feeding_list_count++;
              $city = $item['city'];
              $name = $item['building_name'];
              $unit = $item['unit_number'];
              $unit_sub_type_name = $item['apartment_types_sub_name'];
              $price = $item['monthly_price'];
              $if_force = $item['if_force'];
              $class = '';
              if ($if_force == 1) {
                  $class = 'class="success"';
              }
              //media
              $medium = array();
              if ($item['pictures'] != null)
                  array_push($medium, 'Pictures');
              if ($item['video'] != null)
                  array_push($medium, 'Video');
              if ($item['floor_plan'] != null)
                  array_push($medium, 'Floor plan');
              ?>
            <tr <?php echo $class; ?>>
              <td class="col-md-1 text-center"><?php echo $city; ?></td>
              <td class="col-md-2 text-center"><?php echo $name; ?></td>
              <td class="col-md-1 text-center"><?php echo $unit ?></td>
              <td class="col-md-1 text-center"><?php echo $unit_sub_type_name; ?></td>
              <td class="col-md-1 text-center"><?php echo '$' . $price; ?></td>
              <td class="col-md-1 text-center"><?php echo implode(',', $medium) ?></td>
              <td class="col-md-2 text-center"><?php echo $name.' '.$unit?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- candidate list -->
    <div class="row form-group">
      <div class="col-sm-12 col-md-12">
        <legend>Candidates List | <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Rules</a></legend>
        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
          <div class="panel panel-default">
            <div class="panel-body">
              <form action="custom/kijiji/kijiji_controller.php" method="post">
                <div class="col-sm-6 col-md-3">
                  <table class="table table-striped" style="margin-top: 16px; font-size: 12px;">
                    <thead>
                    <tr><td class="col-md-4 text-center"></td></tr>
                    </thead>
                    <tbody>
                    <tr><td class="col-md-4 text-center">Building Name</td></tr>
                    <tr><td class="col-md-4 text-center">List Priority</td></tr>
                    <tr><td class="col-md-4 text-center">Prioritize the units having medium info</td></tr>
                    <tr><td class="col-md-4 text-center">List units with different number of rooms</td></tr>
                    <tr><td class="col-md-4 text-center">Units count to show in Candidates List</td></tr>
                    <tr>
                      <td class="col-md-4 text-center">
                        <button type="submit" class="btn btn-primary" name="update_buildings_settings">Save</button>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>

                <div class="col-sm-6 col-md-9" style="overflow: auto; white-space: nowrap">
                  <div style="width: 2500px;">
                      <?php
                      $building_settings = $DB_kijiji->get_building_settings_by_employee($employee_id);
                      $building_ids = array();
                      foreach ($building_settings as $building) {
                          $building_id = $building['building_id'];
                          array_push($building_ids, $building_id);
                          $building_name = $building['building_name'];
                          $listing_priority_type = $building['kijiji_listing_priority_type'];
                          $prioritize_media = $building['kijiji_prioritize_media'];
                          $listing_diff_num_room = $building['kijiji_listing_diff_num_room'];
                          $showed_units_count = $building['kijiji_showed_units_count'];
                          ?>
                        <div class="col-sm-1 col-md-1">
                          <div class="thumbnail">
                            <div class="caption caption-text">
                              <h5><?php echo preg_replace('/\(.*?\)/', '', $building_name); ?></h5>
                                <?php echo get_sub_building_name($building_name); ?>
                              <p style="padding-top: 6px;">
                                <select class="form-control" name="<?php echo 'listing_priority_type_' . $building_id ?>">
                                  <option value="lowest_price" <?php if ($listing_priority_type == "lowest_price") echo "selected"; ?>>Lowest Price</option>
                                  <option value="best_value" <?php if ($listing_priority_type == "best_value") echo "selected"; ?>>Best Value</option>
                                  <option value="random"<?php if ($listing_priority_type == "random") echo "selected"; ?>>Random</option>
                                </select>
                              </p>
                              <p style="padding-top: 2px;"><input type="checkbox" class="prioritize_media_switch" name="<?php echo 'prioritize_media_' . $building_id; ?>" data-size="mini" data-on-color="primary" <?php if ($prioritize_media == 1) echo "checked"; ?>/></p>
                              <p style="padding-top: 2px;"><input type="checkbox" class="listing_diff_num_room_switch" name="<?php echo 'listing_diff_num_room_' . $building_id; ?>" data-size="mini" data-on-color="primary" <?php if ($listing_diff_num_room == 1) echo "checked"; ?>/></p>
                              <p style="padding-top: 2px;">
                                <select class="form-control" style="width: 24mm;"
                                        name="<?php echo 'showed_units_count_' . $building_id; ?>">
                                  <option value="0" <?php if ($showed_units_count == 0) echo "selected"; ?>>None
                                  </option>
                                  <option value="9" <?php if ($showed_units_count == 9) echo "selected"; ?>>All</option>
                                  <option value="1" <?php if ($showed_units_count == 1) echo "selected"; ?>>1</option>
                                  <option value="2" <?php if ($showed_units_count == 2) echo "selected"; ?>>2</option>
                                  <option value="3" <?php if ($showed_units_count == 3) echo "selected"; ?>>3</option>
                                  <option value="4" <?php if ($showed_units_count == 4) echo "selected"; ?>>4</option>
                                  <option value="5" <?php if ($showed_units_count == 5) echo "selected"; ?>>5</option>
                                  <option value="6" <?php if ($showed_units_count == 6) echo "selected"; ?>>6</option>
                                  <option value="7" <?php if ($showed_units_count == 7) echo "selected"; ?>>7</option>
                                  <option value="8" <?php if ($showed_units_count == 8) echo "selected"; ?>>8</option>
                                </select>
                              </p>
                            </div>
                          </div>
                        </div>
                      <?php } ?>
                  </div>
                </div>
                <input type="hidden" name="building_ids" value="<?php echo implode(',', $building_ids); ?>">
                <input type="hidden" name="employee_id" id="employee_id" value="<?php echo $employee_id; ?>">
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-12">
        <table class="table table-hover" id="candidates_list" style="width: 100%!important;background-color: white;">
          <thead>
          <tr>
            <td class="col-md-1 text-center">City</td>
            <td class="col-md-3 text-center">Building</td>
            <td class="col-md-2 text-center">Unit</td>
            <td class="col-md-2 text-center">Unit Sub Type</td>
            <td class="col-md-2 text-center">Price</td>
            <td class="col-md-2 text-center">Medium</td>
          </tr>
          </thead>
          <tbody>
          <?php
          $candidates_list = $DB_kijiji->get_candidates_info($employee_id);
          foreach ($candidates_list as $candidate) {
              $city = $candidate['city'];
              $name = $candidate['building_name'];
              $unit = $candidate['unit_number'];
              $unit_sub_type_name = $candidate['apartment_types_sub_name'];
              $price = $candidate['monthly_price'];
              $if_force = $candidate['if_force'];
              $class = '';
              if ($if_force == 1) {
                  $class = 'class="success"';
              }
              //media
              $media = array();
              if ($candidate['pictures'] != null)
                  array_push($media, 'Pictures');
              if ($candidate['video'] != null)
                  array_push($media, 'Video');
              if ($candidate['floor_plan'] != null)
                  array_push($media, 'Floor plan');

              ?>
            <tr <?php echo $class ?>>
              <td class="col-md-1 text-center"><?php echo $city; ?></td>
              <td class="col-md-3 text-center"><?php echo $name; ?></td>
              <td class="col-md-2 text-center"><?php echo $unit ?></td>
              <td class="col-md-2 text-center"><?php echo $unit_sub_type_name; ?></td>
              <td class="col-md-2 text-center"><?php echo '$' . $price; ?></td>
              <td class="col-md-2 text-center"><?php echo implode(',', $media) ?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>


  <!-- Modal -->
  <div class="modal fade" id="buy_form" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <form method="post" action="pay.php">
          <input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>">
          <input type="hidden" name="employee_name" value="<?php echo $employee_name; ?>">
          <input type="hidden" name="this_url" value="kijiji_listing.php">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <div class="modal-body">
            <div class="container">
              <div class=" col-md-2 col-sm-4" style="margin-left: 60px;">
                <h5>Slots Count</h5>
                <input type="number" id="slot_number" name="slot_number" class="form-control" min="0" required>
              </div>

              <div class="form-group col-md-2 col-sm-4" style="padding-left: 40px;">
                <h5>Slots Period</h5>
                <select id="slot_period" name="slots_period" class="form-control" style="width: 5cm;" required>
                  <option value="1" selected>1 Month</option>
                  <option value="2">2 Months</option>
                  <option value="3">3 Months</option>
                  <option value="6">6 Months</option>
                  <option value="9">9 Months</option>
                  <option value="12">1 Year</option>
                </select>
              </div>

              <div class="col-sm-10 col-md-9">
                <div id="modal-amount-block">Total :
                  <span id="slots_number_calculator">1</span><span style="font-size: 10px;">times</span>&nbsp;X
                  <span>20</span> $ <span style="font-size: 10px;">per time</span> =
                  <span id="total_value"></span> $
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" id="form-submit" name="kijiji_payment" class="btn btn-primary">Pay Now!</button>
            <button aria-hidden="true" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
          <input type="hidden" name="price" value="20">
        </form>
      </div>
    </div>
  </div>


  <!--script-->
  <script src="custom/kijiji/js/bootstrap-switch.js"></script>
  <script>
      $(document).ready(function() {
          $('#candidates_list').DataTable({
              "searching":false,
              "iDisplayLength": 10,
              "scrollX": true,
              "ordering": false
          });
          $('#feeding_list').DataTable({
              "searching":false,
              "iDisplayLength": 10,
              "scrollX": true,
              "ordering": false
          });
      } );

      $("[name='feed_by_price_switch']").bootstrapSwitch();
      $("[name='feed_by_value_switch']").bootstrapSwitch();
      $("[name='carousel_switch']").bootstrapSwitch();
      $(".prioritize_media_switch").bootstrapSwitch();
      $(".listing_diff_num_room_switch").bootstrapSwitch();


      $("#feed_by_price_priority_level_2").change(function () {
          change_alert();
      });

      $("#feed_by_price_priority_level_1").change(function () {
          change_alert();
      });

      $("#feed_by_value_priority_level_2").change(function () {
          change_alert();
      });

      $("#feed_by_value_priority_level_1").change(function () {
          change_alert();
      });

      $("#feed_by_value_order_strategy_ASC").change(function () {
          change_alert();
      });

      $("#feed_by_value_order_strategy_DESC").change(function () {
          change_alert();
      });

      $("#feed_by_price_order_strategy_ASC").change(function () {
          change_alert();
      });

      $("#feed_by_price_order_strategy_DESC").change(function () {
          change_alert();
      });


      $("#slot_number").change(function () {
          $("#slots_number_calculator").text($("#slot_number").val());
          $("#total_value").text($("#slot_number").val() * $("#slot_period").val() * 20);
      });
      $("#slot_period").change(function () {
          $("#slots_period_calculator").text($("#slot_period").val());
          $("#total_value").text($("#slot_number").val() * $("#slot_period").val() * 20);
      });


      $('[name="carousel_switch"]').on('switchChange.bootstrapSwitch', function (event, state) {
          if (state == true) {
              $('input[name="feed_by_price_switch"]').bootstrapSwitch('state', false);
              $('input[name="feed_by_value_switch"]').bootstrapSwitch('state', false);
          }
          change_alert();
      });

      $('[name="feed_by_price_switch"]').on('switchChange.bootstrapSwitch', function (event, state) {
          if (state == true) {
              $('[name="carousel_switch"]').bootstrapSwitch('state', false);
              $('[name="feed_by_price_priority_level"]').attr("required", true);
          }
          change_alert();
      });

      $('[name="feed_by_value_switch"]').on('switchChange.bootstrapSwitch', function (event, state) {
          if (state == true) {
              $('[name="carousel_switch"]').bootstrapSwitch('state', false);
              $('[name="feed_by_value_priority_level"]').attr("required", true);
          }
          change_alert();
      });

      $('#feed_by_price_priority_level_2').click(function () {
          if ($('input:radio[name="feed_by_value_priority_level"]:checked').val() == 2) {
              $('#feed_by_value_priority_level_1').trigger("click");
          }
      });


      $('#feed_by_value_priority_level_2').click(function () {
          if ($('input:radio[name="feed_by_price_priority_level"]:checked').val() == 2) {
              $('#feed_by_price_priority_level_1').trigger("click");
          }
      });

      $('#feed_by_price_priority_level_1').click(function () {
          if ($('input:radio[name="feed_by_value_priority_level"]:checked').val() == 1) {
              $('#feed_by_value_priority_level_2').trigger("click");
          }
      });

      $('#feed_by_value_priority_level_1').click(function () {
          if ($('input:radio[name="feed_by_price_priority_level"]:checked').val() == 1) {
              $('#feed_by_price_priority_level_2').trigger("click");
          }
      });


      function change_alert() {
          if ($("#alert").length == 0) {
              $('#alert-window').append('<div class="alert alert-dismissible fade in" role="alert" id="alert" style="color: #3c763d;background-color: #dff0d8;border-color: #d6e9c6;">' +
                  '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' +
                  '<b>Your settings have changed. Remember saving your settings, after completing settings ! </b>' +
                  '<button type="submit" name="save_kijiji_settings" class="btn btn-success" style="margin-left: 20px;">Save Settings</button>' +
                  '</div>');
          }
      }

  </script>

<?php
}
  function get_sub_building_name($origin){
          $output = array();
          preg_match_all("/(?:\()(.*)(?:\))/i",$origin, $output);
          return '('.$output[1][0].')';
  }
?>
