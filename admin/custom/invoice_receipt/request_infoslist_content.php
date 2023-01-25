<?php

$user_id = $_SESSION['UserID'];
$user_level = $_SESSION['UserLevel'];

include("../pdo/dbconfig.php");


if ($user_level == 5) {     //tenant
  $user_unit_id = $_GET['unit_id'];
  // to handle the case(one tenant has many units)
  $issues = $DB_request->get_tenant_issue_list($user_id, $user_unit_id);
  $tenant_accessbility = $DB_tenant->get_tenant_settings_about_request($user_id);
  $allow_create_request = $tenant_accessbility['allow_create_request'];
  $view_past_request = $tenant_accessbility['view_past_issues'];
} else {      //employee
  $user_unit_id = 0;
  $issues = $DB_request->get_employee_issue_list($user_id);
  $view_past_request = 1;
  $allow_create_request = 1;
}

$raw_past_issues = array();
$raw_current_issues = array();
$open_issue_counter = 0;
$unread_issue_count = 0;

foreach ($issues as $one) {
  $issue_status = $one['issue_status'];
  $issue_past_after_days = $one['issue_past_after_days'];
  $last_update_time = date('Y-m-d', strtotime($one['last_update_time']));
  $time_flag = strtotime("$last_update_time + $issue_past_after_days day"); //timestamp for past issue list
  if ($issue_status == 'closed' && strtotime(date('Y-m-d')) > $time_flag) {
    array_push($raw_past_issues, $one);
  } else {
    array_push($raw_current_issues, $one);
  }
}

//count
foreach ($raw_current_issues as $row) {
  //count - open issue
  if ($row['issue_status'] == 'open') {
    $open_issue_counter += 1;
  }
  //count - unread issue
  if (strtotime($row['last_access_time']) < strtotime($row['last_update_time'])) {
    $unread_issue_count += 1;
  }
}

//separate page
$current_issues = array();
if (sizeof($raw_current_issues) > 20) {
  $current_issues = array_slice($raw_current_issues, 0, 20);
} else {
  $current_issues = $raw_current_issues;
}


$past_issues = array();
if (sizeof($raw_past_issues) > 20) {
  $past_issues = array_slice($raw_past_issues, 0, 20);
} else {
  $past_issues = $raw_past_issues;
}

?>
<link rel="stylesheet" href="custom/request/css/table_style.css">
<link rel="stylesheet" href="custom/request/css/request_info.css">
<link rel="stylesheet" href="custom/request/css/lightbox.min.css">

