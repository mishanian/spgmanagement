<?php
// use PHPMaker2023\spgmanagement\Snapshot;
include("../pdo/dbconfig.php");
include_once '../pdo/Class.Employee.php';
$DB_employee = new Employee($DB_con);
include_once '../pdo/Class.Apt.php';
$DB_apt = new Apt($DB_con);
include_once '../pdo/Class.Building.php';
$DB_building = new Building($DB_con);
include_once '../pdo/Class.Request.php';
$DB_request = new Request($DB_con);
include_once '../pdo/Class.Tenant.php';
$DB_tenant = new Tenant($DB_con);
include_once '../pdo/Class.Snapshot.php';
$DB_snapshot = new Snapshot($DB_con);

$employeeId = null;
$companyId = null;
$employeeIdValue = null;

if (isset($_SESSION["employee_id"])) {
    $employeeId = $_SESSION['employee_id'];
    $employeeId = isEmployeeAdmin($employeeId, $DB_employee) ? "0000" : $employeeId;
    $employeeIdValue = $_SESSION['employee_id'];
}
if (isset($_SESSION["company_id"])) {
    $companyId = $_SESSION["company_id"];
}

function getEmployeeBuildings($DB_employee, $employeeId)
{
    $employeeData = $DB_employee->getEmployeeInfo($employeeId);
    $buildingIds = $employeeData["building_ids"];

    $buildingIdArray = explode(",", $buildingIds);
    return $buildingIdArray;
}

function isEmployeeAdmin($employeeId, $DB_employee)
{
    $employeeData = $DB_employee->getEmployeeInfo($employeeId);
    if ($employeeData["admin_id"] == 1) {
        return true;
    }
    return false;
}

function vacantParkingInAllBuildings($DB_building, $DB_apt, $employeeId, $companyId, $DB_employee)
{
    $isAdmin = isEmployeeAdmin($employeeId, $DB_employee);

    if ($isAdmin) {
        $allBuildingsIds = $DB_building->getAllBdIdsByCompany($companyId)["GROUP_CONCAT(building_id)"];
        $allBuildingsIds = explode(',', $allBuildingsIds);
    } else {
        $allBuildingsIds = getEmployeeBuildings($DB_employee, $employeeId);
    }

    $totalParkingAvbl = $DB_building->parkingAvailableSlots(); // Total parking slots available in all the buildings

    if ($totalParkingAvbl) {
        if (count($totalParkingAvbl) < 1) {
            return array("percentage" => "0", "vacant" => "0");
        }

        $totalParkingAvblByBuildingIds = array();

        foreach ($totalParkingAvbl as $index => $parking) {
            $buildingId = $parking["building_id"];

            if (!in_array($buildingId, $allBuildingsIds)) { // If the building doesn't belong the companyID that's logged in
                continue;
            }
            array_push($totalParkingAvblByBuildingIds, $parking);
        }

        if (!$totalParkingAvblByBuildingIds) {
            return array("percentage" => "0", "vacant" => "0");
        }

        $totalParkingAvblByBldsCount = count($totalParkingAvblByBuildingIds);

        $vacantParking = $DB_building->parkingVacantSlots(); // Total parking slots vacant in the building

        if ($vacantParking) {
            $vacantParkingAvblByBId = array();

            foreach ($vacantParking as $index => $parking) {
                $buildingId = $parking["building_id"];

                if (!in_array($buildingId, $allBuildingsIds)) { // If the building doesn't belong the companyID that's logged in
                    continue;
                }
                array_push($vacantParkingAvblByBId, $parking);
            }
            $vacantParkingCount = count($vacantParkingAvblByBId);
        } else {
            $vacantParkingCount = 0;
        }

        $vacantPercentage = ($vacantParkingCount / $totalParkingAvblByBldsCount) * 100;
        return array("percentage" => round($vacantPercentage, 2), "vacant" => $vacantParkingCount);
    }
    return array("percentage" => "-", "vacant" => "-");
}

