<?php
$user_id = $_SESSION['UserID'];
$user_level = $_SESSION['UserLevel'];
$employee_id = $_SESSION['employee_id'];
include("../pdo/dbconfig.php");

$vendorId = null;
if (isset($_GET["vid"])) {
    $vendorId = $_GET["vid"];
}

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

function cast_event_date_value($event_date, $event_frequency_type)
{
    if ($event_frequency_type == "day")
        return "workdays";
    else if ($event_frequency_type == "week") {
        $weekday = date('l', strtotime($event_date));
        return $weekday . 's';
    } else if ($event_frequency_type == "month") {
        $month_day = date('d', strtotime($event_date));
        return $month_day . ' per month';
    } else if ($event_frequency_type == "year") {
        $year_day = date('F,j', strtotime($event_date));
        return $year_day . ' every year';
    }
}

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
<link href="custom/request/css/bootstrap-datepicker3.standalone.css" rel="stylesheet" type="text/css" />
<script src="custom/request/js/bootstrap-datepicker.js"></script>

<div id="container">
    <div class="col-xs-12 col-md-4 report-btn" style="margin-left: 0px;">
        <button id="startReport"
            class="btn btn-primary <?php echo $allow_create_request == 0 ? 'remove-for-tenant' : ''; ?>"
            data-toggle="modal" data-target="#reportModal"> <i class="fas fa-plus"></i>
            <?php echo $DB_snapshot->echot("New Task"); ?></button>
        <button id="startBulletin" class="btn btn-primary remove-for-tenant" data-toggle="modal"
            data-target="#newBulletinModal"><i class="fas fa-plus"></i>
            <?php echo $DB_snapshot->echot("Notification"); ?></button>
        <a href="office-create-event.php?event_category=office"><button id="newAgenda"
                class="btn btn-primary remove-for-tenant"><i class="fas fa-plus"></i>
                <?php echo $DB_snapshot->echot("Fixed Event"); ?></button></a>
    </div>

    <div class="hidden-xs col-md-6" style="margin-top: 10px;">
        <div class="col-md-3 remove-for-tenant" style="cursor: pointer;" id="category-fixed">
            <div class="category-box category-fixed"></div>
            <div class="box-text"><?php echo $DB_snapshot->echot("Fixed Event"); ?></div>
        </div>
        <div class="col-md-3 remove-for-tenant" style="cursor: pointer;" id="category-internal">
            <div class="category-box category-internal"></div>
            <div class="box-text"><?php echo $DB_snapshot->echot("Internal Request"); ?></div>
        </div>
        <div class="col-md-3 remove-for-tenant" style="cursor: pointer;" id="category-tenant">
            <div class="category-box category-tenant"></div>
            <div class="box-text"><?php echo $DB_snapshot->echot("Tenant Request"); ?></div>
        </div>
        <div class="col-md-3" style="cursor: pointer;" id="unread_request_mark">
            <div class="category-box category-unread"></div>
            <div class="box-text"><?php echo $DB_snapshot->echot("Unread Request"); ?></div>
        </div>
    </div>

    <div class="hidden-xs col-md-2">
        <div class="box-text"><span id="open_issue_count"><?php echo $DB_snapshot->echot("OPEN REQUESTS"); ?>:
                <?= $open_issue_counter ?></span><br><?php echo $DB_snapshot->echot("UNREAD"); ?>:<span
                id="unread_issue_count"><?= $unread_issue_count ?></span></div>
    </div>

    <div class="col-md-12" style="padding-top: 20px;">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a id="current_issues_a" href="#current-issues"
                    aria-controls="current-issues" role="tab"
                    data-toggle="tab"><?php echo $DB_snapshot->echot("Current Requests"); ?></a></li>
            <li role="presentation" class=" <?php echo $view_past_request == 0 ? 'remove-for-tenant' : ''; ?> "><a
                    href="#past-issues" aria-controls="past-issues" role="tab"
                    data-toggle="tab"><?php echo $DB_snapshot->echot("Past Requests"); ?></a></li>
            <li role="presentation"><a href="#bulletin" aria-controls="bulletin" role="tab"
                    data-toggle="tab"><?php echo $DB_snapshot->echot("Bulletins"); ?></a></li>
            <li id="fixed_events_tabli" class="hidden" role="presentation"><a href="#fixed_events"
                    aria-controls="fixed_events" role="tab"
                    data-toggle="tab"><?php echo $DB_snapshot->echot("Fixed Events"); ?></a></li>
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
            <div id="filter-part-current" class="col-sm-12 col-md-12 well" style="padding: 15px 0;">
                <div class="col-sm-4 col-md-3 filter-block">
                    <label for="filter_building_current"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Building"); ?>:</label>
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
                        <label for="filter_category_current"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Category"); ?>:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="filter_category_current">
                                <option value="all">All Requests</option>
                                <option value="1">Internal Requests</option>
                                <option value="2">Tenant Requests</option>
                                <option value="0">System Generated</option>
                                <option value="3">Fixed Events</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_status_current"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Status"); ?>:</label>
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
                        <label for="filter_units_current"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Units"); ?>:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="filter_units_current" disabled>
                                <option value="all" selected>All Units</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_created_from_current"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("From"); ?>:</label>
                        <div class="col-sm-8 col-md-9">
                            <input type="text" class="form-control date_input" id="filter_created_from_current"
                                style="min-width: 0;" placeholder="YYYY-MM-DD">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_created_to_current"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("To"); ?>:</label>
                        <div class="col-sm-8 col-md-9">
                            <input type="text" class="form-control date_input" id="filter_created_to_current"
                                style="min-width: 0;" placeholder="YYYY-MM-DD">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_employee_current"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Employee"); ?>:</label>
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
                        <label for="order_by_current"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("OrderBy"); ?>:</label>
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
                        <label for="filter_tenant_current"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Tenant"); ?>:</label>
                        <div class="col-sm-8 col-md-9">
                            <input class="form-control" id="filter_tenant_current" placeholder="Wildcard Search">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_read_category"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Read Category"); ?>:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="filter_read_category">
                                <option value="all">All Request</option>
                                <option value="1">Unread</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="request_type_detail"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Request Type"); ?>:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" name="request_type_detail" id="request_type_detail" required>
                                <option value="all">Select Request Type</option>
                                <?php
                                $request_types = $DB_request->get_request_types_all();
                                foreach ($request_types as $singleRequestType) {
                                    echo "<option value='$singleRequestType[id]'> $singleRequestType[name] </option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="col-sm-4 col-md-3 filter-block " style="padding-top: 5px;">
                    <button class="btn btn-primary col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"
                        id="default_current"><?php echo $DB_snapshot->echot("Clear"); ?></button>
                    <button class="btn btn-primary col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-1"
                        id="search_current"><?php echo $DB_snapshot->echot("Search"); ?></button>
                </div>
            </div>
            <!-- filter end-->

            <!-- issue list -->
            <div id="issue-list" class="col-sm-12 col-md-12">
                <legend><?php echo $DB_snapshot->echot("Current Requests List"); ?></legend>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-fixed" style="background-color: white">
                        <thead>
                            <tr>
                                <td class="col-md-2 text-center"><?php echo $DB_snapshot->echot("Status"); ?></td>
                                <td class="col-md-2 text-center"><?php echo $DB_snapshot->echot("Requests Type"); ?>
                                </td>
                                <td class="col-md-1 text-center"><?php echo $DB_snapshot->echot("Level"); ?></td>
                                <td class="col-md-1 text-center"><?php echo $DB_snapshot->echot("Date Created"); ?></td>
                                <td class="col-md-1 text-center"><?php echo $DB_snapshot->echot("Days Old"); ?></td>
                                <td class="col-md-1 text-center"><?php echo $DB_snapshot->echot("Creator"); ?></td>
                                <td class="col-md-1 text-center"><?php echo $DB_snapshot->echot("Closed By"); ?></td>
                                <td class="col-md-1 text-center"><?php echo $DB_snapshot->echot("Location"); ?></td>
                                <td class="col-md-2 text-center"><?php echo $DB_snapshot->echot("Description"); ?></td>
                            </tr>
                        </thead>
                        <tbody id="current_issue_tbody">
                            <?php
                            // current issue list
                            foreach ($current_issues as $row) {
                                $request_id = $row['id'];

                                if (isset($vendorId)) {
                                    // check if this vendor id is an assignee for this current request
                                    // Yes - show the row
                                    // No - continue;
                                    $assignedUsersIds = $DB_request->get_assigned_recipients_user_id($request_id);
                                    $assignedUsersIdsExplode = explode(",", $assignedUsersIds["user_id"]);
                                    if (!in_array($vendorId, $assignedUsersIdsExplode)) {
                                        continue;
                                    }
                                }

                                $request_type_id = $row['request_type_id'];
                                $building_id = $row['building_id'];
                                $request_level = $DB_request->get_request_level($request_type_id, $building_id);

                                $level_label_class = '';
                                if ($request_level == 'SERIOUS') {
                                    $level_label_class = 'level-label-serious';
                                } elseif ($request_level == 'URGENT') {
                                    $level_label_class = 'level-label-urgent';
                                }

                                //calculate time diff
                                $created_time = strtotime($row['created_time']);
                                $now = strtotime(date('Y-m-d H:i:s'));
                                $timediff = timediff($created_time, $now);    // array
                                $diff_day = $timediff['day'];
                                $diff_hour = $timediff['hour'];
                                $diff_min = $timediff['min'];
                                $diff_sec = $timediff['sec'];

                                $time_interval = '- ';
                                if ($diff_day == 1) {
                                    $time_interval .= $diff_day . ' day';
                                } elseif ($diff_day > 1) {
                                    $time_interval .= $diff_day . ' days';
                                } elseif ($diff_hour == 1) {
                                    $time_interval .= $diff_hour . ' hour';
                                } elseif ($diff_hour > 1) {
                                    $time_interval .= $diff_hour . ' hours';
                                } elseif ($diff_min == 1) {
                                    $time_interval .= $diff_min . ' min';
                                } elseif ($diff_min > 1) {
                                    $time_interval .= $diff_min . ' mins';
                                } elseif ($diff_sec == 1) {
                                    $time_interval .= $diff_sec . ' sec';
                                } elseif ($diff_sec > 1) {
                                    $time_interval .= $diff_sec . ' secs';
                                }

                                if (is_null($row['created_user_id'])) {
                                    $created_user_info['full_name'] = "SYSTEM";
                                } else {
                                    $created_user_info = $DB_request->get_user_info($row['created_user_id']);
                                }

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

                                $closed_by_user_name = " - ";
                                if ($row['closed_by'] != NULL) {
                                    $closed_by_user_name = $row['closed_by'];
                                }

                                $request_detailed_status_id = $row['status_id'];
                                $request_detailed_name = $DB_request->get_request_status_name($request_detailed_status_id)['name'];

                                if ($request_status == "closed") {
                                    $status_txt_class = "txt-grey";
                                } else {
                                    $status_txt_class = "txt-black";
                                }
                                $created_time = date('Y-m-d', $created_time);
                            ?>
                            <tr class="<?php echo $style_class . ' ' . $status_txt_class; ?> issue-line"
                                id="<?php echo 'issue_row_' . $request_id; ?>" data-toggle="" data-target="#iModal"
                                data-request="<?php echo $request_id; ?>">
                                <td class="col-md-2 text-center"><?php echo strtoupper($request_detailed_name); ?></td>
                                <td class="col-md-2 text-center"><?php echo $request_type ?></td>
                                <td class="col-md-1 text-center <?php echo $level_label_class; ?>">
                                    <?php echo $request_level; ?></td>
                                <td class="col-md-1 text-center"><?php echo $created_time; ?></td>
                                <td class="col-md-1 text-center"><?php echo $time_interval; ?></td>
                                <td class="col-md-1 text-center non-overflow" data-toggle="tooltip"
                                    data-container="body"
                                    <?php if ($created_user_info['full_name'] != "SYSTEM") echo 'title="' . ' Telephone :' . $created_user_info['mobile'] . 'Email :' . $created_user_info['email'] . '"'; ?>>
                                    <?php echo $created_user_info['full_name']; ?></td>
                                <td class="col-md-1 text-center non-overflow" data-toggle="tooltip"
                                    data-container="body" title="<?php echo $closed_by_user_name; ?>">
                                    <?php echo $closed_by_user_name; ?></td>
                                <td class="col-md-1 text-center non-overflow" data-toggle="tooltip"
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
                            echo ('<li><a href="#" id="page_' . $i . '" onclick="get_current_issue_list(' . $i . ')">' . $i . '</a></li>');
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
        <div role="tabpanel" class="tab-pane row <?php echo $view_past_request == 0 ? 'remove-for-tenant' : ''; ?>"
            id="past-issues">
            <div id="filter-part" class="col-sm-12 col-md-12 remove-for-tenant" style="padding: 15px 0;">
                <div class="col-sm-4 col-md-3 filter-block">
                    <label for="filter_building_past"
                        class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Building"); ?>:</label>
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
                        <label for="filter_category_past"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Category"); ?>:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="filter_category_past">
                                <option value="all">All Requests</option>
                                <option value="1">Internal Requests</option>
                                <option value="2">Tenant Requests</option>
                                <option value="0">System Generated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_status_past"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Status"); ?>:</label>
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
                        <label for="filter_units_past"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Units"); ?>:</label>
                        <div class="col-sm-8 col-md-9">
                            <select class="form-control" id="filter_units_past" disabled>
                                <option value="all" selected>All Units</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_created_from_past"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("From"); ?>:</label>
                        <div class="col-sm-8 col-md-9">
                            <input type="text" class="form-control date_input" id="filter_created_from_past"
                                style="min-width: 0;" placeholder="YYYY-MM-DD">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_created_to_past"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("To"); ?>:</label>
                        <div class="col-sm-8 col-md-9">
                            <input type="text" class="form-control date_input" id="filter_created_to_past"
                                style="min-width: 0;" placeholder="YYYY-MM-DD">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block">
                    <div class="form-group">
                        <label for="filter_employee_past"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Employee"); ?>:</label>
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
                        <label for="order_by_past"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("OrderBy"); ?>:</label>
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
                        <label for="filter_tenant_past"
                            class="col-sm-4 col-md-3 filter-text"><?php echo $DB_snapshot->echot("Tenant"); ?>:</label>
                        <div class="col-sm-8 col-md-9">
                            <input class="form-control" id="filter_tenant_past" placeholder="Wildcard Search">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-md-3 filter-block col-sm-off-8 col-md-offset-6" style="padding-top: 5px;">
                    <button class="btn btn-primary col-sm-4 col-md-4 col-sm-offset-2 col-md-offset-2"
                        id="default_past"><?php echo $DB_snapshot->echot("Clear"); ?></button>
                    <button class="btn btn-primary col-sm-4 col-md-4 col-sm-offset-1 col-md-offset-1"
                        id="search_past"><?php echo $DB_snapshot->echot("Search"); ?></button>
                </div>
            </div>


            <!-- past issue list -->
            <div id="issue-list" class="col-sm-12 col-md-12">
                <legend><?php echo $DB_snapshot->echot("Past Requests List"); ?></legend>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-fixed" style="background-color: white">
                        <thead>
                            <tr>
                                <td class="col-md-1 text-center"><?php echo $DB_snapshot->echot("Date Closed"); ?></td>
                                <td class="col-md-2 text-center"><?php echo $DB_snapshot->echot("Requests Type"); ?>
                                </td>
                                <td class="col-md-1 text-center"><?php echo $DB_snapshot->echot("Level"); ?></td>
                                <td class="col-md-1 text-center"><?php echo $DB_snapshot->echot("Date Created"); ?></td>
                                <td class="col-md-1 text-center"><?php echo $DB_snapshot->echot("Creator"); ?></td>
                                <td class="col-md-2 text-center"><?php echo $DB_snapshot->echot("Location"); ?></td>
                                <td class="col-md-3 text-center"><?php echo $DB_snapshot->echot("Description"); ?></td>
                            </tr>
                        </thead>
                        <tbody id="past_issues_tbody">
                            <?php
                            // past issue list
                            foreach ($past_issues as $row) {
                                $request_id = $row['id'];

                                if (isset($vendorId)) {
                                    // check if this vendor id is an assignee for this current request
                                    // Yes - show the row
                                    // No - continue;
                                    $assignedUsersIds = $DB_request->get_assigned_recipients_user_id($request_id);
                                    $assignedUsersIdsExplode = explode(",", $assignedUsersIds["user_id"]);
                                    if (!in_array($vendorId, $assignedUsersIdsExplode)) {
                                        continue;
                                    }
                                }

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

                                if ($request_category == 1 || $request_category == 0) //internal
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
                <legend><?php echo $DB_snapshot->echot("Bulletins"); ?></legend>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered table-fixed"
                        style="background-color: white">
                        <thead>
                            <tr>
                                <td class="col-md-2 text-center"><?php echo $DB_snapshot->echot("Issue Time"); ?></td>
                                <td class="col-md-2 text-center"><?php echo $DB_snapshot->echot("Building"); ?></td>
                                <td class="col-md-2 text-center"><?php echo $DB_snapshot->echot("Issuer"); ?></td>
                                <td class="col-md-2 text-center"><?php echo $DB_snapshot->echot("Active Period"); ?>
                                </td>
                                <td class="col-md-4 text-center"><?php echo $DB_snapshot->echot("Title"); ?></td>
                                <td class="col-md-1 text-center"><?php echo $DB_snapshot->echot("Close"); ?></td>
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
                                $is_active = $row['is_active'];
                                $title = $row['title'];
                                $is_show = true;
                                if ($is_active == 0) {
                                    $is_show = false;
                                }
                                if (strtotime($row['issue_to']) < strtotime(date('Y-m-d h:i:s'))) {
                                    $is_show = false;
                                }
                            ?>
                            <tr class="bulletins-line" data-toggle="modal" data-target="#iModal_bulletin_details"
                                data-request="<?= $bulletin_id ?>">
                                <td class="col-md-2 text-center"><?php echo $issue_time; ?></td>
                                <td class="col-md-2 text-center"><?php echo $building_name; ?></td>
                                <td class="col-md-1 text-center"><?php echo $issuer_name; ?></td>
                                <td class="col-md-2 text-center"><?php echo $issue_from . ' - ' . $issue_to; ?></td>
                                <td class="col-md-4 text-center non-overflow" data-toggle="tooltip"
                                    data-container="body" title="<?php echo $title; ?>"><?php echo $title; ?></td>
                                <td class="col-md-4 text-center">
                                    <?php if ($is_show) { ?>
                                    <button type="button" class="btn bulletin-close table-button"
                                        id="bulletin_close_<?php echo $bulletin_id; ?>">Close</button>
                                    <?php } else {
                                            echo '<span>CLOSED</span>';
                                        } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Fixed Events panel -->
        <div role="tabpanel" class="tab-pane row" id="fixed_events">
            <div class="col-sm-12 col-md-12" style="margin-top: 10px;">
                <legend><?php echo $DB_snapshot->echot("Fixed Events"); ?></legend>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed well">
                        <thead>
                            <tr>
                                <th class="col-xs-2 text-center">Event Name</th>
                                <th class="col-xs-2 text-center">Event Category</th>
                                <th class="col-xs-2 text-center">Event Date</th>
                                <th class="col-xs-2 text-center">Created By</th>
                                <th class="text-center" style="width: 2%">Edit</th>
                                <th class="text-center" style="width: 2%">Del.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $results = $DB_calendar->get_office_maintenance_eventswo_bid($employee_id);
                            foreach ($results as $row) {
                                $event_id = $row['id'];
                                $event_name = $row['event_name'];
                                $event_date = $row['event_date'];
                                $event_type = $row['event_type'];
                                $event_category = $row['event_category'];
                                $event_created_by = $row['event_created_by_user_id'];
                                //get created by persons' name
                                $results_for_name = $DB_calendar->get_employee_info($event_created_by);
                                $event_created_by_name = $results_for_name['full_name'];

                                $event_frequency_type = $row['event_frequency_type'];
                                if ($event_type == "regular") {
                                    $event_date_value = cast_event_date_value($event_date, $event_frequency_type);
                                } else {
                                    continue;
                                }
                            ?>
                            <tr>
                                <form action="custom/calendar_visit/office-controller-event.php" method="post">
                                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
                                    <input type="hidden" name="building_id" value="<?php echo $building_id; ?>" />

                                    <td class="text-center"><?php echo ucfirst($event_name); ?></td>
                                    <td class="text-center"><?php echo ucfirst($event_category); ?></td>
                                    <td class="text-center"><?php echo $event_date_value; ?></td>
                                    <td class="text-center"><?php echo $event_created_by_name; ?></td>

                                    <td class="text-center" style="width: 2%">
                                        <button type="submit" class="table-icon" name="details_event">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </td>

                                    <td class="text-center" style="width: 2%">
                                        <button type="submit" class="table-icon" name="delete_event"
                                            onclick="return confirm('Are you sure to delete this event?')">
                                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                        </button>
                                    </td>
                                </form>
                            </tr>
                            <?php
                            }
                            ?>
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
                    <i class="title"><?php echo $DB_snapshot->echot("Created By"); ?>:</i>
                    <br>
                    <span class="glyphicon glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;<span
                        id="modal_user_name" class="box-text"></span><br>
                    <span class="glyphicon glyphicon glyphicon glyphicon glyphicon-phone"
                        aria-hidden="true"></span>&nbsp;<span id="modal_user_telephone" class="box-text"></span>
                </div>

                <div class="modal-created-daytime">
                    <i class="title"><?php echo $DB_snapshot->echot("Created At"); ?>:</i>
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
                            aria-controls="communication" role="tab"
                            data-toggle="tab"><?php echo $DB_snapshot->echot("Communications"); ?></a></li>
                    <!--          <li role="presentation"><a id="attach_tag" href="#attach" aria-controls="attach" role="tab" data-toggle="tab">--><?php //echo $DB_snapshot->echot("Attachments");
                                                                                                                                                    ?>
                    <!--</a></li>-->
                    <li role="presentation"><a id="recipient_tag" href="#recipient" aria-controls="recipient" role="tab"
                            data-toggle="tab"><?php echo $DB_snapshot->echot("Recipient"); ?></a></li>
                    <li role="presentation"><a id="edit_tag" href="#edit" aria-controls="edit" role="tab"
                            data-toggle="tab"><?php echo $DB_snapshot->echot("Editing"); ?></a></li>
                    <li role="presentation"><a id="payment_tag" href="#payment_tab" aria-controls="payment_tab"
                            role="tab" data-toggle="tab"><?php echo $DB_snapshot->echot("Payment"); ?></a></li>
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
                            <form enctype="multipart/form-data" id="post_communication_form">
                                <div class="row">
                                    <div id="new_message" class="col-sm-12 form-group">
                                        <div id="communiation_input" class="communication-input" contenteditable="true">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 form-group" id="opeartions_new_message">
                                        <button type="button" class="btn btn-primary btn-post" id="post_communication"
                                            onclick="add_communication()"><?php echo $DB_snapshot->echot("Post"); ?></button>
                                        <label class="btn btn-primary btn-post">Add Image<input
                                                class="request-pic-upload" type="file" name="upload_img"
                                                style="display: none" accept="image/*"
                                                onchange="add_communication_image()"></label>
                                        <div class="checkbox remove-for-tenant modal-communication-selection">
                                            <label><input type="checkbox"
                                                    id="hide_for_tenant"><?php echo $DB_snapshot->echot("Hidden for tenants"); ?></label>
                                        </div>
                                        <div class="checkbox remove-for-tenant modal-communication-selection">
                                            <label><input type="checkbox"
                                                    id="force_ntf"><?php echo $DB_snapshot->echot("Notify recipients"); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="recipient">
                        <div class="recipient-div">
                            <div class="form">
                                <form id="editRecipient">
                                    <div class="row">
                                        <div class="col-sm-12" id="recipient-employee-wrap">
                                            <div class="form-group">
                                                <label
                                                    class="edit-label col-sm-4"><?php echo $DB_snapshot->echot("Employee"); ?></label>
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
                                                        class="btn btn-primary"><?php echo $DB_snapshot->echot("Save"); ?></button>
                                                    <button id="cancelRecipients" type="button" class="btn btn-default"
                                                        data-dismiss="modal"><?php echo $DB_snapshot->echot("Cancel"); ?></button>
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
                                                    for="location"><?php echo $DB_snapshot->echot("Location"); ?></label>
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
                                                <label class="edit-label col-sm-4 col-md-3"
                                                    for="request-type"><?php echo $DB_snapshot->echot("Request Type"); ?></label>
                                                <div class="col-sm-8 col-md-8 request-type-div">
                                                    <select class="edit-select form-control request-type"
                                                        id="request-type" name="request_type" title="Type">
                                                        <option value="">null</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--                  <div class="row">-->
                                    <!--                    <div class="col-sm-12">-->
                                    <!--                      <div class="form-group">-->
                                    <!--                        <label class="edit-label col-sm-4 col-md-3">--><?php //echo $DB_snapshot->echot("Reserve repair Time");
                                                                                                                ?>
                                    <!--</label>-->
                                    <!--                        <div class="col-sm-8 col-md-8 request-approve-div">-->
                                    <!--                          <label class="radio-inline">-->
                                    <!--                            <input class="edit-input request-visit-approved" type="radio" name="approved" value="1">Yes-->
                                    <!--                          </label>-->
                                    <!--                          <label class="radio-inline">-->
                                    <!--                            <input class="edit-input request-visit-not-approved" type="radio" name="approved" value="0" checked>No-->
                                    <!--                          </label>-->
                                    <!--                        </div>-->
                                    <!--                      </div>-->
                                    <!--                    </div>-->
                                    <!--                  </div>-->
                                    <!--                  <div class="row">-->
                                    <!--                    <div class="col-sm-12 request-visit-time-wraps">-->
                                    <!--                      <div class="form-group">-->
                                    <!--                        <label class="edit-label col-sm-4 col-md-3" for="visitFrom">--><?php //echo $DB_snapshot->echot("Visit Time");
                                                                                                                                ?>
                                    <!--</label>-->
                                    <!--                        <div class="col-sm-8 col-md-8 request-time-div">-->
                                    <!--                          <input class="edit-input form-control request-visit-from" type="time" name="visitFrom" id="visitFrom" value="09:00:00">-->
                                    <!--                        </div>-->
                                    <!--                      </div>-->
                                    <!--                    </div>-->
                                    <!--                  </div>-->

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label
                                                    class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Message"); ?></label>
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
                                                <label
                                                    class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Status"); ?></label>
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
                                                    id="notifyLabel"><?php echo $DB_snapshot->echot("Notify Me by"); ?></label>
                                                <div class="col-sm-8 col-md-8">
                                                    <label class="checkbox-inline"><input type="hidden"
                                                            name="notifyMeByEmail" value="0"><input
                                                            class="edit-input notify-me-by-email" type="checkbox"
                                                            name="notifyMeByEmail" id="editNotifyMeEmail"
                                                            value="1"><?php echo $DB_snapshot->echot("Email"); ?></label>
                                                    <label class="checkbox-inline"><input type="hidden"
                                                            name="notifyMeBySms" value="0"><input
                                                            class="edit-input notify-me-by-sms" type="checkbox"
                                                            name="notifyMeBySms" id="editNotifyMeSms"
                                                            value="1"><?php echo $DB_snapshot->echot("SMS"); ?></label>
                                                    <label class="checkbox-inline"><input type="hidden"
                                                            name="notifyMeByVoice" value="0"><input
                                                            class="edit-input notify-me-by-voice" type="checkbox"
                                                            name="notifyMeByVoice" id="editNotifyMeVoice"
                                                            value="1"><?php echo $DB_snapshot->echot("Voice"); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-4">
                                                    <button type="button" class="btn btn-primary"
                                                        id="saveEdit"><?php echo $DB_snapshot->echot("Save"); ?></button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal"
                                                        id="cancelEdit"><?php echo $DB_snapshot->echot("Cancel"); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="request_op_success_closed" class="row" style="display: none;">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div
                                                    class="col-sm-8 col-sm-offset-2 alert alert-success alert-dismissible fade in">
                                                    <a href="#" class="close" data-dismiss="alert"
                                                        aria-label="close">&times;</a>
                                                    <span>Request is saved! Please update your Payment details.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="payment_tab">
                        <div class="row form-group">
                            <div class="col-sm-12">
                                <div id="payment_tab_alert"></div>
                            </div>
                        </div>
                        <div id="payment_tab_content">
                            <form id="edit-payment">
                                <legend>Payment Details</legend>
                                <div class="row form-group">
                                    <div class="col-sm-12 request-payperhr-wrap">
                                        <div class="form-group">
                                            <label class="col-sm-4 col-md-3"
                                                for="request_pay_perhr"><?php echo $DB_snapshot->echot("Per Hour Wage"); ?></label>
                                            <div class="col-sm-8 col-md-8 request-paymentperhr-div">
                                                <input name="request_pay_perhr" class="form-control"
                                                    id="request_pay_perhr" name="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-12 request-payinfo-wrap">
                                        <div class="form-group">
                                            <label class="col-sm-4 col-md-3"
                                                for="request_payinfo"><?php echo $DB_snapshot->echot("Job Detail"); ?></label>
                                            <div class="col-sm-8 col-md-8 request-paymentinfo-div">
                                                <input class="form-control" id="request_payinfo" name="request_payinfo">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-12 request-payhours-wrap">
                                        <div class="form-group">
                                            <label class="col-sm-4 col-md-3"
                                                for="request_payhours"><?php echo $DB_snapshot->echot("Job Total Hours"); ?></label>
                                            <div class="col-sm-8 col-md-8 request-paymenthours-div">
                                                <input class="form-control" id="request_payhours"
                                                    name="request_payhours">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-12 request-payexpenses-wrap">
                                        <div class="form-group">
                                            <label class="col-sm-4 col-md-3"
                                                for="request_pay_expenses"><?php echo $DB_snapshot->echot("Other Expenses Amount"); ?></label>
                                            <div class="col-sm-8 col-md-8 request-paymentexpenses-div">
                                                <input class="form-control" id="request_pay_expenses"
                                                    name="request_pay_expenses">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="file-holder-payments"></div>
                            </form>
                            <div class="row form-group">
                                <div class="col-sm-12 request-payinvoice-wrap">
                                    <div class="form-group">
                                        <label class="col-sm-4 col-md-3"
                                            for="request_pay_invoice"><?php echo $DB_snapshot->echot("Attach Invoice"); ?></label>
                                        <div class="col-sm-8 col-md-8 request-paymentinvoice-div">
                                            <form id="paymentinv_form" enctype="multipart/form-data">
                                                <input name="paymentinvoicenum" id="paymentinvoicenum"
                                                    placeholder="Invoice #" type="text" class="form-control" />
                                                <input name="paymentinvoicefile" id="paymentinvoicefile" type="file" />
                                            </form>
                                            <progress style="display: none;" id="paymentinv_form_progress"></progress>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="col-sm-8 col-sm-offset-3">
                                            <button type="button" class="btn btn-primary"
                                                id="savePaymentdetails"><?php echo $DB_snapshot->echot("Save"); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--  Admin Approval -->
                            <form id="payment_approval">
                                <legend>Payment Detail Validation</legend>
                                <h5 id="request_approve_alert"></h5>
                                <div id="request-payapprove-form-div">
                                    <div class="row form-group">
                                        <div class="col-sm-12 request-payapprove-wrap">
                                            <div class="form-group">
                                                <label class="col-sm-4 col-md-3"
                                                    for="request_is_payapprove"><?php echo $DB_snapshot->echot("Approve the Payment"); ?></label>
                                                <div class="col-sm-8 col-md-8 request_is_payapprove-div">
                                                    <input type="checkbox" name="request_is_payapprove"
                                                        id="request_is_payapprove" value="1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-sm-12 request-payfinalamt-wrap">
                                            <div class="form-group">
                                                <label class="col-sm-4 col-md-3"
                                                    for="request_approve_finalamt"><?php echo $DB_snapshot->echot("Final Amount"); ?></label>
                                                <div class="col-sm-8 col-md-8 request_approve_finalamt-div">
                                                    <input type="text" class="form-control"
                                                        name="request_approve_finalamt" id="request_approve_finalamt"
                                                        placeholder="Enter the final amount to Pay">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-sm-12 request-payapprovecomments-wrap">
                                            <div class="form-group">
                                                <label class="col-sm-4 col-md-3"
                                                    for="request_approve_comments"><?php echo $DB_snapshot->echot("Comments"); ?></label>
                                                <div class="col-sm-8 col-md-8 request_approve_finalamt-div">
                                                    <textarea class="form-control" name="request_approve_comments"
                                                        id="request_approve_comments" placeholder=""></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-3">
                                                    <button type="button" class="btn btn-primary"
                                                        id="savePaymentApproval"><?php echo $DB_snapshot->echot("Confirm Approval"); ?></button>
                                                </div>
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


<!-- modal for report issue -->
<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="iModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2><?php echo $DB_snapshot->echot("Add a new Task"); ?></h2>
            </div>
            <div class="modal-body">

                <ul class="nav nav-tabs" role="tablist" style="margin-top: 10px;">
                    <li role="presentation" class="active"><a id="report_details_tag" href="#report_details"
                            aria-controls="report_details" role="tab"
                            data-toggle="tab"><?php echo $DB_snapshot->echot("Details"); ?></a></li>
                    <li role="presentation"><a id="recipient_report_tag" href="#recipient_report"
                            aria-controls="recipient_report" role="tab"
                            data-toggle="tab"><?php echo $DB_snapshot->echot("Recipients"); ?></a></li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="report_details">
                        <div class="reportAnIssue">
                            <div class="form">
                                <form id="reportIssue" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-sm-12 request-location-wrap" id="reportLocationWrap">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4 col-md-3"
                                                    for="location"><?php echo $DB_snapshot->echot("Location"); ?></label>
                                                <div id="reportLocation" class="col-sm-8 col-md-8 request-location-div">
                                                    <select class="edit-input form-control report-location"
                                                        id="reportBuilding" name="building">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4 col-md-3"
                                                    for="request-type"><?php echo $DB_snapshot->echot("Request Type"); ?></label>
                                                <div class="col-sm-8 col-md-8">
                                                    <select class="edit-select form-control request-type"
                                                        id="report-request-type" name="reportRequestType" title="Type">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12" id="requestTypeWrap">
                                            <div class="form-group">
                                                <label
                                                    class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Reserve Handyman"); ?></label>
                                                <div class="col-sm-8 col-md-8">
                                                    <label class="radio-inline">
                                                        <input id="report-visitApproved"
                                                            class="edit-input request-visit-approved" type="radio"
                                                            name="reportApprovedVisit" value="1">Yes
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input id="report-visitNotApproved"
                                                            class="edit-input request-visit-not-approved" type="radio"
                                                            name="reportApprovedVisit" value="0" checked>No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 request-visit-time-wraps">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4 col-md-3"
                                                    for="handyman_avail_date"><?php echo $DB_snapshot->echot("Visit Time"); ?></label>
                                                <div class="col-sm-8 col-md-8">
                                                    <select id="handyman_avail_date" name="reportVisitDate"
                                                        class="form-control"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 request-visit-time-wraps" id="visitFromWrap">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4 col-md-3"
                                                    for="visitFrom"><?php echo $DB_snapshot->echot("Visit Time"); ?></label>
                                                <div class="col-sm-8 col-md-8">
                                                    <input type="hidden" name="reportVisitDuration"
                                                        id="reportVisitDuration" value="0">
                                                    <select class="edit-input form-control" name="reportVisitTime"
                                                        id="report_reserve_time"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label
                                                    class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Pictures"); ?></label>
                                                <div class="col-sm-8 col-md-8" id="reportPicturesWrap">
                                                    <label
                                                        class="btn btn-primary btn-file"><?php echo $DB_snapshot->echot("Select Images"); ?><input
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
                                                <label
                                                    class="edit-label col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Message"); ?></label>
                                                <div class="col-sm-8 col-md-8">
                                                    <textarea id="reportMessage" class="edit-input form-control"
                                                        name="reportMessage" rows="4" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 request-notify-me-wrap">
                                            <div class="form-group">
                                                <label class="edit-label col-sm-4 col-md-3 request-notify-label"
                                                    id="notifyLabel"><?php echo $DB_snapshot->echot("Notify Me by"); ?></label>
                                                <div class="col-sm-8 col-md-8">
                                                    <label class="checkbox-inline"><input type="hidden"
                                                            name="notifyMeByEmail" value="0"><input
                                                            class="edit-input notify-me-by-email" type="checkbox"
                                                            name="notifyMeByEmail" id="reportNotifyMeEmail"
                                                            value="1"><?php echo $DB_snapshot->echot("Email"); ?></label>
                                                    <label class="checkbox-inline"><input type="hidden"
                                                            name="notifyMeBySms" value="0"><input
                                                            class="edit-input notify-me-by-sms" type="checkbox"
                                                            name="notifyMeBySms" id="reportNotifyMeSms"
                                                            value="1"><?php echo $DB_snapshot->echot("SMS"); ?></label>
                                                    <label class="checkbox-inline"><input type="hidden"
                                                            name="notifyMeByVoice" value="0"><input
                                                            class="edit-input notify-me-by-voice" type="checkbox"
                                                            name="notifyMeByVoice" id="reportNotifyMeVoice"
                                                            value="1"><?php echo $DB_snapshot->echot("Voice"); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row form-group" id="new_request_loader" style="display: none;">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-3">
                                                    <span class="alert alert-warning">
                                                        <i class="fa fa-spinner fa-spin" style="font-size:24px"></i>
                                                        Working on creating your request! Please wait.
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-4">
                                                    <button type="button" class="btn btn-primary"
                                                        id="submitReport"><?php echo $DB_snapshot->echot("Report this request"); ?></button>
                                                    <button class="btn btn-default"
                                                        data-dismiss="modal"><?php echo $DB_snapshot->echot("Cancel"); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="recipient_report">
                        <div class="recipientReport-div">
                            <div class="recipientReport-alert"></div>
                            <form id="editRecipientReport">
                                <div class="row">
                                    <div class="col-sm-12" id="recipient-report-vendor-wrap">
                                        <div class="form-group">
                                            <label
                                                class="edit-label col-sm-4"><?php echo $DB_snapshot->echot("Vendors"); ?></label>
                                            <div class="col-sm-7">
                                                <select class="form-control" id="recipient-report-vendor"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
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
                    <div class="modal-h-id"><i>Bulletin #: </i><span id="bulletin_shown_id"></span></div>
                </div>
                <div class="modal-created-by-info">
                    <i class="title"><?php echo $DB_snapshot->echot("Created By"); ?>:</i>
                    <br>
                    <span class="glyphicon glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;<span
                        id="bulletin_creator_name" class="box-text"></span><br>
                    <span class="glyphicon glyphicon glyphicon glyphicon glyphicon-phone"
                        aria-hidden="true"></span>&nbsp;<span id="bulletin_creator_telephone" class="box-text"></span>
                </div>

                <div class="modal-created-daytime">
                    <i class="title"><?php echo $DB_snapshot->echot("Created At"); ?>:</i>
                    <br>
                    <img src="custom/request/img/date-time.svg">&nbsp<span id="issued_date" class="box-text"></span><br>
                    <img src="custom/request/img/time.svg">&nbsp<span id="issued_time" class="box-text"></span>
                </div>

            </div>

            <div class="modal-body">

                <ul class="nav nav-tabs" role="tablist" style="margin-top: 10px;">
                    <li role="presentation" class="active"><a id="bulletin_content_tag" href="#bulletin_content"
                            role="tab" aria-controls="bulletin_content"
                            data-toggle="tab"><?php echo $DB_snapshot->echot("Details"); ?></a></li>
                    <li role="presentation" class="remove-for-tenant"><a id="bullentin_read_status_tag"
                            href="#bulletin_read_status" role="tab"
                            data-toggle="tab"><?php echo $DB_snapshot->echot("Read Tenants"); ?></a></li>
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
                                    <td class="col-md-3 text-center"><?php echo $DB_snapshot->echot("Tenant Name"); ?>
                                    </td>
                                    <td class="col-md-3 text-center"><?php echo $DB_snapshot->echot("Login Account"); ?>
                                    </td>
                                    <td class="col-md-3 text-center">
                                        <?php echo $DB_snapshot->echot("Last Readed Time"); ?></td>
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
                                    <label
                                        class="col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Building"); ?></label>
                                    <div class="col-sm-8 col-md-8">
                                        <select class="form-control" id="bulletinBuilding" name="bulletinBuilding"
                                            required>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("From"); ?></label>
                                    <div class="col-sm-8 col-md-8">
                                        <input class="form-control date_input" id="bulletinFrom" type="text"
                                            name="bulletinFrom" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("To"); ?></label>
                                    <div class="col-sm-8 col-md-8">
                                        <input class="form-control date_input" id="bulletinTo" type="text"
                                            name="bulletinTo" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Title"); ?></label>
                                    <div class="col-sm-8 col-md-8">
                                        <textarea class="form-control" id="bulletinTitle" name="bulletinTitle" rows="1"
                                            required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <label
                                        class="col-sm-4 col-md-3"><?php echo $DB_snapshot->echot("Content"); ?></label>
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
                                            id="submitNewBulletin"><?php echo $DB_snapshot->echot("Submit"); ?></button>
                                        <button class="btn btn-default"
                                            data-dismiss="modal"><?php echo $DB_snapshot->echot("Cancel"); ?></button>
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

