<?
session_start();
include('../../../pdo/dbconfig.php');
// die(print_r($_POST));
$lease_id=$_POST['lease_id'];
    $signatureData = $_POST['signatureData'];
    $manager_signature = "../../files/lease_signs/manager_sign_".$lease_id.".png";
    $encoded_image = explode(",", $signatureData)[1];
    $decoded_image = base64_decode($encoded_image);
    file_put_contents($manager_signature, $decoded_image);

    $signatureDataInit = $_POST['signatureDataInit'];
    $manager_signature_init = "../../files/lease_signs/manager_sign_init_".$lease_id.".png";
    $encoded_image_init = explode(",", $signatureDataInit)[1];
    $decoded_image_init = base64_decode($encoded_image_init);
    file_put_contents($manager_signature_init, $decoded_image_init);


    $IP=$_SERVER['REMOTE_ADDR'];
    $user_info=$_SERVER['HTTP_USER_AGENT'];

    if(!empty($lease_id)){
        $query = "update lease_infos set is_signed=1, signDT='".date("Y-m-d H:i:s")."', signIP='$IP', sign_user_info='$user_info', sign_employee_id=".$_SESSION['employee_id']." where id=$lease_id";
        $stmt = $DB_con->prepare($query);
        $stmt->execute();
    }


    $query = "select * from lease_infos where id=$lease_id";
    $stmt = $DB_con->prepare($query);
    $stmt->execute();
    $row=$stmt->fetch(\PDO::FETCH_ASSOC);

    $lease_amount=$row['lease_amount'];
    $start_date=date_create($row['start_date']);
    $length_of_lease=$row['length_of_lease'];
    
    $start_date = new \DateTime($row['start_date']);
    $end_date = new \DateTime($row['end_date']);
    $diff=date_diff($start_date,$end_date);
    $diff=$diff->format("%y")*12+$diff->format("%m")+1;
    $payment_per_month=$lease_amount;///$length_of_lease;
    for ($i=0;$i<$diff;$i++){
        $original_start_date = new \DateTime($row['start_date']);
        $du_date=$original_start_date->add(new \DateInterval('P'.$i.'M'));
        $du_date=$du_date->format('Y-m-d');
        if ($i==0){$invoice_type_id=1;}else{$invoice_type_id=2;}
        $queryInsert = "insert IGNORE into lease_payments (lease_id,invoice_type_id,due_date,lease_amount,total,outstanding) values ($lease_id,$invoice_type_id,'$du_date',$payment_per_month,$payment_per_month,$payment_per_month)";
        $stmt = $DB_con->prepare($queryInsert);
        $stmt->execute();
    }


    ?>
Dear <?=$_SESSION['UserFullName']?>,<br>
Thanks for siging the lease. <br><a href="lease_page.php?lid=<?=$lease_id?>">Click here to come back to lease page</a>