function vacantParkingInBuilding($DB_building, $DB_apt, $buildingId, $employeeId, $DB_employee)
{

    $totalParkingAvbl = $DB_building->parkingAvailableSlots($buildingId); // Total parking slots available in the building

    if ($totalParkingAvbl) {
        $totalParkingAvblCount = count($totalParkingAvbl);
        if ($totalParkingAvblCount < 1) {
            return array("percentage" => "0", "vacant" => "0", "totalAvbl" => "0");
        }

        $vacantParking = $DB_building->parkingVacantSlots($buildingId); // Total parking slots vacant in the building
        if ($vacantParking) {
            $vacantParkingCount = count($vacantParking);
        } else {
            $vacantParkingCount = 0;
        }

        $vacantPercentage = ($vacantParkingCount / $totalParkingAvblCount) * 100;
        return array("percentage" => round($vacantPercentage, 2), "vacant" => $vacantParkingCount, "totalAvbl" => $totalParkingAvblCount);
    }
    return array("percentage" => "-", "vacant" => "-", "totalAvbl" => "-");
}

/**
 * Get vacant data of all buildings for the current employee ID
 * @param DB Object for Apartment $DB_apt
 * @return array
 */
function vacantAptInAllBuildings($DB_apt, $employeeId, $companyId, $employeeIdValue, $DB_employee)
{
    $admin = false;
    if ($employeeId == "0000") {
        $allApts = $DB_apt->getAllApts();
        $allAptsCount = count($DB_apt->filterByCompanyId($allApts, $companyId));
        $admin = true;
    } else {
        // If Not an Admin - Get all the building IDs from the employee info
        $employeeData = $DB_employee->getEmployeeInfo($employeeIdValue);
        $buildingIds = $employeeData["building_ids"];

        $buildingIdArray = explode(",", $buildingIds);
        $buildingTotalAptCount = 0;

        foreach ($buildingIdArray as $bid) {
            $buildingTotalAptCount += count($DB_apt->getAptInfoInBuilding($bid));
        }

        $allAptsCount = $buildingTotalAptCount;
    }

    if ($allAptsCount) {

        if ($admin) {
            $vacantApts = $DB_apt->getVacantAptInfoInAllBuildings(); // Vacant apartments in the building
            $vacantApts = $DB_apt->filterByCompanyId($DB_apt->getVacantAptInfoInAllBuildings(), $companyId);
            $buildingVacantAptCount = count($vacantApts);
        } else {
            $buildingVacantAptCount = 0;
            foreach ($buildingIdArray as $bid) {
                $buildingVacantAptCount += count($DB_apt->getVacantAptInfoInBuilding($bid));
            }
        }

        $vacantPercentage = ($buildingVacantAptCount / $allAptsCount) * 100;
        return array("percentage" => round($vacantPercentage, 2), "vacant" => $buildingVacantAptCount);
    }
    return array("percentage" => "-", "vacant" => "-");
}

/**
 * Filtered by the current employee ID
 * @param DB Object for Apartment $DB_apt
 * @param int $buildingId
 * @param int $employee
 * @return array
 */
function vacantAptInBuilding($DB_apt, $buildingId, $employeeId, $DB_employee)
{
    $allApts = $DB_apt->getAptInfoInBuilding($buildingId);
    if ($allApts) {

        $totalAptsCount = count($DB_apt->getAptInfoInBuilding($buildingId)); // Total apartments in the building

        if ($totalAptsCount < 1) {
            return array("percentage" => "0", "vacant" => "0", "totalAvbl" => "0");
        }

        $vacantApts = $DB_apt->getVacantAptInfoInBuilding($buildingId); // Vacant apartments in the building
        $vacantAptsCount = count($vacantApts);

        $vacantPercentage = ($vacantAptsCount / $totalAptsCount) * 100;
        return array("percentage" => round($vacantPercentage, 2), "vacant" => $vacantAptsCount, "totalAvbl" => $totalAptsCount);
    }
    return array("percentage" => "-", "vacant" => "-", "totalAvbl" => "-");
}

