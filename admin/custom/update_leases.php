<?php

try {
$SelectSql="UPDATE lease_payments SET payment_status_id=3 WHERE outstanding=0 and payment_status_id<>4  "; // Paid
$statement = $DB_con->prepare($SelectSql);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
echo $e->getMessage();
}

try {
$SelectSql="UPDATE lease_payments SET payment_status_id=2 WHERE outstanding<total and outstanding<>0 and payment_status_id<>4  "; // Partialy
$statement = $DB_con->prepare($SelectSql);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
echo $e->getMessage();
}

try {
$SelectSql="UPDATE lease_payments SET payment_status_id=1 WHERE outstanding=total and payment_status_id<>4  "; // UnPaid
$statement = $DB_con->prepare($SelectSql);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
echo $e->getMessage();
}
die("1");
try {
$SelectSql="UPDATE lease_payments SET payment_status_id=5 WHERE outstanding<0 and payment_status_id<>4  "; // Over Paid
$statement = $DB_con->prepare($SelectSql);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
echo $e->getMessage();
}


try {
$SelectSql="UPDATE lease_payments SET invoice_type_id=1 WHERE due_date>CURDATE() AND payment_status_id in (3,5)  "; //invoiced
$statement = $DB_con->prepare($SelectSql);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
echo $e->getMessage();
}
try {
$SelectSql="UPDATE lease_payments SET invoice_type_id=4 WHERE due_date<CURDATE() AND payment_status_id in (1,2)  "; //Passed Due
$statement = $DB_con->prepare($SelectSql);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
echo $e->getMessage();
}
try {
$SelectSql="UPDATE lease_payments SET invoice_type_id=1 WHERE due_date<CURDATE() AND payment_status_id in (1,2,3,5)  "; //invoiced
$statement = $DB_con->prepare($SelectSql);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
echo $e->getMessage();
}
?>