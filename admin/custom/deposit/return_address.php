<?php
function return_address($chequeData)
{
  global $DB_ls_payment, $DB_building;

  $building_names = array();
  foreach ($chequeData as $pid => $data) {
    $leaseInfo = $DB_ls_payment->get_lease_info_by_lease_payment_detail_id($pid);
    // echo ($pid);
    if (!empty($leaseInfo)) {
      $building_name = $DB_building->getBdName($leaseInfo["building_id"]);
      // $apartmentId = $leaseInfo["apartment_id"];
      array_push($building_names, $building_name);
    }
  }
  $building_names = array_unique($building_names);
  // die(print_r($building_names));

  return implode(", ", $building_names);
}