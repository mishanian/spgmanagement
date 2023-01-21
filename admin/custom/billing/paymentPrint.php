<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
          integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
            integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"
            integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>

</head>

<style>
    body{
        font-size:18px !important;
    }
    .table td, .table th {
        border: none !important;
        padding: 3px !important;
    }

    @media print {
        .no-print, .no-print * {
            display: none !important;
        }
    }
</style>

<?php

?>
<?php
/**
 * Created by PhpStorm.
 * User: Beaver2
 * Date: 2018-06-14
 * Time: 14:53
 */

if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

$dbClass = $path . 'Class.Bill.php';
include_once($dbClass);
$DB_bill = new Bill($DB_con);

$paymentPrint = $DB_bill->getPaymentById($_POST['id']);
$billPaid = $DB_bill->getBillByID($paymentPrint['bill_id']);

$vendors = $DB_vendor->getVendorsList();

function numToWords($num)
{

    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    return $f->format($num);

}

 ?>
<div class="no-print">
    <button onclick="window.print()">Print</button>
    <!--  <button><a href="javascript:demoFromHTML()" class="button">Download PDF</a></button>-->
</div>

<div id="content">
    <div class="container-fluid">
        <br/>
        <br/>
        <br/>
        <div class="row">
            <div class="col-md-10"></div>
            <div class="col-md-2">
                DATE  <?php echo $paymentPrint['payment_date']; ?>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-9">
                ******<?php echo numToWords($paymentPrint['amount']); ?>
            </div>
            <div class="col-md-2">
                ***<?php echo $paymentPrint['amount']; ?>
            </div>
        </div>
        <br/>
        <br/>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-9">
                <?php
                $vendorInfo = $DB_vendor->getVendorInfo($billPaid['vendor_id']);
                echo $vendorInfo['company_name'];
                ?>
                <br/>
                <?php
                echo $vendorInfo['address'];
                ?>
            </div>
            <div class="col-md-2">
            </div>
        </div>
        <br/>
        <br/>
        <br/>
        <div class="row">
            <div class="col-md-10">
                <p>MEMO  <?php echo $paymentPrint['memo']; ?></p>
            </div>
            <div class="col-md-2">

            </div>
        </div>
        <br/>
        <br/>

        <table class="table">
            <thead>
            <tr>
                <th scope="col"></th>
                <th scope="col"><?php
                    $result = $DB_vendor->getVendorInfo($billPaid['vendor_id']);
                    echo $result['company_name'];
                    ?></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"><?php echo $paymentPrint['payment_date'] ?></th>
                <th scope="col"></th>
            </tr>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Type</th>
                <th scope="col">Reference</th>
                <th scope="col">Original Amount</th>
                <th scope="col">Balance Due</th>
                <th scope="col">Discount</th>
                <th scope="col">Payment</th>
            </tr>
            </thead>
            <tbody>
            <tr style="margin-bottom: 200px !important">
                <td><?php echo $billPaid['bill_date'] ?></td>
                <td>Bill</td>
                <td><?php echo $paymentPrint['memo']; ?></td>
                <td><?php echo $billPaid['grand_total']; ?></td>
                <td><?php echo $billPaid['grand_total'] - $billPaid['paid_amount']; ?></td>
                <td>Cheque Amount</td>
                <td><?php echo $paymentPrint['amount']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php echo $paymentPrint['amount']; ?></td>
            </tr>
            </tbody>
        </table>

        <div style="padding-top:300px !important;"></div>

        <table class="table">
            <thead>
            <tr>
                <th scope="col"></th>
                <th scope="col"><?php
                    $result = $DB_vendor->getVendorInfo($billPaid['vendor_id']);
                    echo $result['company_name'];
                    ?></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"><?php echo $paymentPrint['payment_date'] ?></th>
                <th scope="col"></th>
            </tr>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Type</th>
                <th scope="col">Reference</th>
                <th scope="col">Original Amount</th>
                <th scope="col">Balance Due</th>
                <th scope="col">Discount</th>
                <th scope="col">Payment</th>
            </tr>
            </thead>
            <tbody>
            <tr style="margin-bottom: 200px !important">
                <td><?php echo $billPaid['bill_date'] ?></td>
                <td>Bill</td>
                <td><?php echo $paymentPrint['memo']; ?></td>
                <td><?php echo $billPaid['grand_total']; ?></td>
                <td><?php echo $billPaid['grand_total'] - $billPaid['paid_amount']; ?></td>
                <td>Cheque Amount</td>
                <td><?php echo $paymentPrint['amount']; ?></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php echo $paymentPrint['amount']; ?></td>
            </tr>
            </tbody>
        </table>
        <div style="padding-top:300px !important;"></div>
   <div class="row">
       <div class="col-md-6">

       </div>
       <div class="col-md-6">
           <div style="text-align:right"><?php echo $paymentPrint['amount']; ?></div>
       </div>
   </div>
    </div>
</div>
<script>
    function demoFromHTML() {
        var pdf = new jsPDF('p', 'pt', 'letter');
        // source can be HTML-formatted string, or a reference
        // to an actual DOM element from which the text will be scraped.
        source = $('#content')[0];

        // we support special element handlers. Register them with jQuery-style
        // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
        // There is no support for any other type of selectors
        // (class, of compound) at this time.
        specialElementHandlers = {
            // element with id of "bypass" - jQuery style selector
            '#bypassme': function (element, renderer) {
                // true = "handled elsewhere, bypass text extraction"
                return true
            }
        };
        margins = {
            top: 80,
            bottom: 60,
            left: 40,
            width: 522
        };
        // all coords and widths are in jsPDF instance's declared units
        // 'inches' in this case
        pdf.fromHTML(
            source, // HTML string or DOM elem ref.
            margins.left, // x coord
            margins.top, { // y coord
                'width': margins.width, // max width of content on PDF
                'elementHandlers': specialElementHandlers
            },

            function (dispose) {
                // dispose: object with X, Y of the last line add to the PDF
                //          this allow the insertion of new lines after html
                pdf.save('Test.pdf');
            }, margins
        );
    }
</script>