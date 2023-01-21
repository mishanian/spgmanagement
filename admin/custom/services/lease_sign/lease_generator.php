<?php
require('fpdf/fpdf.php');
require('send_sign.php');

$name_lessor1=$_POST['name_lessor1'];
$email_lessor1=$_POST['email_lessor1'];
$address_lessor1=$_POST['address_lessor1'];
$municipality_lessor1=$_POST['municipality_lessor1'];
$postcode_lessor1=$_POST['postcode_lessor1'];
$telephone_lessor1=$_POST['telephone_lessor1'];
$otherphone_lessor1="";
if(isset($_POST['otherphone_lessor1']))
    $otherphone_lessor1=$_POST['otherphone_lessor1'];

$name_lessor2="";
$email_lessor2="";
$address_lessor2="";
$municipality_lessor2="";
$postcode_lessor2="";
$telephone_lessor2="";
$otherphone_lessor2="";
if(isset($_POST['second_lessor'])){
    $name_lessor2=$_POST['name_lessor2'];
    $email_lessor2=$_POST['email_lessor2'];
    $address_lessor2=$_POST['address_lessor2'];
    $municipality_lessor2=$_POST['municipality_lessor2'];
    $postcode_lessor2=$_POST['postcode_lessor2'];
    $telephone_lessor2=$_POST['telephone_lessor2'];
    if(isset($_POST['otherphone_lessor2']))
        $otherphone_lessor2=$_POST['otherphone_lessor2'];
}

$name_lessee1=$_POST['name_lessee1'];
$email_lessee1=$_POST['email_lessee1'];
$address_lessee1=$_POST['address_lessee1'];
$municipality_lessee1=$_POST['municipality_lessee1'];
$postcode_lessee1=$_POST['postcode_lessee1'];
$telephone_lessee1=$_POST['telephone_lessee1'];
$otherphone_lessee1="";
if(isset($_POST['otherphone_lessee1']))
    $otherphone_lessee1=$_POST['otherphone_lessee1'];

$name_lessee2="";
$email_lessee2="";
$address_lessee2="";
$municipality_lessee2="";
$postcode_lessee2="";
$telephone_lessee2="";
$otherphone_lessee2="";
if(isset($_POST['second_lessee'])){
    $name_lessee2=$_POST['name_lessee2'];
    $email_lessee2=$_POST['email_lessee2'];
    $address_lessee2=$_POST['address_lessee2'];
    $municipality_lessee2=$_POST['municipality_lessee2'];
    $postcode_lessee2=$_POST['postcode_lessee2'];
    $telephone_lessee2=$_POST['telephone_lessee2'];
    if(isset($_POST['otherphone_lessee2']))
    $otherphone_lessee2=$_POST['otherphone_lessee2'];
}

//partB
$address_lease=$_POST['address_lease'];
$municipality_lease=$_POST['municipality_lease'];
$postcode_lease=$_POST['postcode_lease'];
$no_rooms=$_POST['no_rooms'];
$resident_only=$_POST['resident_only'];
if($resident_only==1){
    $usage_lease="";
}else{
    $usage_lease=$_POST['usage_lease'];
}
$divided_co_ownership=$_POST['divided_co_ownership'];

$outdoor_parking_num="";
$outdoor_parking_ps="";
if(isset($outdoor_parking)){
    $outdoor_parking_num=$_POST['outdoor_parking_num'];
    $outdoor_parking_ps=$_POST['outdoor_parking_ps'];
}

$indoor_parking_num="";
$indoor_parking_ps="";
if(isset($indoor_parking)){
    $indoor_parking_num=$_POST['indoor_parking_num'];
    $indoor_parking_ps=$_POST['indoor_parking_ps'];
}
$locker_spf="";
if(isset($_POST['locker']))
    $locker_spf=$_POST['locker_spf'];

$other_accessory="";
if(isset($_POST['other_accessory']))
    $other_accessory=$_POST['other_accessory'];

$furniture_include=$_POST['furniture_include'];

$table_num="";
if(isset($_POST['table']))
    $table_num=$_POST['table_num'];
$chair_num="";
if(isset($_POST['chair']))
    $chair_num=$_POST['chair_num'];
$chest_num="";
if(isset($_POST['chest']))
    $chest_num=$_POST['chest_num'];
$couch_num="";
if(isset($_POST['couch']))
    $couch_num=$_POST['couch_num'];
$armchair_num="";
if(isset($_POST['armchair']))
    $armchair_num=$_POST['armchair_num'];
$bed_num="";
if(isset($_POST['bed']))
    $bed_num=$_POST['bed_num'];
$appliance_other="";
if(isset($_POST['appliance_other']))
    $appliance_other=$_POST['appliance_other'];

$term_type=$_POST['term_type'];
$lease_date_start=explode('-',$_POST['lease_date_start']);
if($term_type==1)
    $lease_date_end=explode('-',$_POST['lease_date_end']);

$pure_rent=$_POST['pure_rent'];
$pure_rent_period=$_POST['pure_rent_period'];
$cost_service=$_POST['cost_service'];
$cost_service_period=$_POST['cost_service_period'];
$rent_total=$_POST['rent_total'];
$rent_total_period=$_POST['rent_total_period'];
$subsidy_program=$_POST['subsidy_program'];

$rent_pay_date=explode('-',$_POST['rent_pay_date']);
$rent_pay_time=$_POST['rent_pay_time'];
$pay_method=$_POST['pay_method'];
$pay_method_other="";
if(in_array('0',$pay_method))
    $pay_method_other=$_POST['pay_method_other'];
$postdated_cheque=$_POST['postdated_cheque'];
$pay_place=$_POST['pay_place'];

$immovable_date=explode('-',$_POST['immovable_date']);
$before_work=$_POST['before_work'];
$during_work=$_POST['during_work'];
$janitorial_service=$_POST['janitorial_service'];

$janitor_name="";
$janitor_email="";
$janitor_tele1="";
$janitor_tele2="";
if(isset($_POST['if_janitor'])){
    $janitor_name=$_POST['janitor_name'];
    $janitor_email=$_POST['janitor_email'];
    $janitor_tele1=$_POST['janitor_tele1'];
    $janitor_tele2=$_POST['janitor_tele2'];
}

$service_heating=$_POST['service_heating'];
$water_consumption=$_POST['water_consumption'];
$gas=$_POST['gas'];
$electricity=$_POST['electricity'];
$hot_water_heater=$_POST['hot_water_heater'];
$hot_water=$_POST['hot_water'];
$sonwrm_parking=$_POST['sonwrm_parking'];
$snowrm_balcony=$_POST['snowrm_balcony'];
$snowrm_entrance=$_POST['snowrm_entrance'];
$snowrm_stars=$_POST['snowrm_stars'];

$right_access_land=$_POST['right_access_land'];
$right_access_land_spf=$_POST['right_access_land_spf'];
$right_keep_animals=$_POST['right_keep_animals'];
$right_keep_animals_spf=$_POST['right_keep_animals_spf'];
$other_services=$_POST['other_services'];

$restriction_immovable=$_POST['restriction_immovable'];
$restriction_immovable_date=explode('-',$_POST['restriction_immovable_date']);

$lowest_rent=$_POST['lowest_rent'];
$lowest_rent_period=$_POST['lowest_rent_period'];
$lowest_rent_period_other="";
if($lowest_rent_period==0){
    $lowest_rent_period_other=$_POST['lowest_rent_period_other'];
}

$condition_same=$_POST['condition_same'];
$condition_changes="";
if($condition_same==0){
    $condition_changes=$_POST['condition_changes'];
}



$pdf = new FPDF('P','mm','Legal');
$pdf->SetAutoPageBreak(true,0);
$pdf->SetTopMargin(0);

########### page1 ##########
$pdf->AddPage();
$pdf->Image('lease_top.png',$pdf->GetX(),0,195,35,'PNG');

$pdf->SetY(35);
$pdf->SetFillColor(205,173,0);
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(195,6,"MANDATORY FORM | TWO COPIES",1,1,'L','F');

#page1-A
$pdf->SetFillColor(65,105,225);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(6,6,"A",1,0,'L','F');

$pdf->SetFont('Arial','B',11);
$pdf->Cell(47,6,"BETWEEN THE LESSOR","B",0,'L','F');

$pdf->SetFont('Arial','',9);
$pdf->Cell(45,6,"(WRITE LEGIBLY)","B",0,'L','F');

$pdf->SetFont('Arial','B',11);
$pdf->Cell(30,6,"AND THE LESS","B",0,'L','F');

$pdf->SetFont('Arial','',9);
$pdf->Cell(67,6,"(WRITE LEGIBLY)","BR",1,'L','F');
$pdf->SetTextColor(0,0,0);

//form
$y_origin=$pdf->GetY();
$x_origin=$pdf->GetX();
$pdf->Line($x_origin,$y_origin,$x_origin,$y_origin+295);//left
$pdf->Line($x_origin+97,$y_origin+3,$x_origin+97,$y_origin+102);//upper-center
$pdf->Line($x_origin+195,$y_origin,$x_origin+195,$y_origin+295);//right

$pdf->SetFont('Times',"",9.2);
$pdf->SetX($pdf->GetX()+2);
$pdf->Cell(92,6,$name_lessor1,'B',0,'L');
$pdf->SetX($pdf->GetX()+6);
$pdf->Cell(92,6,$name_lessee1,'B',1,'L');
$pdf->SetX($pdf->GetX()+1);
$pdf->SetFont('Arial',"B",9);
$pdf->Cell(98,4,"Name",0,0,'L');
$pdf->Cell(95,4,"Name",0,1,'L');

$pdf->SetFont('Times',"",9.2);
$pdf->SetX($pdf->GetX()+2);
$pdf->Cell(92,5,$address_lessor1,'B',0,'L');
$pdf->SetX($pdf->GetX()+6);
$pdf->Cell(92,5,$address_lessee1,'B',1,'L');
$pdf->SetFont('Arial',"",9);
$pdf->SetX($x_origin+2);
$pdf->Cell(20,4,"No.",0,0,'L');
$pdf->Cell(50,4,"Street",0,0,'L');
$pdf->Cell(28,4,"Apt.",0,0,'L');
$pdf->Cell(20,4,"No.",0,0,'L');
$pdf->Cell(50,4,"Street",0,0,'L');
$pdf->Cell(25,4,"Apt.",0,1,'L');

$pdf->SetX($pdf->GetX()+2);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(92,5,$municipality_lessor1.str_pad('',58).$postcode_lessor1,'B',0,'L');
$pdf->SetX($pdf->GetX()+6);
$pdf->Cell(92,5,$municipality_lessee1.str_pad('',58).$postcode_lessee1,'B',1,'L');
$pdf->SetX($x_origin+2);
$pdf->SetFont('Arial',"",9);
$pdf->Cell(60,4,"Municipality",0,0,'L');
$pdf->Cell(38,4,"Postal code",0,0,'L');
$pdf->Cell(60,4,"Municipality",0,0,'L');
$pdf->Cell(35,4,"Postal code",0,1,'L');


$pdf->SetX($pdf->GetX()+2);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(92,5,$telephone_lessor1.str_pad('',30).$otherphone_lessor1,'B',0,'L');
$pdf->SetX($pdf->GetX()+6);
$pdf->Cell(92,5,$telephone_lessee1.str_pad('',30).$otherphone_lessee1,'B',1,'L');
$pdf->SetX($x_origin+2);
$pdf->SetFont('Arial',"",9);
$pdf->Cell(40,4,"Telephone No.",0,0,'L');
$pdf->Cell(58,4,"Other Telephone No.(cell phone)",0,0,'L');
$pdf->Cell(40,4,"Telephone No.",0,0,'L');
$pdf->Cell(55,4,"Other Telephone No.(cell phone)",0,1,'L');

