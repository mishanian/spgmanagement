<?
include('../pdo/dbconfig.php');
$crud = new Crud($DB_con);
if (empty($_SESSION['employee_id'])) {
    die("wrong ID.");
} else {
    $employee_id = $_SESSION['employee_id'];
}
$company_id = $_SESSION['company_id'];

$crud->query("SELECT count(*) as building_count FROM building_infos WHERE company_id = $company_id");
$building_count = $crud->resultField();
$crud->query("SELECT count(*) as unit_count FROM apartment_infos WHERE company_id = $company_id");
$unit_count = $crud->resultField();
$crud->query("SELECT count(*) as tenant_count FROM tenant_infos WHERE company_id = $company_id");
$tenant_count = $crud->resultField();
$crud->query("SELECT count(*) as lease_count FROM lease_infos WHERE company_id = $company_id");
$lease_count = $crud->resultField();
$crud->query("SELECT RI.id as request_id, BI.building_name, AI.unit_number, RI.message, RI.entry_datetime FROM request_infos RI
  LEFT JOIN apartment_infos AI ON AI.apartment_id=RI.apartment_id
  LEFT JOIN building_infos BI ON BI.building_id=RI.building_id
  WHERE AI.company_id =  $company_id  AND  RI.status_id<>4
  order by RI.entry_datetime DESC"); //
$requests = $crud->resultSet();
$request_count = count($requests);
$crud->query("SELECT lease_id, lease_payment_id, building_name, unit_number, paid, total FROM rental_payments WHERE paidornot_status=2 AND year_due_date=YEAR(CURDATE()) AND month_due_date=MONTH(CURDATE()) AND company_id = $company_id");
$unpaids = $crud->resultSet();

$crud->query("SELECT SUM(total)-SUM(discount)-SUM(paid) AS un_paid FROM rental_payments WHERE paidornot_status=2 AND year_due_date=YEAR(CURDATE()) AND company_id = $company_id GROUP BY month_due_date;");
$unpaid_months = $crud->resultArray();
$unpaid_months = "[" . implode(",", $unpaid_months) . "]";
// var_dump(  $unpaid_months);
$crud->query("SELECT SUM(paid) AS paid FROM rental_payments WHERE paidornot_status=1 AND year_due_date=YEAR(CURDATE()) AND company_id = $company_id GROUP BY month_due_date;");
$paid_months = $crud->resultArray();
$total_revenue = array_sum($paid_months);
$paid_months = "[" . implode(",", $paid_months) . "]";

$total_cost = 0;
$total_profit = $total_revenue - $total_cost;


/* Request Chart*/

// $crud->query("SELECT BI.building_id, BI.building_name, MONTH(entry_datetime), COUNT(*) AS counter FROM request_infos RI LEFT JOIN building_infos BI ON RI.building_id=BI.building_id
// WHERE YEAR(entry_datetime)=YEAR(CURDATE())  GROUP BY BI.building_name, MONTH(entry_datetime) ORDER BY  MONTH(entry_datetime), BI.building_name");
//$crud->query("SELECT COUNT(*) AS counter FROM request_infos RI WHERE YEAR(entry_datetime)=YEAR(CURDATE())  GROUP BY MONTH(entry_datetime) ORDER BY  MONTH(entry_datetime)");

$crud->query("SELECT IFNULL(COUNT(*),0) AS counter FROM request_infos RI LEFT JOIN building_infos BI ON BI.building_id=RI.building_id WHERE YEAR(entry_datetime)=YEAR(CURDATE()) AND BI.company_id = $company_id  GROUP BY MONTH(entry_datetime) ORDER BY  MONTH(entry_datetime)");
$requests_per_month = $crud->resultArray();
$requests_per_month_sum = array_sum($requests_per_month);

$crud->query("SELECT IFNULL(COUNT(*),0) AS counter FROM request_infos RI LEFT JOIN building_infos BI ON BI.building_id=RI.building_id WHERE YEAR(entry_datetime)=YEAR(CURDATE()) AND BI.company_id = $company_id  AND status_id=4 GROUP BY MONTH(entry_datetime) ORDER BY  MONTH(entry_datetime)");
$requests_resolved_per_month = $crud->resultArray();

$crud->query("SELECT BI.building_name, IFNULL(COUNT(*),0) AS counter FROM request_infos RI LEFT JOIN building_infos BI ON RI.building_id=BI.building_id WHERE YEAR(entry_datetime)=YEAR(CURDATE()) AND BI.company_id = $company_id  GROUP BY BI.building_name ORDER BY counter DESC");
$requests_per_building_arr = $crud->resultAssoc();

if (!empty($requests_per_building_arr)) {
    $requests_building_names = array_keys($requests_per_building_arr);
    $requests_per_building = array_values($requests_per_building_arr);
    $requests_per_building_sum = array_sum($requests_per_building);
}
// die(var_dump($requests_per_building_arr));
// $crud->query("SELECT BI.building_name AS counter FROM request_infos RI LEFT JOIN building_infos BI ON RI.building_id=BI.building_id WHERE YEAR(entry_datetime)=YEAR(CURDATE())  GROUP BY BI.building_name ORDER BY counter DESC");
// $requests_building_names=$crud->resultArray();
// die(var_dump($requests_per_building_arr));

// $crud->query("SELECT BI.building_id, BI.building_name, MONTH(entry_datetime), COUNT(*) AS counter FROM request_infos RI LEFT JOIN building_infos BI ON RI.building_id=BI.building_id
// WHERE YEAR(entry_datetime)=YEAR(CURDATE()) AND status_id=4 GROUP BY BI.building_name, MONTH(entry_datetime) ORDER BY  MONTH(entry_datetime), BI.building_name");
// $requests_resolved=$crud->resultSet();


$crud->query("SELECT building_name, ROUND(SUM(paid),0) AS paid FROM rental_payments WHERE paidornot_status=1 AND year_due_date=YEAR(CURDATE()) AND company_id = $company_id GROUP BY building_name ORDER BY paid DESC;");
$building_revenue_arr = $crud->resultAssoc();
if (!empty($building_revenue_arr)) {
    $building_revenue_names = array_keys($building_revenue_arr);
    $building_revenue_paid = array_values($building_revenue_arr);
    $building_revenue_sum = number_format(array_sum($building_revenue_arr), 0);
}


$crud->query("SELECT MONTH(start_date), BI.building_name, COUNT(*) AS counter FROM lease_infos LI LEFT JOIN building_infos BI ON LI.building_id=BI.building_id
  WHERE YEAR(start_date) = YEAR(CURDATE())  AND BI.company_id = $company_id
  GROUP BY BI.building_name, MONTH(start_date)
  ORDER BY MONTH(start_date)");
$start_leases_arr = $crud->resultSet(); // Not used , not proper chart yet


$goal_comp = 0;

// var_dump($paid_months);
?>
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
<script>
    loadjs.ready(['head'], function() {
        loadjs([
            'custom/css/introjs.min.css',
            'https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css',
            'custom/homepage/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
            'custom/homepage/plugins/jqvmap/jqvmap.min.css',
            'custom/homepage/plugins/daterangepicker/daterangepicker.css',
            'custom/homepage/plugins/summernote/summernote-bs4.css',
            'https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css'
        ]);
    });
</script>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- <button onclick="javascript:introJs().start();" class="btn" style="color:#369"><i class="fa fa-info-circle" aria-hidden="true"></i> Interactive Help</button><br> -->

        <? if (in_array($_SESSION['UserLevel'], [23, 24])) {
            include('home_projects.php');
        } ?>






        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-6 " data-step='2' data-intro="First add your building here">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $building_count ?></h3>

                        <p>Properties</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <a href="buildinginfoslist?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6" data-step='3' data-intro="and add your desire units">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $unit_count ?></h3>

                        <p>Units</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <a href="apartmentinfoslist?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6" data-step='4' data-intro="then add a tenant">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= $tenant_count ?></h3>

                        <p>Tenants</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="viewtenantinfoslist?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6" data-step='5' data-intro="then you can add a lease here">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= $lease_count ?></h3>

                        <p>Leases</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <a href="leaseinfoslist?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <section class="col-lg-7 connectedSortable">
                <!-- Custom tabs (Charts with tabs)-->

                <!-- /.card -->

                <!-- DIRECT CHAT -->
                <div class="card direct-chat direct-chat-primary" data-step='6' data-intro="you can see your tenant's request here!">
                    <div class="card-header">
                        <h3 class="card-title">Requests</h3>

                        <div class="card-tools">
                            <span data-toggle="tooltip" title="<?= $request_count ?> New Messages" class="badge badge-primary"><?= $request_count ?></span>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle2">
                                <i class="fas fa-comments"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Conversations are loaded here -->
                        <div class="direct-chat-messages">
                            <!-- Message. Default to the left -->


                            <? foreach ($requests as $request) {
                                $CurrentDT = date_create(date("Y-m-d H:i:s"));
                                $RequestDT = date_create($request['entry_datetime']);

                            ?>
                                <div class="direct-chat-msg">
                                    <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-name float-left"><?= $request['building_name'] ?> -
                                            <?= $request['unit_number'] ?></span>
                                        <span class="direct-chat-timestamp float-right"><?= date_diff($RequestDT, $CurrentDT)->format('%a Day and %h hours'); ?></span>
                                    </div>
                                    <!-- /.direct-chat-infos -->
                                    <img class="direct-chat-img" src="custom/homepage/dist/img/user1-128x128.jpg" alt="message user image">
                                    <!-- /.direct-chat-img -->
                                    <div class="direct-chat-text">
                                        <a href="requestadd?action=rview&rid=<?= $request['request_id'] ?>"><?= $request['message'] ?></a>
                                    </div>
                                    <!-- /.direct-chat-text -->
                                </div>
                            <? } ?>
                            <!-- /.direct-chat-msg -->






                        </div>
                        <!--/.direct-chat-messages-->


                        <!-- /.direct-chat-pane -->
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        <a href="requests?cmd=reset" class="btn btn-sm btn-secondary float-right">View All
                            Requests</a>
                    </div>
                    <!-- /.card-footer -->
                </div>
                <!--/.direct-chat -->


                <!-- /.card -->
            </section>
            <!-- /.Left col -->
            <!-- right col (We are only adding the ID to make the widgets sortable)-->
            <section class="col-lg-5 connectedSortable" data-step='7' data-intro="find your Un-Collected rent here!">

                <!-- TABLE: Un-Collected Rent -->
                <div class="card">
                    <div class="card-header border-transparent">
                        <h3 class="card-title">Un-Collected Rent</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0" style="height:250px;overflow: auto; padding: 10px;">
                        <div class="table-responsive">
                            <table class="table m-0" id="uncollected">
                                <thead>
                                    <tr>
                                        <th>Building</th>
                                        <th>Total</th>
                                        <th>Paid</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?

                                    if (!empty($unpaids)) {
                                        foreach ($unpaids as $unpaid) {
                                    ?>
                                            <tr>
                                                <td><a href="lease_infosview.php?cmd=reset&showdetail=&id=<?= $unpaid['lease_id'] ?>"><?= $unpaid['building_name'] ?>
                                                        - <?= $unpaid['unit_number'] ?></a></td>
                                                <td><?= $unpaid['total'] ?></td>
                                                <td><span class="badge badge-success"><?= $unpaid['paid'] ?></span></td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>

                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        <a href="rental_paymentslist.php?cmd=reset" class="btn btn-sm btn-secondary float-right">View
                            All Renewal Results</a>
                    </div>
                    <!-- /.card-footer -->
                </div>
                <!-- /.card -->
            </section>
        </div>



    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<script>
    loadjs.ready(['head', 'jquery'], function() {
        loadjs([
            'custom/js/intro.min.js',
        ], 'introjs');
    });
</script>
<? if (empty($_COOKIE['intro_help'])) {
    setcookie("intro_help", "done", time() + (10 * 365 * 24 * 60 * 60));
    echo "<script>
    loadjs.ready(['head', 'jquery','introjs'], function() {
    introJs().start();
});
    </script>";
} ?>
<? if (in_array($_SESSION['UserLevel'], [1, 23, 24])) {
    include('home_charts.php');
}

?>

</body>

</html>