<?

namespace PHPMaker2023\spgmanagement;

use \PDO;
use \Analyze;

if (empty($_SESSION)) {
    session_start();
}
$company_id = $_SESSION['company_id'];
//echo $company_id;
$root = dirname(__DIR__);
?>

<meta name="viewport" content="width=device-width, initial-scale=1">
<?

?>
<link rel="stylesheet" href="custom/css/analyze_styles.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
<script type="text/javascript" src="custom/js/Chart.PieceLabel.min.js"></script>
<!--    <script src="https://codepen.io/anon/pen/aWapBE.js"></script>-->


<?php

include_once('../pdo/dbconfig.php');
include_once('../pdo/Class.Analyze.php');

$DB_analyze = new Analyze($DB_con);

?>

<div class="container" id="myDiv" style="background-color: white!important;">
    <div class="row">

        <div class="col-lg-12" id="myDiv2">
            <!--        <img src="admin/phpImages/--><? //=$_SESSION['logo']
                                                        ?>
            <!--">-->
            <h4 align="center" style="background-color: #bbe530"><b>Show Data </b></h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-2 padding-0 ">
            <div class="panel panel-default">
                <div class="panel-heading headcolfont">
                    <h4>Province</h4>
                </div>
                <div class="panel-body scroll heightPanelBody ">

                    <?php
                    $province_id_names = $DB_analyze->getProvince();
                    for ($i = 0; $i < count($province_id_names); $i++) {
                        $ProvinceName = $province_id_names[$i]['name'];
                        $ProvinceID = $province_id_names[$i]['id'];
                        echo "<label><input type=checkbox value='$ProvinceID' id='province_id' name='province_id[]' > $ProvinceName </label><br>";
                    } ?>
                </div>
            </div>
        </div>

        <div class="col-lg-2 padding-0">
            <div class="panel panel-default ">
                <div class="panel-heading headcolfont">
                    <h4>Payment Type</h4>
                </div>
                <div class="panel-body scroll heightPanelBody">
                    <?php
                    $payment_types = $DB_analyze->getAllPaymentTypes();
                    for ($i = 0; $i < count($payment_types); $i++) {
                        $Payment_types = $payment_types[$i]['name'];
                        $PaymentID = $payment_types[$i]['id'];

                        echo "<label><input type=checkbox value='$PaymentID' name='payment_type_id'  id='payment_type_id'> $Payment_types </label><br>";
                    } ?>
                </div>

            </div>
        </div>
        <div class="col-lg-2 padding-0 ">
            <div class="panel panel-default">
                <div class="panel-heading headcolfont ">
                    <h4>Payment Method</h4>
                </div>
                <div class="panel-body scroll heightPanelBody">

                    <?php
                    $payment_methods = $DB_analyze->getAllPaymentMethods();
                    for ($i = 0; $i < count($payment_methods); $i++) {
                        $Payment_methods = $payment_methods[$i]['name'];
                        $PaymentID = $payment_methods[$i]['id'];
                        echo "<label><input type=checkbox value='$PaymentID' name='payment_method_id'  id='payment_method_id' > $Payment_methods </label><br>";
                    } ?>
                </div>
            </div>
        </div>

        <div class="col-lg-2 padding-0">
            <div class="panel panel-default panel">
                <div class="panel-heading headcolfont">
                    <h4>Time Stamp Year And Month</h4>
                </div>
                <div class="panel-body scroll heightPanelBody">
                    <b>Select Year -> Month</b>
                    <div class="checkbox">
                        <label><input type="checkbox"> <b>Select All</b></label>
                    </div>
                    <? $months = array(
                        1 => 'Jan.', 2 => 'Feb.', 3 => 'Mar.', 4 => 'Apr.', 5 => 'May', 6 => 'Jun.', 7 => 'Jul.', 8 => 'Aug.',
                        9 => 'Sep.', 10 => 'Oct.', 11 => 'Nov.', 12 => 'Dec.'
                    ); ?>

                    <? for ($i = date("Y") - 3; $i < date("Y") + 1; $i++) { ?>
                        <div class="checkbox">
                            <label><input type="checkbox" value='<?= $i ?>' name='year_due_date' id='year_due_date'>
                                <b><?= $i ?></b></label><br>

                            <? for ($j = 1; $j <= 12; $j++) { ?>
                                &nbsp;&nbsp;&nbsp; <label><input type="checkbox" value='<?= $i . "-" . $j ?>' name='month_due_date' id='month_due_date'>
                                    <b><?= $months[$j] ?></b></label>
                            <? } ?>
                        </div>
                    <? } ?>


                </div>
            </div>
        </div>

        <div class="col-lg-2 padding-0">
            <div class="panel panel-default panel">
                <div class="panel-heading headcolfont">
                    <h4>Size Type</h4>
                </div>
                <div class="panel-body scroll heightPanelBody">
                    <?php
                    $size_types_names = $DB_analyze->getAllSizeTypeNames();
                    for ($i = 0; $i < count($size_types_names); $i++) {
                        $SizeTypeName = $size_types_names[$i]['name'];
                        $SizeTypeID = $size_types_names[$i]['id'];
                        echo "<label><input type=checkbox value='$SizeTypeID'  id='size_type_id' name='size_type_id[]'> $SizeTypeName </label><br>";
                    } ?>
                </div>
            </div>
        </div>
        <div class="col-lg-2 padding-0">
            <div class="panel panel-default">
                <div class="panel-heading headcolfont">
                    <h4>Payments</h4>
                </div>
                <div class="panel-body heightPanelBody">
                    <div class="row">
                        <b>Number of Payments:</b>
                        <h3 id="NoPayments" style="text-align: center"></h3>
                        <hr>
                    </div>
                    <div class="row">
                        <b>Total Payments:</b>
                        <h2 id="TotalPaid" style="text-align: center"></h2>
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
                    $building_id_names = $DB_analyze->getAllBdIdNameByCompany($company_id);
                    for ($i = 0; $i < count($building_id_names); $i++) {
                        $BuildingName = $building_id_names[$i]['building_name'];
                        $BuildingID = $building_id_names[$i]['building_id'];
                        echo "<label><input type=checkbox value='$BuildingID'  id='building_id' name='building_id[]' onchange='show_units($BuildingID);' > $BuildingName </label><br>";
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
            <div class="panel panel-default ">
                <div class="panel-heading headcolfont">
                    <h4>Payment Details &nbsp;
                        <a href="custom/analyze_getRental_excel.php" style="color: #562922">
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
                    "        <th class=\"text_clr\">Timestamp</th>\n" +
                    "        <th class=\"text_clr\">Building Name</th>\n" +
                    "        <th class=\"text_clr\">Unit</th>\n" +
                    "        <th class=\"text_clr\">Paid Amount</th>\n" +
                    // <th class=\"text_clr\">Deposit</th>\n" +

                    "        <th class=\"text_clr\">Date Paid</th>\n" +
                    "        <th class=\"text_clr\">Payment Author</th>\n" +
                    "        <th class=\"text_clr\">Payment Method</th>\n" +
                    "        <th class=\"text_clr\">Size Type</th>\n" +
                    "        <th class=\"text_clr\">Comments</th>\n" +
                    "        <th class=\"text_clr\">Tenant Comments</th>\n" +
                    "    </tr>\n" +
                    "    </thead>\n" +
                    "    <tbody>";

                building_ids = getCheckBox('building_id');
                building_ids_array = getCheckBoxArray('building_id');
                apartment_id = getCheckBox('apartment_id');
                payment_method_id = getCheckBox('payment_method_id');
                payment_type_id = getCheckBox('payment_type_id');
                year_due_date = getCheckBox('year_due_date');
                month_due_date = getCheckBox('month_due_date');
                province_id = getCheckBox('province_id');
                size_type_id = getCheckBox('size_type_id');
                rnd = Math.random();

                $.ajax({
                    type: "GET",
                    data: {
                        bid: building_ids,
                        uid: apartment_id,
                        pmid: payment_method_id,
                        ptid: payment_type_id,
                        yid: year_due_date,
                        mid: month_due_date,
                        provid: province_id,
                        sid: size_type_id,
                        rnd: rnd
                    },
                    url: "custom/analyze_getRental_json.php",
                    context: document.body
                }).done(function(result) {
                    var jsonData = JSON.parse(result);
                    jsonDataPayment = jsonData.Payment;
                    jsonDataBuildingIncome = jsonData.Buildings;
                    jsonDataBuildingMonthlyIncome = jsonData.Monthly;
                    TotalPaid = 0;

                    for (i = 0; i < jsonDataPayment.length; i++) {
                        table = table + "<tr>";
                        table = table + "<td>" + jsonDataPayment[i]['DueDate'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['BuildingName'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['UnitNumber'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['PaidAmount'] + "</td>";
                        // table = table +"<td>"+ jsonDataPayment[i]['Deposit'] +"</td>";

                        table = table + "<td>" + jsonDataPayment[i]['DueDate'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['TenantNames'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['PaymentMethod'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['SizeTypeName'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['Comments'] + "</td>";
                        table = table + "<td>" + jsonDataPayment[i]['TenantComments'] + "</td>";
                        table = table + "</tr>";
                        TotalPaid = parseFloat(TotalPaid) + parseFloat(jsonDataPayment[i]['PaidAmount']);
                    }
                    TotalPaid = (Math.round(TotalPaid * 100) / 100).toFixed(2);
                    table = table + "</table>";
                    $("#body_payment").html(table);
                    var res = result.split("ResultNoPayments=");
                    numbers = res[1];
                    // NoPayments = numbers.split("&");
                    $("#NoPayments").html(i);
                    $("#TotalPaid").html(TotalPaid);

                    //////////////////////////////////////
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
                    //////////////////////////////////////
                    var building_data = [];
                    var building_names = [];
                    for (i = 0; i < jsonDataBuildingIncome.length; i++) { //
                        building_data.push(jsonDataBuildingIncome[i]['PaidAmount']);
                        building_names.push(jsonDataBuildingIncome[i]['building_name']);
                    }

                    // alert(building_ids);
                    //   building_ids = 119;

                    var building_data_monthly = [];
                    var building_name_monthly = [];
                    var building_data_monthly_all = [];
                    var building_name_monthly_all = [];

                    for (b = 0; b < building_ids_array.length; b++) {
                        building_data_monthly = [];
                        building_name_monthly = [];
                        //   alert("b="+b+" id="+building_ids_array[b]);
                        for (month = 1; month <= 12; month++) {
                            if (jsonDataBuildingMonthlyIncome.hasOwnProperty(building_ids_array[b]) && jsonDataBuildingMonthlyIncome[building_ids_array[b]].hasOwnProperty(month)) {
                                amount = jsonDataBuildingMonthlyIncome[building_ids_array[b]][month][0];
                                //    buildling_name= jsonDataBuildingMonthlyIncome[building_ids_array[b]][month][1];
                            } else {
                                amount = 0;
                                //     buildling_name="-";
                            }

                            buildling_name = building_names_array[building_ids_array[b]];
                            building_data_monthly.push(amount);
                            building_name_monthly.push(buildling_name);
                        }
                        //     alert(buildling_name);
                        //buildling_name = jsonDataBuildingMonthlyIncome[building_ids_array[b]][1][1];
                        building_data_monthly_all.push(building_data_monthly);
                        building_name_monthly_all.push(buildling_name);
                    }


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
                        //backgroundColor:palette('tol', building_data.length).map(function(hex) {
                        //    return '#' + hex;
                        //})
                        backgroundColor: [getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor(), getRandomColor()]
                    }],
                    // These labels appear in the legend and in the tooltips when hovering different arcs
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

                ////  if (getCheckBoxChart)
                //   var mybarChart = new Chart(ctx,{
                //       type: 'bar',
                //       data: data,
                //       options: {
                //           pieceLabel: {
                //               render: 'percentage',
                //               fontColor: font_color,
                //               borderWidth: 100,
                //               precision: 2
                //           }
                //       }
                //   });
            }

            function getRandomColor() {
                var letters = '0123456789ABCDEF'.split('');
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }

            function show_units(building_ids) {
                $.ajax({
                    type: "GET",
                    data: {
                        bid: building_ids
                    },
                    url: "custom/analyze_show_units.php",
                    context: document.body
                }).done(function(result) {
                    $("#show_units").html(result);

                });
            }

            $('input[name="CheckBoxChart"]').click(function() {});

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