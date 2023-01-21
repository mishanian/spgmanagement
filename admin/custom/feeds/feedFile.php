<?php
error_reporting(E_ALL);

//step 1 -  establish connection to your database - Code to be included by your programmers
$path = "../../../pdo/";

include_once($path . 'dbconfig.php');

include_once $path . "Class.Feed.php";
include_once $path . "Class.Country.php";
include_once $path . "Class.Company.php";
include_once $path . "Class.Building.php";
include_once $path . "Class.Province.php";
include_once $path . "Class.Apt.php";
$DB_feed = new Feed($DB_con);
$DB_country = new Country($DB_con);
$DB_company = new Company($DB_con);
$DB_building  = new Building($DB_con);
$DB_province   = new Province($DB_con);
$DB_apt    = new Apt($DB_con);
//step 3 - form the xml document by looping on unitdatafeed - to be discussed or explained to your programmers
//xml eclosure
$xml = new DOMDocument("1.0", "utf-8");
$xml->version = "1.0";
$xml->preserveWhiteSpace = false;
$xml->formatOutput = true;

//forming xml document
$Properties = $xml->createElement("Properties");
$xml->appendChild($Properties);

//step 2
//Get data from your database for all the unit that need to be posted - data to be retrieved for posting (view or tables)
$company_ids = $DB_company->getCompanyCanAdvertise(); //only for spg-canada
//die(var_dump($company_ids));
foreach ($company_ids as $company_id) {
    $building_set = $DB_building->getAllBdRowsByCompany($company_id);
//die(var_dump($building_set));
// Company
    $company_infos = $DB_company->getCompanyInfo($company_id);
    $company_id = $company_infos['id'];
    $company_name = $company_infos['name'];
    $company_email = $company_infos['contact_email'];
    $company_phone = $company_infos['phone'];
    $company_website = $company_infos['website'];
    $company_sub_domain = $company_infos['sub_domain'];

    $xml_bld_fields = array("id" => "building_id", "URL" => "URL",  "TagLine" => "building_name", "BuildingType" => "BuildingType"); //"Name" => "building_name",
    $xml_location_fields = array("Address" => "address", "City" => "city", "Province" => "Province", "ProvinceCode" => "ProvinceCode", "Country" => "Country", "CountryCode" => "country_short_name","PostalCode"=>"postal_code", "Intersection" => "Intersection", "Neighbourhood" => "Neighbourhood", "Latitude" => "latitude", "Longitude" => "longitude");
    $xml_company_fields = array("id" => "company_id", "Name" => "company_name", "Email" => "company_email", "Phone" => "company_phone", "Website" => "company_website");
    $xml_contact_fields = array("Name" => "contact_name", "Email" => "contact_email", "Phone" => "contact_phone", "PhoneExtension" => "contact_phone_ext", "AltPhone" => "contact_alt_phone", "AltPhoneExtension" => "contact_alt_phone_ext", "Fax" => "contact_fax");
    $xml_other_fields = array("OfficeHours" => "OfficeHours", "PetFriendlyCats" => "PetFriendlyCats", "PetFriendlyLargeDogs" => "PetFriendlyLargeDogs", "PetFriendlySmallDogs" => "PetFriendlySmallDogs", "ParkingDetails" => "ParkingDetails", "PetFriendly" => "PetFriendly", "PetFriendlyNotAllowed" => "PetFriendlyNotAllowed");
    $app_array = array();

    // <Videos>
    // <Video>
    // <Title>Video Title (optional)</Title>
    // <Description>Short description (optional)</Description>
    // <EmbedCode>
    // <![CDATA[<iframe src="https://www.youtube.com/embed/xxxxxxxxxxxx" width="480" height="360" frameborder="0" style="position:absolute;width:100%;height:100%;left:0" allowfullscreen> </iframe>]]>
    // </EmbedCode>
    // </Video>
    // </Videos>

    $id_counter = 0;
    foreach ($building_set as $one) {
        foreach ($one as $key => $value) {
            $$key = $value;
        }

        //Loop at locations to create each Property
        $Property = $xml->createElement("Property");
        $Properties->appendChild($Property);

        $x_Name = $xml->createElement("Name",$building_name);//."-**".$building_id."**"
        $Property->appendChild($x_Name);
/// Define Custom Fields
        $URL = "https://".$company_sub_domain.".spgmanagement.com/property-view.php?pass_bd_id=$building_id";
        $BuildingType = $DB_feed->BuildingType($building_id);


        foreach ($xml_bld_fields as $xml_field => $field_name) {
            $x_field = $xml->createElement($xml_field, $$field_name);
            $Property->appendChild($x_field);
        }

        // Location
        $x_Location = $xml->createElement("Location");
        $Property->appendChild($x_Location);

        foreach ($xml_location_fields as $key => $value) {
            $$key = $value;
        }
        $Intersection = "";
        $Neighbourhood = "";
        $province_info = $DB_province->getProvinceInfo($province_id);
        $Province = $province_info['name'];
        $ProvinceCode = $province_info['province_short_code'];
        $Country = $DB_country->getCountryInfo($county_id)['name'];
        $country_short_name = $DB_country->getCountryInfo($county_id)['country_short_code'];

        foreach ($xml_location_fields as $xml_field => $field_name) {
            $x_field = $xml->createElement($xml_field, $$field_name);
            $x_Location->appendChild($x_field);
        }




        $x_Company = $xml->createElement("Company");
        $Property->appendChild($x_Company);
        foreach ($xml_company_fields as $xml_field => $field_name) {
            $x_field = $xml->createElement($xml_field, $$field_name);
            $x_Company->appendChild($x_field);
        }


//ContactInformation
        $contact_name = $company_infos['name'];
        $contact_email = $company_infos['contact_email'];
        $contact_phone = $company_infos['phone'];
        $contact_phone_ext = "";
        $contact_alt_phone = "";
        $contact_alt_phone_ext = "";
        $contact_fax = $company_infos['fax'];

        $x_ContactInformation = $xml->createElement("ContactInformation");
        $Property->appendChild($x_ContactInformation);
        foreach ($xml_contact_fields as $xml_field => $field_name) {
            $x_field = $xml->createElement($xml_field, $$field_name);
            $x_ContactInformation->appendChild($x_field);
        }

        $building_infos = $DB_building->getBdInfo($building_id);
//BuildingDescription
        $BuildingDescription = htmlspecialchars(strip_tags($building_infos['public_comments']), ENT_XML1);
        $x_BuildingDescription = $xml->createElement("BuildingDescription", $BuildingDescription);
        $Property->appendChild($x_BuildingDescription);


//BuildingFeatures
        $amenities = implode(",", $DB_building->getAmenityNamesBds($building_id));
        $x_BuildingFeatures = $xml->createElement("BuildingFeatures", $amenities);
        $Property->appendChild($x_BuildingFeatures);


//SuiteDetails
        $amenities_res = implode(",", $DB_building->getAmenityResNamesBds($building_id));
        $x_SuiteDetails = $xml->createElement("SuiteDetails", $amenities_res);
        $Property->appendChild($x_SuiteDetails);


        $OfficeHours = "";
        $ParkingDetails = "";
        $PetFriendly = ($DB_building->getPetAllowed($building_id) > 0 ? 1 : 0);
        $PetFriendlyCats = $DB_building->getSmallPetAllowed($building_id);
        $PetFriendlyLargeDogs = $DB_building->getLargeDogAllowed($building_id);
        $PetFriendlySmallDogs = $DB_building->getSmallPetAllowed($building_id);
        $PetFriendlyNotAllowed = ($DB_building->getPetAllowed($building_id) > 0 ? 0 : 1);

        foreach ($xml_other_fields as $xml_field => $field_name) {
            $x_field = $xml->createElement($xml_field, $$field_name);
            $Property->appendChild($x_field);
        }

        //Suites
        $x_Suites = $xml->createElement("Suites");
        $Property->appendChild($x_Suites);


        $xml_app_fields = array("id" => "apartment_id", "TypeName" => "TypeName", "Bathrooms" => "Bathrooms", "SquareFeet" => "SquareFeet",
            "Rate" => "Rate", "Deposit" => "Deposit", "Furnished" => "furnished", "Name" => "unit_number", "AvailabilityDate" => "available_date", "Description" => "BuildingDescription", "video"=>"video");

        $apartment_set = $DB_apt->getAptInfoInBuildingLessRenewalDay($building_id, $company_id);


        foreach ($apartment_set as $app) {
            if ($DB_apt->isUnitShowed($app['apartment_id'])) {
                
                if (!$app["front_force_list"]) {
                    if (in_array($app['renovation_status'], array(2, 3))) {
                        continue;
                    }
                }
                
                foreach ($app as $key => $value) {
                    $$key = $value;
                }

                $app_array[] = $apartment_id;
                $TypeName = $DB_apt->getSizeType($apartment_id);
                $Bathrooms = $app['bath_rooms'];
                $Bedrooms = $app['bedrooms'];
                $SquareFeet = $app['area'];
                $Rate = str_replace('$', '', $DB_apt->getMonthlyPrice($apartment_id, true));
                $Deposit = "";
                $Available = ($available_date <= date("Y-m-d") ? 1 : 0);
                $BuildingDescription = htmlspecialchars(strip_tags($app['public_comments']), ENT_XML1);
                //Suite
                $x_Suite = $xml->createElement("Suite");
                $x_Suites->appendChild($x_Suite);
                foreach ($xml_app_fields as $xml_field => $field_name) {
                    $x_field = $xml->createElement($xml_field, $$field_name);
                    $x_Suite->appendChild($x_field);
                }

                //Photo for Appartment
                $x_Photos = $xml->createElement("Photos");
                $x_Suite->appendChild($x_Photos);

                $app_photos = $DB_apt->getAppPhotos($apartment_id);
                //  var_dump($app_photos);

                if (!empty($app_photos[0]['pictures'])) {
                    $app_pics = $app_photos[0]['pictures'];
                    $pic_arr = explode("|", $app_pics);

                    foreach ($pic_arr as $pic) {
                        $x_Photo = $xml->createElement("Photo");
                        $x_Photos->appendChild($x_Photo);
                        $x_PhotoURL = $xml->createElement("Url", "https://".$company_sub_domain.".spgmanagement.com/admin/files/apartment_pictures/" . $pic);
                        $x_Photo->appendChild($x_PhotoURL);

                        $x_PhotoId = $xml->createElement("Id", $apartment_id * 10000 + $id_counter);
                        $x_Photo->appendChild($x_PhotoId);
                        $id_counter++;

                        $x_PhotoName = $xml->createElement("Name", $app["unit_number"]);
                        $x_Photo->appendChild($x_PhotoName);
                    }

                }

                $x_Videos = $xml->createElement("Videos");
                $x_Suite->appendChild($x_Videos);
                if (!empty($video)) {
                    $x_Video = $xml->createElement("Video");
                    $x_Videos->appendChild($x_Video);
                    $x_VideoTitle = $xml->createElement("Title", $app["unit_number"]);
                    $x_Video->appendChild($x_VideoTitle);
                    $x_VideoDescription = $xml->createElement("Description", $app["unit_number"]." Video");
                    $x_Video->appendChild($x_VideoDescription);
                    $x_VideoEmbedCode = $xml->createElement("EmbedCode", '<![CDATA[<iframe src="https://www.youtube.com/embed/'.$app["video"].'" width="480" height="360" frameborder="0" style="position:absolute;width:100%;height:100%;left:0" allowfullscreen> </iframe>]]>');
                    $x_Video->appendChild($x_VideoEmbedCode);
                    
                }

            }
        }
        //Building Picture
        $x_Photos = $xml->createElement("Photos");
        $Property->appendChild($x_Photos);

        $building_photos = $DB_building->getBldPhotos($building_id);
//    var_dump($building_photos);
        if (!empty($building_photos[0]['pictures'])) {
            $building_pics = $building_photos[0]['pictures'];

            $pic_arr = explode("|", $building_pics);


            foreach ($pic_arr as $pic) {
                $x_Photo = $xml->createElement("Photo");
                $x_Photos->appendChild($x_Photo);
                $x_PhotoURL = $xml->createElement("Url", "https://".$company_sub_domain.".spgmanagement.com/admin/files/building_pictures/" . $pic);
                $x_Photo->appendChild($x_PhotoURL);

                $x_PhotoId = $xml->createElement("Id", $building_id * 20000 + $id_counter);
                $x_Photo->appendChild($x_PhotoId);
                $id_counter++;
                $x_PhotoName = $xml->createElement("Name", "");
                $x_Photo->appendChild($x_PhotoName);
            }
        }

    }
} // foreach ($company_ids as $company_id){

//step - 4 write the xml to local directory

//Write feed file to local directory
echo $xml->save("feed_feeding.xml");


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
echo('feed feeding-xml auto update successfully!!');
echo("<br>");


?>