<div id="container">
    <div class="col-xs-12 col-md-4 report-btn" style="margin-left: 0px;">
        <button id="startReport"
            class="btn btn-primary <?php echo $allow_create_request == 0 ? 'remove-for-tenant' : ''; ?>"
            data-toggle="modal" data-target="#reportModal">Report an Request</button>
        <button id="startBulletin" class="btn btn-primary remove-for-tenant" data-toggle="modal"
            data-target="#newBulletinModal">New Notification</button>
    </div>

    <div class="hidden-xs col-md-6" style="margin-top: 10px;">
        <div class="col-md-4 remove-for-tenant">
            <div class="category-box category-internal"></div>
            <div class="box-text">Internal Request</div>
        </div>
        <div class="col-md-4 remove-for-tenant">
            <div class="category-box category-tenant"></div>
            <div class="box-text">Tenant Request</div>
        </div>
        <div class="col-md-4" id="unread_request_mark">
            <div class="category-box category-unread"></div>
            <div class="box-text">Unread Request</div>
        </div>
    </div>

    <div class="hidden-xs col-md-2">
        <div class="box-text"><span id="open_issue_count">OPEN REQUESTS:
                <?= $open_issue_counter ?></span><br>UNREAD:<span
                id="unread_issue_count"><?= $unread_issue_count ?></span></div>
    </div>

    <div class="col-md-12" style="padding-top: 20px;">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#current-issues" aria-controls="current-issues" role="tab"
                    data-toggle="tab">Current Requests</a></li>
            <li role="presentation" class=" <?php echo $view_past_request == 0 ? 'remove-for-tenant' : ''; ?> "><a
                    href="#past-issues" aria-controls="past-issues" role="tab" data-toggle="tab">Past Requests</a></li>
            <li role="presentation"><a href="#bulletin" aria-controls="bulletin" role="tab"
                    data-toggle="tab">Bulletins</a></li>
        </ul>
    </div>

    <?php
  $filter_building_list = array();
  $building_lst = $DB_request->get_building_list($user_id);
  if ($user_level == 5) {
    $temp = array();
    $temp['building_id'] = $building_lst['building_id'];
    $temp['building_name'] = $building_lst['building_name'];
    array_push($filter_building_list, $temp);
  } else {
    foreach ($building_lst as $r) {
      $temp = array();
      $temp['building_id'] = $r['building_id'];
      $temp['building_name'] = $r['building_name'];
      array_push($filter_building_list, $temp);
    }
  }


  $filter_employee_lst = $DB_request->get_employees_lst($user_id);
  ?>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active row" id="current-issues">
            <div id="filter-part-current" class="col-sm-12 col-md-12" style="padding: 15px 0;">
                <div class="col-sm-4 col-md-3 filter-block">
                    <label for="filter_building_current" class="col-sm-4 col-md-3 filter-text">Building:</label>
                    <div class="col-sm-8 col-md-9">
                        <select class="form-control" id="filter_building_current">
                            <option value="all">All Buildings</option>
                            <?php
              foreach ($filter_building_list as $r) {
                echo ('<option value="' . $r['building_id'] . '">' . $r['building_name'] . '</option>');
              }
              ?>
                        </select>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_category_current" class="col-sm-4 col-md-3 filter-text">Category:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="filter_category_current">
                                <option value="all">All Requests</option>
                                <option value="1">Internal Requests</option>
                                <option value="2">Tanent Requests</option>
                                <option value="0">System Generated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_status_current" class="col-sm-4 col-md-3 filter-text">Status:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="filter_status_current">
                                <option value="all">All Status</option>
                                <option value="open">Open</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_units_current" class="col-sm-4 col-md-3 filter-text">Units:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="filter_units_current" disabled>
                                <option value="all" selected>All Units</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_created_from_current" class="col-sm-4 col-md-3 filter-text">From:</label>
                        <div class="col-sm-8 col-md-9">
                            <input type="date" class="form-control" id="filter_created_from_current">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_created_to_current" class="col-sm-4 col-md-3 filter-text">To:</label>
                        <div class="col-sm-8 col-md-9">
                            <input type="date" class="form-control" id="filter_created_to_current">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_employee_current" class="col-sm-4 col-md-3 filter-text">Employee:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="filter_employee_current">
                                <option value="all" selected>All Employees</option>
                                <?php
                foreach ($filter_employee_lst as $row) {
                  echo ('<option value="' . $row['employee_id'] . '">' . $row['full_name'] . '</option>');
                }
                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="order_by_current" class="col-sm-4 col-md-3 filter-text">OrderBy:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="order_by_current">
                                <option value="recent_first">Recent First</option>
                                <option value="unread_first">Unread First</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_tenant_current" class="col-sm-4 col-md-3 filter-text">Tenant:</label>
                        <div class="col-sm-8 col-md-9">
                            <input class="form-control" id="filter_tenant_current" placeholder="Wildcard Search">
                        </div>
                    </div>
                </div>


                <div class="col-sm-4 col-md-3 filter-block col-sm-off-8 col-md-offset-6" style="padding-top: 5px;">
                    <button class="btn btn-primary col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"
                        id="default_current">Clear</button>
                    <button class="btn btn-primary col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-1"
                        id="search_current">Search</button>
                </div>

            </div>
            <!-- filter end-->

            <!-- issue list -->
            <div id="issue-list" class="col-sm-12 col-md-12">
                <legend>Current Requests List</legend>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-fixed">
                        <thead>
                            <tr>
                                <td class="col-md-1 text-center">Status</td>
                                <td class="col-md-2 text-center">Requests Type</td>
                                <td class="col-md-1 text-center">Level</td>
                                <td class="col-md-2 text-center">Date Created</td>
                                <td class="col-md-1 text-center">Days Old</td>
                                <td class="col-md-1 text-center">Creator</td>
                                <td class="col-md-2 text-center">Location</td>
                                <td class="col-md-2 text-center">Description</td>
                            </tr>
                        </thead>
                        <tbody id="current_issue_tbody">
                            <?php
              // current issue list
              foreach ($current_issues as $row) {
                $request_id = $row['id'];
                $request_type_id = $row['request_type_id'];
                $building_id = $row['building_id'];
                $request_level = $DB_request->get_request_level($request_type_id, $building_id);

                $level_label_class = '';
                if ($request_level == 'SERIOUS') {
                  $level_label_class = 'level-label-serious';
                } elseif ($request_level == 'URGENT') {
                  $level_label_class = 'level-label-urgent';
                }


                date_default_timezone_set('America/New_York');
                $created_time = date("M j, Y", strtotime($row['created_time']));
                $created_datetime = date_create($created_time);
                $today = date_create(date("M j, Y", time()));
                $interval = date_diff($created_datetime, $today)->format('%d');;


                $created_user_info = $DB_request->get_user_info($row['created_user_id']);
                $apart_info = $DB_request->get_apartment_info($request_id);
                $address = $apart_info['building_name'] . ' ' . $apart_info['specific_area'];
                $message = $row['message'];
                if (strlen($message) == 0)
                  $message = ' - ';

                $request_status = $row['issue_status'];
                $request_type = $row['request_type'];
                $last_access_time = $row['last_access_time'];
                $last_update_time = $row['last_update_time'];
                $request_category = $row['request_category'];

                if (strtotime($last_access_time) < strtotime($last_update_time))
                  $style_class = 'danger';
                else if ($request_category == 1) //internal
                  $style_class = 'success';
                else //tenant
                  $style_class = 'warning';
              ?>
                            <tr class="<?php echo $style_class; ?> issue-line"
                                id="<?php echo 'issue_row_' . $request_id; ?>" data-toggle="" data-target="#iModal"
                                data-request="<?php echo $request_id; ?>">
                                <td class="col-md-1 text-center"><?php echo strtoupper($request_status) ?></td>
                                <td class="col-md-2 text-center"><?php echo $request_type ?></td>
                                <td class="col-md-1 text-center <?php echo $level_label_class; ?>">
                                    <?php echo $request_level; ?></td>
                                <td class="col-md-2 text-center"><?php echo $created_time; ?></td>
                                <td class="col-md-1 text-center"><?php echo $interval; ?></td>
                                <td class="col-md-1 text-center non-overflow" data-toggle="tooltip"
                                    data-container="body"
                                    title="<?php echo " Telephone : " . $created_user_info['mobile'] . " Email : " . $created_user_info['email']; ?>">
                                    <?php echo $created_user_info['full_name']; ?></td>
                                <td class="col-md-2 text-center non-overflow" data-toggle="tooltip"
                                    data-container="body" title="<?php echo $address; ?>"><?php echo $address; ?></td>
                                <td class="col-md-2 text-center non-overflow" data-toggle="tooltip"
                                    data-container="body" title="<?php echo $message; ?>"><?php echo $message; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- issue list end -->

            <div id="pagination-bottom" class="col-sm-12 col-md-12">
                <nav aria-label="Page navigation">
                    <ul class="pagination" id="current_issues_paging">
                        <li class="disabled" id="current_issues_previous_page"><a href="#" aria-label="Previous"><span
                                    aria-hidden="true">&laquo;</span></a></li>
                        <li class="active"><a href="#" id="page_1" onclick="get_current_issue_list(1)">1</a></li>
                        <?php
            $raw_current_issues_count = sizeof($raw_current_issues);
            $pages = intval(ceil($raw_current_issues_count / 20));
            for ($i = 2; $i <= $pages; $i++) {
              echo ('<li class=""><a href="#" id="page_' . $i . '" onclick="get_current_issue_list(' . $i . ')">' . $i . '</a></li>');
            }
            ?>
                        <li id="current_issues_next_page"><a href="#" aria-label="Next"><span
                                    aria-hidden="true">&raquo;</span></a></li>
                    </ul>
                </nav>
                <input type="hidden" id="current_page_number" value="<?= $pages ?>">
            </div>

        </div>

        <!--past issue panel-->
        <div role="tabpanel" class="tab-pane remove-for-tenant row" id="past-issues">
            <div id="filter-part" class="col-sm-12 col-md-12" style="padding: 15px 0;">
                <div class="col-sm-4 col-md-3 filter-block">
                    <label for="filter_building_past" class="col-sm-4 col-md-3 filter-text">Building:</label>
                    <div class="col-sm-8 col-md-9">
                        <select class="form-control" id="filter_building_past">
                            <option value="all">All Buildings</option>
                            <?php
              foreach ($filter_building_list as $r) {
                echo ('<option value="' . $r['building_id'] . '">' . $r['building_name'] . '</option>');
              }
              ?>
                        </select>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_category_past" class="col-sm-4 col-md-3 filter-text">Category:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="filter_category_past">
                                <option value="all">All Requests</option>
                                <option value="1">Internal Requests</option>
                                <option value="2">Tanent Requests</option>
                                <option value="0">System Generated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_status_past" class="col-sm-4 col-md-3 filter-text">Status:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="filter_status_past">
                                <option value="all">All Status</option>
                                <option value="open">Open</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_units_past" class="col-sm-4 col-md-3 filter-text">Units:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="filter_units_past" disabled>
                                <option value="all" selected>All Units</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_created_from_past" class="col-sm-4 col-md-3 filter-text">From:</label>
                        <div class="col-sm-8 col-md-9">
                            <input type="date" class="form-control" id="filter_created_from_past">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_created_to_past" class="col-sm-4 col-md-3 filter-text">To:</label>
                        <div class="col-sm-8 col-md-9">
                            <input type="date" class="form-control" id="filter_created_to_past">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_employee_past" class="col-sm-4 col-md-3 filter-text">Employee:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="filter_employee_past">
                                <option value="all" selected>All Employees</option>
                                <?php
                foreach ($filter_employee_lst as $row) {
                  echo ('<option value="' . $row['employee_id'] . '">' . $row['full_name'] . '</option>');
                }
                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="order_by_past" class="col-sm-4 col-md-3 filter-text">OrderBy:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="order_by_past">
                                <option value="recent_first">Recent First</option>
                                <option value="unread_first">Unread First</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_tenant_past" class="col-sm-4 col-md-3 filter-text">Tenant:</label>
                        <div class="col-sm-8 col-md-9">
                            <input class="form-control" id="filter_tenant_past" placeholder="Wildcard Search">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block col-sm-off-8 col-md-offset-6" style="padding-top: 5px;">
                    <button class="btn btn-primary col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"
                        id="default_past">Clear</button>
                    <button class="btn btn-primary col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-1"
                        id="search_past">Search</button>
                </div>

            </div>


            <!-- past issue list -->
            <div id="issue-list" class="col-sm-12 col-md-12">
                <legend>Past Requests List</legend>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-fixed">
                        <thead>
                            <tr>
                                <td class="col-md-1 text-center">Date Closed</td>
                                <td class="col-md-2 text-center">Requests Type</td>
                                <td class="col-md-1 text-center">Level</td>
                                <td class="col-md-1 text-center">Date Created</td>
                                <td class="col-md-1 text-center">Creator</td>
                                <td class="col-md-2 text-center">Location</td>
                                <td class="col-md-3 text-center">Description</td>
                            </tr>
                        </thead>
                        <tbody id="past_issues_tbody">
                            <?php
              // past issue list
              foreach ($past_issues as $row) {
                $request_id = $row['id'];
                $request_type_id = $row['request_type_id'];
                $building_id = $row['building_id'];
                $request_level = $DB_request->get_request_level($request_type_id, $building_id);

                $level_label_class = '';
                if ($request_level == 'SERIOUS') {
                  $level_label_class = 'level-label-serious';
                } elseif ($request_level == 'URGENT') {
                  $level_label_class = 'level-label-urgent';
                }

                $created_time = date("Y-m-d", strtotime($row['created_time']));
                $created_datetime = date_create($created_time);

                $created_user_info = $DB_request->get_user_info($row['created_user_id']);
                $apart_info = $DB_request->get_apartment_info($request_id);
                $address = $apart_info['building_name'] . ' ' . $apart_info['specific_area'];
                $message = $row['message'];
                if (strlen($message) == 0)
                  $message = ' - ';

                $request_type = $row['request_type'];
                $last_access_time = $row['last_access_time'];
                $last_update_time = date("Y-m-d", strtotime($row['last_update_time']));
                $request_category = $row['request_category'];

                if ($request_category == 1) //internal
                  $style_class = 'success';
                else //tenant
                  $style_class = 'warning';
              ?>
                            <tr class="<?php echo $style_class; ?> issue-line" data-toggle="modal" data-target="#iModal"
                                data-request="<?php echo $request_id; ?>">
                                <td class="col-md-1 text-center"><?php echo $last_update_time; ?></td>
                                <td class="col-md-2 text-center"><?php echo $request_type; ?></td>
                                <td class="col-md-1 text-center <?php echo $level_label_class; ?>">
                                    <?php echo $request_level; ?></td>
                                <td class="col-md-1 text-center"><?php echo $created_time; ?></td>
                                <td class="col-md-1 text-center non-overflow" data-toggle="tooltip"
                                    data-container="body"
                                    title="<?php echo " Telephone : " . $created_user_info['mobile'] . " Email : " . $created_user_info['email']; ?>">
                                    <?php echo $created_user_info['full_name']; ?></td>
                                <td class="col-md-2 text-center non-overflow" data-toggle="tooltip"
                                    data-container="body" title="<?php echo $address; ?>"><?php echo $address; ?></td>
                                <td class="col-md-3 text-center non-overflow" data-toggle="tooltip"
                                    data-container="body" title="<?php echo $message; ?>"><?php echo $message; ?></td>
                            </tr>

                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- issue list end -->

            <div class="col-sm-12 col-md-12">
                <nav aria-label="Page navigation">
                    <ul class="pagination" id="past_issues_paing">
                        <li class="disabled" id="past_issues_previous_page"><a href="#" aria-label="Previous"><span
                                    aria-hidden="true">&laquo;</span></a></li>
                        <li class="active"><a href="#" id="page_1" onclick="get_past_issue_page(1)">1</a></li>
                        <?php
            $raw_past_issues_count = sizeof($raw_past_issues);
            $pages = intval(ceil($raw_past_issues_count / 20));
            for ($i = 2; $i <= $pages; $i++) {
              echo ('<li class=""><a href="#" id="page_' . $i . '" onclick="get_past_issue_page(' . $i . ')">' . $i . '</a></li>');
            }
            ?>
                        <li id="past_issues_next_page"><a href="#" aria-label="Next"><span
                                    aria-hidden="true">&raquo;</span></a></li>
                    </ul>
                </nav>
                <input type="hidden" id="past_page_number" value="<?= $pages ?>">
            </div>
        </div>

        <!-- bulletins panel -->
        <div role="tabpanel" class="tab-pane row" id="bulletin">
            <div class="col-sm-12 col-md-12" style="margin-top: 10px;">
                <legend>Bulletins</legend>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered table-fixed">
                        <thead>
                            <tr>
                                <td class="col-md-2 text-center">Issue Time</td>
                                <td class="col-md-2 text-center">Building</td>
                                <td class="col-md-2 text-center">Issuer</td>
                                <td class="col-md-2 text-center">Active Period</td>
                                <td class="col-md-4 text-center">Title</td>
                            </tr>
                        </thead>
                        <tbody id="bulletins_tbody">
                            <?php
              $bulletins = $DB_request->get_bulletin_list($user_id, $user_unit_id);
              foreach ($bulletins as $row) {
                $bulletin_id = $row['bulletin_id'];
                $building_name = $row['building_name'];
                $issuer_name = $row['issuer_name'];
                $issuer_tele = $row['issuer_telephone'];
                $issue_time = date('Y-m-d H:i', strtotime($row['create_time']));
                $issue_from = date('Y-m-d', strtotime($row['issue_from']));
                $issue_to = date('Y-m-d', strtotime($row['issue_to']));
                $title = $row['title'];
              ?>
                            <tr class="bulletins-line" data-toggle="modal" data-target="#iModal_bulletin_details"
                                data-request="<?= $bulletin_id ?>">
                                <td class="col-md-2 text-center"><?php echo $issue_time; ?></td>
                                <td class="col-md-2 text-center"><?php echo $building_name; ?></td>
                                <td class="col-md-1 text-center"><?php echo $issuer_name; ?></td>
                                <td class="col-md-2 text-center"><?php echo $issue_from . ' - ' . $issue_to; ?></td>
                                <td class="col-md-4 text-center non-overflow" data-toggle="tooltip"
                                    data-container="body" title="<?php echo $title; ?>"><?php echo $title; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!--end all tags-->
    </div>
</div>





<!-- Modal -->
<div class="modal fade" id="iModal" tabindex="-1" role="dialog" aria-labelledby="iModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <input type="hidden" id="request_id" value="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <div class="modal-title" id="iModalLabel">
                    <div class="modal-h-id"><i>Requests #: </i><span id="issue_id"></span><span
                            id="issue_h_type"></span></div>
                </div>
                <div class="modal-status modal-status-open" id="open_or_closed"><span class="main-status"
                        id="open_or_closed_text">OPEN</span></div>

                <div class="modal-created-by-info">
                    <i class="title">Created By:</i>
                    <br>
                    <span class="glyphicon glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;<span
                        id="modal_user_name" class="box-text"></span><br>
                    <span class="glyphicon glyphicon glyphicon glyphicon glyphicon-phone"
                        aria-hidden="true"></span>&nbsp;<span id="modal_user_telephone" class="box-text"></span>
                </div>

                <div class="modal-created-daytime">
                    <i class="title">Created At:</i>
                    <br>
                    <img src="custom/request/img/date-time.svg">&nbsp<span id="modal_date" class="box-text"></span><br>
                    <img src="custom/request/img/time.svg">&nbsp<span id="modal_time" class="box-text"></span>
                </div>

            </div>

            <div class="modal-body" style="padding: 10px 16px">
                <div class="modal-apart-info">
                    <img class="image modal-img" id="modal_building_img"
                        src="files/requests/request_building_mask.jpeg">
                    <div class="text">
                        <span class="text-item-title box-text-bigger" id="modal_building"></span>
                        <span class="text-item box-text-bigger" id="modal_apart"></span>
                        <span class="text-item box-text-bigger" id="modal_address"></span>
                    </div>
                </div>



                <ul class="nav nav-tabs" role="tablist" style="margin-top: 10px;">
                    <li role="presentation" class="active"><a id="communication_tag" href="#communication"
                            aria-controls="communication" role="tab" data-toggle="tab">Communications</a></li>
                    <li role="presentation"><a id="attach_tag" href="#attach" aria-controls="attach" role="tab"
                            data-toggle="tab">Attachments</a></li>
                    <li role="presentation"><a id="recipient_tag" href="#recipient" aria-controls="recipient" role="tab"
                            data-toggle="tab">Recipient</a></li>
                    <li role="presentation"><a id="edit_tag" href="#edit" aria-controls="edit" role="tab"
                            data-toggle="tab">Editing</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="communication">
                        <div class="communication-div">
                            <div class="row">
                                <div id="communications" class="col-sm-12 col-md-12"
                                    style="overflow: auto; white-space: nowrap">
                                    <!-- communications -->
                                </div>
                            </div>

                            <div class="row">
                                <div id="new_message" class="col-sm-12 form-group">
                                    <label></label>
                                    <textarea class="form-control" id="communication_text" name="message"
                                        placeholder="Communicaiton Messgae"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group" id="opeartions_new_message">
                                    <div class="col-sm-12">
                                        <button type="button" class="btn btn-primary btn-post" id="post_communication"
                                            onclick="add_communication()">Post</button>
                                        <div class="checkbox remove-for-tenant modal-communication-selection">
                                            <label><input type="checkbox" id="hide_for_tenant">Hidden for
                                                tenants</label></div>
                                        <div class="checkbox remove-for-tenant modal-communication-selection">
                                            <label><input type="checkbox" id="force_ntf">Notify recipients</label></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="attach">
                        <div class="attach-div">
                            <div class="attach-title">
                                <p>Images:</p>
                            </div>
                            <div class="attach-image" id="attach_images"></div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="recipient">
                        <div class="recipient-div">
                            <div class="form">
                                <form id="editRecipient">
                                    <div class="row">
                                        <div class="col-sm-12" id="recipient-employee-wrap">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4">Employee</label>
                                                <div class="col-sm-7">
                                                    <table id="recipient-employee">
                                                        <tr>
                                                            <td>
                                                                <label class="checkbox"><input class="edit-input"
                                                                        type="checkbox" name="employee">null</label>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-4">
                                                    <button id="saveRecipients" type="submit"
                                                        class="btn btn-primary">Save</button>
                                                    <button id="cancelRecipients" type="button" class="btn btn-default"
                                                        data-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="edit">
                        <div class="edit-div">
                            <div class="form">
                                <form id="edit-request">
                                    <div class="row">
                                        <div class="col-sm-12 request-location-wrap">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4 col-md-3"
                                                    for="location">Location</label>
                                                <div class="col-sm-8 col-md-8 request-location-div">
                                                    <input class="edit-input form-control" id="location"
                                                        name="location">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4 col-md-3" for="request-type">Request
                                                    Type</label>
                                                <div class="col-sm-8 col-md-8 request-type-div">
                                                    <select class="edit-select form-control request-type"
                                                        id="request-type" name="request_type" title="Type">
                                                        <option value="">null</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4 col-md-3">Approve to Visit</label>
                                                <div class="col-sm-8 col-md-8 request-approve-div">
                                                    <label class="radio-inline">
                                                        <input class="edit-input request-visit-approved" type="radio"
                                                            name="approved" value="1" checked>Yes
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input class="edit-input request-visit-not-approved"
                                                            type="radio" name="approved" value="0">No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 request-visit-time-wraps">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4 col-md-3" for="visitFrom">Visit
                                                    Time</label>
                                                <div class="col-sm-8 col-md-8 request-time-div">
                                                    <input class="edit-input form-control request-visit-from"
                                                        type="time" name="visitFrom" id="visitFrom" value="09:00:00">
                                                    <div class="time-note">Note: Please be prepared for at least 4 hours
                                                        of visit for our handyman to work on this request.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 request-file-wrap">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4 col-md-3">Pictures</label>
                                                <div class="col-sm-8 col-md-8">
                                                    <label class="btn btn-primary btn-file">
                                                        Select Images<input class="request-pic-upload" type="file"
                                                            name="pictures[]" style="display: none">
                                                    </label>
                                                    <div class="report-location-margin-top">
                                                        <img id="reportPic" class="request-pic-preview">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4 col-md-3">Message</label>
                                                <div class="col-sm-8 col-md-8 request-message-div">
                                                    <textarea id="editMessage"
                                                        class="edit-input form-control request-message" name="message"
                                                        rows="4" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 request-status-wrap">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4 col-md-3">Status</label>
                                                <div class="col-sm-8 col-md-8 request-status-div">
                                                    <select class="edit-select form-control request-status"
                                                        id="request-status" name="request_status" title="Status">
                                                        <option value="">open</option>
                                                        <option value="">closed</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 request-notify-me-wrap">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4 col-md-3 request-notify-label"
                                                    id="notifyLabel">Notify Me by</label>
                                                <div class="col-sm-8 col-md-8">
                                                    <label class="checkbox-inline"><input type="hidden"
                                                            name="notifyMeByEmail" value="0"><input
                                                            class="edit-input notify-me-by-email" type="checkbox"
                                                            name="notifyMeByEmail" id="editNotifyMeEmail"
                                                            value="1">Email</label>
                                                    <label class="checkbox-inline"><input type="hidden"
                                                            name="notifyMeBySms" value="0"><input
                                                            class="edit-input notify-me-by-sms" type="checkbox"
                                                            name="notifyMeBySms" id="editNotifyMeSms"
                                                            value="1">SMS</label>
                                                    <label class="checkbox-inline"><input type="hidden"
                                                            name="notifyMeByVoice" value="0"><input
                                                            class="edit-input notify-me-by-voice" type="checkbox"
                                                            name="notifyMeByVoice" id="editNotifyMeVoice"
                                                            value="1">Voice</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-4">
                                                    <button type="button" class="btn btn-primary"
                                                        id="saveEdit">Save</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal"
                                                        id="cancelEdit">Cancel</button>
                                                </div>
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
    </div>
</div>


<!-- modal for report issue button-->
<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="iModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Report an Request</h2>
            </div>
            <div class="modal-body">
                <div class="reportAnIssue">
                    <div class="form">
                        <form id="reportIssue" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-sm-12 request-location-wrap" id="reportLocationWrap">
                                    <div class="form-group">
                                        <label class="edit-label col-sm-4 col-md-3" for="location">Location</label>
                                        <div id="reportLocation" class="col-sm-8 col-md-8 request-location-div">
                                            <select class="edit-input form-control report-location" id="reportBuilding"
                                                name="building">
                                                <option>1</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="edit-label col-sm-4 col-md-3" for="request-type">Request
                                            Type</label>
                                        <div class="col-sm-8 col-md-8">
                                            <select class="edit-select form-control request-type"
                                                id="report-request-type" name="reportRequestType" title="Type">
                                                <option value="0">null</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12" id="requestTypeWrap">
                                    <div class="form-group">
                                        <label class="edit-label col-sm-4 col-md-3">Approve to Visit</label>
                                        <div class="col-sm-8 col-md-8">
                                            <label class="radio-inline">
                                                <input id="report-visitApproved"
                                                    class="edit-input request-visit-approved" type="radio"
                                                    name="reportApprovedVisit" value="1" checked>Yes
                                            </label>
                                            <label class="radio-inline">
                                                <input id="report-visitNotApproved"
                                                    class="edit-input request-visit-not-approved" type="radio"
                                                    name="reportApprovedVisit" value="0">No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 request-visit-time-wraps" id="visitFromWrap">
                                    <div class="form-group">
                                        <label class="edit-label col-sm-4 col-md-3" for="visitFrom">Visit Time</label>
                                        <div class="col-sm-8 col-md-8">
                                            <input class="edit-input form-control" type="time" name="reportVisitFrom"
                                                id="report-visitFrom" value="09:00:00">
                                            <div class="time-note">Note: Please be prepared for at least 4 hours of
                                                visit for our handyman to work on this request.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="edit-label col-sm-4 col-md-3">Pictures</label>
                                        <div class="col-sm-8 col-md-8" id="reportPicturesWrap">
                                            <label class="btn btn-primary btn-file">Select Images<input
                                                    class="request-pic-upload" id="reportButton" type="file"
                                                    name="file[]" style="display: none" accept="image/*"
                                                    multiple></label>
                                            <div class="report-location-margin-top" id="report_preview_imgs">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="edit-label col-sm-4 col-md-3">Message</label>
                                        <div class="col-sm-8 col-md-8">
                                            <textarea id="reportMessage" class="edit-input form-control"
                                                name="reportMessage" rows="4" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 request-status-wrap" id="report-status">
                                    <div class="form-group">
                                        <label class="edit-label col-sm-4 col-md-3">Status</label>
                                        <div class="col-sm-8 col-md-8">
                                            <select class="edit-select form-control request-status"
                                                id="report-request-status" name="reportRequestStatus" title="Status">
                                                <option value="0">null</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 request-notify-me-wrap">
                                    <div class="form-group">
                                        <label class="edit-label col-sm-4 col-md-3 request-notify-label"
                                            id="notifyLabel">Notify Me by</label>
                                        <div class="col-sm-8 col-md-8">
                                            <label class="checkbox-inline"><input type="hidden" name="notifyMeByEmail"
                                                    value="0"><input class="edit-input notify-me-by-email"
                                                    type="checkbox" name="notifyMeByEmail" id="reportNotifyMeEmail"
                                                    value="1">Email</label>
                                            <label class="checkbox-inline"><input type="hidden" name="notifyMeBySms"
                                                    value="0"><input class="edit-input notify-me-by-sms" type="checkbox"
                                                    name="notifyMeBySms" id="reportNotifyMeSms" value="1">SMS</label>
                                            <label class="checkbox-inline"><input type="hidden" name="notifyMeByVoice"
                                                    value="0"><input class="edit-input notify-me-by-voice"
                                                    type="checkbox" name="notifyMeByVoice" id="reportNotifyMeVoice"
                                                    value="1">Voice</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-8 col-sm-offset-4">
                                            <button type="button" class="btn btn-primary" id="submitReport">Report this
                                                request</button>
                                            <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                                        </div>
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

<!--modal for bulletin details-->
<div class="modal fade" id="iModal_bulletin_details" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <input type="hidden" id="bulletin_id" value="">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modal-title">
                    <div class="modal-h-id"><i>Notification #: </i><span id="bulletin_shown_id"></span></div>
                </div>
                <div class="modal-created-by-info">
                    <i class="title">Issured By:</i>
                    <br>
                    <span class="glyphicon glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;<span
                        id="bulletin_creator_name" class="box-text"></span><br>
                    <span class="glyphicon glyphicon glyphicon glyphicon glyphicon-phone"
                        aria-hidden="true"></span>&nbsp;<span id="bulletin_creator_telephone" class="box-text"></span>
                </div>

                <div class="modal-created-daytime">
                    <i class="title">Issured At:</i>
                    <br>
                    <img src="custom/request/img/date-time.svg">&nbsp<span id="issued_date" class="box-text"></span><br>
                    <img src="custom/request/img/time.svg">&nbsp<span id="issued_time" class="box-text"></span>
                </div>

            </div>

            <div class="modal-body">

                <ul class="nav nav-tabs" role="tablist" style="margin-top: 10px;">
                    <li role="presentation" class="active"><a id="bulletin_content_tag" href="#bulletin_content"
                            role="tab" aria-controls="bulletin_content" data-toggle="tab">Details</a></li>
                    <li role="presentation" class="remove-for-tenant"><a id="bullentin_read_status_tag"
                            href="#bulletin_read_status" role="tab" data-toggle="tab">Read Tenants</a></li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="bulletin_content">
                        <p class="bulletin-title" id="bullent_modal_title"></p>
                        <hr>
                        <p class="bulletin-content" id="bullent_modal_content"></p>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="bulletin_read_status">
                        <table class="table table-striped table-condensed" id="bulletin_reading_status">
                            <thead>
                                <tr>
                                    <td class="col-md-3 text-center">Tenant Name</td>
                                    <td class="col-md-3 text-center">Login Account</td>
                                    <td class="col-md-3 text-center">Last Readed Time</td>
                                </tr>
                            </thead>
                            <tbody id="bulletin_read_status_tbody"></tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!--modal for create a new bulletin-->
<div class="modal fade" id="newBulletinModal" tabindex="-1" role="dialog" aria-labelledby="iModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Bulletin</h2>
            </div>

            <div class="modal-body">
                <div class="bulletinFormWrap">
                    <div class="form">
                        <form id="bulletinForm">

                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 col-md-3">Building</label>
                                    <div class="col-sm-8 col-md-8">
                                        <select class="form-control" id="bulletinBuilding" name="bulletinBuilding"
                                            required>
                                            <option>null</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 col-md-3">From</label>
                                    <div class="col-sm-8 col-md-8">
                                        <input class="form-control" id="bulletinFrom" type="date" name="bulletinFrom"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 col-md-3">To</label>
                                    <div class="col-sm-8 col-md-8">
                                        <input class="form-control" id="bulletinTo" type="date" name="bulletinTo"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 col-md-3">Title</label>
                                    <div class="col-sm-8 col-md-8">
                                        <textarea class="form-control" id="bulletinTitle" name="bulletinTitle" rows="1"
                                            required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 col-md-3">Content</label>
                                    <div class="col-sm-8 col-md-8">
                                        <textarea class="form-control" id="bulletinContent" name="bulletinContent"
                                            rows="4" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <div class="col-sm-8 col-sm-offset-4">
                                        <button type="submit" class="btn btn-primary"
                                            id="submitNewBulletin">Submit</button>
                                        <button class="btn btn-default" data-dismiss="modal">Cancel</button>
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
<script src="custom/request/js/lightbox.min.js"></script>
<script>
lightbox.option({
    'alwaysShowNavOnTouchDevices': true
});

$(function() {
    $('[data-toggle="tooltip"]').tooltip();
});

$('#iModal').on('show.bs.modal', function(event) {
    var tr = $(event.relatedTarget);
    var request_id = tr.data('request');
    var modal = $(this);
    modal.find('.modal-header input').val(request_id);
    $('#communication_tag').trigger('click');
    init_modal(request_id);
});

$('#iModal_bulletin_details').on('show.bs.modal', function(event) {
    var tr = $(event.relatedTarget);
    var bulletin_id = tr.data('request');
    var modal = $(this);
    modal.find('.modal-header input').val(bulletin_id);
    init_bulletin_modal(bulletin_id);
});


$('#iModal').on('hidden.bs.modal', function(e) {
    $('#communications').empty();
    $('#attach_images').empty();

});

$('#communication_tag').on('click', function(e) {
    var request_id = $('#request_id').val();
    init_communications(request_id);
});

$('#attach_tag').on('click', function(e) {
    var request_id = $('#request_id').val();
    init_attachments(request_id);
});

$('#recipient_tag').on('click', function(e) {
    var request_id = $('#request_id').val();
    init_recipient(request_id);
});

$('#edit_tag').on('click', function(e) {
    init_request('#edit-request');
    var request_id = $('#request_id').val();
    init_editing('#edit-request', request_id);
});

//-----------save button-----------
$('#saveRecipients').on('click', function(e) {
    var request_id = $('#request_id').val();
    set_recipients(request_id);
});

$('#saveEdit').on('click', function(e) {
    var request_id = $('#request_id').val();
    submit_edit(request_id);
});

//-----------report button------------
$('#startReport').on('click', function(e) {
    init_request('#reportIssue');
});

$('#submitReport').on('click', function(e) {
    submit_report();
});

//------------bulletin button-----------
$('#startBulletin').on('click', function(e) {
    init_new_bulletin();
});

$('#submitNewBulletin').on('click', function() {
    submit_new_bulletin();
});

$('#bullentin_read_status_tag').on('click', function(e) {
    var bulletin_id = $('#bulletin_id').val();
    set_bulletin_reading_status(bulletin_id);
});

// --------------- ajax methods ---------------
var user_id = <?php echo $user_id; ?>;
var user_level = <?php echo $user_level; ?>;
var user_unit_id = <?php echo $user_unit_id; ?>;
var relative_path = "custom/request/";


//-----------------  communications -------------------

function init_communications(request_id) {
    $.ajax({
        type: "post",
        url: relative_path + "request_communication_controller.php",
        data: {
            action: "get_communications",
            request_id: request_id,
            user_id: user_id
        },
        dataType: "json",
        success: function(result) {
            set_communications_view(result);
        },
        error: function(result) {
            console.log("Error:" + result);
        }
    });
}

function add_communication() {
    var request_id = $('#request_id').val();
    var message = $('#communication_text').val();
    var if_seen_by_tenant = 1;
    var if_notify = false;

    if ($('#hide_for_tenant').is(':checked')) {
        if_seen_by_tenant = 0;
    }
    if ($('#force_ntf').is(':checked')) {
        if_notify = true;
    }

    if (message.length > 0) {
        $.ajax({
            type: "post",
            url: relative_path + "request_communication_controller.php",
            data: {
                action: "add_communication",
                user_id: user_id,
                request_id: request_id,
                message: message,
                if_seen_by_tenant: if_seen_by_tenant,
                if_notify: if_notify
            },
            dataType: "json",
            async: false,
            success: function(result) {
                set_communications_view(result);
            },
            error: function(result) {
                console.log("error:" + result);
            }
        });
    }

    $('#communication_text').val('');
    $('#hide_for_tenant').prop('checked', true);
    $('#force_ntf').prop('checked', false);
}

function set_communications_view(data) {
    $('#communications').empty();

    for (var i in data) {
        var bg_color = 'bg-info';
        if (data[i].creator_id == user_id) {
            bg_color = 'bg-success';
        }
        if (data[i].is_system_msg == 1) {
            bg_color = 'bg-warning';
        }


        var creator_info = '';
        if (data[i].creator_id != 0) {
            creator_info = data[i].creator_name + ', ' + data[i].creator_role;
        } else {
            creator_info = data[i].creator_role;
        }

        $('#communications').append('<div class="message-container ' + bg_color + '">\n' +
            '                  <div class="message-text">' + data[i].remark + '</div>\n' +
            '                  <div class="message-info">\n' +
            '                    <span class="message-info-name">' + creator_info + '</span>\n' +
            '                    <a id="read_status_' + i +
            '" class="message-info-read-status" tabindex="0" role="button" data-toggle="popover" data-container="body" data-trigger="focus">Reading status</a>\n' +
            '                    <span class="message-info-date">' + data[i].created_time + '</span>\n' +
            '                  </div>\n' +
            '                </div>');

        var employee_text = '';
        var tenant_text = '';
        var assignees_status = data[i].assignees_status;
        for (r in assignees_status) {
            if (assignees_status[r].read_status == 'read')
                var temp =
                    '<button type="button" class="btn btn-primary read-status-bk" data-toggle="tooltip" data-placement="right" title="last access: ' +
                    assignees_status[r].last_access_time + '">' + assignees_status[r].user_name + '</button>';
            else if (assignees_status[r].read_status == 'unread')
                var temp =
                    '<button type="button" class="btn btn-warning read-status-bk" data-toggle="tooltip"  data-placement="right" title="last access: ' +
                    assignees_status[r].last_access_time + '">' + assignees_status[r].user_name + '</button>';

            if (assignees_status[r].user_role == 'Employee')
                employee_text += temp;
            else if (assignees_status[r].user_role == 'Tenant')
                tenant_text += temp;
        }

        var text = '<div><h6>Employees:</h6>' + employee_text + '</span> <h6>Tenants:</h6>' + tenant_text + '<div>';
        //yuhong: add legend to show the color coding
        text = text +
            '<div><h6> Legend: </h6> Read: <button type="button" class = "btn btn-primary"> &nbsp; &nbsp;  </button> ' +
            'Unread: <button type="button" class = "btn btn-warning">  &nbsp; &nbsp;</button></div>';

        $('#read_status_' + i).popover({
            html: true,
            content: text
        });
    }

    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
    $(function() {
        $('[data-toggle="popover"]').popover();
    });
}


// ------------------- attachments -------------------------

function init_attachments(request_id) {
    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: {
            action: "get_attachments",
            request_id: request_id
        },
        dataType: "json",
        async: false,
        success: function(result) {
            set_attachments_view(result);
        },
        error: function(result) {
            console.log("Error:" + result);
        }
    });
}

function set_attachments_view(data) {
    $('#attach_images').empty();
    for (index in data) {
        $('#attach_images').append('<a class="attach-a" href="' + data[index] +
            '" data-lightbox="attach-img" data-title="Picture Title"><img src="' + data[index] + '" href="#"></a>');
    }
}

//---------------------------------------------

function init_recipient(request_id) {
    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: {
            action: "get_recipients",
            request_id: request_id
        },
        async: false,
        dataType: "json",
        success: function(result) {
            set_recipients_view(result);
        },
        error: function(result) {
            console.log("Error: " + result);
        }
    });
};


