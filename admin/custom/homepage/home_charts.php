<!-- solid request per month graph -->
<div class="row">
    <div class="col-md-12">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Request Per Month</h5>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <!-- <div class="btn-group">
                    <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                      <i class="fas fa-wrench"></i>
                    </button>
                  </div> -->
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!--./card-tools-->
            </div>

            <div class="card-body">
                <div class="d-flex">
                    <p class="d-flex flex-column">
                        <span class="text-bold text-lg"><?= $requests_per_month_sum ?></span>
                        <span>Request Over Time (<?= date("Y") ?>)</span>
                    </p>

                </div>
                <!-- /.d-flex -->

                <div class="position-relative mb-4">
                    <canvas id="request_per_month_chart" height="200"></canvas>
                </div>


            </div>
        </div>
        <!-- /.card -->
    </div>

</div>
<!--/.row-->

<div class="row">
    <div class="col-md-6">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Request Per Building</h5>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!--./card-tools-->
            </div>

            <div class="card-body">
                <div class="d-flex">
                    <p class="d-flex flex-column">
                        <span class="text-bold text-lg"><?= $requests_per_building_sum ?></span>
                        <span>Request Over Time (<?= date("Y") ?>)</span>
                    </p>

                </div>
                <!-- /.d-flex -->

                <div class="position-relative mb-4">
                    <canvas id="request_per_building_chart" height="300"></canvas>
                </div>


            </div>
        </div>
        <!-- /.card -->
    </div>


    <!-- Chart Revenue -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Builings Revenue</h5>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!--./card-tools-->
            </div>
            <!--./card-header-->
            <div class="card-body">
                <div class="d-flex">
                    <p class="d-flex flex-column">
                        <span class="text-bold text-lg"><?= $building_revenue_sum ?></span>
                        <span>Year:(<?= date("Y") ?>)</span>
                    </p>
                </div>
                <!-- /.d-flex -->

                <div class="position-relative mb-4">
                    <canvas id="building_revenue_chart" height="300"></canvas>
                </div>
            </div>
            <!--./card-body-->
        </div>
        <!-- /.card -->
    </div>
</div>








<div class="row">

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Monthly Rent Collected</h5>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <!-- <div class="btn-group">
                    <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                      <i class="fas fa-wrench"></i>
                    </button>
                  </div> -->
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


                            <canvas id="rentChart" height="180" style="height: 300px;"
                                class="chartjs-render-monitor"></canvas>


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
                    <div class="col-sm-4 col-6">
                        <div class="description-block border-right">
                            <!-- <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 17%</span> -->
                            <h6 class="description-header">$<?= number_format($total_revenue, 0, ".", ",") ?></h6>
                            <span class="description-text">TOTAL REVENUE</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 col-6">
                        <div class="description-block border-right">
                            <!-- <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i> 0%</span> -->
                            <h6 class="description-header">$<?= number_format($total_cost, 0, ".", ",") ?></h6>
                            <span class="description-text">TOTAL COST</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-4 col-6">
                        <div class="description-block border-right">
                            <!-- <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 20%</span> -->
                            <h6 class="description-header">$<?= number_format($total_profit, 0, ".", ",") ?></h6>
                            <span class="description-text">TOTAL PROFIT</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->

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

<script>
loadjs.ready(['head', 'jquery'], function() {
    loadjs([
        'https://cdn.jsdelivr.net/npm/chart.js',
    ], 'jsloaded');
});


loadjs.ready(['head', 'jquery', 'jsloaded'], function() {
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
                    borderColor: "#0275d8",
                },
                {
                    data: unpaid_date,
                    label: "Un-Paid",
                    borderColor: "#5cb85c",
                }
            ]
        },

        options: {
            fill: true,
        }
    });


    var mode = 'index';
    var intersect = true;
    var ticksStyle = {
        fontColor: '#495057',
        fontStyle: 'bold'
    }

    var $ReqMonChart = $('#request_per_month_chart')
    var ReqMonChart = new Chart($ReqMonChart, {

        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                'Dec'
            ],
            datasets: [{
                    label: 'Total Requests',
                    type: 'line',
                    data: [<?= implode(",", $requests_per_month) ?>],
                    backgroundColor: 'transparent',
                    borderColor: '#007bff',
                    pointBorderColor: '#007bff',
                    pointBackgroundColor: '#007bff',
                    // label: {
                    //     align: 'end',
                    //     anchor: 'end'
                    // }
                    // pointHoverBackgroundColor: '#007bff',
                    // pointHoverBorderColor    : '#007bff'
                },
                {
                    label: 'Resolved',
                    type: 'line',
                    data: [<?= implode(",", $requests_resolved_per_month) ?>],
                    backgroundColor: 'tansparent',
                    borderColor: '#ced4da',
                    pointBorderColor: '#ced4da',
                    pointBackgroundColor: '#ced4da',
                    fill: false,
                    // label: {
                    //     align: 'start',
                    //     anchor: 'start'
                    // }
                    // pointHoverBackgroundColor: '#ced4da',
                    // pointHoverBorderColor    : '#ced4da'
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            hover: {
                mode: mode,
                intersect: intersect
            },
            legend: {
                display: true
            },
            // scales: {
            //     yAxes: [{
            //         ticks: $.extend({
            //             // beginAtZero : true
            //         }, ticksStyle)
            //     }],
            //     xAxes: [{
            //         display: true,
            //     }]
            // }
        }
    })
    let i = 0;
    var colors = [];
    for (i = 0; i < 20; i++) {
        colors.push(getRandomColor());
    }


    var $ReqBldChart = $('#request_per_building_chart')
    var ReqBldChart = new Chart($ReqBldChart, {

        type: 'bar',
        data: {
            labels: [
                <?= "'" . implode("','", $requests_building_names) . "'" ?>
            ],
            datasets: [{
                data: [<?= implode(",", $requests_per_building) ?>],
                backgroundColor: colors,
                label: 'Total Requests'
            }]
        },
        options: {
            responsive: true,
            legend: {
                display: false
            },
        }
    });






    var $ReqBldChart = $('#building_revenue_chart')
    var ReqBldChart = new Chart($ReqBldChart, {
        type: 'bar',
        data: {
            datasets: [{
                data: [<?= implode(",", $building_revenue_paid) ?>],
                backgroundColor: colors,
                label: 'Revenue'
            }],
            labels: [<?= "'" . implode("','", $building_revenue_names) .
                                "'" ?>]

        },
        options: {
            responsive: true,
            legend: {
                display: false
            },
        }
    });



});
</script>