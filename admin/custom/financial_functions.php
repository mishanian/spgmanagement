<?

namespace PHPMaker2023\spgmanagement; ?>
<style type="text/css">
    @media print {
        .noprint {
            display: none;
        }
    }
</style>
<div class="container ">
    <!--  <h1 style="color: #00e25b"> Financial Functions</h1>-->
    <div class="row ">
        <div class="col-lg-6">
            <div class="card card.text-white.bg-info noprint" style="height: 615px">
                <div class="card-header" style="color: #003399; background-color: #bce8f1!important;">Calculate mortgage payments</div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3">Price of property</div>
                            <div class="col-lg-4"><input type="number" class="form-control" id="property_price" name="property_price" value="1000000" onblur="calc()"></div>
                            <div class="col-lg-2">$</div>

                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3">Down payment</div>
                            <div class="col-lg-4"><input type="number" class="form-control" id="down_payment" name="down_payment" onclick="$('#down_payment_perc').val('');" onblur="calc()" value="0" style="width: 100px"> $
                            </div>
                            <div class="col-lg-4"><input type="number" class="form-control" id="down_payment_perc" name="down_payment_perc" onclick="$('#down_payment').val('');" onblur="calc()" style="width: 100px"> %
                            </div>


                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3">Mortgage amount</div>
                            <div class="col-lg-4"><input type="number" class="form-control" id="mortgage_amount" name="mortgage_amount" value="1000000"></div>
                            <div class="col-lg-2">$</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3">Amortization</div>
                            <div class="col-lg-4"><input type="number" class="form-control" id="amortization" name="amortization" value="25"></div>
                            <div class="col-lg-2">year(s)</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3" align="center">Mortgage rate</div>
                            <div class="col-lg-4"><input type="number" class="form-control" id="mortgage_rate" name="mortgage_rate" value="3.50"></div>
                            <div class="col-lg-2">%</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3" align="center">Payment frequency</div>
                            <div class="col-lg-4">
                                <select id="payment_frequency" name="payment_frequency" onblur="calc()" class="form-control">
                                    <option value="12">Monthly</option>
                                    <option value="52">Weekly</option>
                                    <option value="26">Bi-Weekly</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3" align="center">Payment amount</div>
                            <div class="col-lg-4"><input type="number" class="form-control" id="payment_amount" name="payment_amount" value="0"></div>
                            <div class="col-lg-1">$</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6 col-lg-push-3">
                                <button type="button" class="btn btn-default" onclick="showReport();" id="payment_chart">Payment chart
                                </button>
                            </div>
                        </div>
                    </div>
                    </form>

                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card.bg-danger.text-white noprint">
                <div class="card-header" style="color: #003399; background-color: #F2DEDE!important;">Property Transfer Tax</div>
                <div class="card-body">

                    <div class="form-group">
                        <div class="row">
                            <br>
                            <div class="col-lg-4">Calculation for City of Montreal</div>
                            <div class="col-lg-4"><input type="checkbox" id="calculation_city" name="calculation_city" onclick="taxM()"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3">Price of property</div>
                            <div class="col-lg-4"><input type="number" class="form-control" id="c_property_price" name="c_property_price" value="1000000" onblur="taxM()"></div>
                            <div class="col-lg-2">$</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3">Municipal assessment total</div>
                            <div class="col-lg-4"><input type="number" class="form-control" id="Municipal_assessment" name="Municipal_assessment" value="50000" onblur="taxM()">
                            </div>
                            <div class="col-lg-2">$</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3">Aproximate transfer tax</div>
                            <div class="col-lg-4"><input type="number" class="form-control" id="transfer_tax" name="transfer_tax" value="0" onblur="taxM()"></div>
                            <div class="col-lg-1">$</div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card card.bg-danger.text-white noprint">
                <div class="card-header" style="color: #003399; background-color: #F2DEDE!important;">Calculate borrowing capacity</div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3 ">Amortization</div>
                            <div class="col-lg-4 "><input type="number" class="form-control" id="b_amortization" name="b_amortization" value="25" onblur="borrow()"></div>
                            <div class="col-lg-1 ">year(s)</div>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3 ">Interest rate</div>
                            <div class="col-lg-4 "><input type="number" class="form-control" id="b_mortgage_rate" name="b_mortgage_rate" value="3.45" onblur="borrow()"></div>
                            <div class="col-lg-1 ">%</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3  ">Payment frequency</div>
                            <div class="col-lg-5 ">
                                <select id="b_payment_frequency" name="b_payment_frequency" change="borrow()" class="form-control">
                                    <option value="12">Monthly</option>
                                    <option value="52">Weekly</option>
                                    <option value="26">Bi-Weekly</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3 ">Payment amount</div>
                            <div class="col-lg-4 "><input type="number" class="form-control" id="b_payment_amount" name="b_payment_amount" value="0" onblur="borrow()"></div>
                            <div class="col-lg-1 ">$</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-3 ">Mortgage amount</div>
                            <div class="col-lg-4 "><input type="number" class="form-control" id="b_mortgage_amount" name="b_mortgage_amount" value="0" onblur="borrow()"></div>
                            <div class="col-lg-1 ">$</div>
                        </div>
                    </div>


                </div>
            </div>


        </div>

    </div>
    <div class="col-lg-12" style="display: inline" id="showReport">
        <div class="card card.bg-success.text-white">
            <div class="card-header" style="color: #003399">Monthly payment chart for the amortization period <input type='button' id='btn' value='Print' onclick='printDiv();'></div>
            <div class="card-body" style="overflow: scroll; width: 100%; height: 400px">

                <div class="row" id="tablePrint">
                    <div id="report" class="col-xs-12">
                        <table class="table table-hover">
                            <tr>

                                <th class="text_clr">No</th>
                                <th class="text_clr">Payment</th>
                                <th class="text_clr">Interest</th>
                                <th class="text_clr">Principle</th>
                                <th class="text_clr">Balance</th>

                            </tr>
                            <tbody id="table_paymentChart">
                                <tr>
                                    <td>IDs</td>
                                    <td>333333</td>
                                    <td>656515</td>
                                    <td>236255</td>
                                    <td>2000055</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </form>
    <div class="col-lg-12">

        <b> All amounts are for information only. The exact amounts will be determined by your financial
            institution.<br><br>

            You must purchase mortgage default insurance if your down payment is less than 20 per cent of the purchase
            price of the property. <a href="">Click here</a> for more information.<br><br>

            Increase in transfer duties on property values of $500,000 or more in Montréal <a href=""> Click here</a>
            for more information.</b>
    </div>

