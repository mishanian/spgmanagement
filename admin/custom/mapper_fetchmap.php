<?php

if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

$coordinateData = array();
$imagePath = "";

if (!empty($_GET["data"]) && !empty($_GET["level"])) {
    $level = $_GET["level"];
    // Based on the level, fetch the "mapping_area".
    switch ($level) {
        case 'bid':
            if (isset($_GET["bid"])) {
                $floorsMappingData = array();
                // Get mapping picture and area
                /*                 * *
                 * 0 - FloorID = Map area coordinates (explode this to get the floor ID)
                 * 1 - Shape 
                 * 2 - title
                 * 3 - href                 
                 */
                $mappingDataResult = $DB_building->getMappingData($_GET["bid"]);
                $mappingData = $mappingDataResult["mapping_area"];
                $imagePath = $mappingDataResult["mapping_picture"];
                $mappingDataExplode = explode("@", $mappingData);

                array_pop($mappingDataExplode); // remove the last empty array index without any data - this exists because there is a @ at the end of each floor string and it causes an empty index when the string is exploded

                foreach ($mappingDataExplode as $floorData) {
                    $mappingDataExplode = explode("&", $floorData);
                    $singleFloorData["mapping_area"] = array();
                    $singleFloorData["mapping_area"][0] = explode("=", $mappingDataExplode[0])[1];
                    $singleFloorData["mapping_area"][1] = $mappingDataExplode[1];
                    $singleFloorData["mapping_area"][2] = $mappingDataExplode[2];
                    $singleFloorData["mapping_area"][3] = $mappingDataExplode[3];
                    $singleFloorData["mapping_area"][4] = explode("=", $mappingDataExplode[0])[0]; // Floor ID 

                    array_push($coordinateData, $singleFloorData);
                }

//            $mapDataFloorCoord = $mappingDataExplode[0];
//            $mapDataFlCoExplode = explode("=",$mapDataFloorCoord); // 0 - Floor ID ; 1 - Coordinates
            }
            break;
            
        case 'fid':            
                // Get mapping picture and area
                /*                 * *
                 * 0 - UnitID = Map area coordinates (explode this to get the floor ID)
                 * 1 - Shape 
                 * 2 - Floor ID
                 * 3 - Building ID
                 * 4 - Title
                 * 5 - href                                  
                 */
                $mappingDataResult = $DB_floor->getMappingData($_GET["data"]);
                $mappingData = $mappingDataResult["mapping_area"];
                $imagePath = "../files/floor_floorplans/".$mappingDataResult["mapping_picture"];
                $mappingDataExplode = explode("@", $mappingData);

                array_pop($mappingDataExplode); // remove the last empty array index without any data - this exists because there is a @ at the end of each floor string and it causes an empty index when the string is exploded

                foreach ($mappingDataExplode as $floorData) {
                    $mappingDataExplode = explode("&", $floorData);
                    $singleFloorData["mapping_area"] = array();
                    $singleFloorData["mapping_area"][0] = explode("=", $mappingDataExplode[0])[1];
                    $singleFloorData["mapping_area"][1] = $mappingDataExplode[1];
                    $singleFloorData["mapping_area"][2] = $mappingDataExplode[4];
                    $singleFloorData["mapping_area"][3] = $mappingDataExplode[5];
                    $singleFloorData["mapping_area"][4] = explode("=", $mappingDataExplode[0])[0]; // Unit ID 

                    array_push($coordinateData, $singleFloorData);
                }
                
                echo json_encode(["data" => $coordinateData, "picture" => $imagePath]);
                die();
                
            break;
        case 'uid':
            if (isset($_GET["uid"])) {
                // Get mapping picture and area
                $mappingData = $DB_apt->getMappingData($_GET["uid"]);
                print_r($mappingData);
            }
            break;
    }
}
?>
