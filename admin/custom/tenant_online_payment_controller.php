<?php
$selected_payment_ids = $_POST['selected_lease_payment_ids'];
if(is_array($selected_payment_ids)){
  echo "yes arr";
}else{
  echo "not arr";
}
echo "tp of :" . gettype($selected_payment_ids).'<br>';

$apartment_id = $_POST['apartment_id'];
$lease_id = $_POST['lease_id'];
$selected_gateway_name = 'paymentType'.$apartment_id.'_'.$lease_id;
$selected_gateway = $_POST[$selected_gateway_name];


echo "selected payment_ids:";


foreach ($selected_payment_ids as $one){
    echo "$one"."|";
}
echo '<br>';

echo "apartment_id:".$apartment_id;
echo "<br>";
echo "lease_id" . $lease_id;
echo "<br>";
echo "gateway:" . $selected_gateway;


