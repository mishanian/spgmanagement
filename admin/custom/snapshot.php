<!DOCTYPE html>
<html lang="en">
<head>
<title>Snapshot</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="css/bootstrap-select.css" rel="stylesheet" type="text/css" />
<link href="css/font-awesome.css" rel="stylesheet" type="text/css" />
<link href="css/owl.carousel.css" rel="stylesheet" type="text/css" />
<link href="css/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="css/bootstrapValidator.min.css" rel="stylesheet" type="text/css" />
<link href="../../css/css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/styles_snapshot.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div class="snapshot-container container-fluid">
	<div class="snapshot-row row">
		<div class="col-md-4 col-snapshot-1">
			<div class="col-md-6 col-snapshot-2 snapshot-icon-color-1">
				<div class="col-md-6 col-snapshot-3">
					<img src="images/house.png" alt="landlord" height="100" width="100">
				</div>
				<?php
                include_once ("pdo/dbconfig.php");
                session_start();

               	if(isset($_SESSION['employee_id'])) {
                	$id = $_SESSION['employee_id'];
                	$role = "employee";
               	}
               	else if(isset($_SESSION['owner_id'])) {
                	$id = $_SESSION['owner_id'];
                	$role = "owner";
               	}
               	else if(isset($_SESSION['manager_id'])) {
                	$id = $_SESSION['manager_id'];
                	$role = "manager";
               	}

                $id = 1;
                $role = "employee";

                $owner_num = $DB_snapshot->getOwnerNum($id, $role);
                $building_num = $DB_snapshot->getBuildingNum($id, $role);
                $manager_num = $DB_snapshot->getManagerNum($id, $role);
                $tenant_num = $DB_snapshot->getTenantNum($id, $role);
                $unpaid_payment_num = $DB_snapshot->getUnpaidPaymentNum($id, $role);
                $late_payment_num = $DB_snapshot->getLatePaymentNum($id, $role);
                $unread_request_num = $DB_snapshot->getUnreadRequestNum($id, $role);
                $open_request_num = $DB_snapshot->getOpenRequestNum($id, $role);
                $pending_renewal_num = $DB_snapshot->getPendingRenewalNum($id, $role);
                $potential_tenant_num = $DB_snapshot->getPotentialTenantNum($id, $role);
                ?>
				<div class="col-md-6 col-snapshot-4 snapshot-text-2">
					<span class="snapshot-span"><?php echo $owner_num; ?></span>
				</div>
				<div class="col-snapshot-5 snapshot-text-1 spanshot-text-color-1">
					<span>Owners</span>
				</div>
				<a class="snapshot-link" href="#"></a>		
			</div>
			<div class="col-md-6 col-snapshot-8 snapshot-icon-color-2">
				<div class="col-md-6 col-snapshot-3">
					<img src="images/house.png" alt="landlord" height="100" width="100">
				</div>
				<div class="col-md-6 col-snapshot-4 snapshot-text-2">
					<span class="snapshot-span"><?php echo $building_num; ?></span>
				</div>
				<div class="col-snapshot-5 snapshot-text-1 spanshot-text-color-2">
					<span>Buildings</span>
				</div>
				<a class="snapshot-link" href="#"></a>				
			</div>	
		</div>
		<div class="col-md-4 col-snapshot-1">
			<div class="col-snapshot-6 snapshot-icon-color-3">
				<div class="col-md-3 col-snapshot-10">
					<img src="images/house.png" alt="landlord" height="100" width="100">
				</div>
				<div class="col-md-9 col-snapshot-9 snapshot-text-3">
					<span class="snapshot-span"><?php echo $unpaid_payment_num; ?> UNPAID</span>
				</div>
				<div class="col-md-9 col-snapshot-9 snapshot-text-3">
					<span class="snapshot-span"><?php echo $late_payment_num; ?> LATE</span>
				</div>
				<div class="col-snapshot-7 snapshot-text-1 spanshot-text-color-3">
					<span>Rental Payments & Deposits</span>
				</div>
				<a class="snapshot-link" href="#"></a>				
			</div>		
		</div>
		<div class="col-md-4 col-snapshot-1">
			<div class="col-snapshot-6 snapshot-icon-color-4">
				<div class="col-md-3 col-snapshot-10">
					<img src="images/house.png" alt="landlord" height="100" width="100">
				</div>
				<div class="col-md-9 col-snapshot-9 snapshot-text-3">
					<span class="snapshot-span"><?php echo $unread_request_num; ?> UNREAD</span>
				</div>
				<div class="col-md-9 col-snapshot-9 snapshot-text-3">
					<span class="snapshot-span"><?php echo $open_request_num; ?> OPEN</span>
				</div>
				<div class="col-snapshot-7 snapshot-text-1 spanshot-text-color-4">
					<span>Requests</span>
				</div>
				<a class="snapshot-link" href="#"></a>				
			</div>		
		</div>
	</div>
	<div class="snapshot-row row">
		<div class="col-md-4 col-snapshot-1">
			<div class="col-md-6 col-snapshot-2 snapshot-icon-color-5">
				<div class="col-md-6 col-snapshot-3">
					<img src="images/house.png" alt="landlord" height="100" width="100">
				</div>
				<div class="col-md-6 col-snapshot-4 snapshot-text-2">
					<span class="snapshot-span"><?php echo $manager_num; ?></span>
				</div>
				<div class="col-snapshot-5 snapshot-text-1 spanshot-text-color-5">
					<span>Managers</span>
				</div>
				<a class="snapshot-link" href="#"></a>		
			</div>
			<div class="col-md-6 col-snapshot-8 snapshot-icon-color-6">
				<div class="col-md-6 col-snapshot-3">
					<img src="images/house.png" alt="landlord" height="100" width="100">
				</div>
				<div class="col-md-6 col-snapshot-4 snapshot-text-2">
					<span class="snapshot-span"><?php echo $tenant_num; ?></span>
				</div>
				<div class="col-snapshot-5 snapshot-text-1 spanshot-text-color-6">
					<span>Tenants</span>
				</div>
				<a class="snapshot-link" href="#"></a>				
			</div>	
		</div>
		<div class="col-md-4 col-snapshot-1">
			<div class="col-snapshot-6 snapshot-icon-color-7">
				<div class="col-md-3 col-snapshot-10">
					<img src="images/house.png" alt="landlord" height="100" width="100">
				</div>
				<div class="col-md-9 col-snapshot-9 snapshot-text-3">
					<span class="snapshot-span"><?php echo $pending_renewal_num; ?> PENDING RENEWAL</span>
				</div>
				<div class="col-snapshot-7 snapshot-text-1 spanshot-text-color-7">
					<span>Leases</span>
				</div>
				<a class="snapshot-link" href="#"></a>				
			</div>		
		</div>
		<div class="col-md-4 col-snapshot-1">
			<div class="col-md-6 col-snapshot-2 snapshot-icon-color-8">
				<div class="col-md-6 col-snapshot-3">
					<img src="images/house.png" alt="landlord" height="100" width="100">
				</div>
				<div class="col-snapshot-5 snapshot-text-1 spanshot-text-color-8">
					<span>Trends & Reports</span>
				</div>
				<a class="snapshot-link" href="#"></a>		
			</div>
			<div class="col-md-6 col-snapshot-8 snapshot-icon-color-9">
				<div class="col-md-6 col-snapshot-3">
					<img src="images/house.png" alt="landlord" height="100" width="100">
				</div>
				<div class="col-snapshot-5 snapshot-text-1 spanshot-text-color-9">
					<span>Physical Mail</span>
				</div>
				<a class="snapshot-link" href="#"></a>				
			</div>	
		</div>
	</div>
	<div class="snapshot-row row">
		<div class="col-md-4 col-snapshot-1">
			<div class="col-snapshot-6 snapshot-icon-color-10">
				<div class="col-md-3 col-snapshot-10">
					<img src="images/house.png" alt="landlord" height="100" width="100">
				</div>
				<div class="col-md-9 col-snapshot-7 snapshot-text-1 spanshot-text-color-10">
					<span>Personal Setting</span>
				</div>
				<a class="snapshot-link" href="#"></a>				
			</div>		
		</div>
		<div class="col-md-4 col-snapshot-1">
			<div class="col-snapshot-6 snapshot-icon-color-11">
				<div class="col-md-3 col-snapshot-10">
					<img src="images/house.png" alt="landlord" height="100" width="100">
				</div>
				<div class="col-md-9 col-snapshot-9 snapshot-text-3">
					<span class="snapshot-span"><?php echo $potential_tenant_num; ?> OPEN</span>
				</div>
				<div class="col-snapshot-7 snapshot-text-1 spanshot-text-color-11">
					<span>Potential Tenants</span>
				</div>
				<a class="snapshot-link" href="#"></a>				
			</div>		
		</div>
	</div>
</div>

</body>
</html>