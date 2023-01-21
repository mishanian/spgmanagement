<?

namespace PHPMaker2023\spgmanagement;

use \PDO;
use \Analyze;

if (empty($_SESSION)) {
    session_start();
}
$company_id = $_SESSION['company_id'];
//echo $company_id;
?>
<meta charset="UTF-8">
<title>Expense Analyze</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="custom/css/analyze_styles.css">
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>-->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
<script type="text/javascript" src="custom/js/Chart.PieceLabel.min.js"></script>
<!--    <script src="https://codepen.io/anon/pen/aWapBE.js"></script>-->
<?php
include_once('../pdo/dbconfig.php');
include_once('../pdo/Class.Analyze.php');

$DB_analyze = new Analyze($DB_con);

?>

<div class="container" id="myDiv">
    <div class="row">
        <div class="col-lg-12" id="myDiv2">
            <!--        <img src="admin/phpImages/--><? //=$_SESSION['logo']
                                                        ?>
            <!--">-->
            <h4 align="center" style="background-color: #bbe530"><b>Show Data</b></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 padding-0 ">
            <div class="panel panel-default">
                <div class="panel-heading headcolfont">
                    <h4>Vendor</h4>
                </div>
                <div class="panel-body scroll heightPanelBody ">

                    <?php
                    $Vendor_id_names = $DB_analyze->getAllVendorNames();
                    for ($i = 0; $i < count($Vendor_id_names); $i++) {
                        $VendorName = $Vendor_id_names[$i]['company_name'];
                        $VendorID = $Vendor_id_names[$i]['vendor_id'];
                        echo "<label><input type=checkbox value='$VendorID' id='vendor_id' name='vendor_id[]' > $VendorName </label><br>";
                    } ?>
                </div>
            </div>
        </div>

        <div class="col-lg-3 padding-0 ">
            <div class="panel panel-default">
                <div class="panel-heading headcolfont ">
                    <h4>Vendor Type</h4>
                </div>
                <div class="panel-body scroll heightPanelBody">
                    <?php
                    $vendor_types = $DB_analyze->getAllVendorTypeNames();
                    for ($i = 0; $i < count($vendor_types); $i++) {
                        $vendor_type_name = $vendor_types[$i]['name'];
                        $vendor_type_id = $vendor_types[$i]['id'];
                        echo "<label><input type=checkbox value='$vendor_type_id' name='vendor_type_id'  id='vendor_type_id' > $vendor_type_name </label><br>";
                    } ?>
                </div>
            </div>

        </div>
        <div class="col-lg-3 padding-0">
            <div class="panel panel-default">
                <div class="panel-heading headcolfont">
                    <h4>Job Type</h4>
                </div>
                <div class="panel-body scroll heightPanelBody">
                    <?php
                    $request_types = $DB_analyze->getAllRequestTypes();
                    for ($i = 0; $i < count($request_types); $i++) {
                        $request_type_name = $request_types[$i]['name'];
                        $request_type_id = $request_types[$i]['id'];
                        echo "<label><input type=checkbox value='$request_type_id' name='request_type_id'  id='request_type_id' > $request_type_name </label><br>";
                    } ?>
                </div>
            </div>
        </div>
        <div class="col-lg-3 padding-0">
            <div class="panel panel-default">
                <div class="panel-heading headcolfont">
                    <h4>Expenses</h4>
                </div>
                <div class="panel-body heightPanelBody">
                    <div class="row">
                        <b>Number of Expenses:</b>
                        <h3 id="NoPayments" style="text-align: center"></h3>
                        <hr>
                    </div>
                    <div class="row">
                        <b>Total Expenses:</b>
                        <h2 id="TotalExpense" style="text-align: center"></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-2 padding-0">
            <div class="panel panel-default">
                <div class="panel-heading headcolfont">
                    <h4>Buildings</h4>
                </div>
                <div class="panel-body scroll heightPanelBody2 ">

                    <?php
                    $building_id_names = $DB_analyze->getAllBdIdNameByCompany($_SESSION['company_id']);
                    for ($i = 0; $i < count($building_id_names); $i++) {
                        $BuildingName = $building_id_names[$i]['building_name'];
                        $BuildingID = $building_id_names[$i]['building_id'];
                        echo "<label><input type=checkbox value='$BuildingID'  id='building_id' name='building_id[]' onchange='show_units();' > $BuildingName </label><br>";
                    } ?>
                </div>
            </div>
        </div>
        <div class="col-lg-2 padding-0">
            <div class="panel panel-default ">
                <div class="panel-heading headcolfont">
                    <h4>Search Unit</h4>
                </div>
                <div class="panel-body scroll heightPanelBody2 ">

                    <div id="show_units"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 padding-0">
            <div class="panel panel-default">
                <div class="panel-heading headcolfont">
                    <h4>Expense Details&nbsp;
                        <a href="custom/analyze_getExpense_excel.php" style="color: #562922">
                            <i class="fa fa-lg fa-file-excel-o" aria-hidden="true">&nbsp;</i> Export to excel</a>
                    </h4>
                </div>
                <div class="panel-body scroll heightPanelBody2">
                    <div id="body_payment">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal_wait"></div>
    <div class="row" id="chartContainer1">
        <div class="col-lg-6"><canvas id="myChart"></canvas></div>
        <div class="col-lg-6"><canvas id="myChart2"></canvas></div>
    </div>
    <div class="row" id="chartContainer2">
        <div class="col-lg-12"><canvas id="myChart3"></canvas></div>
    </div>
    <script>
        loadjs.ready("jquery", function() {
            ajaxStart: function a() {
                $("body").addClass("loading");
            };
            ajaxStop: function b() {
                $("body").removeClass("loading");
            };


            changeResult();
            $("input:checkbox").change(function() {
                changeResult();
            });

            function changeResult() {

                table = "<table class=\"table table-bordered\" id=\"body_payment\">\n" +
                    "    <thead>\n" +
                    "    <tr>\n" +
                    "        <th class=\"text_clr\">Date</th>\n" +
                    "        <th class=\"text_clr\">Job Type</th>\n" +
                    "        <th class=\"text_clr\">Property</th>\n" +
                    "        <th class=\"text_clr\">Location</th>\n" +
                    "        <th class=\"text_clr\">Price</th>\n" +
                    "        <th class=\"text_clr\">Description</th>\n" +
                    "        <th class=\"text_clr\">Vendor</th>\n" +
                    "        <th class=\"text_clr\">VendorType</th>\n" +
                    "        <th class=\"text_clr\">Store</th>\n" +
                    "\n" +
                    "\n" +
                    "    </tr>\n" +
                    "    </thead>\n" +
                    "    <tbody>";

                building_ids = getCheckBox('building_id');
                building_ids_array = getCheckBoxArray('building_id');
                apartment_id = getCheckBox('apartment_id');
                vendor_id = getCheckBox('vendor_id');
                vendor_type_id = getCheckBox('vendor_type_id');
                online_shopping = getCheckBox('online_shopping');
                request_type_id = getCheckBox('request_type_id');
                rnd = Math.random();

                $.ajax({
                    type: "GET",
                    data: {
                        bid: building_ids,
                        uid: apartment_id,
                        vid: vendor_id,
                        vtid: vendor_type_id,
                        oshid: online_shopping,
                        reqid: request_type_id,
                        rnd: rnd

                    },
                    url: "custom/analyze_getExpense_json.php",
                    context: document.body
                }).done(function(result) {
                    var jsonData = JSON.parse(result);
                    jsonDataPayment = jsonData.Payment;
                    jsonDataBuildingExpense = jsonData.Buildings;
                    jsonDataBuildingMonthlyExpense = jsonData.Monthly;

                    TotalExpense = 0;
                    for (i = 0; i < jsonDataPayment.length; i++) {
                        table = table + "<tr>";
                        table = table + "<td>" + jsonDataPayment[i]['invoice_date'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['request_type_name'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['building_name'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['UnitNumber'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['paid_amount'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['description'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['vendor_name'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['vendor_type_name'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['Store'] + "</td>";

                        //         table = table +"<td>"+ jsonDataPayment[i]['TenantComments'] +"</td>";
                        table = table + "</tr>";
                        TotalExpense = parseFloat(TotalExpense) + parseFloat(jsonDataPayment[i]['paid_amount']);
                    }


                    table = table + "</table>";
                    $("#body_payment").html(table);


                    var res = result.split("ResultNoPayments=");
                    numbers = res[1];
                    // NoPayments = numbers.split("&");
                    $("#NoPayments").html(i);
                    $("#TotalExpense").html(Math.round(TotalExpense * 100) / 100);


                    ///
                    var months = [];
                    var years = [];
                    var building_names_array = [];
                    //            alert(building_name_monthly_all);
                    <?
                    $building_id_names = $DB_analyze->getAllBdIdNameByCompany($company_id);
                    for ($i = 0; $i < count($building_id_names); $i++) {
                        $BuildingName = $building_id_names[$i]['building_name'];
                        $BuildingID = $building_id_names[$i]['building_id'];
                        echo "building_names_array[$BuildingID]='$BuildingName';\n";
                    }
                    ?>
                    //
                    var building_data = [];
                    var building_names = [];
                    for (i = 0; i < jsonDataBuildingExpense.length; i++) { //
                        building_data.push(jsonDataBuildingExpense[i]['total']);
                        building_names.push(jsonDataBuildingExpense[i]['building_name']);
                    }

                    var building_data_monthly = [];
                    var building_name_monthly = [];
                    var building_data_monthly_all = [];
                    var building_name_monthly_all = [];

                    for (b = 0; b < building_ids_array.length; b++) {
                        building_data_monthly = [];
                        building_name_monthly = [];
                        for (month = 1; month <= 12; month++) {
                            if (jsonDataBuildingMonthlyExpense.hasOwnProperty(building_ids_array[b]) && jsonDataBuildingMonthlyExpense[building_ids_array[b]].hasOwnProperty(month)) {
                                amount = jsonDataBuildingMonthlyExpense[building_ids_array[b]][month][0];
                            } else {
                                amount = 0;
                                //     buildling_name="-";
                            }

                            buildling_name = building_names_array[building_ids_array[b]];
                            // alert(buildling_name);
                            building_data_monthly.push(amount);
                            building_name_monthly.push(buildling_name);
                        }
                        building_data_monthly_all.push(building_data_monthly);
                        building_name_monthly_all.push(buildling_name);
                    }
                    // alert(building_data_monthly_all);

                    makeChart(building_data, building_names, building_data_monthly_all, building_name_monthly_all);

                });
            }

            function removeData(chart) {
                chart.data.labels.pop();
                chart.data.datasets.forEach((dataset) => {
                    dataset.data.pop();
                });
                chart.update();
            }

            function makeChart(building_data, building_names, building_data_monthly_all, building_name_monthly_all) {
                month_names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                if (myPieChart != undefined || myPieChart != null) {
                    myPieChart.destroy();
                }
                $("#chartContainer1").html("<div class=\"col-lg-6\"><canvas id=\"myChart\"></canvas></div>\n" +
                    "        <div class=\"col-lg-6\"><canvas id=\"myChart2\"></canvas></div>");
                $("#chartContainer2").html("<div class=\"col-lg-12\"><canvas id=\"myChart3\"></canvas></div>\n");


                var ctx = $("#myChart");
                var ctx2 = $("#myChart2");
                var ctx3 = $("#myChart3");

                var font_color = [];
                for (i = 0; i < building_data.length; i++) { //
                    font_color.push('white');
                }
                data = {
                    datasets: [{
                        data: building_data,
                        label: "Analyze",
                        // backgroundColor: palette('tol', building_data.length).map(function (hex) {
                        //     return '#' + hex;
                        // })
                        backgroundColor: [getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor()]
                    }],
                    labels: building_names
                };
                var myPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: data,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'left'
                        },
                        pieceLabel: {
                            render: 'percentage',
                            fontColor: font_color,
                            fontStyle: "bold",
                            fontSize: 14,
                            borderWidth: 100,
                            precision: 2
                        }
                    }
                });
                var mybarChart = new Chart(ctx2, {
                    type: 'bar',
                    data: data,
                    options: {
                        legend: {
                            display: false
                        }
                    }
                });

                var datasetLine = [];
                //alert(building_data_monthly_all);
                for (i = 0; i < building_data_monthly_all.length; i++) {

                    datasetLine.push({
                        label: building_name_monthly_all[i],
                        data: building_data_monthly_all[i],
                        fill: false,
                        backgroundColor: getRandomColor(),
                        borderColor: getRandomColor()
                    });
                }
                data3 = {
                    datasets: datasetLine,
                    labels: month_names
                };

                var myLineChart = new Chart(ctx3, {
                    type: 'line',
                    data: data3,
                    options: {
                        responsive: true,
                        title: {
                            display: false,
                            text: 'Building Chart'
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Income'
                                }
                            }]
                        }
                    }
                });
            }

            function getRandomColor() {
                var letters = '0123456789ABCDEF'.split('');
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }


            function show_units() {
                building_id = getCheckBox('building_id');
                $.ajax({
                    type: "GET",
                    data: {
                        bid: building_id
                    },
                    url: "custom/analyze_show_units.php",
                    context: document.body
                }).done(function(result) {
                    $("#show_units").html(result);

                });
            }

            function getCheckBox(checkBoxName) {
                var val = [];
                $("#" + checkBoxName + ":checked").each(function(i) {
                    val[i] = $(this).val();
                });
                return val.toString();
            }

            function getCheckBoxArray(checkBoxName) {
                var val = [];
                $("#" + checkBoxName + ":checked").each(function(i) {
                    val[i] = $(this).val();
                });
                return val;
            }

        });
    </script>