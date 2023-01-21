<?
include('../pdo/dbconfig.php');
$crud = new Crud($DB_con);
if (empty($_SESSION['employee_id'])) {
  die("wrong ID.");
} else {
  $employee_id = $_SESSION['employee_id'];
}
$company_id = $_SESSION['company_id'];

$crud->query("SELECT count(*) as project_count FROM project_infos WHERE company_id = $company_id");
$project_count = $crud->resultField();
$crud->query("SELECT count(*) as proposal_count FROM proposal_infos WHERE company_id = $company_id and is_proposal=1");
$proposal_count = $crud->resultField();
$crud->query("SELECT count(*) as contract_count FROM contract_infos WHERE company_id = $company_id and is_proposal=0");
$contract_count = $crud->resultField();
$crud->query("SELECT count(*) as invoice_count FROM invoice_infos WHERE company_id = $company_id");
$invoice_count = $crud->resultField();
$crud->query("SELECT count(*) as vendor_count FROM vendor_infos WHERE company_id = $company_id");
$vendor_count = $crud->resultField();
$crud->query("SELECT count(*) as attachment_count FROM attachment_infos WHERE company_id = $company_id");
$attachment_count = $crud->resultField();


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
  WHERE RI.status_id=1 AND AI.company_id =  $company_id
  order by RI.entry_datetime DESC");
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
$goal_comp = 0;

// var_dump($paid_months);
?>
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
<script>
  loadjs.ready(['head'], function() {
    loadjs([
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
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-lg-2 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
          <div class="inner">
            <h3><?= $project_count ?></h3>

            <p>Projects</p>
          </div>
          <div class="icon">
            <i class="fas fa-building"></i>
          </div>
          <a href="projectinfoslist?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-2 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
          <div class="inner">
            <h3><?= $proposal_count ?></h3>

            <p>Proposals</p>
          </div>
          <div class="icon">
            <i class="fas fa-file-signature fa-2x"></i>
          </div>
          <a href="proposal_infoslist.php?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-2 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
          <div class="inner">
            <h3><?= $contract_count ?></h3>

            <p>Contracts</p>
          </div>
          <div class="icon">
            <i class="fas fa-file-contract fa-2x"></i>
          </div>
          <a href="contract_infoslist.php?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-2 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
          <div class="inner">
            <h3><?= $invoice_count ?></h3>

            <p>Invoices</p>
          </div>
          <div class="icon">
            <i class="fas fa-file-invoice  fa-2x"></i>
          </div>
          <a href="invoice_infoslist.php?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-2 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
          <div class="inner">
            <h3><?= $vendor_count ?></h3>

            <p>Vendors</p>
          </div>
          <div class="icon">
            <i class="fa fa-briefcase fa-2x"></i>
          </div>
          <a href="vendor_infoslist.php?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <!-- ./col -->
      <div class="col-lg-2 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
          <div class="inner">
            <h3><?= $attachment_count ?></h3>

            <p>File Center</p>
          </div>
          <div class="icon">
            <i class="fa fa-file-pdf-o fa-2x"></i>
          </div>
          <a href="attachment_infoslist.php?cmd=resetall" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
    </div>
    <!-- /.row -->
    <!-- Main row -->

    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-lg-3 col-6">
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
      <div class="col-lg-3 col-6">
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
      <div class="col-lg-3 col-6">
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
      <div class="col-lg-3 col-6">
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
        <div class="card direct-chat direct-chat-primary">
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
            <a href="requests.php?cmd=reset" class="btn btn-sm btn-secondary float-right">View All
              Requests</a>
          </div>
          <!-- /.card-footer -->
        </div>
        <!--/.direct-chat -->


        <!-- /.card -->
      </section>
      <!-- /.Left col -->
      <!-- right col (We are only adding the ID to make the widgets sortable)-->
      <section class="col-lg-5 connectedSortable">

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

    <!-- solid sales graph -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">Monthly Rent Collected</h5>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <div class="btn-group">
                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                  <i class="fas fa-wrench"></i>
                </button>
              </div>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
            <!--./card-tools-->
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <div class="chart">
                  <!-- Sales Chart Canvas -->


                  <canvas id="rentChart" height="180" style="height: 180px;" class="chartjs-render-monitor"></canvas>


                </div>
                <!-- /.chart-responsive -->
              </div>
              <!-- /.col -->

            </div>
            <!-- /.row -->
          </div>
          <!-- ./card-body -->
          <div class="card-footer">
            <div class="row">
              <div class="col-sm-3 col-6">
                <div class="description-block border-right">
                  <span class="description-percentage text-success"><i class="fas fa-caret-up"></i>
                    17%</span>
                  <h5 class="description-header">$<?= number_format($total_revenue, 2, ".", ",") ?>
                  </h5>
                  <span class="description-text">TOTAL REVENUE</span>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-3 col-6">
                <div class="description-block border-right">
                  <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i>
                    0%</span>
                  <h5 class="description-header">$<?= number_format($total_cost, 2, ".", ",") ?></h5>
                  <span class="description-text">TOTAL COST</span>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-3 col-6">
                <div class="description-block border-right">
                  <span class="description-percentage text-success"><i class="fas fa-caret-up"></i>
                    20%</span>
                  <h5 class="description-header">$<?= number_format($total_profit, 2, ".", ",") ?>
                  </h5>
                  <span class="description-text">TOTAL PROFIT</span>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-sm-3 col-6">
                <div class="description-block">
                  <span class="description-percentage text-danger"><i class="fas fa-caret-down"></i>
                    18%</span>
                  <h5 class="description-header">$<?= number_format($goal_comp, 2, ".", ",") ?></h5>
                  <span class="description-text">GOAL COMPLETIONS</span>
                </div>
                <!-- /.description-block -->
              </div>
            </div>
            <!-- /.row -->
          </div>
          <!-- /.card-footer -->
        </div>
        <!-- /.card -->

      </div>
      <!--./col-->

    </div>
    <!--/.row-->

  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
<script>
  loadjs.ready(['head', 'jquery'], function() {
    loadjs([
      'custom/homepage/plugins/chart.js/Chart.min.js',
    ], 'jsloaded');
  });


  loadjs.ready(['head', 'jquery', 'jsloaded'], function() {
    // $.widget.bridge('uibutton', $.ui.button);
    //   $('#uncollected').DataTable();
    function getRandomColor() {
      var letters = '0123456789ABCDEF'.split('');
      var color = '#';
      for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      return color;
    }

    var paid_data = <?= $paid_months ?>;
    var unpaid_date = <?= $unpaid_months ?>;
    new Chart(document.getElementById("rentChart"), {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
          'Dec'
        ],
        datasets: [{
            data: paid_data,
            label: "Paid",
            borderColor: "#0275d8"
          },
          {
            data: unpaid_date,
            label: "Un-Paid",
            borderColor: "#5cb85c"
          }
        ]
      },

      options: {
        fill: true,
      }
    });

  });
</script>
</body>

</html>