function init_modal(request_id) {
    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: {
            action: "get_modal_info",
            request_id: request_id,
            user_id: user_id,
            user_unit_id: user_unit_id
        },
        dataType: "json",
        async: false,
        success: function(result) {
            set_modal_view(result);
        },
        error: function(result) {
            console.log("Error:" + result);
        }
    });
};

function set_recipients(request_id) {
    var recipientEmployee = [];
    $('#recipient-employee input').each(function(i, item) {
        var assigned = '0';
        if (item.checked) {
            assigned = '1';
        }
        var employee = {
            employee_id: item.value,
            assigned: assigned
        };
        recipientEmployee.push(employee);
    });

    var recipientTenant = [];
    $('#recipient-tenant input').each(function(i, item) {
        var assigned = '0';
        if (item.checked) {
            assigned = '1';
        }
        var tenant = {
            tenant_id: item.value,
            assigned: assigned
        };
        recipientTenant.push(tenant);
    });

    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: {
            action: "set_recipients",
            request_id: request_id,
            employees: recipientEmployee,
            tenants: recipientTenant
        },
        success: function(result) {
            init_recipient(result)
        },
        error: function(result) {
            console.log("error:" + result);
        }
    });
}


function set_notify_methods() {
    var is_ntf_email = false;
    var is_ntf_sms = false;
    var is_ntf_voice = false;
    var request_id = $('#request_id').val();

    if ($('#notify_email').is(':checked'))
        is_ntf_email = true;
    if ($('#notify_sms').is(':checked'))
        is_ntf_sms = true;
    if ($('#notify_voice').is(':checked'))
        is_ntf_voice = true;

    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: {
            action: "set_notify_methods",
            user_id: user_id,
            request_id: request_id,
            notify_email: is_ntf_email,
            notify_sms: is_ntf_sms,
            notify_voice: is_ntf_voice
        },
        dataType: "json",
        success: function(result) {},
        error: function(result) {
            console.log('ERROR' + result);
        }
    });
};


