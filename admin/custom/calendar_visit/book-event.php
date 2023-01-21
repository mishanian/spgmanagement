<?php
$building_id = $_GET['building_id'];
$event_id = $_GET['event_id'];
?>

<link href="custom/calendar_visit/css/form-control.css" rel="stylesheet" type="text/css" />
<style>
  label {
    font-weight: normal;
  }
</style>

<section id="section-body">
  <div class="container">

    <div class="page-title breadcrumb-top">
      <div class="row">
        <div class="col-sm-12">
          <ol class="breadcrumb">
            <li><a href="event-list.php?building_id=<?php echo $building_id; ?>&direct=visitor_events">Visitor
                Events List</a>
            </li>
            <li class="active">Create Office & Maintenance Events List</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div id="content-area" class="contact-area">
          <div class="white-block">
            <div class="row">

              <?php
              include_once("../pdo/dbconfig.php");
              $row = $DB_calendar->get_visit_event($event_id);
              $event_name = $row['event_name'];
              $event_location = $row['event_location'];
              $event_description = $row['event_description'];
              $event_person_in_change_id = $row['resonsible_employee_id'];
              $event_person_in_charge = $DB_calendar->get_employee_info($event_person_in_change_id);
              $event_company_id = $event_person_in_charge['company_id'];
              $event_person_in_charge_name = $event_person_in_charge['full_name'];
              $event_person_in_charge_telephone = $event_person_in_charge['mobile'];
              $event_person_in_charge_email = $event_person_in_charge['email'];
              if ($row['event_duration'] == 0) {
                $event_duration = $row['event_custom_duration'];
              } else {
                $event_duration = $row['event_duration'];
              }
              ?>


              <div class="col-md-12">
                <legend>Appointment Information</legend>
              </div>
              <div class="col-sm-12 col-xs-12 contact-block-inner">
                <div class="form-group create-event-form-col-1 col-md-6">
                  <h5>Appointment Name: <?php echo $event_name ?></h5>
                </div>
                <div class="form-group create-event-form-col-1 col-md-6">
                  <h5>Duration: <?php echo $event_duration ?> min</h5>
                </div>
                <div class="form-group create-event-form-col-2 col-md-6">
                  <h5>Location: <?php echo $event_location ?></h5>
                </div>
                <div class="form-group create-event-form-col-2 col-md-6">
                  <h5>Person in charge: <?php echo $event_person_in_charge_name ?></h5>
                </div>
                <div class="form-group create-event-form-col-2 col-md-12">
                  <h5>Description: <?php echo $event_description ?></h5>
                </div>
              </div>



              <div class="col-md-12">
                <legend>Please Fill in Client's Information</legend>
              </div>
              <form id="add-event-oneonone-availability-form" action="custom/calendar_visit/controller_event.php" method="post">
                <input type="hidden" id="event_id" name="event_id" value="<?php echo $event_id; ?>" />
                <input type="hidden" id="building_id" name="building_id" value="<?php echo $building_id; ?>">
                <input type="hidden" name="event_description" value="<?php echo $event_description; ?>">
                <input type="hidden" name="event_location" value="<?php echo $event_location ?>">
                <input type="hidden" name="person_in_charge_name" value="<?php echo $event_person_in_charge_name; ?>" />
                <input type="hidden" name="person_in_charge_telephone" value="<?php echo $event_person_in_charge_telephone; ?>" />
                <input type="hidden" name="person_in_charge_email" value="<?php echo $event_person_in_charge_email; ?>" />
                <input type="hidden" name="event_company_id" value="<?php echo $event_company_id; ?>">
                <input type="hidden" name="event_person_in_charge_id" value="<?php echo $event_person_in_change_id; ?>">

                <div class="form-group col-md-12">
                  <div class="book-event-col-1 col-md-4"><label>Name</label><input type="text" class="form-control" name="visitor_name" id="visitor_name"></div>
                </div>
                <div class="form-group col-md-12">
                  <div class="book-event-col-1 col-md-4"><label>Email</label><input type="email" class="form-control" name="visitor_email" id="visitor_email" pattern="[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" required></div>
                </div>
                <div class="form-group col-md-12">
                  <div class="book-event-col-1 col-md-4"><label>Telephone</label><input type="tel" class="form-control" name="visitor_phone" id="visitor_phone" pattern="[\+]\d{11}" placeholder="+01231234567" required>
                  </div>
                </div>
                <div class="form-group col-md-12">
                  <div class="book-event-col-1 col-md-4"><label>Desired Unit</label>
                    <select id="desired_unit" name="desired_unit" class="form-control" type="text" autocomplete="off" required>
                      <?php
                      foreach ($DB_apt->getAptInfoInBuilding($building_id) as $apt_row) {
                        if ($DB_apt->isUnitShowed($apt_row['apartment_id'])) {
                      ?>
                          <option value="<?php echo $apt_row['apartment_id']; ?>">
                            <?php echo $apt_row['unit_number']; ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<?php echo $DB_apt->getSizeType($apt_row['apartment_id']); ?>
                            )
                          </option>
                      <?php
                        }
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="form-group col-md-12">
                  <div class="book-event-col-1 col-md-4">
                    <label>Date</label>
                    <select id="book_event_date" name="book_event_date" class="form-control book_event_field" type="text" autocomplete="off" onchange="getEventTime()">
                      <?php
                      require('custom/calendar_visit/action_booking.php');
                      $action_booking = new action_booking();
                      $dates = $action_booking->get_availablility_dates($event_id);

                      for ($i = 0; $i < count($dates); $i++) {
                        $year = date("Y", strtotime($dates[$i]));
                        $month = date("m", strtotime($dates[$i]));
                        $day = date("d", strtotime($dates[$i]));
                        $jd = gregoriantojd($month, $day, $year);
                        $dayofweek = jddayofweek($jd, 1);
                        $text = date('Y-m-d', strtotime($dates[$i])) . ' ' . $dayofweek;
                      ?>
                        <option value="<?php echo $dates[$i]; ?>"><?php echo $text; ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="form-group col-md-12">
                  <div class="book-event-col-1 col-md-4">
                    <label>Time</label>
                    <select id="book_event_time" name="book_event_time" class="form-control" type="text" autocomplete="off" required></select>
                  </div>
                  <div class="book-event-col-2 col-md-4 form-item-top-padding-1">
                  </div>
                </div>
                <div class="form-group col-md-12">
                  <div class="book-event-col-1 col-md-6">
                    <button type="submit" class="btn btn-primary" name="book_event_choose_time" style="margin-top: 5px;">Confirm & Book</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script type="text/javascript">
  $(document).ready(function() {
    getEventTime();
  });

  function getEventTime() {
    $('#book_event_time option').remove();
    $.ajax({
      type: "GET",
      url: "custom/calendar_visit/book_event_time_source.php",
      dataType: 'json',
      data: {
        event_id: $('#event_id').val(),
        book_event_date: $('#book_event_date').val()
      },
      async: true,
      success: function(json) {
        $.each(json, function(key, value) {
          $('#book_event_time').append('<option value="' + value + '">' + value +
            '</option>');
        });
      },
      error: function(xhr, status, error) {
        alert(xhr.responseText);
      }
    });
    $('#book_event_time').show();
  }
</script>