$('.date_input').datepicker({
    format: 'y-MM-dd'
});

$(function() {
    $('[data-toggle="tooltip"]').tooltip();

    // Show fixed events tab on Fixed event button click
    $("#category-fixed").on("click", function() {
        $("#fixed_events_tabli > a").toggleClass("hidden").trigger("click");
    });

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
    //$('#attach_images').empty();
});

$('#reportModal').on('hidden.bs.modal', function(e) {
    console.log("close");
    $("#reportIssue .request-pic-upload").unbind("change");
});

$('#communication_tag').on('click', function(e) {
    var request_id = $('#request_id').val();
    init_communications(request_id);
});

$('#recipient_tag').on('click', function(e) {
    var request_id = $('#request_id').val();
    init_recipient(request_id, true);
});

$('#payment_tag').on('click', function(e) {
    var request_id = $('#request_id').val();
    init_payment(request_id);
});

$('#edit_tag').on('click', function(e) {
    init_request('#edit-request');
    var request_id = $('#request_id').val();
    init_editing('#edit-request', request_id);
});

// New request report -recipients tab click
$('#recipient_report_tag').on('click', function(e) {
    let selectedRequestType = $("#report-request-type").val();
    if (selectedRequestType == "0") {
        // Default type - ask the user to select a request type to see the recipients / vendors list
        $('#editRecipientReport').hide();
        $(".recipientReport-alert").html("No vendors found for the selected request type.").show();
    }
});