//----  update view methods ------

function set_modal_view(data) {
    var request_id = '1000000' + data.request_id;
    var request_category = data.request_category;
    var request_status = data.request_status;
    var request_type = data.request_type;
    var open_or_close = data.open_or_close;
    var created_user_name = data.created_user_name;
    var created_user_telephone = data.created_user_telephone;
    var created_date = data.created_date;
    var created_time = data.created_time;
    var building_name = data.building_name;
    var apart = data.specific_area;
    var building_address = data.building_address;
    var building_picture = data.building_picture;


    $('#issue_id').text(request_id);
    $('#modal_user_name').text(created_user_name);
    $('#modal_user_telephone').text(created_user_telephone);
    $('#modal_date').text(created_date);
    $('#modal_time').text(created_time);
    $('#modal_building').text(building_name);
    $('#modal_apart').text(apart);
    $('#modal_address').text(building_address);
    $('#modal_building_img').attr('src', building_picture);

    //decarate the request type
    var issue_h_type = $('#issue_h_type');
    issue_h_type.text(request_type);

    if (user_level != 5) {
        if (request_category == 0)
            issue_h_type.attr('class', 'issue-h-type-system');
        else if (request_category == 1)
            issue_h_type.attr('class', 'issue-h-type-internal');
        else
            issue_h_type.attr('class', 'issue-h-type-tenant');
    }

    if (user_level == 5) {
        $('#recipient_tag').remove();
        $('#recipient').remove();
    }

    // change the issue bkg
    var issue_tr_id = '#issue_row_' + data.request_id;

    if (request_category == 2) {
        $(issue_tr_id).attr('class', 'issue-line warning');
    } else {
        $(issue_tr_id).attr('class', 'issue-line success');
    }
    //change unread count
    $('#unread_issue_count').text(data.open_issue_count);
    $('#unread_issue_count').text(data.unread_issue_count);

    //open or close
    if (open_or_close == 'open') {
        $('#open_or_closed_text').text(request_status);
        $('#open_or_closed').attr("class", "modal-status modal-status-open");

        //allow communication
        $('#new_message').attr('hidden', false);
        $('#opeartions_new_message').attr('hidden', false);
    } else {
        $('#open_or_closed_text').text(request_status);
        $('#open_or_closed').attr("class", "modal-status modal-status-close");

        //disable communication
        $('#new_message').attr('hidden', true);
        $('#opeartions_new_message').attr('hidden', true);
    }

    $('#hide_for_tenant').prop('checked', true);
    $('#force_ntf').prop('checked', false);
}