</div>

</div>

</div>


<script>
    loadjs.ready("jquery", function() {
        $("#showReport").hide();
    });

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

    function calc() {
        var property_price = $("#property_price").val();
        var down_payment = $("#down_payment").val();
        var down_payment_perc = $("#down_payment_perc").val();
        var compound_period = 12;

        var mortgage_amount = $("#mortgage_amount").val();
        var amortization = $("#amortization").val();
        var mortgage_rate = $("#mortgage_rate").val();
        var payment_frequency = $("#payment_frequency").val();
        var interest_per_payment = (Math.pow((1 + mortgage_rate / compound_period), (compound_period / payment_frequency)) - 1) / 100;
        //alert(payment_frequency);
        if ($.isNumeric(property_price) && ($.isNumeric(down_payment) || $.isNumeric(down_payment_perc))) {
            if ($.isNumeric(down_payment_perc) && down_payment_perc > 0) {
                down_payment = property_price * down_payment_perc / 100;
                $("#down_payment").val(down_payment);
            }
            var mortgage_amount = property_price - down_payment;
            $("#mortgage_amount").val(mortgage_amount);
            //alert(property_price);
        }
        if ($.isNumeric(mortgage_amount) && $.isNumeric(amortization) && $.isNumeric(mortgage_rate)) {
            var interest_per_year = Math.pow((1 + ((mortgage_rate / 100) / 2)), (2 / payment_frequency)) - 1;
            //alert(interest_per_year);
            // payment_amount=Math.round(PMT(interest_per_year, amortization*payment_frequency, -1*mortgage_amount, 0, 0)*100)/100;
            nper = amortization * payment_frequency;

            payment_amount = Math.round(-1 * PMT(interest_per_payment, nper, mortgage_amount) * 100) / 100;
            $("#payment_amount").val(payment_amount);
            //alert(interest_per_payment);
        }
    }

    function PMT(ir, np, pv, fv, type) {
        /*
         * ir   - interest rate per month
         * np   - number of periods (months)
         * pv   - present value
         * fv   - future value
         * type - when the payments are due:
         *        0: end of the period, e.g. end of month (default)
         *        1: beginning of period
         */
        var pmt, pvif;
        fv || (fv = 0);
        type || (type = 0);

        if (ir === 0)
            return -(pv + fv) / np;

        pvif = Math.pow(1 + ir, np);
        pmt = -ir * pv * (pvif + fv) / (pvif - 1);

        if (type === 1)
            pmt /= (1 + ir);

        return pmt;
    }

    function showReport() {
        calc();
        $("#showReport").show();
        var compound_period = 12;
        var amortization = $("#amortization").val();
        var payment_frequency = $("#payment_frequency").val();
        var payment_amount = $("#payment_amount").val();
        var mortgage_amount = $("#mortgage_amount").val();
        var balance = mortgage_amount;

        var mortgage_rate = $("#mortgage_rate").val();
        var interest = mortgage_rate * balance;
        var interest_per_payment = (Math.pow((1 + mortgage_rate / compound_period), (compound_period / payment_frequency)) - 1) / 100;
        for (i = 1; i <= amortization * payment_frequency; i++) {
            interest = Math.round(((interest_per_payment * balance) * 100)) / 100;
            nper = amortization * payment_frequency;
            if (i == amortization * payment_frequency || (payment_amount > Math.round((1 + interest_per_payment) * balance) * 100) / 100) {
                payment = Math.round(((1 + interest_per_payment) * balance) * 100) / 100;

            } else {
                payment = payment_amount;
            }
            var principal = payment_amount - interest;
            principal = Math.round((payment - interest) * 100) / 100;
            balance = Math.round((balance - principal) * 100) / 100;

            var payment_show = numberWithCommas(payment);
            var interest_show = numberWithCommas(interest);
            var principal_show = numberWithCommas(principal);
            var balance_show = numberWithCommas(balance);

            var chart = chart + "<tr><td>" + i + "</td><td>" + payment_show + "</td><td>" + interest_show + "</td><td>" + principal_show + "</td><td>" + balance_show + "</td></tr>\n";
        }
        $("#table_paymentChart").html(chart);
    }

    function borrow() {

        var b_amortization = $("#b_amortization").val();
        var b_mortgage_rate = $("#b_mortgage_rate").val() / 100;
        var b_payment_frequency = $("#b_payment_frequency").val();
        var b_payment_amount = $("#b_payment_amount").val();
        var b_mortgage_amount = $("#b_mortgage_amount").val();

        var b_mortgage_amount = 0;

        if (b_payment_frequency == 52) {
            b_mortgage_amount = 4.3333333 * b_payment_amount * getA(b_amortization * 12, getR(b_mortgage_rate, 2, 12));
        } else if (b_payment_frequency == 26) {
            b_mortgage_amount = 2.16666666 * b_payment_amount * getA(b_amortization * 12, getR(b_mortgage_rate, 2, 12));
        } else {
            b_mortgage_amount = b_payment_amount * getA(b_amortization * b_payment_frequency, getR(b_mortgage_rate, 2, b_payment_frequency));
        }

        if (isNaN(b_mortgage_amount)) {
            $("#b_mortgage_amount").val('');
        } else {
            $("#b_mortgage_amount").val(Math.round(b_mortgage_amount * 100) / 100);
        }

    }

    function getR(nr, cf, pf) {
        var I = nr / cf;
        var i = Math.pow((1 + I), cf) - 1;
        var j = Math.pow((1 + i), (1 / pf)) - 1

        return j;
    }

    function getA(n, rr) {
        var A = (1 - Math.pow((1 + rr), (-1 * n))) / rr;

        return A;

    }

    function taxM() {
        var calculation_city = $("#calculation_city").is(':checked');
        var c_property_price = $("#c_property_price").val();
        var Municipal_assessment = $("#Municipal_assessment").val();
        var transfer_tax = $("#transfer_tax").val();
        //alert(calculation_city);
        if (parseFloat(Municipal_assessment) > parseFloat(c_property_price)) c_property_price = parseFloat(Municipal_assessment);

        if (!isNaN(c_property_price * 1)) {
            if (c_property_price <= 50000) {
                tax = Math.round(c_property_price * 0.005 * 100) / 100;
            } else if (c_property_price <= 250000) {
                tax = Math.round((250 + ((c_property_price - 50000) * 0.01)) * 100) / 100;
            } else if (c_property_price <= 500000 || calculation_city == false) {
                tax = Math.round((2250 + ((c_property_price - 250000) * 0.015)) * 100) / 100;
            } else if (c_property_price <= 1000000) {
                tax = Math.round((6000 + ((c_property_price - 500000) * 0.02)) * 100) / 100;
            } else {
                tax = Math.round((16000 + ((c_property_price - 1000000) * 0.025)) * 100) / 100;
            }
            $("#transfer_tax").val(tax);
        }

    }

    function printDiv() {

        var divToPrint = document.getElementById('tablePrint');

        var newWin = window.open('', 'Print-Window');

        newWin.document.open();

        newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');

        newWin.document.close();

        setTimeout(function() {
            newWin.close();
        }, 10);

    }
</script>