<?php

if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

// Fetch the mapper data through AJAX request and process it - save it to the database 
if (!empty($_POST["data"]) && !empty($_POST["level"])) {

    $mapperData = json_decode(json_encode(json_decode($_POST["data"], true)), true);
    $level = json_decode(json_encode(json_decode($_POST["level"], true)), true); // Indicates : whether the request is for a Building | Floor | Unit
    // Based on the Level - Call the method 
    // POST Paramters required - Based on Level :
    // Bid :- bid
    // Fid :- bid, fid
    // Uid :- bid, fid, uid
//    echo "<pre>";
//    print_r($mapperData);
//    exit;

    $floorMappers = [];

    switch ($level["level"]) {
        case 'fid':
            $fid = trim($level["fid"]); // Floor ID
            $bid = trim($level["bid"]); // Building ID

            $unitWiseMapping = "";

            // In this case : $mapperAreas["floor"] is not the Floor -  It is the UNIT ID - the front form "NAME" attribute is not dynamic hence it shows "Floor" as the name 
            foreach ($mapperData["area"] as $mapperAreas) {
                // Mapper areas - Get the coordinates for each floor and frame a JSON to store it to the database 
                $unitID = intval($mapperAreas["floor"]); // floor key is not the floor value - it is the Unit value 
                $mapValues = [];

                // Push the Map values into an array to convert into a comma separated string next
                foreach ($mapperAreas["coords"] as $mapperCoords) {
                    array_push($mapValues, $mapperCoords["naturalX"]);
                    array_push($mapValues, $mapperCoords["naturalY"]);
                }

                // Concatenate the Map values into a string 
                $mapValueString = implode(",", $mapValues);
                /***
                 * 0 - UnitID = Map area coordinates (explode this to get the floor ID)
                 * 1 - Shape 
                 * 2 - Floor ID
                 * 3 - Building ID
                 * 4 - Title
                 * 5 - href                                  
                 */
                $unitWiseMapping .= "$unitID=$mapValueString&$mapperAreas[shape]&$fid&$bid&$mapperAreas[title]&$mapperAreas[href]@";
            }

            $result = $DB_floor->saveFloorUnitsMaps($fid, $unitWiseMapping); // Save the Unit wise mapping for the Floor

            if ($result > 0) {
                die(true);
            }

            break;

        case 'bid':
            $floorWiseMapping = "";
            $bid = trim($level["bid"]);

            foreach ($mapperData["area"] as $mapperAreas) {
                // Mapper areas - Get the coordinates for each floor and frame a JSON to store it to the database 
                $floorID = intval($mapperAreas["floor"]);
                $mapValues = [];

                // Push the Map values into an array to convert into a comma separated string next
                foreach ($mapperAreas["coords"] as $mapperCoords) {
                    array_push($mapValues, $mapperCoords["naturalX"]);
                    array_push($mapValues, $mapperCoords["naturalY"]);
                }

                // Concatenate the Map values into a string 
                $mapValueString = implode(",", $mapValues);
//                $floorMappers[$floorID] = "$mapValueString&$mapperAreas[shape]&$mapperAreas[title]&$mapperAreas[href]";
                /***
                 * 0 - FloorID = Map area coordinates (explode this to get the floor ID)
                 * 1 - Shape 
                 * 2 - title
                 * 3 - href                 
                 */
                $floorWiseMapping .= "$floorID=$mapValueString&$mapperAreas[shape]&$mapperAreas[title]&$mapperAreas[href]@";
            }

            // Send ALL FLOOR Mapper Values to the PDO Floor Class to store to floor_infos table
//            foreach ($floorMappers as $floorId => $floorInfo) {
            $result = $DB_building->saveFloorsMap($bid, $floorWiseMapping);
//            }

            if ($result > 0) {
                die(true);
            }

            break;

        case 'uid':
            $unitId = $level["uid"];
            $fid = trim($level["fid"]); // Floor ID
            $bid = trim($level["bid"]); // Building ID

            $intraUnitMapping = "";

            foreach ($mapperData["area"] as $mapperAreas) {
                // Mapper areas - Get the coordinates for each floor and frame a JSON to store it to the database 
                $unitID = intval($mapperAreas["floor"]); // floor key is not the floor value - it is the Unit value 
                $mapValues = [];

                // Push the Map values into an array to convert into a comma separated string next
                foreach ($mapperAreas["coords"] as $mapperCoords) {
                    array_push($mapValues, $mapperCoords["naturalX"]);
                    array_push($mapValues, $mapperCoords["naturalY"]);
                }

                // Concatenate the Map values into a string 
                $mapValueString = implode(",", $mapValues);
                /***
                 * 0 - Map area coordinates (explode this to get the floor ID)
                 * 1 - Shape 
                 * 2 - Floor ID
                 * 3 - Building ID
                 * 4 - Unit ID                 
                 * 5 - title
                 * 6 - href                 
                 */
                $intraUnitMapping .= "$mapValueString&$mapperAreas[shape]&$fid&$bid&$unitId&$mapperAreas[title]&$mapperAreas[href]@";
            }

            $result = $DB_apt->saveUnitMap($unitId, $intraUnitMapping);

            if ($result > 0) {
                die(true);
            }

            break;
    }

//    echo true;
}
?>