function set_recipients_view(data) {

    $('#recipient-employee').empty();
    var employees = data.employees;
    for (var x in employees) {
        //if the employee is already assigned with this request, then check the box
        if (employees[x].assigned === 1) {
            var checked = 'checked';
            if (employees[x].notifyEmployeesByEmail === 1) {
                $('#editRecipient .notify-employees-by-email').prop('checked', true);
            }
            if (employees[x].notifyEmployeesBySms === 1) {
                $('#editRecipient .notify-employees-by-sms').prop('checked', true);
            }
            if (employees[x].notifyEmployeesByVoice === 1) {
                $('#editRecipient .notify-employees-by-voice').prop('checked', true);
            }
        } else {
            var checked = '';
        }
        $('#recipient-employee').append(
            '<tr><td><label class="checkbox"><input class="edit-input" type="checkbox" name="employee" value="' +
            employees[x].employee_id + '" ' + checked + '>' + employees[x].full_name + '</label></td></tr>');
    }

    $('#editRecipient .tenant-recipient-wraps').remove();
    if (data.tenants) {
        $('#recipient-employee-wrap').after('<div class="col-sm-12 tenant-recipient-wraps">\n' +
            '                    <div class="form-group">\n' +
            '                      <label class="edit-label col-sm-4">Tenants</label>\n' +
            '                      <div class="col-sm-7">\n' +
            '                        <table id="recipient-tenant">\n' +
            '                        </table>\n' +
            '                      </div>\n' +
            '                    </div>\n' +
            '                  </div>\n'
        );
        var tenants = data.tenants;
        for (var y in tenants) {
            //if the tenant is already assigned with this request, then check the box
            if (tenants[y].assigned === 1) {
                var checked = 'checked';
            } else {
                var checked = '';
            }
            $('#recipient-tenant').append(
                '<tr><td><label class="checkbox"><input class="edit-input" type="checkbox" name="tenant" value="' +
                tenants[y].tenant_id + '" ' + checked + '>' + tenants[y].full_name + '</label></td></tr>')
        }
    }

    //if closed request, disable operations
    if (data.open_or_close == 'closed') {
        console.log("closed");
        $('#saveRecipients').hide();
        $('#cancelRecipients').hide();
    } else {
        console.log("open");
        $('#saveRecipients').show();
        $('#cancelRecipients').show();
    }
}



