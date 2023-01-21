<?php
error_reporting(E_ALL);

//step 1 -  establish connection to your database - Code to be included by your programmers

//originally
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';

include_once("Kijiji_value_mappings.php");

//step 2
//Get data from your database for all the unit that need to be posted - data to be retrieved for posting (view or tables)
$building_set = $DB_kijiji->get_all_kijiji_classified_by_building();
//die(var_dump($building_set));
//step 3 - form the xml document by looping on unitdatakijiji - to be discussed or explained to your programmers
//xml eclosure
$xml = new DOMDocument("1.0", "utf-8");
$xml->version = "1.0";
$xml->preserveWhiteSpace = false;
$xml->formatOutput = true;

//forming xml document
$Locations = $xml->createElement("Locations");
$xml->appendChild($Locations);

foreach ($building_set as $one) {
    $sub_domain = $one['sub_domain'];

    //Loop at locations to create each location
    $Location = $xml->createElement("Location");
    $Locations->appendChild($Location);


    //get Client Location Id
    $building_id = $one['building_id'];
    $location_id = $building_id + 1000;
    $ClientLocationId = $xml->createElement("ClientLocationId", $location_id);
    $Location->appendChild($ClientLocationId);

    //Building Name
    $building_name = $one['building_name'];
    $BuildingName = $xml->createElement("BuildingName", $building_name);
    $Location->appendChild($BuildingName);

    //StreetAddress
    $street_address = $one['address'];
    $StreetAddress = $xml->createElement("StreetAddress", $street_address);
    $Location->appendChild($StreetAddress);

    //city
    $city = $one['city'];
    $City = $xml->createElement("City", $city);
    $Location->appendChild($City);

    //Province
    $province_id = $one['province_id'];
    $Province = $xml->createElement("Province", province_mapping($province_id));
    $Location->appendChild($Province);


    //PostalCode
    $postal_code = $one['postal_code'];
    $PostalCode = $xml->createElement("PostalCode", $postal_code);
    $Location->appendChild($PostalCode);


    //PhoneNumber
    $phone_number = $one['mobile'];
    $PhoneNumber = $xml->createElement("PhoneNumber", $phone_number);
    $Location->appendChild($PhoneNumber);


    //Website
    $web = $one['website'];
    if (strpos($web, 'http:') === false)
        $web = 'http://' . $web;
    $Website = $xml->createElement("Website", $web);
    $Location->appendChild($Website);


    //CatchPhrase
    $CatchPhrase = $xml->createElement("CatchPhrase", "Apartment for rent");
    $Location->appendChild($CatchPhrase);


    //kijiji_location_id - Optional
    //$kijiji_location_id = $xml->createElement("kijiji_location_id", "1700281");
    //$Location->appendChild($kijiji_location_id);


    //Logo - Optional
    $Logo = $xml->createElement("Logo");
    $Location->appendChild($Logo);

    //Small - Optional
    $logo = 'https://www.spgmanagement.com/admin/files/logos/' . $one['logo'];
    $Small = $xml->createElement("Small", $logo);
    $Logo->appendChild($Small);

    $EmailRecipients = $xml->createElement("EmailRecipients");
    $Location->appendChild($EmailRecipients);

    //EmailRecipient - Optional - Loop
    $email = $one['email'];
    $EmailRecipient = $xml->createElement("EmailRecipient", $email);
    $EmailRecipients->appendChild($EmailRecipient);


    $building_public_comment = $one['public_comments'];

    //Units - Optional
    $Units = $xml->createElement("Units");
    $Location->appendChild($Units);

    //building_pictures
    $building_pics_arr = array();
    if(strlen($one['building_feature_pic'])>0){
        $temp_arr = explode('|', $one['building_feature_pic']);
        foreach ($temp_arr as $pic){
            array_push($building_pics_arr, $pic);
        }
    }
    if(strlen($one['building_pic'])>0){
        $temp_arr = explode('|', $one['building_pic']);
        foreach ($temp_arr as $pic){
            array_push($building_pics_arr, $pic);
        }
    }

    $apart_sets = $DB_kijiji->get_all_kijiji_within_building($one['building_id']);

    foreach ($apart_sets as $apart) {
        //Unit - Optional
        $Unit = $xml->createElement("Unit");
        $Units->appendChild($Unit);


        //ClientUnitId - O
        $unit_id = $apart['apartment_id'] + 10000;
        $ClientUnitId = $xml->createElement("ClientUnitId", $unit_id);
        $Unit->appendChild($ClientUnitId);


        //RentOrSale - O
        $RentOrSale = $xml->createElement("RentOrSale", "Rent");
        $Unit->appendChild($RentOrSale);


        //OfferedBy - O
        $OfferedBy = $xml->createElement("OfferedBy", "professional");
        $Unit->appendChild($OfferedBy);


        //title - O
        $unit_number = $apart['unit_number'];
        $Title = $xml->createElement("Title", $street_address . ' ' . $unit_number);
        $Unit->appendChild($Title);


        //UnitType - O
        // $sub_type = sub_type_mapping($apart['apartment_type_sub_id']);
        $UnitType = $xml->createElement("UnitType", "apartments, condos");
        $Unit->appendChild($UnitType);


        //Bedrooms - O
        $size_type = $apart['size_type_id'];
        $bed_rooms = $apart['bedrooms'];
        $Bedrooms = $xml->createElement("Bedrooms", size_type_mapping($size_type, $bed_rooms));
        $Unit->appendChild($Bedrooms);


        //Bathrooms - O
        $bath_room = $apart['bath_rooms'];
        $Bathrooms = $xml->createElement("Bathrooms", $bath_room);
        $Unit->appendChild($Bathrooms);


        //SquareFootage - O
        $area = $apart['area'];
        if ($area > 0) {
            $SquareFootage = $xml->createElement("SquareFootage", $area);
            $Unit->appendChild($SquareFootage);
        }


        //Price - O
        $monthly_price = $apart['monthly_price'];
        $Price = $xml->createElement("Price", $monthly_price);
        $Unit->appendChild($Price);


        //Furnished - O
        $furnish = $apart['furnished'];

        if ($furnish == null)
            $furnish = "No";
        else
            $furnish = "Yes";

        $Furnished = $xml->createElement("Furnished", $furnish);
        $Unit->appendChild($Furnished);


        //PetsAllowed - O
        $PetsAllowed = $xml->createElement("PetsAllowed", "No");
        $Unit->appendChild($PetsAllowed);


        //Description - O
        $description = '';
        $unit_public_comment = $apart['public_comments'];

        if (strlen($building_public_comment) != 0) {
            $description .= '<p>'.str_replace("&"," and ",strip_tags($building_public_comment)). '</p>';
        }
        if (strlen($unit_public_comment) != 0) {
            $description .= '<p>'.str_replace("&"," and ",strip_tags($unit_public_comment)). '</p>';
        }
        $description .= 'For more details, please go to <p><b>'.$sub_domain.'.spgmanagement.com/property-view.php?pass_bd_id=' . $building_id.'</b></p>';
        $Description = $xml->createElement("Description", htmlspecialchars($description));
        $Unit->appendChild($Description);


        //Size-acres - O
        $size = $apart['area'];
        if ($size > 0) {
            $Size_acres = $xml->createElement("Size-acres", $size);
            $Unit->appendChild($Size_acres);
        }


        if (strlen($apart['pictures']) > 0 || sizeof($building_pics_arr) > 0) {
            $Images = $xml->createElement("Images");
            $Unit->appendChild($Images);

            //building picture
            if (sizeof($building_pics_arr) > 0) {
                foreach ($building_pics_arr as $pic) {
                    if(empty($pic) || strlen($pic) < 4)     //.jpg .png ...
                        continue;

                    //Image - Loop
                    $Image = $xml->createElement("Image");
                    $Images->appendChild($Image);

                    //Image Name
                    $Name = $xml->createElement("Name", "Picture");
                    $Image->appendChild($Name);

                    //Image SourceUrl
                    $url = 'https://www.spgmanagement.com/admin/files/building_pictures/' . $pic;
                    $SourceUrl = $xml->createElement("SourceUrl", $url);
                    $Image->appendChild($SourceUrl);
                }

            }

            //unit pictures
            if (strlen($apart['pictures']) > 0) {
                $pictures = explode('|', $apart['pictures']);
                if (sizeof($pictures) > 0) {

                    foreach ($pictures as $pic) {
                        if(empty($pic) || strlen($pic) < 4)     //.jpg .png ...
                            continue;

                        //Image - Loop
                        $Image = $xml->createElement("Image");
                        $Images->appendChild($Image);

                        //Image Name
                        $Name = $xml->createElement("Name", "Picture");
                        $Image->appendChild($Name);

                        //Image SourceUrl
                        $url = 'www.spgmanagement.com/admin/files/apartment_pictures/' . $pic;
                        $SourceUrl = $xml->createElement("SourceUrl", $url);
                        $Image->appendChild($SourceUrl);
                    }
                }

            }
        }
    }
}

//step - 4 write the xml to local directory

//Write feed file to local directory
echo $xml->save("kijijirss/kijiji_feeding.xml");


// step - 5 transfer/ftp file to directory on server
//$file = 'readme.xml';
//$remote_file = 'readme.xml';
//
//$ftp_server = "ftp.vaidacare.com";
//$ftp_user_name = "user1235";
//$ftp_user_pass = "ftp!user1";
//
//// set up basic connection
//$conn_id = ftp_connect($ftp_server);
////$conn_id = ftp_ssl_connect($ftp_server, 22) or die("Could not connect to $ftp_server");
//
//// 71 login with username and password
//$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
//
//// upload a file
//if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
//    echo "successfully uploaded $file\n";
//} else {
//    echo "There was a problem while uploading $file\n";
//}
//
////close the connection
//ftp_close($conn_id);
echo("<br>");
echo('Kijiji feeding-xml auto update successfully!!');
echo("<br>");

?>


