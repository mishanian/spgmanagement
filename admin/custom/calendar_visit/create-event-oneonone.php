<?php
$building_id = $_GET['building_id'];
$employee_id = $_SESSION['employee_id'];
?>

<link href="custom/calendar_visit/css/form-control.css" rel="stylesheet" type="text/css"/>
<style>
  label {
    font-weight: normal;
  }
</style>

<?php
include_once("../pdo/dbconfig.php");
$row = $DB_building->getBdInfo($building_id);
$building_location = $row['address'];
?>
<section id="section-body">
  <div class="container">

    <div class="page-title breadcrumb-top">
      <div class="row">
        <div class="col-sm-12">
          <ol class="breadcrumb">
            <li><a href="event-list.php?building_id=<?php echo $building_id;?>&direct=visitor_events">Visitor Events</a>
            </li>
            <li class="active">Create Event</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div id="content-area" class="contact-area">
          <div class="white-block">
            <div class="row">

              <form id="create-event-oneonone-form" action="custom/calendar_visit/controller_event.php" method="post">
                <input type="hidden" name="building_id" value="<?php echo $building_id; ?>">


                <div class="col-md-12">
                  <legend>What event is this?</legend>
                </div>
                <div class="col-sm-12 col-xs-12 contact-block-inner">
                  <div class="form-group col-md-6">
                    <label class="control-label" for="event_name">Event Name</label>
                    <input class="form-control" name="event_name" id="event_name"
                           value="appointment to look around apartments" required>
                  </div>
                  <div class="form-group col-md-6">
                    <label class="control-label" for="event_location">Location</label>
                    <input class="form-control" name="event_location" id="event_location"
                           value="<?php echo $building_location; ?>" required>
                  </div>
                  <div class="form-group col-md-12">
                    <label class="control-label" for="event_description">Description</label>
                    <textarea class="form-control" name="event_description" rows="4" id="event_description">reserving a time slot to look around the apartments, to look the apartment that you want.</textarea>
                  </div>
                </div>


                <div class="col-md-12">
                  <legend>When can people book this event?</legend>
                </div>
                <div class="col-sm-12 col-xs-12 contact-block-inner">
                  <div class="form-group col-md-6">
                    <div class=" col-md-6">
                      <label class="control-label" for="event_duration">Event Duration</label>
                      <select name="event_duration" id="event_duration" class="form-control" type="text"
                              autocomplete="off" onchange="if_custom_min()">
                        <option value="15">15 min</option>
                        <option value="30">30 min</option>
                        <option value="45">45 min</option>
                        <option value="60">60 min</option>
                        <option value="0">Custom min</option>
                      </select>
                    </div>
                    <div class=" col-md-6">
                      <label class="control-label" for="event_custom_duration">Input Custom Min</label>
                      <input class="form-control" name="event_custom_duration" id="event_custom_duration" value="0"
                             disabled="disabled">
                    </div>
                  </div>
                  <div class="form-group col-md-6">
                    <div class=" col-md-6">
                      <label class="control-label" for="event_max_book_per_day">Max number of books per day</label>
                      <input class="form-control" name="event_max_book_per_day" id="event_max_book_per_day" value="1">
                    </div>
                    <div class=" col-md-6">
                      <label class="control-label" for="event_increment">Show availability in increments of</label>
                      <select name="event_increment" class="form-control" type="text" autocomplete="off">
                        <option value="5">5 min</option>
                        <option value="10">10 min</option>
                        <option value="15" selected>15 min</option>
                        <option value="30">30 min</option>
                        <option value="45">45 min</option>
                        <option value="60">60 min</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group col-md-6">
                    <div class=" col-md-6">
                      <label class="control-label" for="event_buffer_before">Buffer before event</label>
                      <select name="event_buffer_before" class="form-control" type="text" autocomplete="off">
                        <option value="0">0 min</option>
                        <option value="15">15 min</option>
                        <option value="30">30 min</option>
                        <option value="45">45 min</option>
                        <option value="60">60 min</option>
                      </select>
                    </div>
                    <div class=" col-md-6">
                      <label class="control-label" for="event_buffer_after">Buffer after event</label>
                      <select name="event_buffer_after" class="form-control" type="text" autocomplete="off">
                        <option value="0">0 min</option>
                        <option value="15">15 min</option>
                        <option value="30">30 min</option>
                        <option value="45">45 min</option>
                        <option value="60">60 min</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group col-md-6 ">
                    <div class="col-md-6">
                      <label class="control-label" for="event_less_hour">Book event more than away(H)</label>
                      <input class="form-control" name="event_less_hour" id="event_less_hour" value="0">
                    </div>
                    <div class=" col-md-6">
                      <label class="control-label" for="person_in_charge">Person in charge</label>
                      <select name="person_in_charge" id="person_in_charge" class="form-control" type="text"
                              autocomplete="off">
                          <?php
                          $staff = $DB_calendar->get_same_company_staff($employee_id);
                          foreach ($staff as $row) {
                              $id = $row['employee_id'];
                              $name = $row['full_name'];
                              $email = $row['email'];
                              echo "<option value=\"$id\">$name - $email</option>";
                          }
                          ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group col-md-12">
                    <div class="col-md-12">
                      <button type="submit" class="btn btn-primary btn-long" name="create_event_oneonone">Save &
                        Continue
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>
<!--end section page body-->


<!--Start Scripts-->
<script type="text/javascript">
    function if_custom_min() {
        var selected = $('#event_duration').val();
        if (selected == "0")
            $('#event_custom_duration').attr("disabled", false);
        else
            $('#event_custom_duration').attr("disabled", true);
    }
</script>