//---------------init_request start--------------
//init-request is the entry to initialize a form. All the functions after that is called directly or indirectly
function init_request(formId) {
    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: {
            action: "get_report_ready",
            user_level: user_level,
            user_id: user_id,
            user_unit_id: user_unit_id
        },
        dataType: "json",
        async: false,
        success: function(result) {
            set_report_view(result, formId);
        },
        error: function(result) {
            console.log("error: " + result);
        }
    });

}
//set view for common are or apartment list
function set_report_view(data, formId) {

    //set location section
    $(formId + ' .request-location-div').empty();
    $(formId + ' .request-tenant-wrap').remove();
    $(formId + ' .request-notify-tenants-wrap').remove();
    $(formId + ' .report-location-margin-top').empty();

    if (user_level === 5) {
        $(formId + ' .request-location-div').append(data.unit_number);
        $(formId + ' .request-location-div').append(
            '<input id="preUnit" type="hidden" name="reportApartmentId" value="' + data.apartment_id + '">' +
            '<input id="preBuilding" type="hidden" name="reportBuildingId" value="' + data.building_id + '">' +
            '<input id="preArea" type="hidden" name="reportArea" value="apartment">');
        //if it is a tenant, then no access to choosing status when reporting an issue.
        if (formId === "#reportIssue") {
            $(formId + ' .request-status-wrap').remove();
        }

    } else if (user_level === 1) {
        var content =
            '<select class="edit-input form-control request-area" id="reportArea" name="reportArea" required>' +
            '<option value="">None</option>' +
            '<option value="common area">Common Area</option>' +
            '<option value="apartment">Apartment</option>' +
            '</select>';

        $(formId + ' .request-location-div').append(content);
        $(formId + ' .request-area').on('change', function() {
            var area = $(this).children('option:selected').val();
            set_building_view(data.buildings, area, formId);
        });
    }

    set_request_type_view(data.request_types, formId + ' .request-type');
    set_request_status_view(data.request_status, formId + ' .request-status');

    //for enabling/disabling the visit from to time
    $(formId + ' .request-visit-approved').on('click', function() {
        $(formId + ' .request-visit-time-wraps').show();
    });
    $(formId + ' .request-visit-not-approved').on('click', function() {
        $(formId + ' .request-visit-time-wraps').hide();
    });

    //uploading pictures ---- preview the picture
    $(formId + ' .request-pic-upload').on('change', function() {
        $(formId + ' .report-location-margin-top').empty();

        //var fileReader = new FileReader();
        //$()[0]returns the DOM
        //var file = $(this)[0].files[0];
        // fileReader.readAsDataURL(file);
        // fileReader.onloadend = function (oFRevent){
        //     var src = oFRevent.target.result;
        //     $(formId+' .report-location-margin-top').append('<img id="reportPic" class="request-pic-preview" style="max-width: 125px;max-height: 125px;" src="' + src + '">');
        // };

        var files = $(this)[0].files;

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var fileReader = new FileReader();
            fileReader.readAsDataURL(file);
            fileReader.onloadend = function(oFRevent) {
                var src = oFRevent.target.result;
                $(formId + ' .report-location-margin-top').append(
                    '<img id="reportPic" class="request-pic-preview" style="max-width: 125px;max-height: 125px;" src="' +
                    src + '">');
            };
        }
    });
}

//set the list of request types
function set_request_type_view(data, element) {
    $(element).empty();
    var content = '';
    for (var i in data) {
        content += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
    }
    $(element).append(content);
}
//set the list of request status
function set_request_status_view(data, element) {
    $(element).empty();
    var content = '';
    for (var i in data) {
        content += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
    }
    $(element).append(content);
}
//set view for building list
function set_building_view(data, area, formId) {
    $(formId + ' .request-area').nextAll().remove();
    $(formId + ' .request-tenant-wrap').remove();
    $(formId + ' .request-notify-wrap').remove();
    if (area == 'common area') {
        var content =
            '<select class="edit-input form-control request-location-building report-location-margin-top" id="reportBuilding" name="reportBuildingId" required><option value="">Select Building ...</option>';
        for (var i in data) {
            content += '<option value="' + data[i].building_id + '">' + data[i].building_name + '</option>'
        }
        content += '</select>';
        $(formId + ' .request-location-div').append(content);
        $(formId + ' .request-location-building').on('change', function() {
            $(this).nextAll().remove();
            $(formId + ' .request-location-div').append(
                '<textarea class="form-control report-location-margin-top" id="reportAreaDetails" name="reportLocationDetails" rows="5" placeholder="Specific Common Area (e.g. Elevator No.2)" ></textarea>'
                );
        });
    } else if (area == 'apartment') {
        var content =
            '<select class="edit-input form-control request-location-building report-location-margin-top" id="reportBuilding" name="reportBuildingId" required><option value="">Select Building ...</option>';
        for (var i in data) {
            content += '<option value="' + data[i].building_id + '">' + data[i].building_name + '</option>'
        }
        content += '</select>';
        $(formId + ' .request-location-div').append(content);
        $(formId + ' .request-location-building').on('change', function() {
            var building_id = $(this).children('option:selected').val();
            init_floor(building_id, formId);
        });

    }
}
//set view for floors list
function init_floor(building_id, formId) {
    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: {
            action: "get_floors",
            building_id: building_id
        },
        dataType: "json",
        async: false,
        success: function(result) {
            set_report_floor_view(result, formId);
        },
        error: function(result) {
            console.log("error: " + result);
        }
    });
}

function set_report_floor_view(data, formId) {
    $(formId + ' .request-location-building ').nextAll().remove();
    $(formId + ' .request-tenant-wrap').remove();
    $(formId + ' .request-notify-wrap').remove();
    var content =
        '<select class="edit-input form-control request-location-floor report-location-margin-top" id="reportFloorEdit" name="reportFloorId" required><option value="">Select Floor ...</option>';
    for (var i in data) {
        content += '<option value="' + data[i].floor_id + '">' + data[i].floor_name + '</option>'
    }
    content += '</select>';
    $(formId + ' .request-location-div').append(content);
    $(formId + ' .request-location-floor').on('change', function() {
        var floor_id = $(this).children('option:selected').val();
        init_apartment(floor_id, formId);
    });
}
//set view for apartments list
function init_apartment(floor_id, formId) {
    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: {
            action: "get_apartments",
            floor_id: floor_id
        },
        dataType: "json",
        async: false,
        success: function(result) {
            set_report_apartment_view(result, formId);
        },
        error: function(result) {
            console.log("error: " + result);
        }
    });
}

function set_report_apartment_view(data, formId) {
    $(formId + ' .request-location-floor').nextAll().remove();
    $(formId + ' .request-tenant-wrap').remove();
    $(formId + ' .request-notify-wrap').remove();
    var content =
        '<select class="edit-input form-control request-location-apartment report-location-margin-top" id="reportApartment" name="reportApartmentId" required><option value="">Select Unit Number ...</option>';
    for (var i in data) {
        content += '<option value="' + data[i].apartment_id + '">' + data[i].unit_number + '</option>'
    }
    content += '</select>';
    $(formId + ' .request-location-div').append(content);
    $(formId + ' .request-location-apartment').on('change', function() {
        var apartment_id = $(this).children('option:selected').val();
        init_tenant(apartment_id, formId);
    });
}

function init_tenant(apartment_id, formId) {
    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: {
            action: "get_tenants",
            apartment_id: apartment_id
        },
        dataType: "json",
        async: false,
        success: function(result) {
            set_report_tenant_view(result, formId);
        },
        error: function(result) {
            console.log("error: " + result);
        }
    });
}

function set_report_tenant_view(data, formId) {
    $(formId + ' .request-location-apartment').nextAll().remove();
    var content =
        '<div class="col-sm-12 request-tenant-wrap" id="reportTenantsWrap"><div class="form-group"><label class="edit-label col-sm-4" for="reportTenants">Include Tenants as Recipients</label><div id="reportTenants" class="col-sm-8"><table>';
    for (var i in data) {
        content +=
            '<tr><td><label class="checkbox-inline"><input class="edit-input" type="checkbox" name="reportTenantIds[]" value="' +
            data[i].tenant_id + '" checked>' + data[i].tenant_name + '</label></td></tr>';
    }
    content += '</table></div></div></div>';
    content += '<div class="col-sm-12 request-notify-tenants-wrap">\n' +
        '              <div class="form-group">\n' +
        '                <label class="edit-label col-sm-4 request-notify-label" id="notifyLabel">Notify the Tenants by</label>\n' +
        '                <div class="col-sm-8">\n' +
        '                  <label class="checkbox-inline"><input type="hidden" name="notifyTenantsByEmail" value="0"><input class="edit-input notify-by-email" type="checkbox" name="notifyTenantsByEmail" id="reportNotifyTenantsEmail" value="1">Email</label>\n' +
        '                  <label class="checkbox-inline"><input type="hidden" name="notifyTenantsBySms" value="0"><input class="edit-input notify-by-sms" type="checkbox" name="notifyTenantsBySms" id="reportNotifyTenantsSms" value="1">SMS</label>\n' +
        '                  <label class="checkbox-inline"><input type="hidden" name="notifyTenantsByVoice" value="0"><input class="edit-input notify-by-voice" type="checkbox" name="notifyTenantsByVoice" id="reportNotifyTenantsVoice" value="1">Voice</label>\n' +
        '                </div>\n' +
        '              </div>\n' +
        '            </div>'
    $(formId + ' .request-tenant-wrap').remove();
    $(formId + ' .request-notify-tenants-wrap').remove();
    $(formId + ' .request-location-wrap').after(content);
}
//--------------init_request functions end----------------


//---------------init_editing function start----------------

