<?php

if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

// Fetch the image url from the database for the level element 
// Eg: if the level is "bid" - fetch the building image.
if (!empty($_GET["data"]) && !empty($_GET["level"])) {

    switch ($_GET["level"]) {
        case 'fid':
            // Get the Floor Plan Image
            $floorId = intval($_GET["data"]);            
            $mapping_picture = $DB_floor->getFloorPlanImage($floorId)["mapping_picture"];
            $path='floor_mapping_pictures';
            break;

        case 'bid':
            $buildingId = intval($_GET["data"]);
            // Get$ the mapping_picture from the building_infos table 
            $mapping_picture = $DB_building->getMappingPicture($buildingId)["mapping_picture"];
            $path='building_mapping_pictures';
            break;

        case 'uid':
            $unitId = intval($_GET["data"]);
            $mapping_picture = $DB_apt->getUnitPlanImage($unitId)["mapping_picture"];
            $path='apartment_mapping_pictures';
            break;
    }

    if (!empty($mapping_picture)) {       
        echo "../files/$path/".$mapping_picture;       
        die();
    }else{
        echo "../files/default.jpg";
        die();
    }
}
echo false;
?>