function vacantStorageInAllBuildings($DB_building, $DB_apt, $employeeId, $companyId, $DB_employee)
{
    $isAdmin = isEmployeeAdmin($employeeId, $DB_employee);

    if ($isAdmin) {
        $allBuildingsIds = $DB_building->getAllBdIdsByCompany($companyId)["GROUP_CONCAT(building_id)"];
        $allBuildingsIds = explode(',', $allBuildingsIds);
    } else {
        $allBuildingsIds = getEmployeeBuildings($DB_employee, $employeeId);
    }

    $totalAvblStorage = $DB_building->storageAvailableSlots();

    if ($totalAvblStorage) {
        if (count($totalAvblStorage) < 1) {
            return array("percentage" => "0", "vacant" => "0");
        }

        $totalAvblStorageByBId = array();

        foreach ($totalAvblStorage as $index => $storage) {
            $buildingId = $storage["building_id"];

            if (!in_array($buildingId, $allBuildingsIds)) { // If the building doesn't belong the companyID that's logged in
                continue;
            }
            array_push($totalAvblStorageByBId, $storage);
        }

        $totalAvblStorageCount = count($totalAvblStorageByBId);

        //-- Vacant Slots
        if ($totalAvblStorageCount) {
            $totalStorageVacant = $DB_building->storageVacantSlots();
            if ($totalStorageVacant) {
                $totalVacantStorageByBId = array();

                foreach ($totalStorageVacant as $index => $storage) {
                    $buildingId = $storage["building_id"];

                    if (!in_array($buildingId, $allBuildingsIds)) { // If the building doesn't belong the companyID that's logged in
                        continue;
                    }
                    array_push($totalVacantStorageByBId, $storage);
                }
                $totalStorageVacantCount = count($totalVacantStorageByBId); // Count of the vacant storage slots
            } else {
                $totalStorageVacantCount = 0;
            }

            $vacantPercentage = ($totalStorageVacantCount / $totalAvblStorageCount) * 100;
            return array("percentage" => round($vacantPercentage, 2), "vacant" => $totalStorageVacantCount);
        } else {
            return array("percentage" => "0", "vacant" => "0");
        }
    }
    return array("percentage" => "-", "vacant" => "-");
}

function vacantStorageInBuilding($DB_building, $DB_apt, $buildingId, $employeeId)
{
    $totalAvblStorage = $DB_building->storageAvailableSlots($buildingId);
    if ($totalAvblStorage) {
        $totalAvblStorageCount = count($totalAvblStorage);

        if (!$totalAvblStorageCount || $totalAvblStorageCount < 1) {
            return array("percentage" => "0", "vacant" => "0", "totalAvbl" => "0");
        }

        $totalStorageVacant = $DB_building->storageVacantSlots($buildingId);
        if ($totalStorageVacant) {
            $totalStorageVacantCount = count($totalStorageVacant);
        } else {
            $totalStorageVacantCount = 0;
        }

        $vacantPercentage = ($totalStorageVacantCount / $totalAvblStorageCount) * 100;
        return array("percentage" => round($vacantPercentage, 2), "vacant" => $totalStorageVacantCount, "totalAvbl" => $totalAvblStorageCount);
    }
    return array("percentage" => "-", "vacant" => "-", "totalAvbl" => "-");
}

/*
  ----------------- Vacancy LOSS Functions ------------------------
 */

/**
 * Amount Loss because of the vacancy of Units in a Building or all the buildings filtered by the employee
 * @param PDO $DB_apt
 * @param int $employeeId
 * @param int $buildingId
 * @return int
 */
function unitVacancyLoss($DB_employee, $DB_apt, $employeeId, $companyId, $buildingId = false)
{
    $vacancyLoss = 0;
    $isAdmin = isEmployeeAdmin($employeeId, $DB_employee);
    $allVacantApts = array();

    if ($buildingId != false) {    // Unit vacancy Loss by Building ID
        $vacantApts = $DB_apt->getVacantAptInfoInBuilding($buildingId); // Vacant apartments in the building
        if ($vacantApts) {
            $vacantAptsByCompanyId = $DB_apt->filterByCompanyId($vacantApts, $companyId);
            foreach ($vacantAptsByCompanyId as $apartment) {
                $vacancyLoss += $apartment['monthly_price']; // Loss SUM because of vacant apartments
            }
            return $vacancyLoss;
        }
    } else {
        // Unit Vacancy Loss for all the buildings for the employee
        $vacantApts = $DB_apt->getVacantAptInfoInAllBuildings(); // Vacant apartments in all buildings

        if ($vacantApts) {
            if ($isAdmin) {
                $allVacantApts = $DB_apt->filterByCompanyId($vacantApts, $companyId);
            } else {
                $buildingIdArray = getEmployeeBuildings($DB_employee, $employeeId);
                foreach ($vacantApts as $apartment) {
                    if (!in_array($apartment["building_id"], $buildingIdArray)) {
                        continue;
                    }
                    array_push($allVacantApts, $apartment);
                }
            }

            if ($allVacantApts) {
                foreach ($allVacantApts as $apartment) {
                    $vacancyLoss += $apartment['monthly_price']; // Loss SUM because of vacant apartments
                }
                return $vacancyLoss;
            }
        }
    }
    return 0; // If no Vacant apartments exist
}