$pdf->SetX($pdf->GetX()+2);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(92,5,$email_lessor1,'B',0,'L');
$pdf->SetX($pdf->GetX()+6);
$pdf->Cell(92,5,$email_lessee1,'B',1,'L');
$pdf->SetX($x_origin+2);
$pdf->SetFont('Arial',"",9);
$pdf->Cell(98,4,"Email address",0,0,'L');
$pdf->Cell(95,4,"Email address",0,1,'L');


$pdf->SetXY($pdf->GetX()+2,$pdf->GetY()+2);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(92,5,$name_lessor2,'B',0,'L');
$pdf->SetX($pdf->GetX()+6);
$pdf->Cell(92,5,$name_lessee2,'B',1,'L');
$pdf->SetFont('Arial',"B",9);
$pdf->SetX($x_origin+2);
$pdf->SetFont('Arial',"",9);
$pdf->Cell(97,5,"Name",0,0,'L');
$pdf->Cell(95,5,"Name",0,1,'L');

$pdf->SetX($pdf->GetX()+2);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(92,5,$address_lessor2,'B',0,'L');
$pdf->SetX($pdf->GetX()+6);
$pdf->Cell(92,5,$address_lessee2,'B',1,'L');
$pdf->SetFont('Arial',"",9);
$pdf->SetX($x_origin+2);
$pdf->SetFont('Arial',"",9);
$pdf->Cell(20,4,"No.",0,0,'L');
$pdf->Cell(50,4,"Street",0,0,'L');
$pdf->Cell(28,4,"Apt.",0,0,'L');
$pdf->Cell(20,4,"No.",0,0,'L');
$pdf->Cell(50,4,"Street",0,0,'L');
$pdf->Cell(25,4,"Apt.",0,1,'L');

$pdf->SetX($pdf->GetX()+2);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(92,5,$municipality_lessor2.str_pad('',60).$postcode_lessor2,'B',0,'L');
$pdf->SetX($pdf->GetX()+6);
$pdf->Cell(92,5,$municipality_lessee2.str_pad('',60).$municipality_lessee2,'B',1,'L');
$pdf->SetX($x_origin+2);
$pdf->SetFont('Arial',"",9);
$pdf->Cell(60,4,"Municipality",0,0,'L');
$pdf->Cell(38,4,"Postal code",0,0,'L');
$pdf->Cell(60,4,"Municipality",0,0,'L');
$pdf->Cell(35,4,"Postal code",0,1,'L');

$pdf->SetX($pdf->GetX()+2);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(92,5,$telephone_lessor2.str_pad('',30).$otherphone_lessor2,'B',0,'L');
$pdf->SetX($pdf->GetX()+6);
$pdf->Cell(92,5,$telephone_lessee2.str_pad('',30).$otherphone_lessee2,'B',1,'L');
$pdf->SetX($x_origin+2);
$pdf->SetFont('Arial',"",9);
$pdf->Cell(40,4,"Telephone No.",0,0,'L');
$pdf->Cell(57,4,"Other Telephone No.(cell phone)",0,0,'L');
$pdf->Cell(40,4,"Telephone No.",0,0,'L');
$pdf->Cell(55,4,"Other Telephone No.(cell phone)",0,1,'L');

$pdf->SetX($pdf->GetX()+2);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(92,5,$email_lessor2,'B',0,'L');
$pdf->SetX($pdf->GetX()+6);
$pdf->Cell(92,5,$email_lessee2,'B',1,'L');
$pdf->SetX($x_origin+3);
$pdf->SetFont('Arial',"",9);
$pdf->Cell(97,4,"Email",0,0,'L');
$pdf->Cell(95,4,"Email",0,1,'L');

$pdf->SetXY($x_origin+1,$pdf->GetY()+2);
$pdf->Cell(50,5,"Where applicable,represented by:","",0,"L");
$pdf->Cell(43,4,'','B',1,'L');


$pdf->SetY($pdf->GetY()+2);
$pdf->SetFillColor(176,196,222);
$pdf->SetFont('Arial',"B",9);
$pdf->Cell(195,5,"The name indicated in the lease must be those that the lessor and the lessee are legally authorized to use.","LR",1,"C",'F');
$pdf->Cell(195,5,'The term "lessor" in the Civil Code of Quebec generally refers to the owner of the immovable. ',"LR",1,"C",'F');



#page1-B
$pdf->SetFillColor(65,105,225);
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',9.8);
$pdf->Cell(6,6,"B",1,0,'L','F');
$pdf->Cell(162,6,"DESCRIPTION AND DESTINATION OF LEASED DWELLING, ACCESSORIES AND DEPENDENCIES","TB",0,'L','F');
$pdf->SetFont('Arial',"",8);
$pdf->Cell(27,6,"(art.1892 C.C.Q.)","TB",1,'L','F');
$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','B',9.5);
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(18,9,"Address",0,0,'L');
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(173,6,$address_lease,"B",1,'L');
$pdf->SetX($pdf->GetX()+20);
$pdf->SetFont('Arial',"",9);

$pdf->Cell(30,4,"No.",0,0,'L');
$pdf->Cell(105,4,"Street",0,0,'L');
$pdf->Cell(55,4,"Apt.",0,1,'L');

$pdf->SetX($pdf->GetX()+3);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(189,5,$municipality_lease.str_pad('',110).$postcode_lease.str_pad('',50).$no_rooms,"B",1,'L');
$pdf->SetX($pdf->GetX()+3);
$pdf->SetFont('Arial',"",9);
$pdf->Cell(100,4,"Municipality",0,0,'L');
$pdf->Cell(40,4,"Postal code",0,0,'L');
$pdf->Cell(50,4,"Number of rooms",0,1,"L");

$pdf->SetFont('Arial',"",10);
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(85,6,"The dwelling is leased for residential purposes only.",0,0,"L");
$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->SetFillColor(0,0,0);
if($resident_only==1){
    $pdf->Rect($x,$y+2,3,3,'F');
    $pdf->Rect($x+16,$y+2,3,3);

}else{
    $pdf->Rect($x,$y+2,3,3);
    $pdf->Rect($x+16,$y+2,3,3,'F');

}
$pdf->Cell(13,6,"Yes",0,0,"R");
$pdf->Cell(13,6,"No",0,1,"R");

$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(149,6,'If the "No" box is checked off,the dwelling is leased for the combined purposes of housing and',0,0,"L");
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(42,5,$usage_lease,"B",1,"L");

$pdf->SetFont('Arial',"",8);
$pdf->SetX($pdf->GetX()+149);
$pdf->Cell(39,3,"Specify (e.g.professional activities)","",1,"L");

$pdf->SetFont('Arial',"",10);
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(190,6,"but no more than one-third of the total floor area will be used for that second purpose (art. 1892 C.C.Q.).)","",1,"L");
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(93,6,"The dwelling is located in a unit under divided co-ownership.","",0,"L");
$x=$pdf->GetX();
$y=$pdf->GetY();
if($divided_co_ownership==1){
    $pdf->Rect($x+5,$y+2,3,3,'F');
    $pdf->Rect($x+21,$y+2,3,3);
}else{
    $pdf->Rect($x+5,$y+2,3,3);
    $pdf->Rect($x+21,$y+2,3,3,'F');
}
$pdf->Cell(16,8,"Yes",0,0,"R");
$pdf->Cell(16,8,"No",0,1,"R");

$x=$pdf->GetX();
$y=$pdf->GetY();
if(isset($outdoor_parking))
    $pdf->Rect($x+2,$y,3,3,'F');
else
    $pdf->Rect($x+2,$y,3,3);
$pdf->Cell(34,3,"Outdoor parking",0,0,"R");
$pdf->Cell(50,5,"Number of place",0,0,"R");
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(25,4,$outdoor_parking_num,"B",0,"C");
$pdf->SetFont('Arial',"",10);
$pdf->Cell(37,6,"Parking space(s)",0,0,"R");
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(46,4,$outdoor_parking_ps,"B",1,"C");
$pdf->SetFont('Arial',"",10);

$x=$pdf->GetX();
$y=$pdf->GetY();
if(isset($indoor_parking))
    $pdf->Rect($x+2,$y+2,3,3,'F');
else
    $pdf->Rect($x+2,$y+2,3,3);
$pdf->Cell(32,7,"Indoor parking",0,0,"R");
$pdf->Cell(52,9,"Number of place",0,0,"R");
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(25,6,$indoor_parking_num,"B",0,"C");
$pdf->SetFont('Arial',"",10);
$pdf->Cell(37,10,"Parking space(s)",0,0,"R");
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(46,6,$indoor_parking_ps,"B",1,"C");
$pdf->SetFont('Arial',"",10);

$x=$pdf->GetX();
$y=$pdf->GetY();
if(isset($_POST['locker']))
    $pdf->Rect($x+2,$y+2,3,3,'F');
else
    $pdf->Rect($x+2,$y+2,3,3);
$pdf->Cell(47,8,"Locker or storage space",0,0,"R");
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(145,6,$locker_spf,"B",1,"L");
$pdf->SetFont('Arial',"",8);
$pdf->Cell(58,4,"Specify",0,1,"R");


$pdf->SetFont('Arial',"B",10);
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(65,5,"Other accessories and dependencies",0,0,"L");
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(126,4,$other_accessory,"B",1,"L");
$pdf->SetFont('Arial',"",8);
$pdf->Cell(76,4,"Specify",0,1,"R");

$pdf->SetFont('Arial',"B",10);
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(17,6,"Furniture",0,0,"L");
$pdf->SetFont('Arial',"",10);
$pdf->Cell(55,6,"is leased and included in the rent",0,0,"L");
$x=$pdf->GetX();
$y=$pdf->GetY();

if($furniture_include==1){
    $pdf->Rect($x,$y+2,3,3,'F');
    $pdf->Rect($x+19,$y+2,3,3);
}else{
    $pdf->Rect($x,$y+2,3,3);
    $pdf->Rect($x+19,$y+2,3,3,'F');
}
$pdf->Cell(13,8,"Yes",0,0,"R");
$pdf->Cell(16,8,"No",0,1,"R");


$pdf->SetFont('Arial',"B",10);
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(20,6,"Appliances",0,0);
$x=$pdf->GetX();
$y=$pdf->GetY();
if(isset($_POST['washer']))
    $pdf->Rect($x+18,$y+1,3,3,'F');
else
    $pdf->Rect($x+18,$y+1,3,3);
$pdf->SetX($x+21);
$pdf->SetFont('Arial',"",10);
$pdf->Cell(16,6,"Washer",0,0,"L");
if(isset($_POST['chest']))
    $pdf->Rect($x+62,$y+1,3,3,'F');
else
    $pdf->Rect($x+62,$y+1,3,3);
$pdf->SetX($x+65);
$pdf->Cell(33,6,"Chest(s) of drawers",0,0,"L");
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(22,4,$chest_num,"B",0,"C");
$pdf->SetFont('Arial',"B",10);
$pdf->Cell(20,4,"Other","",1,"R");
$pdf->SetFont('Arial',"",8);
$pdf->Cell(130,4,"Number",0,1,"R");

$x=$pdf->GetX();
$y=$pdf->GetY();
if(isset($_POST['stove']))
    $pdf->Rect($x+2,$y,3,3,'F');
else
    $pdf->Rect($x+2,$y,3,3);