//-----------save button-----------
$('#saveRecipients').on('click', function(e) {
    e.preventDefault();
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
    let location = $('#reportIssue .request-area').val();
    let requestType = $('#reportIssue .request-type').val();

    // if the values in the form are default - don't submit the form
    if (location == "default" || requestType == "0") {
        return;
    }
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

$('#bulletins_tbody').find('.bulletin-close').on('click', function() {
    var btn_id = $(this).attr('id');
    var bulletin_id = btn_id.substring(15);
    close_bulletin(bulletin_id);
});


// --------------- global params  ---------------

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
    var message = $('#communiation_input').html();
    console.log(message);
    var post_communication_form = new FormData(document.getElementById("post_communication_form"));
    var if_seen_by_tenant = 1;
    var if_notify = false;

    if ($('#hide_for_tenant').is(':checked')) {
        if_seen_by_tenant = 0;
    }
    if ($('#force_ntf').is(':checked')) {
        if_notify = true;
    }
    post_communication_form.append('action', 'add_communication');
    post_communication_form.append('user_id', user_id);
    post_communication_form.append('request_id', request_id);
    post_communication_form.append('message', message);
    post_communication_form.append('if_seen_by_tenant', if_seen_by_tenant);
    post_communication_form.append('if_notify', if_notify);

    if (message.length > 0) {
        message = message.replace(/<(.|\n)*?>/g, '');

        $.ajax({
            type: "post",
            url: relative_path + "request_communication_controller.php",
            data: post_communication_form,
            dataType: "json",
            processData: false,
            contentType: false,
            async: false,
            success: function(result) {
                set_communications_view(result);
            },
            error: function(result) {
                console.log("error:" + result);
            }
        });
    }
    $('#communiation_input').empty();
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
            creator_info = data[i].creator_name + '  [' + data[i].creator_role + ']';
        } else {
            creator_info = data[i].creator_role;
        }

        var string = '<div class="message-container ' + bg_color + '">';

        var communication_id = data[i].communication_id;

        if (data[i].is_image == 1) {
            string += '<div class="message-text"><a class="attach-a" href="' + data[i].remark +
                '" data-lightbox="attach-img" data-title="Picture Title"><img src="' + data[i].remark +
                '" style="max-width: 125px;max-height: 125px;"></a></div>';
        } else if (data[i].is_system_msg == 1 && data[i].system_message_type == 0) {
            string += '<div class="message-text">' + data[i].remark + '</div>';
        } else if (data[i].is_system_msg == 1 && data[i].system_message_type == 1) {
            string +=
                '<div class="message-text"><span>Do you confirm this repair event ? </span><button id="repair_event_force_confirm" type="button" class = "btn btn-danger repair-event-confirm-btn" name="' +
                communication_id +
                '" onclick="change_repair_event_confirm_status.call(this,2)">Force Confirm</button><button id="repair_event_confirm" type="button" class = "btn btn-success repair-event-confirm-btn" name="' +
                communication_id +
                '" onclick="change_repair_event_confirm_status.call(this,1)">Confirm</button><button id="repair_event_cancel" type="button" class = "btn btn-primary repair-event-confirm-btn" name="' +
                communication_id + '" onclick="change_repair_event_confirm_status.call(this,0)">Cancel</button></div>';
        } else if (data[i].is_system_msg == 1 && data[i].system_message_type == 2) {
            string +=
                '<div class="message-text"><span>Do you confirm this repair event ? </span><button id="repair_event_confirm" type="button" class = "btn btn-success repair-event-confirm-btn" name="' +
                communication_id +
                '" onclick="change_repair_event_confirm_status.call(this,1)">Confirm</button><button id="repair_event_cancel" type="button" class = "btn btn-primary repair-event-confirm-btn" name="' +
                communication_id + '" onclick="change_repair_event_confirm_status.call(this,0)">Cancel</button></div>';
        } else {
            string += '<div class="message-text">' + data[i].remark + '</div>';
        }

        string += '<div class="message-info"><span class="message-info-name">' + creator_info + '</span>';
        if (data[i].if_seen_by_tenant == 0) {
            string += '<i class="fa fa-eye-slash hidden-for-tenant-msg"></i>';
        }
        string += '               <a id="read_status_' + i +
            '" class="message-info-read-status" tabindex="0" role="button" data-toggle="popover" data-container="body" data-trigger="focus">Reading status</a>\n' +
            '                    <span class="message-info-date">' + data[i].created_time + '</span>\n' +
            '                  </div>\n' +
            '                </div>';

        $('#communications').append(string);

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
        text = text + '<div><h6> Legend: </h6><button type="button" class = "btn btn-primary">Read</button> ' +
            '<button type="button" class = "btn btn-warning">Unread</button></div>';

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

    //set textare and file input
    $('#post_communication_form')[0].reset();
}