/**
 *
 * @param PDO $DB_building
 * @param PDO $DB_apt
 * @param int $employeeId
 * @param int $buildingId
 * @return int
 */
function parkingVacancyLoss($DB_employee, $DB_building, $DB_apt, $employeeId, $companyId, $buildingId = false)
{
    $vacancyLoss = 0;
    $isAdmin = isEmployeeAdmin($employeeId, $DB_employee);

    // Parking vacancy Loss by Building ID
    if ($buildingId != false) {
        $totalParkingVacant = $DB_building->parkingVacantSlots($buildingId); // Total parking slots vacant in the building
        if ($totalParkingVacant) {
            $totalParkingAvblByCompanyId = $DB_apt->filterByCompanyId($totalParkingVacant, $companyId);

            foreach ($totalParkingAvblByCompanyId as $index => $parking) {
                $vacancyLoss += $parking["monthly_price"];
            }
            return $vacancyLoss;
        }
    } else {
        // Parking Vacancy Loss for all the buildings for the employee
        $vacantParking = $DB_building->parkingVacantSlots(); // Total parking slots vacant in the building

        if ($vacantParking) {

            if ($isAdmin) {
                $allBuildingsIds = $DB_building->getAllBdIdsByCompany($companyId)["GROUP_CONCAT(building_id)"];
                $buildingIdArray = explode(',', $allBuildingsIds);
            } else {
                $buildingIdArray = getEmployeeBuildings($DB_employee, $employeeId);
            }

            foreach ($vacantParking as $index => $parking) {
                $buildingId = $parking["building_id"];
                if (!in_array($buildingId, $buildingIdArray)) { // If the building doesn't belong the companyID that's logged in
                    continue;
                }
                $vacancyLoss += $parking["monthly_price"];
            }
            return $vacancyLoss;
        }
    }
    return 0;
}

/**
 *
 * @param type $DB_building
 * @param type $DB_apt
 * @param type $employeeId
 * @param type $buildingId
 * @return int
 */
function storageVacancyLoss($DB_employee, $DB_building, $DB_apt, $employeeId, $companyId, $buildingId = false)
{
    $vacancyLoss = 0;
    $isAdmin = isEmployeeAdmin($employeeId, $DB_employee);

    // Storage Unit vacancy Loss by Building ID
    if ($buildingId != false) {
        $totalStorageVacant = $DB_building->storageVacantSlots($buildingId);
        if ($totalStorageVacant) {
            $totalStorageVacantByEmpId = $DB_apt->filterByEmployeeId($totalStorageVacant, $employeeId);
            foreach ($totalStorageVacantByEmpId as $index => $storage) {
                $vacancyLoss += $storage["monthly_price"];
            }
            return $vacancyLoss;
        }
    } else {
        // Storage Vacancy Loss for all the buildings for the employee
        $totalStorageVacant = $DB_building->storageVacantSlots();
        if ($totalStorageVacant) {

            if ($isAdmin) {
                $allBuildingsIds = $DB_building->getAllBdIdsByCompany($companyId)["GROUP_CONCAT(building_id)"];
                $buildingIdArray = explode(',', $allBuildingsIds);
            } else {
                $buildingIdArray = getEmployeeBuildings($DB_employee, $employeeId);
            }

            foreach ($totalStorageVacant as $storage) {
                $buildingId = $storage["building_id"];

                if (!in_array($buildingId, $buildingIdArray)) { // If the building doesn't belong the companyID that's logged in
                    continue;
                }

                $vacancyLoss += $storage["monthly_price"];
            }
            return $vacancyLoss;
        }
    }
    return 0;
}

/**
 *
 * @param type $DB_building
 * @param type $DB_request
 * @param type $employee_id
 * @return int
 */
function getTotalOpenIssues($DB_request, $employee_id, $buildingId = false)
{
    $issues["1"] = 0;
    $issues["2"] = 0;
    $issues["3"] = 0;

    $allRequestsByEmpId = $DB_request->get_employee_issue_list($employee_id);

    foreach ($allRequestsByEmpId as $request) {
        $requestLevel = $DB_request->get_request_level($request["request_type_id"], $request["building_id"]);

        if ($buildingId != false) {
            if ($request["building_id"] != $buildingId) {
                continue;
            }
        }

        if ($request["issue_status"] != "open") {
            continue;
        }

        switch ($requestLevel) {
            case "NORMAL":
                $issues["1"]++;
                break;
            case "SERIOUS":
                $issues["2"]++;
                break;
            case "URGENT":
                $issues["3"]++;
                break;
        }
    }
    return $issues;
}