$pdf->SetFont('Arial',"",10);
$pdf->Cell(16,4,"Stove",0,0,"R");
if(isset($_POST['dryer']))
    $pdf->Rect($x+39,$y,3,3,'F');
else
    $pdf->Rect($x+39,$y,3,3);
$pdf->Cell(37,4,"Dryer",0,0,"R");
if(isset($_POST['couch']))
    $pdf->Rect($x+83,$y,3,3,'F');
else
    $pdf->Rect($x+83,$y,3,3);
$pdf->Cell(51,4,"Couch(es)",0,0,"R");
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(37,3,$couch_num,"B",0,"C");
if(isset($_POST['appliance_other']))
    $pdf->Rect($x+151,$y,3,3,'F');
else
    $pdf->Rect($x+151,$y,3,3);
$pdf->SetX($x+155);
$pdf->Cell(37,3,$appliance_other,"B",1,"L");
$pdf->SetFont('Arial',"",8);
$pdf->Cell(117,4,"Number",0,1,"R");

$x=$pdf->GetX();
$y=$pdf->GetY();

if(isset($_POST['microwave']))
    $pdf->Rect($x+2,$y,3,3,'F');
else
    $pdf->Rect($x+2,$y,3,3);
$pdf->SetFont('Arial',"",10);
$pdf->Cell(33,4,"Microwave oven",0,0,"R");
$pdf->SetFont('Arial',"B",10);
$pdf->Cell(22,4,"Furniture",0,0,"R");
if(isset($_POST['armchair']))
    $pdf->Rect($x+83,$y,3,3,'F');
else
    $pdf->Rect($x+83,$y,3,3);
$pdf->SetFont('Arial',"",10);
$pdf->Cell(51,4,"Armchair(s)",0,0,"R");
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(34,3,$armchair_num,"B",0,"C");
$pdf->SetX($x+151);
$pdf->Cell(41,3,"","B",1,"L");
$pdf->SetFont('Arial',"",8);
$pdf->Cell(117,4,"Number",0,1,"R");

$x=$pdf->GetX();
$y=$pdf->GetY();
if(isset($_POST['dishwasher']))
    $pdf->Rect($x+2,$y,3,3,'F');
else
    $pdf->Rect($x+2,$y,3,3);
$pdf->SetFont('Arial',"",10);
$pdf->Cell(26,4,"Dishwasher",0,0,"R");
if(isset($_POST['table']))
    $pdf->Rect($x+39,$y,3,3,'F');
else
    $pdf->Rect($x+39,$y,3,3);
$pdf->Cell(31,4,"Table(s)",0,0,"R");
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(22,3,$table_num,"B",0,"C");
if(isset($_POST['bed']))
    $pdf->Rect($x+83,$y,3,3,'F');
else
    $pdf->Rect($x+83,$y,3,3);
$pdf->Cell(19,4,"Bed(s)",0,0,"R");
$pdf->Cell(43,3,$bed_num,"B",0,"C");
$pdf->SetX($x+151);
$pdf->Cell(41,3,"","B",1,"L");
$pdf->SetFont('Arial',"",8);
$pdf->Cell(68,4,"Number",0,0,"R");
$pdf->Cell(42,4,"Number",0,0,"R");
$pdf->Cell(20,4,"Size",0,1,"R");

$x=$pdf->GetX();
$y=$pdf->GetY();
if(isset($_POST['refrigerator']))
    $pdf->Rect($x+2,$y,3,3,'F');
else
    $pdf->Rect($x+2,$y,3,3);
$pdf->SetFont('Arial',"",10);
$pdf->Cell(26,4,"Refrigerator",0,0,"R");
if(isset($_POST['chair']))
    $pdf->Rect($x+39,$y,3,3,'F');
else
    $pdf->Rect($x+39,$y,3,3);
$pdf->Cell(31,4,"Chair(s)",0,0,"R");
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(22,4,$chair_num,"B",0,"C");
$pdf->SetX($x+151);
$pdf->Cell(41,4,"","B",1,"L");
$pdf->SetFont('Arial',"",8);
$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Cell(68,4,"Number",0,1,"R");


