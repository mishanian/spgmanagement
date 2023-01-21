<?
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);
createPayment(2, $DB_con); //'Manual'
createPayment(1, $DB_con); //EPayment

function createPayment($paymentType, $DB_con)
{
    $NumberofPayments = 0;
    $TotalValueofPayments = 0;
    $Upload_Date = date("Y-m-d");
    $FileCreationNumber = 0;
    $LeasePaymentDetailID[] = 0;
//$PaymentDate=date_format(date_create($Upload_Date), "dmy");//"DDMMYY";
    $day = date("d");
 //   $day = 15;
    $lastday = date("t");
    $body = "";
    switch ($day) {
        case 8:
            $Q = 1;
            $FromDate = date("Y-m-1");
            $ToDate = date("Y-m-7");
            break;
        case 15:
            $Q = 2;
            $FromDate = date("Y-m-8");
            $ToDate = date("Y-m-14");
            break;
        case 22:
            $Q = 3;
            $FromDate = date("Y-m-15");
            $ToDate = date("Y-m-21");
            break;
        case $lastday:
            $Q = 4;
            $FromDate = date("Y-m-12");
            $ToDate = date("Y-m-t");
            break;
        default:
            echo("No Need to Send on $day");
            break;

    }

    if (!empty($FromDate)) {
        echo "Start for Q$Q $FromDate - $ToDate<br>";
        $SelectSql = "select * from settings";
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result[0] as $key => $value) {
            $$key = $value;
        }


        $SelectSql = "select max(cast(FD_file AS SIGNED)) as FD_file from financial_distributes";
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $financial_last_fileno = $result[0]['FD_file'];
//echo "LLLL=".$result[0]['FD_file'];
//print_r($result[0]);

        $StartLine = "H";
        $Originator = setLength($financial_originator, 10, " ");; //10
        $PaymentType = "C";
        $CPA = setLength($financial_CPA, 3, " ");; /////////////////////////////////////////////////////https://www.tdcommercialbanking.com/wbb/help/English/wbwEFTCPACodes.html
        $PaymentDueDate = date("dmy");
        $OriginatorShortName = setLength($financial_originator_short, 15, " "); /////////////////////////////////////////////////////
        $FinancialInstitute = setLength($financial_institute, 9, " ");; // TD
        $AccountNo = setLength($financial_accountNo, 12, " ");
        $FileCreationNumber = $financial_last_fileno; // Sequential Start 0001 to 9999 /////////////////////////////////////////////////////
        $Filler = str_repeat(" ", 19);

        if ($FileCreationNumber == 9999) {
            $FileCreationNumber = 0000;
        }
        $FileCreationNumber++;
        $FileCreationNumberString = setLength($FileCreationNumber, 4, "0");


// Details
        $Counter = 0;
        if ($paymentType == 1) { //ePayment
            $paymentTypeWhere =  " payment_type_id in (1) ";
        }

        if ($paymentType == 2) { //Manual
            $paymentTypeWhere = "  payment_type_id in (3,4,5) ";
        }


            $SelectSql = "SELECT lease_payment_detail_id , DBCR, due_date, amount, description, tenant_ids, VT.comments, VT.employee_id, balance, charge, payment, OI.full_name AS owner_name,  financial_institution, financial_accountNo FROM view_tenant_statement VT
LEFT JOIN owner_infos OI ON OI.owner_id=VT.owner_id WHERE  sent_to_bank=0 and  $paymentTypeWhere and DBCR='c' and due_date BETWEEN '" . $FromDate . "' and '" . $ToDate . "'"; //


        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
//Header
            $body .= $StartLine . $Originator . $PaymentType . $CPA . $PaymentDueDate . $OriginatorShortName . $FinancialInstitute . $AccountNo . $FileCreationNumberString . $Filler . "\n<br>";

            foreach ($result as $row) {
                foreach ($row as $key => $value) {
                    $$key = $value;
                }
                $LeasePaymentDetailID[] = $lease_payment_detail_id;
                $StartLine = "D";
                $PayeeName = setLength($owner_name, 23, " ");
//        echo "----".$PayeeName."---";
                $PaymentDueDate = date("dmy");
                $Originator_Reference_Number = setLength("Beaver AIT", 19, " ");
                $FinancialInstitutionID = setLength($financial_institution, 9, "0");
                $PayorAccountNumber = setLength($financial_accountNo, 12, " ");
                $PaymentAmount = setLength($amount, 10, "0");

                $body .= $StartLine . $PayeeName . $PaymentDueDate . $Originator_Reference_Number . $FinancialInstitutionID . $PayorAccountNumber . $PaymentAmount . "\n<br>";
                $NumberofPayments++;
                $TotalValueofPayments = $TotalValueofPayments + $PaymentAmount;
                $Counter++;
            }

// Tailer
            $StartLine = "T";
            $TotalNumberofPayments = setLength($NumberofPayments, 8, "0");
            $TotalValueofPayments = setLength($TotalValueofPayments, 14, "0");
            $Filler = str_repeat(" ", 57);

            $body .= $StartLine . $TotalNumberofPayments . $TotalValueofPayments . $Filler;

            $SelectSql = "update lease_payment_details set sent_to_bank=1 where id in (" . implode(",", $LeasePaymentDetailID) . ")";
            //  die($SelectSql);
            $statement = $DB_con->prepare($SelectSql);
            $statement->execute();


        }
        $SelectSql = "insert into financial_distributes (FD_date, FD_Q, FD_records, FD_file, FD_from_date, FD_to_date, FD_leasePD_ids, FD_type) values ('" . $Upload_Date . "', $Q, $Counter, '$FileCreationNumber', '$FromDate', '$ToDate', '" . implode(", ", $LeasePaymentDetailID) . "' ,'$paymentType')";
        echo $SelectSql."<br>";
        //   die($SelectSql);
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();


    } // if (!empty($From_Date))

    echo "<hr>" . $body . "<hr>";
    $myfile = fopen("../files/financials/$FileCreationNumber", "w") or die("Unable to open file!");
    fwrite($myfile, str_replace("<br>", "", $body));
    fclose($myfile);

}


function setLength($theField, $len, $spacer)
{
//    echo "$theField-$spacer-<br>";
    if ($spacer == " ") {
        return substr(substr($theField, 0, $len) . str_repeat($spacer, $len - strlen($theField)), 0, $len);
    } else {
        return str_repeat($spacer, $len - strlen($theField)) . substr(substr($theField, 0, $len), 0, $len);
    }
}

?>