function getTenantSignUpStatus($employeeId, $DB_tenant, $DB_apt, $DB_employee, $companyId, $employeeIdValue)
{
    $response = array();

    $employeeAdmin = isEmployeeAdmin($employeeIdValue, $DB_employee);

    $tenantSignupTotal = $DB_tenant->getTenantSignUpDetails($companyId, $employeeIdValue, true, $employeeAdmin);
    //    $tenantSignupTotalByCompanyId = $DB_apt->filterByCompanyId($tenantSignupTotal, $companyId);
    //    $tenantSignupTotalByEmployeeId = $DB_apt->filterByEmployeeId($tenantSignupTotalByCompanyId, $employeeId);

    $tenantSignupLogin = $DB_tenant->getTenantSignUpDetails($companyId, $employeeIdValue, false, $employeeAdmin);
    //    $tenantSignupLoginByCompanyId = $DB_apt->filterByCompanyId($tenantSignupTotal, $companyId);
    //    $tenantSignupLoginByEmployeeId = $DB_apt->filterByEmployeeId($tenantSignupTotalByCompanyId, $employeeId);

    $response["totalTenants"] = count($tenantSignupTotal);
    $response["loginTenants"] = count($tenantSignupLogin);

    return $response;
}

/**
 * Frame response to show in the building CARDS
 * @param int $buildingId
 * @param PDO $DB_apt
 * @param PDO $DB_building
 * @param int $employeeId
 * @param PDO $DB_request
 * @return array
 */
function getBuildingDataToShow($buildingId, $DB_apt, $DB_building, $employeeId, $DB_request, $DB_employee)
{
    $response = array(); // response array
    // Fetch data and store in variables
    $apartmentData = vacantAptInBuilding($DB_apt, $buildingId, $employeeId, $DB_employee);
    $parkingData = vacantParkingInBuilding($DB_building, $DB_apt, $buildingId, $employeeId, $DB_employee);
    $storageData = vacantStorageInBuilding($DB_building, $DB_apt, $buildingId, $employeeId);
    $openIssuesBuilding = getTotalOpenIssues($DB_request, $employeeId, $buildingId);

    // Frame the response Array
    $response["totalUnit"] = $apartmentData["totalAvbl"];
    $response["totalParking"] = $parkingData["totalAvbl"];
    $response["totalStorage"] = $storageData["totalAvbl"];

    $response["vacantUnit"] = $apartmentData["vacant"];
    $response["vacantParking"] = $parkingData["vacant"];
    $response["vacantStorage"] = $storageData["vacant"];

    $response["normalIssue"] = $openIssuesBuilding["1"];
    $response["seriousIssue"] = $openIssuesBuilding["2"];
    $response["urgentIssue"] = $openIssuesBuilding["3"];
    $response["totalOpenIssue"] = $openIssuesBuilding["1"] + $openIssuesBuilding["2"] + $openIssuesBuilding["3"];

    return $response;
}

// Shortens a number and attaches K, M, B, etc. accordingly
function number_shorten($number, $precision = 3, $divisors = null)
{

    // Setup default $divisors if not provided
    if (!isset($divisors)) {
        $divisors = array(
            pow(1000, 0) => '', // 1000^0 == 1
            pow(1000, 1) => 'K', // Thousand
            pow(1000, 2) => 'M', // Million
            pow(1000, 3) => 'B', // Billion
            pow(1000, 4) => 'T', // Trillion
            pow(1000, 5) => 'Qa', // Quadrillion
            pow(1000, 6) => 'Qi', // Quintillion
        );
    }

    // Loop through each $divisor and find the
    // lowest amount that matches
    foreach ($divisors as $divisor => $shorthand) {
        if (abs($number) < ($divisor * 1000)) {
            // We found a match!
            break;
        }
    }

    // We found our match, or there were no matches.
    // Either way, use the last defined value for $divisor.
    return number_format($number / $divisor, $precision) . $shorthand;
}