$pdf->SetLineWidth(0.4);
$pdf->Rect($x+3,$y+4,188,18);
$pdf->SetFont('Arial',"",9);
$pdf->Cell(190,4,"The lessor and the lessee undertake, in accordance with their respective responsibilities, to
comply with the regulations","",1,"C");
$pdf->Cell(190,5,"respecting the presence and proper working order of one or mroe smoke detectors in the dwelling and 
the immovable","",1,"C");
$pdf->SetX($pdf->GetX()+13);
$pdf->SetLineWidth(0.2);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(20,5,'lessor1_init',"B",0,"L");
$pdf->SetX($pdf->GetX()+5);
$pdf->Cell(20,5,"lessor2_init","B",0,"L");
$pdf->SetXY($pdf->GetX()+5,$pdf->GetY()+3);
$pdf->Cell(7,2,"lessor_sign_date","LBR",0,"C");
$pdf->Cell(7,2,"","LBR",0,"C");
$pdf->Cell(7,2,"","LBR",0,"C");
$pdf->SetXY($pdf->GetX()+20,$pdf->GetY()-3);
$pdf->Cell(20,5,"lessee1_init","B",0,"L");
$pdf->SetX($pdf->GetX()+5);
$pdf->Cell(20,5,"lessee2_init","B",0,"L");
$pdf->SetXY($pdf->GetX()+5,$pdf->GetY()+3);
$pdf->Cell(7,2,"lessee1_sign_date","LBR",0,"C");
$pdf->Cell(7,2,"","LBR",0,"C");
$pdf->Cell(7,2,"","LBR",1,"C");
$pdf->SetTextColor(0,0,0);

$pdf->SetX($pdf->GetX()+13);
$pdf->SetFont('Arial',"",7);
$pdf->Cell(20,4,"Initials of lessor","",0,"L");
$pdf->SetX($pdf->GetX()+5);
$pdf->Cell(20,4,"Initial of lessor","",0,"L");
$pdf->SetX($pdf->GetX()+4);
$pdf->Cell(7,4,"Day","",0,"L");
$pdf->Cell(8,4,"Month","",0,"L");
$pdf->Cell(7,4,"Year","",0,"L");
$pdf->SetX($pdf->GetX()+20);
$pdf->Cell(20,4,"Initials of lessee","",0,"L");
$pdf->SetX($pdf->GetX()+5);
$pdf->Cell(20,4,"Initials of lessee","",0,"L");
$pdf->SetX($pdf->GetX()+4);
$pdf->Cell(7,4,"Day","",0,"L");
$pdf->Cell(8,4,"Month","",0,"L");
$pdf->Cell(7,4,"Year","",1,"L");


#page1-C
$pdf->setY($pdf->GetY()+1);
$pdf->SetFillColor(65,105,225);
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial','B',9.8);
$pdf->Cell(6,6,"C",1,0,'L','F');
$pdf->Cell(29,6,"TERM OF LEASE","TB",0,'L','F');
$pdf->SetFont('Arial',"",8);
$pdf->Cell(160,6,"(art.1892 C.C.Q.)","TB",1,'L','F');
$pdf->SetFont('Arial','B',9);
$pdf->SetTextColor(0,0,0);

$x_drawline=$pdf->GetX();
$y_drawline=$pdf->GetY();
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(97,6,"FIXED TERM LEASE","",0,'L');
$pdf->Cell(93,5,"INDETERMINATE TERM LEASE","",1,'L');

$pdf->Line($x_drawline+97,$y_drawline,$x_drawline+97,$y_drawline+22); //底部中线

$pdf->SetFont('Arial','',9.5);
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(37,6,"The term of the lease is","",0,'L');
$pdf->Cell(50,5,"","B",0,'L');
$pdf->Cell(4,6,".","",0,'R');
$pdf->Cell(65,5,"The term of the lease is indeterminate,","",1,'R');
$pdf->SetFont('Arial','',7);
$pdf->Cell(84,4,"Specify number of weeks,months or years","",1,'R');

$pdf->SetFont('Arial','',9.5);
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(10,4,"From","",0,'L');
$pdf->SetX($pdf->GetX()+3);
if($term_type==1){
    $pdf->Cell(10,4,$lease_date_start[0],"LRB",0,'C');
    $pdf->Cell(10,4,$lease_date_start[1],"LRB",0,'C');
    $pdf->Cell(10,4,$lease_date_start[2],"LRB",0,'C');
    $pdf->Cell(10,4,"to","",0,'C');
    $pdf->Cell(10,4,$lease_date_end[0],"LRB",0,'C');
    $pdf->Cell(10,4,$lease_date_end[1],"LRB",0,'C');
    $pdf->Cell(10,4,$lease_date_end[2],"LRB",0,'C');

}else{
    $pdf->Cell(10,4,"","LRB",0,'C');
    $pdf->Cell(10,4,"","LRB",0,'C');
    $pdf->Cell(10,4,"","LRB",0,'C');
    $pdf->Cell(10,4,"to","",0,'C');
    $pdf->Cell(10,4,"","LRB",0,'C');
    $pdf->Cell(10,4,"","LRB",0,'C');
    $pdf->Cell(10,4,"","LRB",0,'C');

}

$pdf->SetX($pdf->GetX()+14);
$pdf->Cell(19,4,"beginning on ","",0,'L');
$pdf->SetX($pdf->GetX()+3);
if($term_type==1){
    $pdf->Cell(10,4,"","LRB",0,'C');
    $pdf->Cell(10,4,"","LRB",0,'C');
    $pdf->Cell(10,4,"","LRB",0,'C');
}else{
    $pdf->Cell(10,4,$lease_date_start[0],"LRB",0,'C');
    $pdf->Cell(10,4,$lease_date_start[1],"LRB",0,'C');
    $pdf->Cell(10,4,$lease_date_start[2],"LRB",0,'C');
}
$pdf->Cell(10,4,".","",1,'L');
$pdf->SetX($pdf->GetX()+13);
$pdf->SetFont('Arial','',7);
$pdf->Cell(10,4,"Day","",0,'L');
$pdf->Cell(10,4,"Month","",0,'L');
$pdf->Cell(10,4,"Year","",0,'L');
$pdf->SetX($pdf->GetX()+9);
$pdf->SetFont('Arial','',7);
$pdf->Cell(10,4,"Day","",0,'L');
$pdf->Cell(10,4,"Month","",0,'L');
$pdf->Cell(10,4,"Year","",0,'L');
$pdf->SetX($pdf->GetX()+37);
$pdf->SetFont('Arial','',7);
$pdf->Cell(10,4,"Day","",0,'L');
$pdf->Cell(10,4,"Month","",0,'L');
$pdf->Cell(10,4,"Year","",1,'L');
$pdf->SetFont('Arial','B',7);
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(10,4,"Neither the lessor nor the lessee may terminate the lease unilaterally, except in tha case provided for by
law(particulars nos,5,9,23,24,45 and 51).","",1,"L");
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(10,5,"However, they may terminate the lease by mutual consent.","",1,'L');

$x_drawline=$pdf->GetX();
$y_drawline=$pdf->GetY();
$pdf->Line($x_drawline,$y_drawline+1,$x_drawline+195,$y_drawline+1); //底线

$pdf->SetFont('Arial','',9);
$pdf->Cell(64,8,"Regie du logement","",0,'L');
$pdf->Cell(75,8,"LES PUBLICATIONS DU QUEBEC","",0,'L');
$pdf->Cell(50,8,"May not be reproduced | August 2016","",1,'L');
$pdf->SetX($pdf->GetX()+139);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(25,3,"lessor1_init","B",0,'L');
$pdf->SetX($pdf->GetX()+5);
$pdf->Cell(25,3,"lessee1_init".str_pad(" ",5)."lessee2_init","B",1,'L');
$pdf->SetFont('Arial','',7);
$pdf->SetX($pdf->GetX()+139);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(25,3,"Initials of lessor","",0,'L');
$pdf->SetX($pdf->GetX()+6);
$pdf->Cell(25,3,"Initials of lessee","",0,'L');



########### page2 ##########

$pdf->AddPage();
$pdf->SetY($pdf->GetY()+2);
$x_drawline=$pdf->GetX();
$y_drawline=$pdf->GetY();
$pdf->Line($x_drawline,$y_drawline,$x_drawline,$y_drawline+345);//left
$pdf->Line($x_drawline+195,$y_drawline,$x_drawline+195,$y_drawline+345);//right
$pdf->Line($x_drawline,$y_drawline+345,$x_drawline+195,$y_drawline+345);//bottom

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(65,105,225);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(6,6,"D",1,0,'C',"F");
$pdf->Cell(11,6,"RENT","TB",0,'L',"F");
$pdf->SetFont('Arial',"",8);
$pdf->Cell(195-11-6,6,"(arts. 1855, 1903 and 1904 C.C.Q.)","TB",1,'L',"F");
$pdf->SetTextColor(0,0,0);

$pdf->SetY($pdf->GetY()+1);
$pdf->SetFont('Arial','',10);
$pdf->Cell(20,5,"The rent is $","",0,'L');
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(42,4,$pure_rent,"B",0,'L');
$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Cell(9,5,".","",0,'L');
$pdf->SetFillColor(0,0,0);
if($pure_rent_period==1){
    $pdf->Rect($x+5,$y+1,3,3,'F');
    $pdf->Rect($x+29,$y+1,3,3);
}
else{
    $pdf->Rect($x+5,$y+1,3,3);
    $pdf->Rect($x+29,$y+1,3,3,'F');
}
$pdf->SetFont('Arial','',10);
$pdf->Cell(23,5,"Per month","",0,'L');
$x_multiCell=$pdf->GetX()+38;
$y_multiCell=$pdf->GetY();
$pdf->Cell(36,5,"Per week","",1,'L');


$pdf->Cell(45,5,"The total cost of service is $","",0,'L');
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(17,4,$cost_service,"B",0,'L');
$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Cell(9,5,".","",0,'L');
if($cost_service_period==1) {
    $pdf->Rect($x + 5, $y + 1, 3, 3,'F');
    $pdf->Rect($x+29,$y+1,3,3);
}
else{
    $pdf->Rect($x + 5, $y + 1, 3, 3);
    $pdf->Rect($x+29,$y+1,3,3,'F');

}
$pdf->SetFont('Arial','',10);
$pdf->Cell(23,5,"Per month","",0,'L');
$pdf->Cell(36,5,"Per week","",1,'L');


$pdf->Cell(28,5,"The total rent is $","",0,'L');
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(34,4,$rent_total,"B",0,'L');
$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Cell(9,5,".","",0,'L');
if($rent_total_period) {
    $pdf->Rect($x + 5, $y + 1, 3, 3,'F');
    $pdf->Rect($x+29,$y+1,3,3);
}
else{
    $pdf->Rect($x + 5, $y + 1, 3, 3);
    $pdf->Rect($x+29,$y+1,3,3,'F');
}
$pdf->SetFont('Arial','',10);
$pdf->Cell(23,5,"Per month","",0,'L');
$pdf->Cell(36,5,"Per week","",1,'L');


$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->SetFont('Arial','B',9);
$pdf->SetXY($x+15,$y+5);
$pdf->SetFillColor(205,173,0);
$pdf->SetTextColor(255,255,255);
$pdf->MultiCell(108,4,"Where applicable,  enter the cost of services of a personal nature in \nSchedule 6 to the lease: Services Offered to the Lessee by the Lessor.",1,1,"F");
$pdf->SetTextColor(0,0,0);

$pdf->SetFont('Arial','',9);
$pdf->Cell(78,6,"The lessee is a beneficiary of a rent subsidy program.","",0,'L');
$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->SetFillColor(0,0,0);
if($subsidy_program==1) {
    $pdf->Rect($x + 3, $y + 2, 3, 3,'F');
    $pdf->Rect($x + 20, $y + 2, 3, 3);
}
else{
    $pdf->Rect($x + 3, $y + 2, 3, 3);
    $pdf->Rect($x + 20, $y + 2, 3, 3,'F');
}
$pdf->Cell(14,7,"Yes","",0,'R');
$pdf->Cell(16,7,"No","",1,'R');


$pdf->SetX($pdf->GetX()+2);
$pdf->Cell(122,3,"","B",1,'L');
$pdf->SetFont('Arial','',7);
$pdf->Cell(12,4,"Specify","",1,'R');

$pdf->SetFont('Arial','B',9);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(12,6,"DATE OF PAYMENT","",1,'L');
$pdf->SetTextColor(0,0,0);

$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->SetFillColor(0,0,255);
$pdf->Rect($x+2,$y+2,1,1,'F');
$pdf->SetFont('Arial','B',7);
$pdf->SetX($x+4);
$pdf->Cell(12,5,"FIRST PAYMENT PERIOD","",1,'L');
$pdf->SetFont('Arial','',10);
$pdf->SetX($pdf->GetX()+5);
$pdf->SetFont('Arial','',9);
$pdf->Cell(35,4,"The rent will be paid on","",0,'L');
$pdf->Cell(9,3,$rent_pay_date[0],"LRB",0,'C');
$pdf->Cell(9,3,$rent_pay_date[1],"LRB",0,'C');
$pdf->Cell(9,3,$rent_pay_date[2],"LRB",0,'C');
$pdf->Cell(9,3,".","",1,'L');
$pdf->SetX($pdf->GetX()+39);
$pdf->SetFont('Arial','',7);
$pdf->Cell(9,3,"Day","",0,'L');
$pdf->Cell(9,3,"Month","",0,'L');
$pdf->Cell(22,3,"Year","",1,'L');


$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Rect($x+2,$y+3,1,1,'F');
$pdf->SetFont('Arial','B',7);
$pdf->SetX($x+4);
$pdf->Cell(12,7,"OTHER PAYMENT PERIODS","",1,'L');
$pdf->SetFont('Arial','',9);
$pdf->SetX($pdf->GetX()+5);
$pdf->Cell(55,3,"The rent will be paid on the 1st day","",0,'L');
$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->SetFillColor(0,0,0);
if($rent_pay_time==1){
    $pdf->Rect($x+5,$y,3,3,'F');
    $pdf->Rect($x+35,$y,3,3);
}
else{
    $pdf->Rect($x+5,$y,3,3);
    $pdf->Rect($x+35,$y,3,3,'F');
}

$pdf->SetX($x+8);
$pdf->Cell(30,3,"Of the month","",0,'L');
$pdf->Cell(10,3,"Of the week","",1,'L');


$pdf->SetX($pdf->GetX()+5);
$pdf->Cell(11,7,"Or on","",0,'L');
$pdf->Cell(102,5,"","B",0,'L');
$pdf->Cell(1,6,".","",1,'L');
$pdf->SetFont('Arial','',7);
$pdf->Cell(25,3,"Specify","",1,'R');


$pdf->SetFont('Arial','B',9);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(25,8,"METHOD OF PAYMENT","",1,'L');
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',9);
$pdf->Cell(125,5,"The rent is payable in accordance with the following method of payment:","",1,'L');
$x=$pdf->GetX();
$y=$pdf->GetY();
if(in_array('1',$pay_method))
    $pdf->Rect($x+2,$y+1,3,3,'F');
else
    $pdf->Rect($x+2,$y+1,3,3);

if(in_array('2',$pay_method))
    $pdf->Rect($x+22,$y+1,3,3,'F');
else
    $pdf->Rect($x+22,$y+1,3,3);

if(in_array('3',$pay_method))
    $pdf->Rect($x+40,$y+1,3,3,'F');
else
    $pdf->Rect($x+40,$y+1,3,3);

if(in_array('0',$pay_method))
    $pdf->Rect($x+85,$y+1,3,3,'F');
else
    $pdf->Rect($x+85,$y+1,3,3);

$pdf->SetX($pdf->GetX()+5);
$pdf->Cell(20,5,"Cash","",0,'L');
$pdf->Cell(18,5,"Cheque","",0,'L');
$pdf->Cell(45,5,"Electronic bank transfer","",0,'L');
$pdf->Cell(10,5,"Other","",0,'L');
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(30,4,$pay_method_other,"B",0,'L');
$pdf->Cell(1,5,".","",1,'L');
$pdf->SetFont('Arial','',9);

$pdf->Cell(100,7,"The lessee agrees to give the lessor postdated cheques for the term of the lease.","",1,'L');
$x=$pdf->GetX();
$y=$pdf->GetY();
if($postdated_cheque==1){
    $pdf->Rect($x+2,$y,3,3,'F');
    $pdf->Rect($x+22,$y,3,3);
}
else{
    $pdf->Rect($x+2,$y,3,3);
    $pdf->Rect($x+22,$y,3,3,'F');
}

$pdf->Cell(13,4,"Yes","",0,'R');
$pdf->Cell(19,4,"No","",0,'R');
$pdf->SetX($pdf->GetX()+10);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(22,4,"lessee1_init","B",0,'L');
$pdf->SetX($pdf->GetX()+8);
$pdf->Cell(22,4,"lessee2_init","B",1,'L');
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',7);
$pdf->Cell(62,4,"Initials of lessee","",0,'R');
$pdf->Cell(30,4,"Initials of lessee","",1,'R');

$pdf->SetFont('Arial','B',9);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(25,7,"PLACE OF PAYMENT","",1,'L');
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',9);
$pdf->Cell(32,5,"The rent is payable at","",0,'L');
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(97,4,$pay_place,"B",0,'L');
$pdf->Cell(1,4,".","",1,'L');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(57,4,"Place of payment","",0,'R');
$pdf->SetFont('Arial','',8);
$pdf->Cell(57,4,"(specify if the payment is made by mail,if applicable)","",0,'L');

//D-right-side-info
//$pdf->SetFillColor(202,225,255);
$pdf->SetXY($x_multiCell,$y_multiCell);
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(64,4,"Rent:The rent is payable in equal instalments not \n exceeding one month's rent,   except for the last \n instalment, which may be less.","","","");

$pdf->SetXY($x_multiCell,$y_multiCell+14);
$pdf->SetFont('Arial','',8);
$pdf->MultiCell(100,4,"A lease with a term of more than 12 months may\n undergo only one adjustment of the rent during \n each 12-month period. No adjustment may be\n made within the first 12 months(art.1906 C.C.Q.).");

$pdf->SetXY($x_multiCell,$y_multiCell+33);
$pdf->SetFont('Arial','B',8);
$pdf->MultiCell(100,4,"The lessor may not exact any other amount\nof the money from the lessee (e.g. deposit for\n the keys.)");

$pdf->SetXY($x_multiCell,$y_multiCell+47);
$pdf->SetTextColor(0,0,255);
$pdf->SetFont('Arial','',8);
$pdf->Cell(57,4,"Payment of rent for the first payment period:","","L");
$pdf->SetTextColor(0,0,0);
$pdf->Cell(1,4,"At","","L");
$pdf->SetXY($x_multiCell,$y_multiCell+51);
$pdf->MultiCell(100,4,"the time of entering into the lease, the lessor may\n require advance payment of the rent for only the");
$pdf->SetXY($x_multiCell,$y_multiCell+59);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(28,4,"first payment period","","L");
$pdf->SetFont('Arial','',8);
$pdf->Cell(29,4,"(e.g.the first month,the first","","L");
$pdf->SetXY($x_multiCell,$y_multiCell+63);
$pdf->MultiCell(100,4,"week)The advance payment may not exceed one\n month's rent.");

$pdf->SetXY($x_multiCell,$y_multiCell+73);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(30,4,"Payment of rent for the other payment periods :","","L");
$pdf->SetXY($x_multiCell,$y_multiCell+77);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(29,4,"The rent is payable on","0","L");
$pdf->SetFont('Arial','B',8);
$pdf->Cell(17,4,"the first day","0","L");
$pdf->SetFont('Arial','',8);
$pdf->Cell(17,4,"of each","0","L");
$pdf->SetXY($x_multiCell,$y_multiCell+81);
$pdf->MultiCell(100,4,"payment period(e.g.month,week),unless other-\nwise agreed.");

$pdf->SetXY($x_multiCell,$y_multiCell+91);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(25,4,"Method of payment:","0","L");
$pdf->Cell(14,4,"The lessor","0","L");
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(25,4,"may not require","0","L");
$pdf->SetXY($x_multiCell,$y_multiCell+95);
$pdf->SetFont('Arial','',7.6);
$pdf->MultiCell(100,4,"payment by means of a postdated cheque or any\nother postdated instrument,unless otherwise agreed");

$pdf->SetXY($x_multiCell,$y_multiCell+105);
$pdf->SetTextColor(0,0,255);
$pdf->SetFont('Arial','',8);
$pdf->Cell(23,4,"Proof of payment:","0","L");
$pdf->SetTextColor(0,0,0);
$pdf->Cell(23,4,"The lessee is entitled to a","0","L");
$pdf->SetXY($x_multiCell,$y_multiCell+109);
$pdf->MultiCell(100,4,"receipt for the payment of his or her rent in cash\n(arts.1564 and 1568 C.C.Q.).");

$pdf->SetXY($x_multiCell,$y_multiCell+119);
$pdf->SetTextColor(0,0,255);
$pdf->SetFont('Arial','',7.6);
$pdf->Cell(22,4,"place of payment:","LT","L");
$pdf->SetTextColor(0,0,0);
$pdf->Cell(41,4,"the rent is payable at the lessee's","T","L");
$pdf->SetXY($x_multiCell,$y_multiCell+123);
$pdf->Cell(63,4,"domicile,unless otherwise agreed(art.1566 C.C.Q.)","LB",1);

#partE
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(65,105,225);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(6,6,"E",1,0,'C',"F");
$pdf->Cell(195-6,6,"SERVICES AND CONDITIONS","TB",1,'L',"F");
$pdf->SetTextColor(0,0,255);
$pdf->SetFillColor(0,0,225);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(50,6,"BY-LAWS OF THE IMMOVABLE","",1,'L');


$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',9);
$pdf->Cell(89.5,5,"A copy of the by-laws of the immovable was given to the lessee","",0,'L');
$pdf->SetFont('Arial','B',8.3);
$pdf->Cell(9.2,5,"before","",0,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(11,5,"entering into the lease.","",1,'L');
$pdf->SetY($pdf->GetY()+1);
$pdf->Cell(15,4,"Given on","",0,'L');
if(isset($_POST['immovable_before'])){
    $pdf->Cell(9,3,$immovable_date[0],"LRB",0,'C');
    $pdf->Cell(9,3,$immovable_date[1],"LRB",0,'C');
    $pdf->Cell(9,3,$immovable_date[2],"LRB",0,'C');
}
else{
    $pdf->Cell(9,3,"","LRB",0,'L');
    $pdf->Cell(9,3,"","LRB",0,'L');
    $pdf->Cell(9,3,"","LRB",0,'L');
}
$pdf->SetX($pdf->GetX()+10);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(25,3,"lessee1_init","B",0,'L');
$pdf->SetX($pdf->GetX()+6);
$pdf->Cell(25,3,"lessee2_init","B",1,'L');
$pdf->SetTextColor(0,0,0);
$pdf->SetX($pdf->GetX()+14);
$pdf->SetFont('Arial','',7);
$pdf->Cell(9,3,"Day","",0,'L');
$pdf->Cell(9,3,"Month","",0,'L');
$pdf->Cell(22,3,"Year","",0,'L');
$pdf->Cell(31,3,"Initials of lessee","",0,'L');
$pdf->Cell(20,3,"Initials of lessee","",1,'L');

$pdf->SetY($pdf->GetY()+1);
$pdf->SetFont('Arial','B',7.5);
$pdf->Cell(20,5,"DIVIDED CO-OWNERSHIP","",1,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(20,6,"A copy of the by-laws of the immovable was given to the lessee.","",1,'L');
$pdf->Cell(15,4,"Given on","",0,'L');
if(!isset($_POST['immovable_before'])){
    $pdf->Cell(9,3,$immovable_date[0],"LRB",0,'C');
    $pdf->Cell(9,3,$immovable_date[1],"LRB",0,'C');
    $pdf->Cell(9,3,$immovable_date[2],"LRB",0,'C');
}
else{
    $pdf->Cell(9,3,"","LRB",0,'L');
    $pdf->Cell(9,3,"","LRB",0,'L');
    $pdf->Cell(9,3,"","LRB",0,'L');
}
$pdf->SetX($pdf->GetX()+10);
$pdf->Cell(25,3,"","B",0,'L');
$pdf->SetX($pdf->GetX()+6);
$pdf->Cell(25,3,"","B",1,'L');
$pdf->SetX($pdf->GetX()+14);
$pdf->SetFont('Arial','',7);
$pdf->Cell(9,3,"Day","",0,'L');
$pdf->Cell(9,3,"Month","",0,'L');
$pdf->Cell(22,3,"Year","",0,'L');
$pdf->Cell(31,3,"Initials of lessee","",0,'L');
$pdf->Cell(20,3,"Initials of lessee","",1,'L');

$pdf->SetY($pdf->GetY()+1);
$pdf->SetTextColor(0,0,255);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(50,8,"WORK AND REPAIRS","",1,'L');
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',9);
$pdf->Cell(50,4,"The work and repairs to be done by the lessor and the timetable for performing them are","",1,'L');
$pdf->Cell(15,4,"as follows:","",1,'L');
$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Rect($x+1,$y+3.5,1,1,"F");
$pdf->SetFont('Arial','B',8.8);
$pdf->Cell(14,8,"Before","",0,'R');
$pdf->SetFont('Arial','',9);
$pdf->Cell(39,8,"the delivery of the dwelling","",0,'L');
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(74,7,$before_work,"B",1,'L');
$pdf->SetX($pdf->GetX()+2);
$pdf->Cell(125,7,"","B",1,'L');

$pdf->SetY($pdf->GetY()+2);
$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Rect($x+1,$y+3.5,1,1,"F");
$pdf->SetFont('Arial','B',8.8);
$pdf->Cell(14,8,"During","",0,'R');
$pdf->SetFont('Arial','',9);
$pdf->Cell(20,8,"the the lease","",0,'L');
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(93,7,$during_work,"B",1,'L');
$pdf->SetX($pdf->GetX()+2);
$pdf->Cell(125,7,"","B",1,'L');

$pdf->SetY($pdf->GetY()+3);
$pdf->SetTextColor(0,0,255);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(50,8,"JANITORIAL SERVICES","",1,'L');
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',9);
$pdf->SetX($pdf->GetX()+2);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(125,4,$janitorial_service,"B",1,'L');
$pdf->SetFont('Arial','',7);
$pdf->Cell(11,4,"Specify","",1,'R');
$pdf->SetX($pdf->GetX()+1);
$pdf->SetFont('Arial','',9);
$pdf->Cell(11,8,"The contact information for the janitor or the person to contact if necessary is as follows:","",1,'L');
$pdf->SetX($pdf->GetX()+2);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(57,4,$janitor_name,"B",0,'L');
$pdf->SetX($pdf->GetX()+8);
$pdf->Cell(59,4,$janitor_tele1,"B",1,'L');
$pdf->SetFont('Arial','',7.5);
$pdf->Cell(10,4,"Name","",0,'R');
$pdf->Cell(76,4,"Telephone No.","",1,'R');

$pdf->SetY($pdf->GetY()+4);
$pdf->SetX($pdf->GetX()+2);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(57,4,$janitor_email,"B",0,'L');
$pdf->SetX($pdf->GetX()+8);
$pdf->Cell(59,4,$janitor_tele2,"B",1,'L');
$pdf->SetFont('Arial','',7.5);
$pdf->Cell(20,4,"Email address","",0,'R');
$pdf->Cell(86,4," other telephone No.(cell phone)","",1,'R');

$pdf->SetY($pdf->GetY()+5);
$pdf->SetTextColor(0,0,255);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(50,8,"SERVICES,TAXES AND CONSUMPTION COSTS","",1,'L');

$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','B',8.5);
$pdf->Cell(50,4,"Will be borne by:","",0,'L');
$pdf->Cell(25,4,"Lessor","",0,'L');
$pdf->Cell(75,4,"Lessee","",0,'L');
$pdf->Cell(25,4,"Lessor","",0,'L');
$pdf->Cell(10,4,"Lessee","",1,'L');

$pdf->SetFillColor(0,0,0);
$pdf->SetFont('Arial','',9);
$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Cell(50,6,"Heating of dwelling","",0,'L');
if($service_heating==1){
    $pdf->Rect($x+54,$y+2,3,3,'F');
    $pdf->Rect($x+80,$y+2,3,3);
}
else if($service_heating==2){
    $pdf->Rect($x+54,$y+2,3,3);
    $pdf->Rect($x+80,$y+2,3,3,'F');
}else{
    $pdf->Rect($x+54,$y+2,3,3);
    $pdf->Rect($x+80,$y+2,3,3);
}
$pdf->SetX($x+94);
$pdf->Cell(50,6,"Water consumption tax for dwelling","",1,'L');
if($water_consumption==1){
    $pdf->Rect($x+155,$y+2,3,3,'F');
    $pdf->Rect($x+180,$y+2,3,3);
}
else if($water_consumption==2){
    $pdf->Rect($x+155,$y+2,3,3);
    $pdf->Rect($x+180,$y+2,3,3,'F');
}else{
    $pdf->Rect($x+155,$y+2,3,3);
    $pdf->Rect($x+180,$y+2,3,3);
}

$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Rect($x+5,$y+1,3,3);
$pdf->Rect($x+29,$y+1,3,3);
$pdf->Rect($x+44,$y+1,3,3);
$pdf->Cell(24,5,"Electricity","0",0,'R');
$pdf->Cell(16,5,"Gas","",0,'R');
$pdf->Cell(22,5,"Fuel oil","",0,'R');
$pdf->Cell(65,5,"Snow and ice removal","",1,'R');

$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Cell(65,4,"Gas","",0,'L');
if($gas==1){
    $pdf->Rect($x+54,$y+1,3,3,'F');
    $pdf->Rect($x+80,$y+1,3,3);
}
else if($gas==2){
    $pdf->Rect($x+54,$y+1,3,3);
    $pdf->Rect($x+80,$y+1,3,3,'F');
}else{
    $pdf->Rect($x+54,$y+1,3,3);
    $pdf->Rect($x+80,$y+1,3,3);
}
$pdf->Rect($x+95,$y+2,1,1,"F");
$pdf->SetX($x+97);
$pdf->Cell(30,5,"Parking area","",1,'L');
if($sonwrm_parking==1){
    $pdf->Rect($x+155,$y+1,3,3,'F');
    $pdf->Rect($x+180,$y+1,3,3);
}
else if($sonwrm_parking==2){
    $pdf->Rect($x+155,$y+1,3,3);
    $pdf->Rect($x+180,$y+1,3,3,'F');
}else{
    $pdf->Rect($x+155,$y+1,3,3);
    $pdf->Rect($x+180,$y+1,3,3);
}

$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Cell(65,4,"Electricity","",0,'L');
if($electricity==1){
    $pdf->Rect($x+54,$y+1,3,3,'F');
    $pdf->Rect($x+80,$y+1,3,3);
}
else if($electricity==2){
    $pdf->Rect($x+54,$y+1,3,3);
    $pdf->Rect($x+80,$y+1,3,3,'F');
}else{
    $pdf->Rect($x+54,$y+1,3,3);
    $pdf->Rect($x+80,$y+1,3,3);
}

$pdf->Rect($x+95,$y+2,1,1,"F");
$pdf->SetX($x+97);
$pdf->Cell(30,5,"Balcony","",1,'L');
if($snowrm_balcony==1){
    $pdf->Rect($x+155,$y+1,3,3,'F');
    $pdf->Rect($x+180,$y+1,3,3);
}
else if($snowrm_balcony==2){
    $pdf->Rect($x+155,$y+1,3,3);
    $pdf->Rect($x+180,$y+1,3,3,'F');
}else{
    $pdf->Rect($x+155,$y+1,3,3);
    $pdf->Rect($x+180,$y+1,3,3);
}

$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Cell(65,4,"Hot water heater(rent fees)","",0,'L');
if($hot_water_heater==1){
    $pdf->Rect($x+54,$y+1,3,3,'F');
    $pdf->Rect($x+80,$y+1,3,3);
} else if($hot_water_heater==2){
    $pdf->Rect($x+54,$y+1,3,3);
    $pdf->Rect($x+80,$y+1,3,3,'F');
}else{
    $pdf->Rect($x+54,$y+1,3,3);
    $pdf->Rect($x+80,$y+1,3,3);
}

$pdf->Rect($x+95,$y+2,1,1,"F");
$pdf->SetX($x+97);
$pdf->Cell(30,5,"Entrance,walkway,driveway","",1,'L');
if($snowrm_entrance==1){
    $pdf->Rect($x+155,$y+1,3,3,'F');
    $pdf->Rect($x+180,$y+1,3,3);
}
else if($snowrm_entrance==2){
    $pdf->Rect($x+155,$y+1,3,3);
    $pdf->Rect($x+180,$y+1,3,3,'F');
}else{
    $pdf->Rect($x+155,$y+1,3,3);
    $pdf->Rect($x+180,$y+1,3,3);
}

$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Cell(65,4,"Hot water(user fees)","",0,'L');
if($hot_water==1){
    $pdf->Rect($x+54,$y+1,3,3,'F');
    $pdf->Rect($x+80,$y+1,3,3);
}
else if($hot_water==2){
    $pdf->Rect($x+54,$y+1,3,3);
    $pdf->Rect($x+80,$y+1,3,3,'F');
}else{
    $pdf->Rect($x+54,$y+1,3,3);
    $pdf->Rect($x+80,$y+1,3,3);
}

$pdf->Rect($x+95,$y+2,1,1,"F");
$pdf->SetX($x+97);
$pdf->Cell(30,5,"Stars","",1,'L');
if($snowrm_stars==1) {
    $pdf->Rect($x + 155, $y + 1, 3, 3,'F');
    $pdf->Rect($x + 180, $y + 1, 3, 3);
}
else if($snowrm_stars==2){
    $pdf->Rect($x + 155, $y + 1, 3, 3);
    $pdf->Rect($x + 180, $y + 1, 3, 3,'F');
}else{
    $pdf->Rect($x + 155, $y + 1, 3, 3);
    $pdf->Rect($x + 180, $y + 1, 3, 3);
}
$pdf->SetY($pdf->GetY()+0);
$pdf->SetTextColor(0,0,255);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(50,6,"CONDITIONS","",1,'L');

$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',9);
$pdf->Cell(36,4,"The lessee has a right of ","",0,'L');
$pdf->SetFont('Arial','B',8.5);
$pdf->Cell(30,4,"access to the land. ","",0,'L');
$x=$pdf->GetX();
$y=$pdf->GetY();
if($right_access_land==1){
    $pdf->Rect($x+2,$y,3,3,'F');
    $pdf->Rect($x+16,$y,3,3);
}else{
    $pdf->Rect($x+2,$y,3,3);
    $pdf->Rect($x+16,$y,3,3,'F');
}
$pdf->SetFont('Arial','',9);
$pdf->Cell(13,4,"Yes","",0,'R');
$pdf->Cell(13,4,"No","",0,'R');
$pdf->SetX($pdf->GetX()+10);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(90,4,$right_access_land_spf,"B",1,'L');
$pdf->SetFont('Arial','',7);
$pdf->Cell(112,2,"Specify","",1,'R');

$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',9);
$pdf->Cell(62,4,"The lessee has a right to keep one or more","",0,'L');
$pdf->SetFont('Arial','B',8.5);
$pdf->Cell(12,4,"animals. ","",0,'L');
$x=$pdf->GetX();
$y=$pdf->GetY();
if($right_keep_animals==1){
    $pdf->Rect($x+2,$y,3,3,'F');
    $pdf->Rect($x+14,$y,3,3);
}else{
    $pdf->Rect($x+2,$y,3,3);
    $pdf->Rect($x+14,$y,3,3,'F');
}
$pdf->SetFont('Arial','',9);
$pdf->Cell(13,4,"Yes","",0,'R');
$pdf->Cell(11,4,"No","",0,'R');
$pdf->SetX($pdf->GetX()+4);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(90,4,$right_keep_animals_spf,"B",1,'L');
$pdf->SetFont('Arial','',7);
$pdf->Cell(112,2,"Specify","",1,'R');


$pdf->SetY($pdf->GetY()+0);
$pdf->SetTextColor(0,0,255);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(82,5,"OTHER SERVICES,CONDITIONS AND RESTRICTIONS","",0,'L');
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',8);
$pdf->Cell(50,4,"(e.g. antenna,barbecue,air conditioner,clothesline,painting,pool,laundry room)","",1,'L');
$pdf->SetX($pdf->GetX()+1);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(191,3.5,$other_services,"B",1,'L');
$pdf->SetFont('Arial','',8);

$pdf->SetY($pdf->GetY()+4);
$pdf->SetX($pdf->GetX()+142);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(23,2,"lessor1_init","B",0,'L');
$pdf->SetX($pdf->GetX()+5);
$pdf->Cell(23,2,"lessee1_init".str_pad(" ",5)."lessee2_init","B",1,'L');
$pdf->SetTextColor(0,0,0);
$pdf->Cell(164,3,"Initials of lessor","",0,'R');
$pdf->Cell(29,3,"Initials of lessee","",0,'R');


//E-right-side
$pdf->SetXY($x_multiCell,$y_multiCell+134);
$pdf->SetTextColor(0,0,255);
$pdf->SetFont('Arial','',7.7);
$pdf->Cell(32.5,4,"By-laws of the immovable:","","L");
$pdf->SetTextColor(0,0,0);
$pdf->Cell(30,4,"the rules to be observed","","L");
$pdf->SetXY($x_multiCell,$y_multiCell+138);
$pdf->MultiCell(100,4,"in the immovable are established by by-laws.  The\nby-laws pertain to the enjoyment, used and mainte-
nance of the dwelling nd of the common premises.");

$pdf->SetXY($x_multiCell,$y_multiCell+138+14);////
$pdf->SetFont('Arial','B',8);
$pdf->Cell(28,4,"If such by-laws exist","","L");
$pdf->SetFont('Arial','',8);
$pdf->Cell(15,4,", the lessor","","L");
$pdf->SetFont('Arial','B',8);
$pdf->Cell(8,4,"must","","L");
$pdf->SetFont('Arial','',8);
$pdf->Cell(30,4,"give a","","L");
$pdf->SetXY($x_multiCell,$y_multiCell+138+18);////
$pdf->Cell(34,4,"copy of them to the lessee","","L");
$pdf->SetFont('Arial','B',8);
$pdf->Cell(10,4,"before","0","L");
$pdf->SetFont('Arial','',7.8);
$pdf->Cell(10,4,"entering into","0","L");
$pdf->SetXY($x_multiCell,$y_multiCell+138+22);////
$pdf->MultiCell(100,4,"the lease so that the by-laws form part of the lease\n(art. 1894 C.C.Q.).");

$pdf->SetXY($x_multiCell,$y_multiCell+138+32);////30+2
$pdf->SetFont('Arial','',8);
$pdf->Cell(34,4,"If the dwelling is located in an immovable under","","L");
$pdf->SetXY($x_multiCell,$y_multiCell+138+36);////
$pdf->SetFont('Arial','B',8);
$pdf->Cell(32,4,"divided co-ownership,","","L");
$pdf->SetFont('Arial','',8);
$pdf->Cell(31,4,"the by-laws will apply","","L");
$pdf->SetXY($x_multiCell,$y_multiCell+138+40);////
$pdf->MultiCell(80,4,"as soon as a copy of them has been given to the\nlessee by the lessor or by the syndicate of the
co-ownership (art. 1057 C.C.Q.).");

$pdf->SetXY($x_multiCell,$y_multiCell+138+53);////52+1
$pdf->SetFont('Arial','B',8);
$pdf->MultiCell(62,4,"The by-laws may not contradict the lease or\nviolate the law.",1);

$pdf->SetXY($x_multiCell-1,$y_multiCell+138+62);////61+1
$pdf->SetTextColor(0,0,255);
$pdf->SetFont('Arial','',7.7);
$pdf->Cell(21.5,4,"Work and repairs:","","L");
$pdf->SetTextColor(0,0,0);
$pdf->Cell(23,4,"on the date fixed for the delivery of","","L");
$pdf->SetXY($x_multiCell-1,$y_multiCell+138+66);////61+1
$pdf->MultiCell(70,4,"the dwelling,the lessor must deliver it in a good state\nof repair in all respects. However, the lessor and
the lessee may decide otherwise and agree on the\nwork to be done and on a timetable for performing\nthe work(art. 1854 1st par. and art.1893 C.C.Q. ).",0);


$pdf->SetXY($x_multiCell-1,$y_multiCell+138+88);////86+2
$pdf->SetFont('Arial','B',8);
$pdf->MultiCell(70,4,"However, the lessor may not release himself\nor herself from the obligation to deliver the
dwelling, its accessories and dependencies in\nclean condition and to deliver and maintain\nthem in good habitable condition (art.1892,
1893, 1910 and 1911 C.C.Q.).",0);

$pdf->SetXY($x_multiCell-1,$y_multiCell+138+114);////112+2
$pdf->SetFont('Arial','',8);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(53,4,"Assessment of the condition of premises:","","L");
$pdf->SetTextColor(0,0,0);
$pdf->Cell(40,4,"In the","","L");
$pdf->SetXY($x_multiCell-1,$y_multiCell+138+118);////112+2
$pdf->MultiCell(70,4,"absence of an assessment of the condition of the\npremises(descriptions,photographs,etc.),the
lessee is presumed to have received the dwelling\nin good condition at the beginning of the lease\n(art. 1890 2nd par. C.C.Q.).");




########### page3 ##########

$pdf->AddPage();
$pdf->SetY($pdf->GetY()+2);
$x_drawline=$pdf->GetX();
$y_drawline=$pdf->GetY();
$pdf->Line($x_drawline,$y_drawline,$x_drawline,$y_drawline+344);//left
$pdf->Line($x_drawline+195,$y_drawline,$x_drawline+195,$y_drawline+344);//right
$pdf->Line($x_drawline,$y_drawline+344,$x_drawline+195,$y_drawline+344);//bottom


$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(65,105,225);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(6,6,"F",1,0,'C',"F");
$pdf->Cell(148,6,"RESTRICTIONS ON THE RIGHT TO HAVE THE RENT FIXED AND THE LEASE MODIFIED","TB",0,'L',"F");
$pdf->SetFont('Arial',"",8);
$pdf->Cell(195-148-6,6,"(arts. 1955 C.C.Q.)","TB",1,'L',"F");
$pdf->SetTextColor(0,0,0);

$pdf->SetY($pdf->GetY()+1);
$pdf->SetFont('Arial','B',9);
$pdf->MultiCell(130,4,'The lessor and the lessee may not apply to the Regie du logement for the fixing of
the rent or for the modification of another condition of the lease if one of the',0,'L');
$pdf->Cell(100,4,"following situations applies:",0,1,'L');

$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->SetFillColor(0,0,0);
if($restriction_immovable==1)
    $pdf->Rect($x+1,$y+3,3,3,'F');
else
    $pdf->Rect($x+1,$y+3,3,3);
$pdf->SetY($y+2);
$pdf->SetFont('Arial','',9);
$pdf->Cell(105,5,"The dwelling is located in an immovable erected five years ago or less.",0,1,'R');

$pdf->SetXY($pdf->GetX()+4,$pdf->GetY()+2);
$pdf->Cell(65,5,"The dwelling became ready for habitation on",0,0,'L');
$x=$pdf->GetX();
$pdf->SetXY($x,$pdf->GetY()+1);
$pdf->SetFont('Times',"",9.2);
if($restriction_immovable==1){
    $pdf->Cell(9,3,$restriction_immovable_date[0],"LRB",0,'C');
    $pdf->Cell(9,3,$restriction_immovable_date[1],"LRB",0,'C');
    $pdf->Cell(9,3,$restriction_immovable_date[2],"LRB",0,'C');
}else{
    $pdf->Cell(9,3,"","LRB",0,'L');
    $pdf->Cell(9,3,"","LRB",0,'L');
    $pdf->Cell(9,3,"","LRB",0,'L');

}
$pdf->Cell(9,3,".","",1,'L');
$pdf->SetX($pdf->GetX()+68);
$pdf->SetFont('Arial','',7);
$pdf->Cell(9,3,"Day","",0,'L');
$pdf->Cell(9,3,"Month","",0,'L');
$pdf->Cell(22,3,"Year","",1,'L');

$pdf->SetFont('Arial','B',10);
$pdf->SetX($pdf->GetX()+4);
$pdf->Cell(10,6,"OR","",1,'L');

$x=$pdf->GetX();
$y=$pdf->GetY();
if($restriction_immovable==2)
    $pdf->Rect($x+1,$y+3,3,3,'F');
else
    $pdf->Rect($x+1,$y+3,3,3);
$pdf->SetY($y+2);
$pdf->SetFont('Arial','',9);
$pdf->Cell(129,5,"The dwelling is located in an immovable whose use for residential purposes results from",0,1,'R');
$pdf->SetX($pdf->GetX()+4);
$pdf->Cell(100,3,"a change of destination that was made five years ago or less.","",1,'L');

$pdf->SetXY($pdf->GetX()+4,$pdf->GetY()+3);
$pdf->Cell(65,5,"The dwelling became ready for habitation on",0,0,'L');
$x=$pdf->GetX();
$pdf->SetXY($x,$pdf->GetY()+1);
$pdf->SetFont('Times',"",9.2);
if($restriction_immovable==2){
    $pdf->Cell(9,3,$restriction_immovable_date[0],"LRB",0,'C');
    $pdf->Cell(9,3,$restriction_immovable_date[1],"LRB",0,'C');
    $pdf->Cell(9,3,$restriction_immovable_date[2],"LRB",0,'C');
}else{
    $pdf->Cell(9,3,"","LRB",0,'L');
    $pdf->Cell(9,3,"","LRB",0,'L');
    $pdf->Cell(9,3,"","LRB",0,'L');
}
$pdf->Cell(9,3,".","",1,'L');
$pdf->SetX($pdf->GetX()+68);
$pdf->SetFont('Arial','',7);
$pdf->Cell(9,3,"Day","",0,'L');
$pdf->Cell(9,3,"Month","",0,'L');
$pdf->Cell(22,3,"Year","",1,'L');

$pdf->SetFont('Arial','',9);
$pdf->SetY($pdf->GetY()+2);
$pdf->Cell(130,5,"However, the tribunal may rule on any other application concerning the lease(e.g.decrease in rent).",0,1,'L');

//F-right-side
$x_right=$pdf->GetX()+131;
$y_right=$pdf->GetY()-61;
$pdf->SetXY($x_right,$y_right);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(64,4,'If not of the two boxes opposite is checked off',0,1);
$pdf->SetFont('Arial','',7.5);
$pdf->SetX($pdf->GetX()+131);
$pdf->MultiCell(64,4,"and if the five-year period has not yet expired, the\nlessee who refuses a modification in his or her lease
requested by the lessor,such as an increase in the");
$pdf->SetX($pdf->GetX()+131);
$pdf->Cell(6.5,4,"rent,",0,0,'L');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(17,4,"must vacate",0,0,'L');
$pdf->SetFont('Arial','',7.8);
$pdf->Cell(64-17-6,4,"the dwelling upon termination",0,1,'L');
$pdf->SetX($pdf->GetX()+131);
$pdf->Cell(64,4,'of the lease(particulars Nos. 39 and 41).',0,1);


$pdf->SetXY($pdf->GetX()+131,$pdf->GetY()+1);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(64,4,'If neither of two the boxes opposite is checked',0,1);
$pdf->SetX($pdf->GetX()+131);
$pdf->Cell(6,4,'off',0,0);
$pdf->SetFont('Arial','',7.6);
$pdf->Cell(64-5,4,"and if the lessee refuses a modification in his",0,1,'L');
$pdf->SetX($pdf->GetX()+131);
$pdf->MultiCell(64,4,"or her lease requested by the lessor and wishes\nto continue to live in the dwelling,  the lease is
then renewed. The lessor may apply to the Regie\ndu logement to have the conditions of the lease
fixed for the purposes of its renewal(particulars Nos.\n41 and 42).");


//G-part
$pdf->SetY($pdf->GetY()+5);

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(205,173,0);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(6,6,"G",1,0,'C',"F");
$pdf->Cell(82,6,"NOTICE TO A NEW LESSEE OR A SUBLESSEE","TB",0,'L',"F");
$pdf->SetFont('Arial',"",8);
$pdf->Cell(195-82-6,6,"(arts. 1896 and 1950 C.C.Q.)","TB",1,'L',"F");
$pdf->SetTextColor(0,0,0);


$X=$pdf->GetX();
$y=$pdf->GetY();
$pdf->SetX($pdf->GetX()+131);
$pdf->SetFont('Arial','',7.8);
$pdf->MultiCell(65,4,"If the new lessee or the sublessee pays a rent higher\nthan that declared in the notice, he or she may,
within 10 days after the date the lease or sublease\nis entered into,apply to the Regie du logement to\nhave the rent fixed.");

$pdf->SetXY($pdf->GetX()+131,$pdf->GetY()+2);
$pdf->MultiCell(65,4,"If the lessor did not give such notice at the time the\nlease or sublease was entered into, the new lessee
or the sublessee may, within two monthes after the\nbeginning of the lease, apply to the Regie du loge-\nment to have his or her rent fixed.");

$pdf->SetXY($pdf->GetX()+131,$pdf->GetY()+2);
$pdf->MultiCell(65,4,"The new lessee or the sublessee may also make\nsuch application within two months after the day
he or she becomes aware of a false statement in\nthe notice.");
$pdf->SetXY($x,$y);
$pdf->SetY($pdf->GetY()+1);
$pdf->SetFont('Arial','B',9);
$pdf->MultiCell(130,4,'Mandatory notice to be given by the lessor at the time the lease or sublease is
entered into, except when one of the two boxes in section F is checked off.',0,'L');
$pdf->SetY($pdf->GetY()+4);
$pdf->SetFont('Arial','',9);
$pdf->MultiCell(130,5,"I hereby notify you that lowest rent paid for your dwelling during the 12 months
preceding the beginning od your lease, or the rent fixed by the Regie du logement during",0);
$pdf->Cell(28,5,"that period, was $");
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(30,4,$lowest_rent,'B');
$pdf->SetFont('Arial','',9);
$pdf->Cell(5,5,".","",1);
$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->SetFillColor(0,0,0);
if($lowest_rent_period==1){
    $pdf->Rect($x+1,$y+3,3,3,'F');
    $pdf->Rect($x+24,$y+3,3,3);
    $pdf->Rect($x+47,$y+3,3,3);
}else if($lowest_rent_period==2){
    $pdf->Rect($x+1,$y+3,3,3);
    $pdf->Rect($x+24,$y+3,3,3,'F');
    $pdf->Rect($x+47,$y+3,3,3);
}else{
    $pdf->Rect($x+1,$y+3,3,3);
    $pdf->Rect($x+24,$y+3,3,3);
    $pdf->Rect($x+47,$y+3,3,3,'F');
}

$pdf->SetY($pdf->GetY()+2);
$pdf->Cell(21,5,"Per month","",0,'R');
$pdf->Cell(21,5,"Per week","",0,'R');
$pdf->Cell(18,5,"Other","",0,'R');
$pdf->Cell(68,4,$lowest_rent_period_other,"B",1,'L');

$pdf->SetY($pdf->GetY()+3);
$pdf->SetFont('Arial','',8.2);
$pdf->Cell(130,8,"The property leased, the services offered by the lessor and the conditions of your lease are the same.",'',1);
$x=$pdf->GetX();
$y=$pdf->GetY();
if($condition_same==1){
    $pdf->Rect($x+1,$y+1,3,3,'F');
    $pdf->Rect($x+15,$y+1,3,3);
}else{
    $pdf->Rect($x+1,$y+1,3,3);
    $pdf->Rect($x+15,$y+1,3,3,'F');
}
$pdf->SetFont('Arial','',9);
$pdf->Cell(12,5,"Yes","",0,'R');
$pdf->Cell(13,5,"No","",1,'R');


$pdf->SetY($pdf->GetY()+3);
$pdf->Cell(100,5,'If the "No" box is checked off, the following changes have been made','',1);
$pdf->SetFont('Arial','',7.2);
$pdf->Cell(100,5,'(e.g. addition of services of a personal nature, personal assistance services and nursing care, parking,heating):','',1);


$pdf->SetX($pdf->GetX()+2);
$pdf->SetFont('Times',"",9.2);
$pdf->Cell(190,6,$condition_changes,'B',1);
$pdf->SetX($pdf->GetX()+2);
$pdf->Cell(190,7,'','B',1);
$pdf->SetX($pdf->GetX()+2);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(80,7,'lessor1_name','B',0);
$pdf->SetXY($pdf->GetX()+3,$pdf->GetY()+3);
$pdf->Cell(9,4,"lessor_sign_date","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",1,'L');
$pdf->SetTextColor(0,0,0);
$pdf->SetX($pdf->GetX()+2);
$pdf->SetFont('Arial','',7.2);
$pdf->Cell(9,4,"Signature of lessor","",0,'L');
$pdf->SetX($pdf->GetX()+73);
$pdf->SetFont('Arial','',7);
$pdf->Cell(9,4,"Day","",0,'L');
$pdf->Cell(9,4,"Month","",0,'L');
$pdf->Cell(22,4,"Year","",1,'L');


//H-part
$pdf->SetY($pdf->GetY()+2);
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(65,105,225);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(6,6,"H",1,0,'C',"F");
$pdf->Cell(189,6,"SIGNATURES","TB",1,'L',"F");
$pdf->SetTextColor(0,0,0);


$pdf->SetX($pdf->GetX()+2);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(62,7,"lessor1_name","B",0,'L');
$pdf->SetXY($pdf->GetX()+3,$pdf->GetY()+3);
$pdf->Cell(9,4,"lessor_sign_date","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",0,'L');
$x=$pdf->GetX();
$pdf->SetXY($x+5,$pdf->GetY()-3);
$pdf->Cell(62,7,"lessee1_name","B",0,'L');
$pdf->SetXY($pdf->GetX()+3,$pdf->GetY()+3);
$pdf->Cell(9,4,"lessee1_sign_date","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",1,'L');
$pdf->SetTextColor(0,0,0);


$pdf->SetFont('Arial','',7);
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(65,4,"Signature of lessor(or his or her mandatary)","",0,'L');
$pdf->Cell(9,4,"Day","",0,'L');
$pdf->Cell(9,4,"Month","",0,'L');
$pdf->Cell(14,4,"Year","",0,'L');
$pdf->Cell(65,4,"Signature of lessee(or his or her mandatary)","",0,'L');
$pdf->Cell(9,4,"Day","",0,'L');
$pdf->Cell(9,4,"Month","",0,'L');
$pdf->Cell(22,4,"Year","",1,'L');


$pdf->SetX($pdf->GetX()+2);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(62,7,"lessor2_name","B",0,'L');
$pdf->SetXY($pdf->GetX()+3,$pdf->GetY()+3);
$pdf->Cell(9,4,"","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",0,'L');
$x=$pdf->GetX();
$pdf->SetXY($x+5,$pdf->GetY()-3);
$pdf->Cell(62,7,"lessee2_name","B",0,'L');
$pdf->SetXY($pdf->GetX()+3,$pdf->GetY()+3);
$pdf->Cell(9,4,"lessee2_sign_date","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",1,'L');
$pdf->SetTextColor(0,0,0);


$pdf->SetFont('Arial','',7);
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(65,4,"Signature of lessor(or his or her mandatary)","",0,'L');
$pdf->Cell(9,4,"Day","",0,'L');
$pdf->Cell(9,4,"Month","",0,'L');
$pdf->Cell(14,4,"Year","",0,'L');
$pdf->Cell(65,4,"Signature of lessee(or his or her mandatary)","",0,'L');
$pdf->Cell(9,4,"Day","",0,'L');
$pdf->Cell(9,4,"Month","",0,'L');
$pdf->Cell(22,4,"Year","",1,'L');

$pdf->SetXY($pdf->GetX()+1,$pdf->GetY()+4);
$pdf->SetFont('Arial','',9);
$pdf->Cell(82,5,"The lessees undertake to be solidarily liable for the lease","",0,'L');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(43,5,"(particulars Nos.11 and 12).","",0,'L');
$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Rect($x+2,$y+1,3,3);
$pdf->Rect($x+16,$y+1,3,3);
$pdf->SetFont('Arial','',9);
$pdf->Cell(13,5,"Yes","",0,'R');
$pdf->Cell(12,5,"No","",0,'R');
$pdf->SetX($pdf->GetX()+2);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(18,4,"lessee1_init","B",0,'L');
$pdf->SetX($pdf->GetX()+2);
$pdf->Cell(19,4,"lessee2_init","B",1,'L');
$pdf->SetTextColor(0,0,0);

$pdf->SetX($pdf->GetX()+152);
$pdf->SetFont('Arial','',7);
$pdf->Cell(20,4,"Initials of lessee","",0,'L');
$pdf->Cell(15,4,"Initials of lessee","",1,'L');

$pdf->SetFont('Arial','B',8.5);
$pdf->Cell(139,5,"Any other person who signs the lease must clearly indicate in what capacity he or she is doing so","",0,'L');
$pdf->SetFont('Arial','',8.3);
$pdf->Cell(30,4,"(e.g.another lessor,another lessee,surety)","",1,'L');
$pdf->Cell(30,4,"(Particular No.12)","",1,'L');

$pdf->SetX($pdf->GetX()+2);
$pdf->Cell(54,7,"","B",0,'L');
$pdf->SetX($pdf->GetX()+3);
$pdf->Cell(80,7,"","B",0,'L');
$pdf->SetX($pdf->GetX()+3);
$pdf->Cell(50,7,"","B",1,'L');
$pdf->SetFont('Arial','',8);
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(8,4,"Name","",0,'L');
$pdf->SetFont('Arial','',6.5);
$pdf->Cell(49,4,"(WRITE LEGIBLY)","",0,'L');
$pdf->SetFont('Arial','',7);
$pdf->Cell(84,4,"Signature","",0,'L');
$pdf->Cell(65,4,"Capacity","",1,'L');


$pdf->SetX($pdf->GetX()+2);
$pdf->Cell(159,7,"","B",0,'L');
$pdf->SetXY($pdf->GetX()+3,$pdf->GetY()+3);
$pdf->Cell(9,4,"","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",1,'L');
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(162,4,"Address of signatory","",0,'L');
$pdf->Cell(9,4,"Day","",0,'L');
$pdf->Cell(9,4,"Month","",0,'L');
$pdf->Cell(14,4,"Year","",1,'L');


$pdf->SetX($pdf->GetX()+2);
$pdf->Cell(54,7,"","B",0,'L');
$pdf->SetX($pdf->GetX()+3);
$pdf->Cell(80,7,"","B",0,'L');
$pdf->SetX($pdf->GetX()+3);
$pdf->Cell(50,7,"","B",1,'L');
$pdf->SetFont('Arial','',8);
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(8,4,"Name","",0,'L');
$pdf->SetFont('Arial','',6.5);
$pdf->Cell(49,4,"(WRITE LEGIBLY)","",0,'L');
$pdf->SetFont('Arial','',7);
$pdf->Cell(84,4,"Signature","",0,'L');
$pdf->Cell(65,4,"Capacity","",1,'L');


$pdf->SetX($pdf->GetX()+2);
$pdf->Cell(159,7,"","B",0,'L');
$pdf->SetXY($pdf->GetX()+3,$pdf->GetY()+3);
$pdf->Cell(9,4,"","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",1,'L');
$pdf->SetX($pdf->GetX()+1);
$pdf->Cell(162,4,"Address of signatory","",0,'L');
$pdf->Cell(9,4,"Day","",0,'L');
$pdf->Cell(9,4,"Month","",0,'L');
$pdf->Cell(14,4,"Year","",1,'L');


$pdf->SetFont('Arial','B',8);
$pdf->SetY($pdf->GetY()+4);
$pdf->SetFillColor(176,196,222);
$pdf->Cell(147,6,"The lessor must give the lessee a copy of the lease within 10 days after entering into the lease","L",0,'R','F');
$pdf->SetFont('Arial','',8);
$pdf->Cell(195-147,6,"(art. 1895 C.C.Q.).","R",1,'L','F');


//I-part
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(65,105,225);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(6,6,"I",1,0,'C',"F");
$pdf->Cell(57,6,"NOTICE OF FAMILY RESIDENCE","TB",0,'L',"F");
$pdf->SetFont('Arial',"",8);
$pdf->Cell(195-57-6,6,"(arts. 403 and 521.6 C.C.Q.)","TB",1,'L',"F");
$pdf->SetTextColor(0,0,0);


$pdf->SetY($pdf->GetY()+2);
$pdf->SetFont('Arial','',8.5);
$pdf->Cell(22,5,"A lessee who is","",0,'L');
$pdf->SetFont('Arial','B',8.8);
$pdf->Cell(39,5,"married or in a civil union","",0,'L');
$pdf->SetFont('Arial','',8.5);
$pdf->Cell(100,5,"may not, without the written consent of his or her spouse, sublease his or her dwelling, assign the","",1,'L');
$pdf->Cell(195,4,"lease or terminate the lease where the lessor has been notified,by either of the spouses, that the dwelling
leased is used as the family residence.","",1,'L');

$pdf->SetY($pdf->GetY()+3);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(40,5,"Notice to lessor","",1,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(37,5,"I hereby declare that i am","",0,'L');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(13,5,"married","",0,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(8,5,"to or","",0,'L');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(23,5,"in a civil union","",0,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(8,5,"with","",0,'L');
$pdf->Cell(70,4,"","B",0,'L');
$pdf->Cell(3,4,".","",1,'L');

$pdf->SetX($pdf->GetX()+89);
$pdf->SetFont('Arial','',7.3);
$pdf->Cell(30,4,"Name of spouse","",1,'L');

$pdf->SetY($pdf->GetY()+3);
$pdf->SetFont('Arial','',9);
$pdf->Cell(100,5,"I hereby notify you that the dwelling covered by the lease will be used as the family residence.","",1,'L');


$pdf->SetX($pdf->GetX()+2);
$pdf->Cell(98,7,"","B",0,'L');
$pdf->SetXY($pdf->GetX()+4,$pdf->GetY()+3);
$pdf->Cell(9,4,"","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",0,'L');
$pdf->Cell(9,4,"","LRB",1,'L');
$pdf->SetX($pdf->GetX()+1);
$pdf->SetFont('Arial','',7);
$pdf->Cell(102,4,"Address of signatory","",0,'L');
$pdf->Cell(9,4,"Day","",0,'L');
$pdf->Cell(9,4,"Month","",0,'L');
$pdf->Cell(14,4,"Year","",1,'L');

$pdf->SetY($pdf->GetY()+10);
$x=$pdf->GetX();
$y=$pdf->GetY();
$pdf->Cell(195,16,"","1",0,'L','F');

$pdf->SetXY($x+4,$y+3);
$pdf->SetFillColor(205,173,0);
$pdf->Cell(187,10,"","0",0,'L','F');

$pdf->SetXY($x+4,$y+3);
$pdf->SetFont('Arial','B',8.6);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(187,5,"If the lease includes services in addition to those indicated on this form,including services","",1,'C','F');
$pdf->SetX($x+4);
$pdf->Cell(187,4,"of a personal nature, complete Schedule 6 to the the lease: Services Offered to the Lessee bu the Lessor.","",0,'C','F');


$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',7);
$pdf->SetY($pdf->GetY()+12);
$pdf->SetX($pdf->GetX()+142);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(23,2,"lessor1_init","B",0,'L');
$pdf->SetX($pdf->GetX()+5);
$pdf->Cell(23,2,"lessee1_init".str_pad(" ",5)."lessee2_init","B",1,'L');
$pdf->SetTextColor(0,0,0);
$pdf->Cell(163,3,"Initials of lessor","",0,'R');
$pdf->Cell(28,3,"Initials of lessee","",0,'R');


$pdf->Output('F', 'Lease.pdf');
request_signature_on_lease($email_lessor1,$name_lessor1,$email_lessee1,$name_lessee1,$email_lessee2,$name_lessee2,"Lease.pdf","Lease.pdf");


header("Location: approve.php");
exit;

?>








