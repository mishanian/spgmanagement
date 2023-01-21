<?php
if (strpos(getcwd(), "custom") == false) {
    $path = "../pdo/";
} else {
    $path = "../../pdo/";
}
$file = $path . 'dbconfig.php';
include_once($file);

// Get the level of Mapping to be done. Based on the level : bid | fid | uid will be set.
if (!isset($_GET["level"])) {
    die("Level value for the Mapping not set.");
}

$level = $_GET["level"];
$coordinateData = array();
$imagePath = "";

// Based on the level, fetch the "mapping_picture" and the "mapping_area".
switch ($level) {
    case 'bid':
        if (isset($_GET["bid"])) {
            $floorsMappingData = array();
            // Get mapping picture and area
            /*             * *
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
        if (isset($_GET["fid"])) {
            // Get mapping picture and area
            $mappingData = $DB_floor->getMappingData($_GET["fid"]);
            print_r($mappingData);
        }
        break;
    case 'uid':
        if (isset($_GET["uid"])) {
            // Get mapping picture and area
            $mappingData = $DB_apt->getMappingData($_GET["uid"]);
            print_r($mappingData);
        }
        break;
}
?>
<div class="container">
    <div class="row">
        <div class="alert alert-info">
            <h4><strong><?php echo $DB_building->getBdName($_GET["bid"]); ?> </strong></h4>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-12 " id="image-map-wrapper">
            <div id="image-map-container">
                <div id="image-map" style="max-width: 100%">
                    <img id="buildingMap" src="<?php echo "../files/building_mapping_pictures/" . $imagePath; ?>" id="mapper"  class="img-rounded img-bordered" usemap="#image-map">
                    <map name="image-map">    
                        <?php
                        foreach ($coordinateData as $floor) {
                            $mapperAreas = $floor['mapping_area'];
                            $mapperCoords = $mapperAreas;
                            /**
                             * "Separated by &"
                             * 0 - coordinates
                             * 1 -shape
                             * 2 - title
                             * 3 - href 
                             */
                            ?>
                            <area data-fid="<?php echo $mapperCoords[4]; ?>" target="<?php echo $mapperCoords[3]; ?>" alt="" name="a1" title="<?php echo $mapperCoords[2]; ?>" href="" coords="<?php echo $mapperCoords[0]; ?>" shape="<?php echo $mapperCoords[1]; ?>">
                            <?php
                        }
                        ?>
                    </map>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!--<script src="admin/custom/js/mapster.js"></script>-->

<script src="js/maphighlight.js"></script>
<style>
    #floor_mapper {
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .center{
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 75%;
    }
</style>
<script>
    $('#buildingMap').maphilight();
</script>

<div id="floorPlan" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Showing Floor Plan</h4>
            </div>
            <div class="modal-body">
                <!-- Show the Floor Plan image and map the areas in the floor Plan --> 
                <div id="modal-floor-plan-map" class="center">
                    <img  class="img-rounded" id="floor_mapper" usemap="#floor-image-map">
                    <map id="floor-image-map" name="floor-image-map"> 

                    </map>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