// ------------- Calling above functions to display the data below in the HTML -------------------
// -- Vacant Units, Storage and Parking Values
$vacantUnits = vacantAptInAllBuildings($DB_apt, $employeeId, $companyId, $employeeIdValue, $DB_employee);
$vacantParking = vacantParkingInAllBuildings($DB_building, $DB_apt, $employeeIdValue, $companyId, $DB_employee);
$vacantStorage = vacantStorageInAllBuildings($DB_building, $DB_apt, $employeeIdValue, $companyId, $DB_employee);

// -- Vacancy loss
$unitLoss = unitVacancyLoss($DB_employee, $DB_apt, $employeeIdValue, $companyId);
$parkingLoss = parkingVacancyLoss($DB_employee, $DB_building, $DB_apt, $employeeIdValue, $companyId);
$storageLoss = storageVacancyLoss($DB_employee, $DB_building, $DB_apt, $employeeIdValue, $companyId);

//-- Open Issues
$openIssues = getTotalOpenIssues($DB_request, $employeeIdValue);

// Signup Status
$signUpStatus = getTenantSignUpStatus($employeeId, $DB_tenant, $DB_apt, $DB_employee, $companyId, $employeeIdValue);
?>

<!--Begin Content-->
<div class="container">
    <div class="row form-group">
        <div class="col-md-3">
            <h6><b><?php echo $DB_snapshot->echot("TOTAL VACANCIES"); ?></b></h6>
            <div class="table-responsive">
                <table id="eftTable" class="table  table-fixed table-condensed">
                    <tr>
                        <td><?php echo $DB_snapshot->echot("Units"); ?></td>
                        <td><a href="apartmentinfoslist?cmd=search&t=apartment_infos&x_apartment_status=5"><?php echo $vacantUnits['vacant'] . '</a> (' . $vacantUnits['percentage'] . '%)'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $DB_snapshot->echot("Parking Spots"); ?></td>
                        <td><a href="parking_unit_infoslist.php?cmd=search&t=parking_unit_infos&z_status_id=%3D&x_status_id=1">
                                <?php echo $vacantParking['vacant'] . '</a> (' . $vacantParking['percentage'] . '%)'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $DB_snapshot->echot("Storage Units"); ?></td>
                        <td><a href="storage_unit_infoslist.php?cmd=search&t=storage_unit_infos&z_status_id=%3D&x_status_id=1"><?php echo $vacantStorage['vacant'] . '</a> (' . $vacantStorage['percentage'] . '%)'; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-md-3">
            <h6><b><?php echo $DB_snapshot->echot("TOTAL CURRENT VACANCY LOSS"); ?></b></h6>
            <div class="table-responsive">
                <table id="eftTable" class="table  table-fixed table-condensed">
                    <tr>
                        <td><?php echo $DB_snapshot->echot("Units"); ?></td>
                        <td> <a href="apartmentinfoslist?cmd=search&t=apartment_infos&x_apartment_status=5">
                                <?php echo "$" . round($unitLoss, 2) . " /mo"; ?> </a></td>
                    </tr>
                    <tr>
                        <td><?php echo $DB_snapshot->echot("Parking Spots"); ?></td>
                        <td> <a href="parking_unit_infoslist.php?cmd=search&t=parking_unit_infos&z_status_id=%3D&x_status_id=1">
                                <?php echo "$" . round($parkingLoss, 2) . " /mo"; ?> </a></td>
                    </tr>
                    <tr>
                        <td><?php echo $DB_snapshot->echot("Storage Units"); ?></td>
                        <td> <a href="storage_unit_infoslist.php?cmd=search&t=storage_unit_infos&z_status_id=%3D&x_status_id=1">
                                <?php echo "$" . round($storageLoss, 2) . " /mo"; ?> </a></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-md-2">
            <h6><b><?php echo $DB_snapshot->echot("TOTAL OPEN ISSUES"); ?>(<?php echo $openIssues["1"] + $openIssues["2"] + $openIssues["3"] ?>)
                </b></h6>
            <div class="table-responsive">
                <table id="eftTable" class="table  table-fixed table-condensed">
                    <tr>
                        <td><?php echo $DB_snapshot->echot("Urgent"); ?></td>
                        <td><?php echo $openIssues["3"]; ?> </td>
                    </tr>
                    <tr>
                        <td><?php echo $DB_snapshot->echot("Serious"); ?></td>
                        <td><?php echo $openIssues["2"]; ?> </td>
                    </tr>
                    <tr>
                        <td><?php echo $DB_snapshot->echot("Normal"); ?></td>
                        <td><?php echo $openIssues["1"]; ?> </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
        $dateToday = urlencode(date("Y-m-d"));
        $date1YearAgo = urlencode(date('Y-m-d', strtotime('-1 year')));
        ?>

        <div class="col-md-3">

            <h6><b> <?php echo $DB_snapshot->echot("TOTAL TENANT SIGNUP STATUS"); ?></b></h6>
            <div class="alert alert-info">
                <?php echo $DB_snapshot->echot("Signed Up"); ?> : <a href="<?php echo "viewtenantinfoslist?cmd=search&t=view_tenant_infos&z_full_name=LIKE&x_full_name=&z_active_id=%3D&x_active_id=&z_mobile=LIKE&x_mobile=&z_building_id=%3D&x_building_id=&z_apartment_id=%3D&x_apartment_id=&z_last_login_time=BETWEEN&x_last_login_time=$date1YearAgo&y_last_login_time=$dateToday&psearch=&psearchtype="; ?>">
                    <span class="badge"> <?php echo $signUpStatus['loginTenants'] ?></span> </a> /
                <?php echo $DB_snapshot->echot("Total"); ?>: <a href="viewtenantinfoslist"><span class="badge"><?php echo $signUpStatus['totalTenants']; ?></span></a>
            </div>
        </div>

    </div>

    <div class="row form-group">

        <?php
        $allBuildingsFromTable = $DB_building->getAllBdRowsByCompany($companyId);

        if ($employeeId != "0000") { // This user is not an admin -  show all the buildings
            $allBuildings = array();

            $employeeData = $DB_employee->getEmployeeInfo($employeeIdValue);
            $buildingIds = $employeeData["building_ids"];

            $buildingIdArray = explode(",", $buildingIds);

            foreach ($buildingIdArray as $bId) {
                array_push($allBuildings, $DB_building->getBdInfo($bId));
            }
        } else {
            $allBuildings = $allBuildingsFromTable;
        }

        if (count($allBuildings) < 1) {
            echo "No Building Data Found";
        }

        $numberOfBuildingsPerRow = 4; // Number of building boxes to show in a row in the page
        $totalBuildingNum = count($allBuildings); // Count the number of buildings in the employees list
        $totalRowsToShow = ceil($totalBuildingNum / $numberOfBuildingsPerRow); // the total number of rows to show for the given number of buildings

        $rowIndex = 0; // Control the number of rows
        $buildingIndex = 0; // Control the building index - to control the real building data index

        while ($rowIndex < $totalRowsToShow) {
            $showingPanelIndex = 0; // Index to control the follow $numberofBuildingsPerRow count
        ?>
            <div class="row form-group">
                <?php
                while ($showingPanelIndex < $numberOfBuildingsPerRow && $buildingIndex < $totalBuildingNum) {

                    $buildingId = $allBuildings[$buildingIndex]["building_id"];
                    $buildingData = getBuildingDataToShow($buildingId, $DB_apt, $DB_building, $employeeIdValue, $DB_request, $DB_employee);

                    $buildingImg = "../../../admin/files/building_pictures/" . $allBuildings[$buildingIndex]["feature_picture"];
                    $alternativeImg = "../../../admin/files/imgnotfound.png";
                ?>
                    <div class="col-md">

                        <div class="card">
                            <div class="card-header text-center bg-info text-white" style="height: 50px">
                                <a href="<?php echo 'building_infosview.php?showdetail=&building_id=' . $buildingId; ?>" style="font-size: 12px; color: white"><strong><?php echo $allBuildings[$buildingIndex]["building_name"]; ?>
                                    </strong></a>
                            </div>

                            <div class="card-body">

                                <div class="row text-center">
                                    <strong class="col-md-4" style="font-size: 10px"><?php echo $DB_snapshot->echot("Total Units"); ?> <br></strong>
                                    <strong class="col-md-4" style="font-size: 10px"><?php echo $DB_snapshot->echot("Total Parking Spots"); ?>
                                    </strong>
                                    <strong class="col-md-4" style="font-size: 10px"><?php echo $DB_snapshot->echot("Total Storage"); ?></strong>
                                </div>
                                <div class="row text-center">

                                    <div class="col-md-4"> <a href="<?php echo "apartmentinfoslist?cmd=search&t=apartment_infos&x_building_id=" . $buildingId; ?>">
                                            <?php echo $buildingData['totalUnit']; ?> </a></div>
                                    <div class="col-md-4"> <a href="<?php echo "parking_unit_infoslist.php?showmaster=building_infos&fk_building_id=" . $buildingId; ?>">
                                            <?php echo $buildingData['totalParking']; ?> </a> </div>
                                    <div class="col-md-4"> <a href="<?php echo "storage_unit_infoslist.php?showmaster=building_infos&fk_building_id=" . $buildingId; ?>">
                                            <?php echo $buildingData['totalStorage']; ?> </a></div>

                                </div>

                                <div class="row text-center bg-dark form-group" style="margin-top:15px;padding:5px;">
                                    <div class="col-md-12 "><strong><?php echo $DB_snapshot->echot("Vacancy"); ?> </strong>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="table-responsive">
                                        <table class="table table-condensed table-striped text-center">
                                            <tbody>
                                                <tr>
                                                    <td style="border:none;" class="col-md-4">
                                                        <?php echo $DB_snapshot->echot("Units"); ?> </td>
                                                    <td style="border:none;" class="col-md-4">
                                                        <?php echo $DB_snapshot->echot("Parking Spots"); ?> </td>
                                                    <td style="border:none;" class="col-md-4">
                                                        <?php echo $DB_snapshot->echot("Storage Units"); ?> </td>
                                                </tr>
                                                <tr>
                                                    <td style="border:none;"> <a href="<?php echo "apartmentinfoslist?cmd=search&t=apartment_infos&x_apartment_status=5&x_building_id=" . $buildingId; ?>">
                                                            <?php echo $buildingData['vacantUnit']; ?> </a></td>
                                                    <td style="border:none;"> <a href="<?php echo "parking_unit_infoslist.php?showmaster=building_infos&x_status_id=1&fk_building_id=" . $buildingId; ?>">
                                                            <?php echo $buildingData['vacantParking']; ?></td>
                                                    <td style="border:none;"> <a href="<?php echo "storage_unit_infoslist.php?showmaster=building_infos&x_status_id=1&fk_building_id=" . $buildingId; ?>">
                                                            <?php echo $buildingData['vacantStorage']; ?></td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>



                                <div class="row form-group text-center" id="buildingImg">
                                    <div class="col-md-12">
                                        <a href="<?php echo 'building_infosview.php?showdetail=&building_id=' . $buildingId; ?>">
                                            <img class="img-responsive img-thumbnail img-building-front" onerror="<?php echo "this.onerror=null;this.src='" . $alternativeImg . "';" ?>" src="<?php echo $buildingImg; ?>" /> </a>
                                    </div>
                                </div>

                                <div class="row text-center alert-info form-group">
                                    <div class="col-md-12 text-center" style="padding:5px;">
                                        <strong><?php echo $DB_snapshot->echot("Unresolved Issues"); ?>
                                            <?php echo $buildingData['totalOpenIssue']; ?> </strong>
                                    </div>
                                </div>

                                <div class="row text-center form-group">

                                    <strong class="col-md-4" style="font-size: 11px"><?php echo $DB_snapshot->echot("Normal"); ?> <br></strong>
                                    <strong class="col-md-4" style="font-size: 11px"><?php echo $DB_snapshot->echot("Serious"); ?> </strong>
                                    <strong class="col-md-4" style="font-size: 11px"><?php echo $DB_snapshot->echot("Urgent"); ?> </strong>

                                </div>
                                <div class="row text-center">

                                    <div class="col-md-4"> <?php echo $buildingData['normalIssue']; ?> </div>
                                    <div class="col-md-4"> <?php echo $buildingData['seriousIssue']; ?> </div>
                                    <div class="col-md-4"> <?php echo $buildingData['urgentIssue']; ?> </div>

                                </div>

                            </div>
                        </div>

                    </div>

                <?php
                    $showingPanelIndex++;
                    $buildingIndex++;
                }
                ?>
            </div>
        <?php
            $rowIndex++;
        }
        ?>

    </div>
</div>

<style>
    .img-building-front {
        height: 110px;
        width: auto;
        /*maintain aspect ratio*/
        max-width: 110px;
    }
</style>
<script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://printjs-4de6.kxcdn.com/print.min.css">

<!--<script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://printjs-4de6.kxcdn.com/print.min.css">-->