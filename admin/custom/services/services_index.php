<?php
if(isset($_SESSION['UserLevel']) && $_SESSION['UserLevel']!= -1){
    $user_id = $_SESSION['UserID'];
    include_once('../pdo/dbconfig.php');
    include_once('../pdo/Class.Services.php');
    $DB_services = new Services($DB_con);
    include_once('../pdo/Class.Payment.php');
    $DB_payment = new Payment($DB_con);
    $result = $DB_services->get_services_availabilities_for_user($user_id);
    $credit_check_avail = $result['credit_check'];
    $lease_sign_avail = $result['lease_sign'];

    $services_price = $DB_payment->get_servies_price();
    $lease_sign_price = $services_price['service_price_leasign'];
    $credit_check_price = $services_price['service_price_crecheck'];

    if (isset($_GET['no_accessibility'])) {
        echo('<div class="alert alert-warning alert-dismissible fade in" role="alert" id="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="close"><span aria-hidden="true">×</span></button>
          <b>Sorry! You do not have availabilities to use the service, please add more availabilities.</b>
          </div>');
    }
    ?>

  <!-- <link href="custom/services/skins/square/blue.css" rel="stylesheet">
  <link href="custom/services/css/service_index_css.css" rel="stylesheet">
  <script src="custom/services/js/icheck.min.js"></script> -->
  <style>
    input[type=text] {
      min-width: 0 !important;
    }
  </style>

  <div class="container">
    <form action="custom/services/services_controller.php" method="post">
      <div class="row">
        <div class="col"><p>Please Choose The Services You Want :</p></div>
      </div>

      <div class="row" id="services_list">
        <div class="col-sm-4 col-md-3">
          <div class="card">
            <img src="custom/services/imgs/credit_check.jpeg" height="205"><br><br>
            <div class="caption" style="text-align: center;">
              <h4>Online Credit Check</h4>
              <input tabindex="1" type="radio" id="radio_credit_check" name="services" value="credit_check"><br><br>
            </div>
          </div>
        </div>

        <div class="col-sm-4 col-md-3">
          <div class="card">
            <img src="custom/services/imgs/lease_example.jpeg" height="205"><br><br>
            <div class="caption" style="text-align: center;">
              <h4>Online Lease Signing</h4>
              <input tabindex="2" type="radio" id="radio_lease_sign" name="services" value="lease_sign"><br><br>
            </div>
          </div>
        </div>

        <div class="col-sm-12 col-md-6">
          <div class="card service-board">
            <h2 class="service-usage">Services Availabilities</h2>
            <ul>
              <li>Online Credit Check : <span class="service-available"><?php echo $credit_check_avail; ?></span><span
                        class="glyphicon glyphicon-plus-sign service-plus" aria-hidden="true" id="credit_check_plus"
                        data-toggle="modal" data-target="#buy_form" data-service="credit_check"></span></li>
              <li>Online Lease Signing : <span class="service-available"><?php echo $lease_sign_avail; ?></span><span
                        class="glyphicon glyphicon-plus-sign service-plus" aria-hidden="true" id="lease_sign_plus"
                        data-toggle="modal" data-target="#buy_form" data-service="lease_sign"></span></li>
            </ul>
          </div>

          <div class="service-btn-block">
            <!-- <button class="btn btn-info btn-lg service-btn-cancel" name="cancel_click"><span
                      class="glyphicon glyphicon-remove-circle service-btn-icon" aria-hidden="true"></span>Cancel
            </button> -->
            <button class="btn btn-success btn-lg service-btn-process" name="process_click"><span
                      class="glyphicon glyphicon-ok-circle service-btn-icon" aria-hidden="true"></span>process
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="buy_form" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <form method="post" action="pay.php">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="container">
              <div class="col-md-2 col-sm-4 col-md-offset-1">
                <h5>Service Type</h5>
                <input type="hidden" id="service_type" name="service_type">
                <input type="hidden" id="service_price_val">
                <input type="text" id="service_type_show" class="form-control" disabled>
              </div>

              <div class="col-md-2 col-sm-4">
                <h5>Services Times</h5>
                <input type="number" id="service_buy_count" name="service_buy_count" class="form-control" required>
              </div>

              <div class="col-sm-10 col-md-10" style="padding-left: 50px;">
                <div id="modal-amount-block">Total :
                  <span id="service_times">1</span><span style="font-size: 10px;">times</span>&nbsp;X
                  <span id="service_price">20</span> $ <span style="font-size: 10px;">per time</span> =
                  <span id="total_value"></span> $
                </div>
              </div>

            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" id="form-submit" name="services_buy" class="btn btn-primary">Buy Now !</button>
            <button aria-hidden="true" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
loadjs.ready(["jquery","head"], function() {
    loadjs([    
        "custom/services/skins/square/blue.css",
        "custom/services/css/service_index_css.css",
        "custom/services/js/icheck.min.js",   
    ], 'jsloaded'
);
});

</script>
  <script>
       loadjs.ready(['jquery','jsloaded'], function() {
      $(document).ready(function () {
          $('[name="services"]').on('ifChecked', function (event) {

          }).iCheck({
              checkboxClass: 'icheckbox_square-blue',
              radioClass: 'iradio_square-blue',
              increaseArea: '20%'
          });
      });
    });
  </script>

  <script>
       loadjs.ready(['jquery','jsloaded'], function() {
      $(document).ready(function () {
      var credit_check_price = <?php echo $credit_check_price;?>;
      var lease_sign_price = <?php echo $lease_sign_price;?>;

      $('#buy_form').on('show.bs.modal', function (event) {
          var tr = $(event.relatedTarget);
          var service_type = tr.data('service');
          var modal = $(this);
          modal.find('#service_type').val(service_type);
          if (service_type === 'credit_check') {
              modal.find('#service_type_show').val("Credit Check");
              modal.find('#service_price').text(credit_check_price);
              modal.find('#service_price_val').val(credit_check_price);
          } else {
              modal.find('#service_type_show').val("Lease Signing");
              modal.find('#service_price').text(lease_sign_price);
              modal.find('#service_price_val').val(lease_sign_price);
          }

      });

      $("#service_buy_count").on('change', function (event) {
          var service_time = $("#service_buy_count").val();
          var service_price_val = $('#service_price_val').val();
          if (service_time > 0) {
              $('#service_times').text(service_time);
              $('#total_value').text(service_time * service_price_val);
          }
      });

    });
    });
  </script>
  <?php
}
else{
    echo('<div class="alert alert-danger alert-dismissible fade in" role="alert" id="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="close"><span aria-hidden="true">×</span></button>
          <b>Sorry!Administrator can not access service module.</b>
          </div>');
}
?>