<html>

<head>
    <style>
    body {
        font-family: Verdana;
        font-size: 10pt
    }
    </style>
</head>

<body>

    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    set_time_limit(240);
    $startExec = microtime(true);
    //include 'sendSMSEmail.php';
    if (strpos(getcwd(), "custom") == false) {
        $path = "../pdo/";
    } else {
        $path = "../../pdo/";
    }
    $file = $path . 'dbconfig.php';
    include_once($file);
    include_once('invoice_receipt/Class.Invoice.php');
    $last_id = 0;
    // Write in DB
    try {
        $SelectSql = "insert into cron_jobs (cron_date,result) VALUES ('" . date("Y-m-d H:i:s") . "', 'started on " . __FILE__ . "') ";
        // echo "$SelectSql<br>";
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        $last_id = $DB_con->lastInsertId();
        // echo "<p>last_id=$last_id</p>";
        //    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }



    /***
     * Cron to Update  the status of the Lease to Active - Auto Renewal if the notice date is set and the renewal gap value is satisfied.
     ***/
    include_once("cron_scripts/lease_pending_ar.php");

    // die();
    try {
        $SelectSql = "UPDATE lease_payments SET payment_status_id=3 WHERE outstanding=0 and payment_status_id NOT IN (4,7) AND due_date > DATE_SUB(NOW(),INTERVAL 1 YEAR)  "; // Paid
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        //    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    try {
        $SelectSql = "UPDATE lease_payments SET payment_status_id=2 WHERE outstanding<total and outstanding<>0 and payment_status_id NOT IN (4,7) AND due_date > DATE_SUB(NOW(),INTERVAL 1 YEAR)    "; // Partialy
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        //    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    try {
        $SelectSql = "UPDATE lease_payments SET payment_status_id=1 WHERE outstanding=total and payment_status_id NOT IN (4,7) AND due_date > DATE_SUB(NOW(),INTERVAL 1 YEAR)   "; // UnPaid
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        //    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    try {
        $SelectSql = "UPDATE lease_payments SET payment_status_id=5 WHERE outstanding<0 and payment_status_id NOT IN (4,7) AND due_date > DATE_SUB(NOW(),INTERVAL 1 YEAR)   "; // Over Paid
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        //    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }


    try {
        $SelectSql = "UPDATE lease_payments SET invoice_type_id=4 WHERE CURDATE()>due_date AND payment_status_id in (1,2) AND invoice_type_id=2  "; //Unpaid , Parially paid but upcoming change to invoice
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        //    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    try {
        $SelectSql = "UPDATE lease_payments SET invoice_type_id=1 WHERE CURDATE()>due_date AND payment_status_id in (3,5) AND invoice_type_id=2  "; //paid, over paid but upcoming change to invoiced
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        //    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }


    try {
        $SelectSql = "UPDATE lease_payments SET invoice_type_id=1 WHERE payment_status_id in (3,5) AND invoice_type_id=4 AND due_date > DATE_SUB(NOW(),INTERVAL 1 YEAR)   "; //paid, over paid but passed due back to invoiced
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        //    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    /*
try {
    $SelectSql = "UPDATE lease_payments SET invoice_type_id=1 WHERE due_date<CURDATE() AND payment_status_id in (1,2,3,5)  "; //invoiced
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
//    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}
*/

    /*
try {
    $SelectSql = "UPDATE lease_payments SET invoice_type_id=4 WHERE due_date<CURDATE() AND payment_status_id in (1,2)  "; //Passed Due
    $statement = $DB_con->prepare($SelectSql);
    $statement->execute();
//    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}
*/

    try {
        $SelectSql = "SELECT * FROM rental_payments WHERE outstanding>0 and due_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 5 DAY AND lease_status_id IN (1,7,8,9,10)";
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    //die('done');
    //echo "Sending Invoice to : <br>";

    foreach ($result as $row) {
        $id = $row['lease_payment_id'];
        $due_date = $row['due_date'];
        $outstanding = $row['outstanding'];
        echo "ID: " . $id . " Due Date: " . $due_date . " Outstanding = $outstanding<br>\n";

        $invoice = new Invoice($id);
        $invoice->send_invoice_by_sms();
        $invoice->send_invoice_by_email();
    }

    ?>

    <?php
    //kijiji-auto-update-xml
    include("kijiji/kijiji_carousel.php");
    include("kijiji/feedFile.php");
    ?>

    <?php
    include_once("td_file.php");

    ?>


    <?php
    /**
     * Cron script to update the tables : lease_infos, apartment_infos
     */
    include_once("update_tables.php");
    ?>

    <?php
    /* * *
 * Cron to check the late payments
 * * */
    include_once("cron_scripts/late_payments.php");

    ?>

    <?php
    /***
     *  CRON to send notifications for the Events based on Notification preferences set in event_notifications table
     */
    include_once("cron_scripts/event_notifications.php");

    ?>

    <?php
    /***
     * Disable tenant account after 20 days of lease move out date
     */
    include_once("cron_scripts/tenant_inactive.php");

    ?>

    <?php
    /***
     *  CRON to send an email when the late payment date + 3 days (number of days is defined at company level ) for the lease payments.
     *  This is a reminder email.
     */
    include_once("cron_scripts/late_payment_reminder.php");
    ?>
    <?php
    /***
     *  CRON to send emails for renewal notification before renewal_notification_day in company (usually 175 days) and 30 days after it
     */
    include_once("cron_scripts/email_renewal_notifications.php");
    ?>
    <?php
    try {
        $SelectSql = "UPDATE cron_jobs set result=CONCAT(result,' ended.') where id=$last_id  "; // Paid
        // echo "$SelectSql<br>";
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        //    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    $time_elapsed_secs = microtime(true) - $startExec;
    echo "Time Elapsed = " . $time_elapsed_secs / 60;
    ?>
</body>

</html>