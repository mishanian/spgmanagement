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

// Based on the Level, the type of dropdown changes to show 
// For Building : Show Floor values as dropdown 
// For Floor : Show Units as dropdown
// For Unit : Show nothing - the rooms and other areas will be selected by the admin

$selectOptionData = array();

switch ($level) {
    case 'bid':
        $selectedOptionTitle = "Floor";
        if (isset($_GET["bid"])) {
            echo "<div id='bidValue' style='display:none;'> $_GET[bid] </div>";
            $floorInfos = $DB_floor->getAllFloorInfos($_GET["bid"]);
            foreach ($floorInfos as $floorSingle) {
                $floor["value"] = $floorSingle["floor_id"];
                $floor["title"] = $floorSingle["floor_name"];
                array_push($selectOptionData, $floor);
            }
        }
        break;
    case 'fid':
        $selectedOptionTitle = "Unit";
        if (isset($_GET["fid"])) {
            $buildingId = $DB_floor->getBuildingId(intval($_GET["fid"]));
            echo "<div id='fidValue' style='display:none;'> $_GET[fid] </div>";
            echo "<div id='bidValue' style='display:none;'> $buildingId </div>";
            $apartmentInfos = $DB_apt->getApartmentInfoByFloor($_GET["fid"], $buildingId);
            foreach ($apartmentInfos as $apartmentSingle) {
                $apt["value"] = $apartmentSingle["apartment_id"];
                $apt["title"] = $apartmentSingle["unit_number"];
                array_push($selectOptionData, $apt);
            }
        }
        break;
    case 'uid':
        if (isset($_GET["uid"])) {
            $apartmentInfo = $DB_apt->getAptInfo($_GET["uid"]);
            $buildingId = $apartmentInfo["building_id"];
            $floorId = $apartmentInfo["floor_id"];
            echo "<div id='fidValue' style='display:none;'> $floorId </div>";
            echo "<div id='bidValue' style='display:none;'> $buildingId </div>";
            echo "<div id='uidValue' style='display:none;'> $_GET[uid] </div>";
        }
        break;
}
?>

<div id="levelValue" style="display:none;"><?php echo $level; ?></div>
<!--<div class="container">
    <div class="row">
        <div class="col-md-12">               
            <div class="step"><button type="button" class="btn btn-success btn-lg" id="image-mapper-upload">Select Image from My PC</button><input type="file" name="S" id="image-mapper-file"> </div>
        </div>
    </div>
</div>-->

<div class="container">
    <div class="row form-group">
        <div class="col-md-12">
            <div class="container">
                <div class="row form-group">
                    <div class="col-md-12" id="image-map-wrapper">
                        <div id="image-map-container">
                            <div id="image-map" style="max-width: 100%"><span class="glyphicon glyphicon-picture"></span></div>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table" id="image-mapper-table">
                <thead>
                    <tr>
                        <th>Active</th>
                        <th>Shape</th>
                        <?php if ($level != 'uid') { ?> <th><?php echo $selectedOptionTitle; ?></th> <?php } ?>
                        <th>Link</th>
                        <th>Title</th>
                        <th>Target</th>
                        <th style="width: 25px"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width: 65px">
                            <div class="control-label input-sm"><input type="radio" name="im[0][active]" value="1"></div>
                        </td>
                        <td>                            
                            <select name="im[0][shape]" class="form-control input-sm">
                                <option value="">---</option>
                                <option value="rect">Rect</option>
                                <option value="poly">Poly</option>
                                <option value="circle">Circle</option>
                            </select>
                        </td>
                        <?php if ($level != 'uid') { ?> <td>
                                <select name="im[0][floor]" class="form-control input-sm">
                                    <option value="default"><?php echo "Select $selectedOptionTitle"; ?></option>                                
                                    <?php
                                    foreach ($selectOptionData as $option) {
                                        echo "<option value='" . $option['value'] . "'> $option[title] </option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        <?php } ?>
                        <td><input type="text" name="im[0][href]" value="" placeholder="Link" class="form-control input-sm"></td>
                        <td><input type="text" name="im[0][title]" value="" placeholder="Title" class="form-control input-sm"></td>
                        <td>
                            <select name="im[0][target]" class="form-control input-sm">
                                <option value="">---</option>
                                <option value="_blank">_blank</option>
                                <option value="_parent">_parent</option>
                                <option value="_self">_self</option>
                                <option value="_top">_top</option>
                            </select>
                        </td>
                        <td><button class="btn btn-default btn-sm remove-row" name="im[0][remove]"><span class="glyphicon glyphicon-remove"></span></button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" style="text-align: right"><button type="button" class="btn btn-danger btn-sm add-row"><span class="glyphicon glyphicon-plus"></span> Add New Area</button></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="container segment">
    <div class="row">
        <div class="col-md-12" style="text-align: center"><button type="button" class="btn btn-success btn-md" id="submitRow">Submit</button></div>
    </div>
</div>

<div class="modal fade" id="image-mapper-load" tabindex="-1" role="dialog" aria-labelledby="image-mapper-load-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="image-mapper-dialog">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="image-mapper-load-label">Load Image from Website</h4>
            </div>
            <div class="modal-body">
                <div class="input-group input-group-sm has-error"><input type="text" value="" placeholder="http://..." id="image-mapper-url" class="form-control input-sm"><span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button> <button type="button" class="btn btn-primary" id="image-mapper-continue">Continue</button></div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-code" tabindex="-1" role="dialog" aria-labelledby="modal-code-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="modal-code-dialog">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modal-code-label">Generated Image Map Output</h4>
            </div>
            <div class="modal-body"><textarea class="form-control input-sm" readonly="readonly" id="modal-code-result" rows="10"></textarea></div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="css/mapper.css">
<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Quantico">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.2/css/bootstrap.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script><script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.2/js/bootstrap.min.js"></script>
<link itemprop="image" href="https://www.image-map.net/images/apple-touch-icon-112x112.png">
<script src="js/mapper.js"></script>

<script>(function ($) {
        $(document).trigger('init');
    })(jQuery);</script>

<script type="text/javascript">var _fo = _fo || [];
    _fo.push({'m': 'true', 'c': 'f8e147', 'i': 10442});
    if (typeof fce == 'undefined') {
        var s = document.createElement('script');
        s.type = 'text/javascript';
        s.async = true;
        s.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'formcrafts.com/js/fc.js';
        var fi = document.getElementsByTagName('script')[0];
        fi.parentNode.insertBefore(s, fi);
        fce = 1;
    }</script>