function init_editing(formId, request_id) {
    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: {
            action: "get_editing",
            request_id: request_id,
            user_id: user_id,
            user_level: user_level
        },
        dataType: "json",
        async: false,
        success: function(result) {
            set_editing_view(formId, result);

        },
        error: function(result) {
            console.log("error: " + result);
        }
    });
}
//to pre set the info of the request to be edited.
function set_editing_view(formId, data) {
    if (user_level == 5) {
        $(formId + ' .request-type-div').empty();
        $(formId + ' .request-type-div').append(data.request_type_name);
        if (data.internal_status === 0) {
            $(formId + ' .request-status').val(data.status_id);
        } else {
            $(formId + ' .request-status-div').empty();
            $(formId + ' .request-status-div').append(data.status_name);
        }
        if (data.approveVisit === 0) {
            $(formId + ' .request-approve-div').empty();
            $(formId + ' .request-approve-div').append('No');
            $(formId + ' .request-visit-time-wraps').hide();
        } else {
            $(formId + ' .request-approve-div').empty();
            $(formId + ' .request-approve-div').append('Yes');
            $(formId + ' .request-visit-time-wraps').show();
            $(formId + ' .request-time-div').empty();
            $(formId + ' .request-time-div').append(data.timeFromVisit);
        }
        $(formId + ' .request-message-div').empty();
        $(formId + ' .request-message-div').append(data.message);
    } else if (user_level == 1) {
        if (data.location === 'apartment') {
            $(formId + ' .request-location-div').empty();
            $(formId + ' .request-location-div').append('The unit of ' + data.unit_number);
        } else if (data.location === 'common area') {
            $(formId + ' .request-location-div').empty();
            $(formId + ' .request-location-div').append('The common area(' + data.common_area_detail + ') in ' + data
                .building_name);

        }
        $(formId + ' .request-type').val(data.request_type_id);
        $(formId + ' .request-status').val(data.status_id);
        if (Number(data.approveVisit) === 0) {
            $(formId + ' .request-visit-not-approved').click();
        } else {
            $(formId + ' .request-visit-approved').click();
            $(formId + ' .request-visit-from').val(data.timeFromVisit);
        }
        $(formId + ' .request-message').val(data.message);
    }

    //set file
    $(formId + ' .request-file-wrap').remove();
    //      if (data.file!== null) {
    //          $(formId + ' .request-pic-preview').attr('src', '../../files/' + data.file);
    //      }

    //set the notify me methods
    if (Number(data.notify_by_email) === 1) {
        $(formId + ' .notify-me-by-email').prop('checked', true);
    }
    if (Number(data.notify_by_sms) === 1) {
        $(formId + ' .notify-me-by-sms').prop('checked', true);
    }
    if (Number(data.notify_by_voice) === 1) {
        $(formId + ' .notify-me-by-voice').prop('checked', true);
    }


    //disable the operations for past requests
    if (data.forbid_editing == 1) {
        $('#saveEdit').hide();
        $('#cancelEdit').hide();
        console.log('closed');
    } else {
        $('#saveEdit').show();
        $('#cancelEdit').show();
        console.log('open');
    }

}
//---------------init_editing function end----------------


//submitting the report with FormData.
function submit_report() {
    var reportFormData = new FormData(document.getElementById("reportIssue"));
    reportFormData.append('reportUserId', user_id);
    reportFormData.append('action', 'add_request');
    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: reportFormData,
        processData: false,
        contentType: false,
        async: false,
        success: function() {
            window.location.replace('requests?unit_id=' + user_unit_id);
        },
        error: function() {
            console.log("submit_report() error");
        }
    });

}

//submitting the edit with FormData
function submit_edit(request_id) {
    var editFormData = new FormData(document.getElementById("edit-request"));
    editFormData.append('action', 'edit_request');
    editFormData.append('request_id', request_id);
    editFormData.append('user_id', user_id);
    editFormData.append('user_level', user_level);
    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: editFormData,
        processData: false,
        contentType: false,
        async: false,
        success: function() {
            window.location.replace('requests?unit_id=' + user_unit_id);
        },
        error: function(result) {
            console.log("error in submitting the edit" + result);
        }
    });
};



//--------------------  paging  -----------------

//previous/next events for current isuses
$('#current_issues_previous_page a').click(function() {
    var current_page_id = $('#current_issues_paging .active').children().first().attr('id');
    var current_page_number = parseInt(current_page_id.substr(5));
    if (current_page_number > 1) {
        get_current_issue_list(current_page_number - 1);
    }
});

$('#current_issues_next_page a').click(function() {
    var current_page_id = $('#current_issues_paging .active').children().first().attr('id');
    var current_page_number = parseInt(current_page_id.substr(5));
    var total_page_number = $('#current_page_number').val();
    if (current_page_number < total_page_number) {
        get_current_issue_list(current_page_number + 1);
    }
});

//previous/next events for past isuses
$('#past_issues_previous_page a').click(function() {
    var current_page_id = $('#past_issues_paing .active').children().first().attr('id');
    var current_page_number = parseInt(current_page_id.substr(5));
    if (current_page_number > 1) {
        get_past_issue_page(current_page_number - 1);
    }
});

$('#past_issues_next_page a').click(function() {
    var current_page_id = $('#past_issues_paing .active').children().first().attr('id');
    var current_page_number = parseInt(current_page_id.substr(5));
    var total_page_number = $('#past_page_number').val();
    if (current_page_number < total_page_number) {
        get_past_issue_page(current_page_number + 1);
    }
});


function get_current_issue_list(page_number) {

    var filter_building_id = $('#filter_building_current').val();
    var filter_category = $('#filter_category_current').val();
    var filter_status = $('#filter_status_current').val();
    var filter_unit = $('#filter_units_current').val();
    var filter_from = $('#filter_created_from_current').val();
    var filter_to = $('#filter_created_to_current').val();
    var filter_employee_id = $('#filter_employee_current').val();
    var filter_order = $('#order_by_current').val();

    $.ajax({
        type: "post",
        url: relative_path + "request_info_paging_controller.php",
        data: {
            action: "get_current_issue_page",
            user_id: user_id,
            page_number: page_number,
            user_unit_id: user_unit_id,
            filter_building_id: filter_building_id,
            filter_category: filter_category,
            filter_status: filter_status,
            filter_unit: filter_unit,
            filter_from: filter_from,
            filter_to: filter_to,
            filter_employee_id: filter_employee_id,
            filter_order: filter_order
        },
        dataType: "json",
        async: false,
        success: function(result) {
            update_current_issue_page(result, page_number);
        },
        error: function(result) {
            console.log("Error:" + result);
        }
    });
}


function update_current_issue_page(data, page_number) {

    var data_arr = data.data_content

    $('#current_issue_tbody').empty();

    for (i in data_arr) {
        var id = 'issue_row_' + data_arr[i].request_id;
        var persson_info = 'Telephone : ' + data_arr[i].creator_mobile + ' Email :' + data_arr[i].creator_email;

        //request level highlight
        var level_label_class = '';
        if (data_arr[i].request_level == 'SERIOUS') {
            level_label_class = 'level-label-serious';
        } else if (data_arr[i].request_level == 'URGENT') {
            level_label_class = 'level-label-urgent';
        }

        $('#current_issue_tbody').append('<tr class="' + data_arr[i].style_class + ' issue-line" id="' + id +
            '" data-toggle="modal" data-target="#iModal" data-request="' + data_arr[i].request_id + '">\n' +
            '              <td class="col-md-1 text-center">' + data_arr[i].issue_status + '</td>\n' +
            '              <td class="col-md-2 text-center">' + data_arr[i].request_type + '</td>\n' +
            '              <td class="col-md-1 text-center ' + level_label_class + '">' + data_arr[i]
            .request_level + '</td>\n' +
            '              <td class="col-md-2 text-center">' + data_arr[i].created_time + '</td>\n' +
            '              <td class="col-md-1 text-center">' + data_arr[i].interval + '</td>\n' +
            '              <td class="col-md-1 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
            persson_info + '">' + data_arr[i].creator_full_name + '</td>\n' +
            '              <td class="col-md-2 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
            data_arr[i].address + '">' + data_arr[i].address + '</td>\n' +
            '              <td class="col-md-2 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
            data_arr[i].message + '">' + data_arr[i].message + '</td>\n' +
            '            </tr>');
    }

    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    // change active class for a tags
    $('#current_issues_paging li').removeClass('active');
    var page_id_seletor = '#page_' + page_number;
    $(page_id_seletor).parent().addClass('active');

    //change the previous/next buttons status
    var total_page_number = $('#current_page_number').val();
    $('#current_issues_previous_page').removeClass('disabled');
    $('#current_issues_next_page').removeClass('disabled');

    if (page_number == 1) {
        $('#current_issues_previous_page').addClass('disabled');
    }

    if (page_number == total_page_number) {
        $('#current_issues_next_page').addClass('disabled');
    }

};


function get_past_issue_page(page_number) {
    var filter_building_id = $('#filter_building_past').val();
    var filter_category = $('#filter_category_past').val();
    var filter_status = $('#filter_status_past').val();
    var filter_unit = $('#filter_units_past').val();
    var filter_from = $('#filter_created_from_past').val();
    var filter_to = $('#filter_created_to_past').val();
    var filter_employee_id = $('#filter_employee_past').val();
    var filter_order = $('#order_by_past').val();

    $.ajax({
        type: "post",
        url: relative_path + "request_info_paging_controller.php",
        data: {
            action: "get_past_issue_page",
            user_id: user_id,
            page_number: page_number,
            user_unit_id: user_unit_id,
            filter_building_id: filter_building_id,
            filter_category: filter_category,
            filter_status: filter_status,
            filter_unit: filter_unit,
            filter_from: filter_from,
            filter_to: filter_to,
            filter_employee_id: filter_employee_id,
            filter_order: filter_order
        },
        dataType: "json",
        success: function(result) {
            update_past_issue_page(result, page_number);
        },
        error: function(result) {
            console.log("Error:" + result);
        }
    });
}

function update_past_issue_page(data, page_number) {

    var data_arr = data.data_content;

    $('#past_issues_tbody').empty();

    for (i in data_arr) {
        var id = 'issue_row_' + data_arr[i].request_id;
        var persson_info = 'Telephone : ' + data_arr[i].creator_mobile + ' Email :' + data_arr[i].creator_email;

        //request level highlight
        var level_label_class = '';
        if (data_arr[i].request_level == 'SERIOUS') {
            level_label_class = 'level-label-serious';
        } else if (data_arr[i].request_level == 'URGENT') {
            level_label_class = 'level-label-urgent';
        }

        $('#past_issues_tbody').append('<tr class="' + data_arr[i].style_class + ' issue-line" id="' + id +
            '" data-toggle="modal" data-target="#iModal" data-request="' + data_arr[i].request_id + '">\n' +
            '              <td class="col-md-1 text-center">' + data_arr[i].last_update_time + '</td>\n' +
            '              <td class="col-md-2 text-center">' + data_arr[i].request_type + '</td>\n' +
            '              <td class="col-md-1 text-center ' + level_label_class + '">' + data_arr[i]
            .request_level + '</td>\n' +
            '              <td class="col-md-1 text-center">' + data_arr[i].created_time + '</td>\n' +
            '              <td class="col-md-1 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
            persson_info + '">' + data_arr[i].creator_full_name + '</td>\n' +
            '              <td class="col-md-2 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
            data_arr[i].address + '">' + data_arr[i].address + '</td>\n' +
            '              <td class="col-md-3 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
            data_arr[i].message + '">' + data_arr[i].message + '</td>\n' +
            '            </tr>');
    }

    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    // change active class for a tags
    $('#past_issues_paing li').removeClass('active');
    var page_id_seletor = '#page_' + page_number;
    $(page_id_seletor).parent().addClass('active');

    //change the previous/next buttons status
    var total_page_number = $('#past_page_number').val();
    $('#past_issues_previous_page').removeClass('disabled');
    $('#past_issues_next_page').removeClass('disabled');

    if (page_number == 1) {
        $('#past_issues_previous_page').addClass('disabled');
    }

    if (page_number == total_page_number) {
        $('#past_issues_next_page').addClass('disabled');
    }
};