function add_communication_image() {
    var ele_communiaction_form = $('#post_communication_form');
    var img = ele_communiaction_form.find('.request-pic-upload')[0].files[0];
    var communication_input = ele_communiaction_form.find('#communiation_input');
    communication_input.html(communication_input.html().replace(/<(.|\n)*?>/g, ''));
    var fileReader = new FileReader();
    fileReader.readAsDataURL(img);
    fileReader.onloadend = function(oFRevent) {
        var src = oFRevent.target.result;
        communication_input.append('<img id="reportPic_' + img.name +
            '" class="request-pic-preview" style="max-width: 150px;max-height: 150px;" src="' + src + '">');
    };
}

//user_action[0:cancel, 1:confirm, 2:force_confirm]
function change_repair_event_confirm_status(user_action) {
    var request_communication_id = this.name;
    var post_action = null;
    if (user_action === 0) {
        confirm("Are you sure to cancel this repair reservation ?");
        post_action = 'cancel';
    } else if (user_action === 1) {
        confirm("Are you sure to conform this repair reservation ?");
        post_action = 'confirm';
    } else if (user_action === 2) {
        confirm("Are you sure to confirm this repair reservation direacly without the confirmation from handyman ?");
        post_action = 'force_confirm';
    }

    var request_id = $('#request_id').val();
    console.log(request_id);

    $.ajax({
        type: "post",
        url: relative_path + "request_communication_controller.php",
        data: {
            action: 'change_repair_event_status',
            request_communication_id: request_communication_id,
            user_action: post_action,
            user_id: user_id,
            request_id: request_id
        },
        dataType: "json",
        async: false,
        success: function(result) {
            set_communications_view(result);
        },
        error: function(result) {
            console.log("Error:" + result);
        }
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

function init_recipient(request_id, update_recipients) {
    if (!update_recipients) {
        alert("Recipients updated!");
        return;
    }
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

// Check if any vendor is assigned to this request
// if no vendor is assigned - ask the user to assignt a vendor first and then add the payment details
function init_payment(request_id) {
    $.ajax({
        type: "post",
        url: relative_path + "request_payment_controller.php",
        data: {
            action: "check_assigned",
            request_id: request_id
        },
        async: false,
        dataType: "json",
        success: function(response) {
            set_payment_view(response, request_id);
        },
        error: function(result) {
            console.log("Error: " + result);
        }
    });
}

function set_payment_view(response, request_id) {
    $("#payment_tab_alert").hide();
    $("#payment_tab_content").show();
    if (response.result) {
        /***
         * Get the vendor details
         * Per Hour wage, vendor id
         */
        $("#request-payapprove-form-div").show();
        $("#request_approve_alert").hide();
        let vendor = response.value[0];
        $("#edit-payment").find("#request_pay_perhr").val(vendor.wage).attr("data-wage", vendor.wage);
        $("#savePaymentdetails").attr("data-vendorid", vendor.user_id).attr("data-wage", vendor.wage).attr("data-rid",
            request_id);
        $("#edit-payment").find("#file-holder-payments").empty();
        $.ajax({
            type: "post",
            url: relative_path + "request_payment_controller.php",
            data: {
                action: "getPaymentDetails",
                request_id: request_id
            },
            async: false,
            dataType: "json",
            success: function(response) {
                $("#edit-payment").find("#request_pay_perhr").val(response.payment_amount);
                $("#edit-payment").find("#request_payinfo").val(response.repair_detail);
                $("#edit-payment").find("#request_payhours").val(response.job_hours);
                $("#edit-payment").find("#request_pay_expenses").val(response.other_expenses);
                $("#paymentinvoicenum").val(response.invoice_detail);
                if (response.invoice_attachment != null && response.invoice_detail != null) {
                    let invoiceFullName = response.invoice_detail + "=" + response.invoice_attachment;
                    let hiddenFileName =
                        "<input type='hidden' style='display:none;' name='invoice_attached[]' value='" +
                        invoiceFullName + "' >";
                    $("#edit-payment").find("#file-holder-payments").append(hiddenFileName);
                }

                $("#savePaymentdetails").attr("disabled", false);

                if (response.is_approved == 1) {
                    $("#request-payapprove-form-div").hide();
                    $("#request_approve_alert").html("Payment is approved for this request").show();
                    $("#savePaymentdetails").attr("disabled", true);
                }

            },
            error: function(result) {
                console.log("Error: " + result);
            }
        });
    } else {
        $("#payment_tab_alert").html(
                "<span class='alert alert-warning'>Assign a vendor to this request to view the payment details.</span>")
            .show();
        $("#payment_tab_content").hide();
    }
}

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

    let recipientVendors = [];
    $('#recipient-vendor input').each(function(i, item) {
        var assigned = '0';
        if (item.checked) {
            assigned = '1';
        }
        var vendor = {
            vendor_id: item.value,
            assigned: assigned
        };
        recipientVendors.push(vendor);
    });

    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: {
            action: "set_recipients",
            request_id: request_id,
            employees: recipientEmployee,
            tenants: recipientTenant,
            vendors: recipientVendors,
        },
        success: function(result) {
            init_recipient(result, false)
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


//--------------------  update view methods -----------------------

function set_modal_view(data) {
    var request_id = data.request_id;
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
        // If the user is tenant
        $('#recipient_tag,#payment_tag').remove();
        $('#recipient,#payment_tab,#payment_approval').remove();
        $('#payment_approval').remove();
    }

    if (user_level == 11) {
        // If the user is handyman - show the payment approval div
        $('#payment_approval').remove();
    }

    // Hide the payment tab when the issue is not closed
    if (open_or_close != "closed") {
        $("#payment_tag").hide();
    }

    // change the issue bkg
    var issue_tr_id = '#issue_row_' + data.request_id;
    var issue_line_class = 'issue-line';
    if (request_category == 2 || request_category == 0) {
        issue_line_class += ' warning ';
    } else {
        issue_line_class += ' success ';
    }
    if (open_or_close == 'open') {
        issue_line_class += ' txt-black ';
    } else {
        issue_line_class += ' txt-grey ';
    }

    $(issue_tr_id).attr('class', issue_line_class);

    var status_selected_line = $(issue_tr_id).children().first();
    if (user_level != 5 && status_selected_line.text() == 'PENDING') {
        status_selected_line.text("READ BY MANAGER");
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


    $('#editRecipient .vendor-recipient-wraps').remove();
    if (data.vendors) {
        $('#recipient-employee-wrap').after('<div class="col-sm-12 vendor-recipient-wraps">\n' +
            '                    <div class="form-group">\n' +
            '                      <label class="edit-label col-sm-4">Vendors</label>\n' +
            '                      <div class="col-sm-7">\n' +
            '                        <table id="recipient-vendor">\n' +
            '                        </table>\n' +
            '                      </div>\n' +
            '                    </div>\n' +
            '                  </div>\n'
        );
        var vendors = data.vendors;
        for (var y in vendors) {
            //if the vendor is already assigned with this request, then check the box
            if (vendors[y].assigned === 1) {
                var vendor_checked = 'checked';
            } else {
                var vendor_checked = '';
            }
            let vendorName = vendors[y].full_name;
            if (!vendorName || vendorName.length < 1 || vendors[y].vendor_type_id == 1) {
                vendorName = vendors[y].company_name;
            }
            $('#recipient-vendor').append(
                '<tr><td><label class="checkbox"><input class="edit-input" type="checkbox" name="vendor" value="' +
                vendors[y].vendor_id + '" ' + vendor_checked + '>' + vendorName + '</label></td></tr>')
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

    return;
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
        $(formId + ' .request-location-div').append(data.building_name + " " + data.unit_number);
        $(formId + ' .request-location-div').append(
            '<input id="preUnit" type="hidden" name="reportApartmentId" value="' + data.apartment_id + '">' +
            '<input id="preBuilding" type="hidden" name="reportBuildingId" value="' + data.building_id + '">' +
            '<input id="preArea" type="hidden" name="reportArea" value="apartment">');
        //if it is a tenant, then no access to choosing status when reporting an issue.
        if (formId === "#reportIssue") {
            $(formId + ' .request-status-wrap').remove();
        }

    } else {
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

    if (formId != '#reportIssue') {
        set_request_status_view(data, formId + ' .request-status');
    }

    //by defalut, hiden handyman time slot
    $(formId + ' .request-visit-time-wraps').hide();

    //for enabling/disabling the visit from to time
    $(formId + ' .request-visit-approved').on('click', function() {
        $(formId + ' .request-visit-time-wraps').show();

    });
    $(formId + ' .request-visit-not-approved').on('click', function() {
        $(formId + ' .request-visit-time-wraps').hide();
    });

    //when the request type is changed, the time slot will be changed
    // Sharan code : in the recipients tab : add the vendors list according to the request type selected
    $(formId + ' .request-type').change(function(e) {
        e.stopImmediatePropagation();
        var selected_request_type = $(formId + ' .request-type').val();
        var building_id = $(formId).find('#preBuilding').val();
        update_handyman_date_slot(building_id, selected_request_type);

        updateVendorsNewRequest(selected_request_type, building_id);
    });

    //uploading pictures ---- preview the picture
    $(formId + ' .request-pic-upload').on('change', function() {
        $(formId).find('#report_preview_imgs').empty();
        var files = $(this)[0].files;

        console.log(files.length);
        var fileReader = new FileReader();
        var img_div = $(formId).find('#report_preview_imgs');

        for (var i = 0; i < files.length; i++) {
            var file_one = files[i];
            fileReader.readAsDataURL(file_one);
            fileReader.onloadend = function(oFRevent) {
                var src = oFRevent.target.result;
                img_div.append(
                    '<img id="reportPic" class="request-pic-preview" style="max-width: 150px;max-height: 150px;" src="' +
                    src + '">');
            };

            fileReader = new FileReader();
        }
    });
}

// Update the vendors list in the recipients tab in the new report request modal
function updateVendorsNewRequest(type, building_id) {
    // Get all the vendors in the system for the given type of the request (job type)
    $('#editRecipientReport').show();
    $('#recipient-report-vendor').empty();
    $('.recipientReport-alert').hide();
    $('#recipient-report-vendor').append("<option value='0'>Select a Vendor</option>");
    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: {
            action: "get_vendors",
            request_type: type
        },
        async: false,
        dataType: "json",
        success: function(result) {
            if (result.value) {
                // vendors exist
                var vendors = result.value;
                for (var y in vendors) {
                    let vendorName = vendors[y].full_name;
                    if (!vendorName || vendorName.length < 1 || vendors[y].vendor_type_id == 1) {
                        vendorName = vendors[y].company_name;
                    }
                    $('#recipient-report-vendor').append(
                        '<option class="edit-input" name="vendor" value="' + vendors[y].vendor_id +
                        '" >' + vendorName + '</option>'
                    );
                }
            } else {
                $('#editRecipientReport').hide();
                $(".recipientReport-alert").html("No vendors found for the selected request type.").show();
            }
        },
        error: function(result) {
            console.log("Error: " + result);
        }
    });
}

//set the list of request types
function set_request_type_view(data, element) {
    $(element).empty();
    var content = '';
    content += '<option value="0">Select request type</option>';
    for (var i in data) {
        content += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
    }
    $(element).append(content);
}

//set the list of request status
function set_request_status_view(data, element) {
    var status = data.request_status;

    $(element).empty();

    var content = '';
    for (var i in status) {
        content += '<option value="' + status[i].id + '">' + status[i].name + '</option>';
    }

    if (user_level != 5) {
        content += '<option disabled>------------------------------------------------------</option>';
        console.log(data);
        var status_2 = data.request_status_2;
        for (var i in status_2) {
            content += '<option value="' + status_2[i].id + '">' + status_2[i].name + '</option>';
        }
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
                '<textarea class="form-control report-location-margin-top" id="reportAreaDetails" name="reportLocationDetails" rows="1" placeholder="Specific Common Area (e.g. Elevator No.2)" ></textarea>'
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
        $(formId + ' .request-status').val(data.status_id);
        // if (data.internal_status === 0) {
        //     $(formId + ' .request-status').val(data.status_id);
        // } else {
        //     $(formId + ' .request-status-div').empty();
        //     $(formId + ' .request-status-div').append(data.status_name);
        // }
        // if (data.approveVisit === 0) {
        //     $(formId + ' .request-approve-div').empty();
        //     $(formId + ' .request-approve-div').append('No');
        //     $(formId + ' .request-visit-time-wraps').hide();
        // } else {
        //     $(formId + ' .request-approve-div').empty();
        //     $(formId + ' .request-approve-div').append('Yes');
        //     $(formId + ' .request-visit-time-wraps').show();
        //     $(formId + ' .request-time-div').empty();
        //     $(formId + ' .request-time-div').append(data.timeFromVisit);
        // }
        $(formId + ' .request-message-div').empty();
        $(formId + ' .request-message-div').append(data.message);
    }
    // else if (user_level == 1) {
    else {
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
        // if (Number(data.approveVisit) === 0) {
        //     $(formId + ' .request-visit-not-approved').click();
        // } else {
        //     $(formId + ' .request-visit-approved').click();
        //     $(formId + ' .request-visit-from').val(data.timeFromVisit);
        // }
        $(formId + ' .request-message').val(data.message);
    }

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
    } else {
        $('#saveEdit').show();
        $('#cancelEdit').show();
    }
}

//---------------init_editing function end----------------

//submitting the report with FormData.
function submit_report() {
    let vendor_id = $("#recipient-report-vendor").val();

    var reportFormData = new FormData(document.getElementById("reportIssue"));
    reportFormData.append('reportUserId', user_id);
    reportFormData.append('action', 'add_request');
    reportFormData.append('vendor_id', vendor_id);

    $.ajax({
        type: "post",
        url: relative_path + "request_info_controller.php",
        data: reportFormData,
        contentType: false,
        processData: false,
        // async: false,
        beforeSend: function() {
            $("#new_request_loader").show(); // show the loader
            $('#submitReport').attr("disabled", true);
        },
        success: function() {
            $("#new_request_loader").fadeOut();
            window.location.replace('requests.php?unit_id=' + user_unit_id);
        },
        error: function() {
            console.log("submit_report() error");
            $('#submitReport').attr("disabled", false);
        }
    });

}

//submitting the edit with FormData
function submit_edit(request_id) {
    var closedStatusIdValues = [4, 17, 21]; // These are possible values that correspond to Close status of the request
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
        // async: false,
        success: function() {
            // Show the Payment Tab when the status is changed to closed ( possible status ID values : 4,17,21 )
            var request_status_updated = parseInt($("#edit-request").find("#request-status :selected")
                .val());
            var requestStatusIsClosed = $.inArray(request_status_updated, closedStatusIdValues);
            if (requestStatusIsClosed != -1) {
                // Status is changed to closed
                $("#payment_tag").show();
                $("#request_op_success_closed").show();
            } else {
                alert("Status is updated.");

                // request status is not changed to closed - no need to show the payment tab - so refresh the page
                // window.location.replace('requests.php?unit_id=' + user_unit_id);
            }
        },
        error: function(result) {
            console.log("error in submitting the edit" + result);
        }
    });
};


//--------------------  paging  -----------------

function curent_issue_paging_event_binding() {
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
}

//call it after first loading
curent_issue_paging_event_binding();


function past_issue_paging_event_binding() {
    //previous/next events for past isuses
    $('#past_issues_previous_page a').click(function() {
        var past_page_id = $('#past_issues_paing .active').children().first().attr('id');
        var past_page_number = parseInt(past_page_id.substr(5));
        if (past_page_number > 1) {
            get_past_issue_page(past_page_number - 1);
        }
    });

    $('#past_issues_next_page a').click(function() {
        var past_page_id = $('#past_issues_paing .active').children().first().attr('id');
        var past_page_number = parseInt(past_page_id.substr(5));
        var total_page_number = $('#past_page_number').val();
        if (past_page_number < total_page_number) {
            get_past_issue_page(past_page_number + 1);
        }
    });
}

//call it after first loading
past_issue_paging_event_binding();


function update_current_pagination(page_number) {
    $('#current_page_number').val(page_number);
    var current_paging_ul = $('#current_issues_paging');
    current_paging_ul.empty();

    var string =
        '<li class="disabled" id="current_issues_previous_page"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>' +
        '<li class="active"><a href="#" id="page_1" onclick="get_current_issue_list(1)" >1</a></li>';
    for (var n = 2; n <= page_number; n++) {
        string += '<li><a href="#" id="page_' + n + '" onclick="get_current_issue_list(' + n + ')">' + n + '</a></li>';
    }
    string +=
        '<li id="current_issues_next_page"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
    current_paging_ul.append(string);

    //event rebinding
    curent_issue_paging_event_binding();
}


function update_past_pagination(page_number) {
    $('#past_page_number').val(page_number);
    var past_paging_ul = $('#past_issues_paing');
    past_paging_ul.empty();

    var string =
        '<li class="disabled" id="past_issues_previous_page"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>' +
        '<li class="active"><a href="#" id="page_1" onclick="get_past_issue_page(1)" >1</a></li>';
    for (var n = 2; n <= page_number; n++) {
        string += '<li><a href="#" id="page_' + n + '" onclick="get_past_issue_page(' + n + ')">' + n + '</a></li>';
    }
    string +=
        '<li id="past_issues_next_page"><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
    past_paging_ul.append(string);

    //event rebinding
    past_issue_paging_event_binding();
}

function get_current_issue_list(page_number) {

    var filter_building_id = $('#filter_building_current').val();
    var filter_category = $('#filter_category_current').val();
    var filter_status = $('#filter_status_current').val();
    var filter_unit = $('#filter_units_current').val();
    var filter_from = $('#filter_created_from_current').val();
    var filter_to = $('#filter_created_to_current').val();
    var filter_employee_id = $('#filter_employee_current').val();
    var filter_order = $('#order_by_current').val();
    var filter_read_category = $('#filter_read_category').val();

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
            filter_order: filter_order,
            filter_read_category: filter_read_category
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
    console.log(data);

    var data_arr = data.data_content;

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

        //open or closed text color
        var txt_class = 'txt-black';
        if (data_arr[i].issue_status == 'CLOSED') {
            txt_class = 'txt-grey';
        }

        $('#current_issue_tbody').append('<tr class="' + data_arr[i].style_class + ' issue-line ' + txt_class +
            ' " id="' + id + '" data-toggle="modal" data-target="#iModal" data-request="' + data_arr[i].request_id +
            '">\n' +
            '              <td class="col-md-1 text-center">' + data_arr[i].detailed_status + '</td>\n' +
            '              <td class="col-md-2 text-center">' + data_arr[i].request_type + '</td>\n' +
            '              <td class="col-md-1 text-center ' + level_label_class + '">' + data_arr[i]
            .request_level + '</td>\n' +
            '              <td class="col-md-2 text-center">' + data_arr[i].created_time + '</td>\n' +
            '              <td class="col-md-1 text-center">' + data_arr[i].interval + '</td>\n' +
            '              <td class="col-md-1 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
            persson_info + '">' + data_arr[i].creator_full_name + '</td>\n' +
            '              <td class="col-md-2 text-center non-overflow"  data-toggle="tooltip" data-container="body" title="' +
            data_arr[i].closed_by + '">' + data_arr[i].closed_by + '</td>\n' +
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
    var page_id_seletor = '#current_issues_paging #page_' + page_number;
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
    var page_id_seletor = '#past_issues_paing #page_' + page_number;
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
    $('#filter_read_category option').first().attr('selected', 'selected');
    $("#request_type_detail").val("all").trigger("change");

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
    var filter_read_category = $('#filter_read_category').val();
    var request_type_detail = $('#request_type_detail').val();

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
            filter_tenant: filter_tenant,
            filter_read_category: filter_read_category,
            request_type_detail: request_type_detail
        },
        dataType: "json",
        async: false,
        success: function(result) {
            update_current_issue_page(result, 1);
            update_current_pagination(result.total_pages);
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
    filtered_past();
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
            update_past_pagination(result.total_pages);
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


function close_bulletin(bulletin_id) {
    $.ajax({
        type: "post",
        url: relative_path + "request_bulletins_controller.php",
        data: {
            action: "close_bulletin",
            bulletin_id: bulletin_id,
        },
        dataType: "json",
        async: false,
        success: function(result) {
            if (result.status == 'success') {
                $('#bulletin_close_' + bulletin_id).hide();
                $('#bulletin_close_' + bulletin_id).parent().append('<span>CLOSED<span>');
            }
        },
        error: function(result) {
            console.log("Error:" + result);
        }
    });
}

//---------------------- handyman time slot ----------------
function update_handyman_date_slot(building_id, request_type_id) {
    $.ajax({
        type: "post",
        url: relative_path + "request_handyman_controller.php",
        data: {
            action: "get_avail_date",
            building_id: building_id,
            request_type_id: request_type_id
        },
        dataType: "json",
        success: function(result) {
            if (result.status === 'success') {
                $('#handyman_avail_date').unbind('change');
                render_avail_time_view(result.content);
            } else {
                console.log("error: no data");
            }
        },
        error: function(result) {
            console.error("error: " + result);
        }
    });
}

function render_avail_time_view(date) {
    var content = '';
    for (var i = 0; i < date.length; i++) {
        content += '<option value="' + date[i].slot_id + '">' + date[i].date + '</option>';
    }
    var handyman_avail_data = $('#handyman_avail_date');
    handyman_avail_data.empty();
    handyman_avail_data.append(content);
    handyman_avail_data.change(function() {
        var selected_slot_id = $(this).val();
        update_handyman_time_slot(selected_slot_id);
    });
}

function update_handyman_time_slot(slot_id) {
    $.ajax({
        type: "post",
        url: relative_path + "request_handyman_controller.php",
        data: {
            action: "get_avail_datetime",
            slot_id: slot_id
        },
        dataType: "json",
        success: function(result) {
            if (result.status === 'success') {
                $('#reportVisitDuration').val(result.content.duration);
                render_handyman_time_slot_view(result.content.slots);
            } else {
                console.log("error");
            }
        },
        error: function(result) {
            console.error("error: " + result);
        }
    });
}

function render_handyman_time_slot_view(data) {
    var report_reserve_time = $('#report_reserve_time');
    report_reserve_time.empty();
    var content = '';
    for (var i = 0; i < data.length; i++) {
        content += '<option value="' + data[i] + '">' + data[i] + '</option>';
    }
    report_reserve_time.append(content);
}

//---------------------- document ready ---------------------

// to avoid open model during loading page
$(document).ready(function() {
    $('.issue-line').attr('data-toggle', 'modal');

    // Sharan's code for the payment tab file upload in request modal
    var paymentForm = $("#edit-payment");

    $('#paymentinvoicefile').on('change', function() {
        var file = this.files[0];
        let invoiceNumber = $("#paymentinvoicenum")
            .val(); // invoice number for this particular attached invoice file

        $('#paymentinv_form_progress').attr({
            value: 0,
            max: 0
        });

        var formdata = new FormData($('#paymentinv_form')[0]);
        formdata.append('action', 'upload_invoice');
        formdata.append('upload_inv', file);
        $("#paymentinv_form_progress").show();
        $.ajax({
            url: relative_path + "request_payment_controller.php",
            type: 'POST',
            data: formdata,
            cache: false,
            dataType: "json",
            contentType: false,
            processData: false,
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    // For handling the progress of the upload
                    myXhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            $('#paymentinv_form_progress').attr({
                                value: e.loaded,
                                max: e.total,
                            });
                        }
                    }, false);
                }
                return myXhr;
            },
            success: function(response) {
                if (response.result) {
                    let invoiceFullName = invoiceNumber + "=" + response.name;
                    let hiddenFileName =
                        "<input type='hidden' style='display:none;' name='invoice_attached[]' value='" +
                        invoiceFullName + "' >";
                    $("#edit-payment").find("#file-holder-payments").append(hiddenFileName);
                }
            }
        });

        // Also see .name, .type
    });

    // Save Button click in the Payment tab in the request modal
    $("#savePaymentdetails").on("click", function() {
        let payment_job_detail = $("#request_payinfo").val();
        let payment_hours = $("#request_payhours").val();
        let payment_expenses = $("#request_pay_expenses").val();
        let request_id = $(this).attr("data-rid");
        let vendor_id = $(this).attr("data-vendorid");

        var form = $("#edit-payment").serialize();

        $.ajax({
            url: relative_path + "request_payment_controller.php",
            type: 'POST',
            data: {
                data: form,
                action: 'update_payment',
                requestid: request_id,
                vendorid: vendor_id
            },
            success: function(data) {
                alert("Payment Data updated for this request.");
            }
        });
    });

    // Approval confirm button click
    $("#savePaymentApproval").on("click", function() {
        let payment_approval_checked = $("#request_is_payapprove").prop("checked");
        let payment_approval_finalamt = $("#request_approve_finalamt").val();
        let payment_approval_comments = $("#request_approve_comments").val();
        let request_id = $("#savePaymentdetails").attr("data-rid");

        if (!payment_approval_checked) {
            // do nothing if the approval checkbox is not checked
            return;
        }

        $.ajax({
            url: relative_path + "request_payment_controller.php",
            type: 'POST',
            data: {
                action: 'approve_payment_details',
                requestid: request_id,
                comment: payment_approval_comments,
                amount: payment_approval_finalamt
            },
            success: function(response) {
                $("#request-payapprove-form-div").hide();
                $("#savePaymentdetails").attr("disabled", true);
                $("#request_approve_alert").html("Payment approved for this request.")
                    .show();
            }
        });
    });

    $('#category-internal').click(function(event) {
        $("#current_issues_a").trigger("click");
        console.log("x");
        $('#filter_category_current option[value=1]').attr("selected", true);
        $('#filter_category_current option[value=2]').attr("selected", false);
        $('#filter_category_current option[value=0]').attr("selected", false);
        current_issue_filtered();
    });

    $('#category-fixed').click(function(event) {
        $("#fixed_events_tabli > a").trigger("click");
        $("#fixed_events_tabli").toggleClass("hidden");
    });

    $('#category-tenant').click(function(event) {
        $("#current_issues_a").trigger("click");
        $('#filter_category_current option[value=1]').attr("selected", false);
        $('#filter_category_current option[value=2]').attr("selected", true);
        $('#filter_category_current option[value=0]').attr("selected", false);
        current_issue_filtered();
    });

    $('#unread_request_mark').click(function(event) {
        $("#current_issues_a").trigger("click");
        $('#filter_read_category option[value=all]').attr("selected", false);
        $('#filter_read_category').val(1).trigger("change");
        current_issue_filtered();
    });
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

<?php
function timediff($begin_time, $end_time)
{
    $timediff = $end_time - $begin_time;

    $days = intval($timediff / 86400);
    $remain = $timediff % 86400;
    $hours = intval($remain / 3600);
    $remain = $remain % 3600;
    $mins = intval($remain / 60);
    $secs = $remain % 60;
    $res = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
    return $res;
}
?>

<style>
.hidden {
    display: none;
}
</style>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css"
    integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">