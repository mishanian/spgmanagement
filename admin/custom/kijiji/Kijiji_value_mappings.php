<?php
function size_type_mapping($size_type_id,$bedrooms){
    $value=0;

    if($size_type_id==1 || $bedrooms==0)
        $value=0;

    if($size_type_id==2 || $bedrooms==1)
        $value=1;

    if($size_type_id==3)
        $value=1.5;

    if($size_type_id==4 || $bedrooms==2)
        $value=2;

    if($size_type_id==5 || $bedrooms==3)
        $value=3;

    if($size_type_id==6 || $bedrooms==4)
        $value=4;

    if($size_type_id==7 || $bedrooms>4)
        $value=5;

    return $value;
}

function city_mapping($city_name){
    if(strtolower(trim($city_name))=="montreal")
        return 1700281;
    if(strtolower(trim($city_name))=="Toronto")
        return 1700273;
    if(strtolower(trim($city_name))=="québec city")
        return 1700124;
}


function province_mapping($province_id){
    if($province_id==1)
        return "QC";
    if($province_id==2)
        return "ON";
    if($province_id==9)
        return "AB";
    if($province_id==6)
        return "BC";
    if($province_id==3)
        return "NS";
}


function sub_type_mapping($sub_type_id){
    if($sub_type_id==1)
        return "Multi Residential";
    if($sub_type_id==2)
        return "Senior";
    if($sub_type_id==3)
        return "Condo";
    if($sub_type_id==4)
        return "Office";
    if($sub_type_id==5)
        return "Retail";
    if($sub_type_id==6)
        return "Leisure";
    if($sub_type_id==7)
        return "Industrial";
}