<? error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
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
    $_SERVER['HTTP_HOST'] = 'spgmanagement.com';
    //include 'sendSMSEmail.php';
    //SendEmail("info@mgmgmt.ca","Info - spgmanagement.com","mishanian@gmail.com","Mehran","Test Cron","Hi<br>Test");
    $CronPath = "../custom"; //
    include_once("$CronPath/../../pdo/dbconfig.php");
    //die(var_dump($DB_kijiji));
    $last_id = 0;
    // Write in DB
    try {
        $SelectSql = "insert into cron_jobs (cron_date,result) VALUES ('" . date("Y-m-d H:i:s") . "', 'started on " . __FILE__ . "') ";
        // $SelectSql = "insert into cron_jobs (cron_date,result) VALUES ('".date("Y-m-d H:i:s")."', 'started ".date("Y-m-d H:i:s")."') ";
        echo "$SelectSql<br>";
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        $last_id = $DB_con->lastInsertId();
        echo "last_id=$last_id";
        //    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    //kijiji-auto-update-xml
    include("kijiji/kijiji_carousel.php");
    include("kijiji/feedFile.php");
    ?>

    <?php
    include_once('request/late_payment_request.php');
    ?>

    <?php
    include_once('cron_scripts/visit_feedback.php');
    ?>

    <?php
    include_once('cron_scripts/agent_nocommunication_reminder.php');
    ?>

    <?php
    try {
        $SelectSql = "UPDATE cron_jobs set result=CONCAT(result,' ended at " . date("Y-m-d H:i:s") . ".') where id=$last_id  "; // Paid
        echo "$SelectSql<br>";
        $statement = $DB_con->prepare($SelectSql);
        $statement->execute();
        //    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    ?>
</body>

</html>