//------------------------ filter (current issues)-----------------

$('#search_current').click(function() {
    current_issue_filtered();
});

$('#default_current').click(function() {
    $('#filter_building_current option').first().attr('selected', 'selected');
    $('#filter_category_current option').first().attr('selected', 'selected');
    $('#filter_status_current option').first().attr('selected', 'selected');
    $('#filter_units_current option').first().attr('selected', 'selected');
    $('#filter_units_current').attr('disabled', true);
    $('#filter_created_from_current').val('');
    $('#filter_created_to_current').val('');
    $('#filter_employee_current option').first().attr('selected', 'selected');
    $('#order_by_current option').first().attr('selected', 'selected');
    $('#filter_tenant_current').val('');
    current_issue_filtered();
});


$('#filter_building_current').change(function() {
    var selected_building_id = $('#filter_building_current').val();
    if (selected_building_id != 'all') {
        $('#filter_units_current').attr('disabled', false);
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_units",
                building_id: selected_building_id
            },
            dataType: "json",
            success: function(result) {
                set_filter_units_current(result);
            },
            error: function(result) {
                console.log("Error:" + result);
            }
        });
    } else {
        $('#filter_units_current').attr('disabled', true);
        $('#filter_units_current').empty();
        $('#filter_units_current').append('<option value="all" selected>All Units</option>');
    }

});

function set_filter_units_current(data) {
    $('#filter_units_current').empty();
    $('#filter_units_current').append('<option value="all" selected>All Units</option>');
    var data_conent = data.data_content;
    for (i in data_conent) {
        $('#filter_units_current').append('<option id="' + data_conent[i].apartment_id + '">' + data_conent[i]
            .unit_number + '</option>');
    }
}

function current_issue_filtered() {
    var filter_building_id = $('#filter_building_current').val();
    var filter_category = $('#filter_category_current').val();
    var filter_status = $('#filter_status_current').val();
    var filter_unit = $('#filter_units_current').val();
    var filter_from = $('#filter_created_from_current').val();
    var filter_to = $('#filter_created_to_current').val();
    var filter_employee_id = $('#filter_employee_current').val();
    var filter_order = $('#order_by_current').val();
    var filter_tenant = $('#filter_tenant_current').val();

    $.ajax({
        type: "post",
        url: relative_path + "request_info_paging_controller.php",
        data: {
            action: "get_current_issue_page",
            user_id: user_id,
            page_number: 1,
            user_unit_id: user_unit_id,
            filter_building_id: filter_building_id,
            filter_category: filter_category,
            filter_status: filter_status,
            filter_unit: filter_unit,
            filter_from: filter_from,
            filter_to: filter_to,
            filter_employee_id: filter_employee_id,
            filter_order: filter_order,
            filter_tenant: filter_tenant
        },
        dataType: "json",
        async: false,
        success: function(result) {
            update_current_issue_page(result, 1);
        },
        error: function(result) {
            console.log("Error:" + result);
        }
    });

}


//------------------------ filter (past issues)-----------------
$('#search_past').click(function() {
    filtered_past();
});

$('#default_past').click(function() {
    $('#filter_building_past option').first().attr('selected', 'selected');
    $('#filter_category_past option').first().attr('selected', 'selected');
    $('#filter_status_past option').first().attr('selected', 'selected');
    $('#filter_units_past option').first().attr('selected', 'selected');
    $('#filter_units_past').attr('disabled', true);
    $('#filter_created_from_past').val('');
    $('#filter_created_to_past').val('');
    $('#filter_employee_past option').first().attr('selected', 'selected');
    $('#order_by_past option').first().attr('selected', 'selected');
    $('#filter_tenant_past').val('');

});


$('#filter_building_past').change(function() {
    var selected_building_id = $('#filter_building_past').val();
    if (selected_building_id != 'all') {
        $('#filter_units_past').attr('disabled', false);
        $.ajax({
            type: "post",
            url: relative_path + "request_info_controller.php",
            data: {
                action: "get_units",
                building_id: selected_building_id
            },
            dataType: "json",
            success: function(result) {
                set_filter_units_past(result);
            },
            error: function(result) {
                console.log("Error:" + result);
            }
        });
    } else {
        $('#filter_units_past').attr('disabled', true);
        $('#filter_units_past').empty();
        $('#filter_units_past').append('<option value="all" selected>All Units</option>');
    }

});


function set_filter_units_past(data) {
    $('#filter_units_past').empty();
    $('#filter_units_past').append('<option value="all" selected>All Units</option>');
    var data_conent = data.data_content;

    for (i in data_conent) {
        $('#filter_units_past').append('<option id="' + data_conent[i].apartment_id + '">' + data_conent[i]
            .unit_number + '</option>');
    }
}


function filtered_past() {
    var filter_building_id = $('#filter_building_past').val();
    var filter_category = $('#filter_category_past').val();
    var filter_status = $('#filter_status_past').val();
    var filter_unit = $('#filter_units_past').val();
    var filter_from = $('#filter_created_from_past').val();
    var filter_to = $('#filter_created_to_past').val();
    var filter_employee_id = $('#filter_employee_past').val();
    var filter_order = $('#order_by_past').val();
    var filter_tenant = $('#filter_tenant_past').val();

    $.ajax({
        type: "post",
        url: relative_path + "request_info_paging_controller.php",
        data: {
            action: "get_past_issue_page",
            user_id: user_id,
            page_number: 1,
            user_unit_id: user_unit_id,
            filter_building_id: filter_building_id,
            filter_category: filter_category,
            filter_status: filter_status,
            filter_unit: filter_unit,
            filter_from: filter_from,
            filter_to: filter_to,
            filter_employee_id: filter_employee_id,
            filter_order: filter_order,
            filter_tenant: filter_tenant
        },
        dataType: "json",
        async: false,
        success: function(result) {
            update_past_issue_page(result, 1);
        },
        error: function(result) {
            console.log("Error:" + result);
        }
    });

}



//------------------------------ bulletins --------------------------

function init_new_bulletin() {
    $.ajax({
        type: "post",
        url: relative_path + "request_bulletins_controller.php",
        data: {
            action: "get_new_bulletin",
            user_id: user_id
        },
        dataType: "json",
        async: false,
        success: function(result) {
            set_new_bulletin(result);
            //alert(result);
        },
        error: function(result) {
            console.log("error in inti_new_bulletin()" + result);
        }
    });
}

function set_new_bulletin(data) {
    $('#bulletinBuilding').empty();
    var building_arr = data;
    var content = '';
    for (var i in building_arr) {
        content += '<option value="' + building_arr[i].building_id + '">' + building_arr[i].building_name + '</option>';
    }
    $('#bulletinBuilding').append(content);
}


function init_bulletin_modal(bulletin_id) {
    $.ajax({
        type: "post",
        url: relative_path + "request_bulletins_controller.php",
        data: {
            action: "get_bulletin_info",
            bulletin_id: bulletin_id,
        },
        dataType: "json",
        async: false,
        success: function(result) {
            set_bulletin_modal_view(result);
        },
        error: function(result) {
            console.log("Error:" + result);
        }
    });
}


function submit_new_bulletin() {
    var bulletinFormData = new FormData(document.getElementById("bulletinForm"));
    bulletinFormData.append('reportUserId', user_id);
    bulletinFormData.append('action', 'add_bulletin');
    $.ajax({
        type: "post",
        url: relative_path + "request_bulletins_controller.php",
        data: bulletinFormData,
        processData: false,
        contentType: false,
        async: false,
        success: function() {
            window.location.reload(true);
        },
        error: function() {
            console.log("submit_new_bulletin() error");
        }
    });
}

function set_bulletin_modal_view(data) {
    $('#bulletin_shown_id').text(data.bulletin_id);
    $('#bulletin_creator_name').text(data.issuer_name);
    $('#bulletin_creator_telephone').text(data.issuer_telephone);
    $('#issued_date').text(data.issue_date);
    $('#issued_time').text(data.issue_time);
    $('#bullent_modal_title').text(data.title);
    $('#bullent_modal_content').text(data.content);
}

function set_bulletin_reading_status(bulletin_id) {
    $.ajax({
        type: "post",
        url: relative_path + "request_bulletins_controller.php",
        data: {
            action: "get_bulletin_reading_status",
            bulletin_id: bulletin_id,
        },
        dataType: "json",
        async: false,
        success: function(result) {
            set_bulletin_read_status_view(result)
        },
        error: function(result) {
            console.log("Error:" + result);
        }
    });
}


function set_bulletin_read_status_view(data) {
    $('#bulletin_read_status_tbody').empty();
    var readed_tenant_lst = data.data_content;

    for (var i in readed_tenant_lst) {
        $('#bulletin_reading_status').append('<tr>\n' +
            '<td class="col-md-3 text-center">' + readed_tenant_lst[i].full_name + '</td>\n' +
            '<td class="col-md-3 text-center">' + readed_tenant_lst[i].username + '</td>\n' +
            '<td class="col-md-3 text-center">' + readed_tenant_lst[i].last_login_time + '</td>\n' +
            '</tr>');
    }
}

//================== document ready ====================
// to avoid open model during loading page
$(document).ready(function() {
    $('.issue-line').attr('data-toggle', 'modal');
});
</script>

<?php
if ($user_id > 100000 && $user_id < 200000) {
?>
<script>
$(document).ready(function() {
    $('.remove-for-tenant').remove();
    $('#unread_request_mark').addClass('col-md-offset-8');
    $('#filter-part-current').hide();
});
</script>

<?php
}

if (isset($_GET['direct']) && $_GET['direct'] == 'report') {
?>
<script>
$(document).ready(function() {
    $('#startReport').trigger('click');
});
</script>
<?php
}
?>