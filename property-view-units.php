<div class="detail-title">
    <h2 class="title-left"><?php echo $DB_snapshot->echot("Available Units List"); ?></h2>
</div>
<div id="table_unit_container" style="overflow-y: auto;max-height: 400px;">
    <table id="table_unit_list" class="sortable">
        <thead>
            <tr>
                <th style="white-space:nowrap"><?php echo $DB_snapshot->echot("Unit"); ?></th>
                <th><?php echo $DB_snapshot->echot("Rent"); ?></th>
                <th><?php echo $DB_snapshot->echot("Bed"); ?></th>
                <th class="availableOnTh">
                    <?if (!$isMobile) {
                            echo $DB_snapshot->echot("Available On");
                        }else{
                            echo $DB_snapshot->echot("Avail. On");
                        } ?>
                </th>
                <th><?php echo $DB_snapshot->echot("Size"); ?></th>
                <?if (!$isMobile) {?>
                <th><?php echo $DB_snapshot->echot("WC"); ?></th>
                <?}?>

                <!--                          <th>Available until</th>-->
            </tr>
        </thead>
        <tbody class="houzez-tabs">
            <?php
            // die(print_r($apt_rows));
            // Iterate over all the apartments in the building and check if they have to be shown to user
            // If the Force Listing is Set to true, show the apartment
            // echo(var_dump($DB_apt->getAptInfoInBuildingLessRenewalDay($building_id, $company_id)));
            foreach ($apt_rows as $apt_row) {
                //  echo $apt_row['apartment_id']."->".$apt_row['bedrooms']."<br>";
                if ($DB_apt->isUnitShowed($apt_row['apartment_id'])) {
                    // echo $apt_row['unit_number']."<br>";
                    $gapBetweenLeases = false;
                    $furnishedDislayText = array(
                        0 => "Not Furnished",
                        1 => "Fully Furnished",
                        2 => "Partially Furnished"
                    );

                    $is_apt_furnished = $apt_row["furnished"];
                    $furnishedDisplay = "";
                    if ($is_apt_furnished != 0) {
                        //   echo  $apt_row['apartment_id']."->".$apt_row["furnished"]."<br>";
                        $furnishedDisplay = '<i  data-toggle="tooltip" title="' . $furnishedDislayText[$is_apt_furnished] . '" style="color:black;" class="fas fa-couch"></i>';
                    }
                    // echo $apt_row['apartment_id']."=>".$apt_row['unit_number'].", ";
                    // Checking if the apartment is under repair
                    if (!$apt_row["front_force_list"]) {
                        if (in_array($apt_row['renovation_status'], array(2, 3))) {
                            continue;
                        }
                    }
                    // echo $apt_row['apartment_id']."<br>";
                    //$apartmentAvailability = "N/A";
                    if ($apt_row['apartment_status'] == '5') {
                        $apartmentAvailability = $DB_snapshot->echot("Available Now!");
                    } else {
                        $apartmentAvailability = $apt_row['available_date'];
                    }
                    // echo $apt_row['apartment_id']."=>".$apt_row['unit_number'].", ";
                    // echo "<hr>";
                    if (!$apt_row["front_force_list"]) {
                        if ($DB_apt->isUnitShowed($apt_row['apartment_id']) || $gapBetweenLeases) { ?>
            <tr id="<?php echo $apt_row['apartment_id']; ?>" <?php if ($default == '0') {
                                                                                    echo 'class="active"';
                                                                                    $default = '1';
                                                                                } ?>>
                <!--unit_number-->
                <td><?php echo $apt_row['unit_number']; ?><?php echo $furnishedDisplay; ?>
                </td>
                <td><?php echo number_format($apt_row['monthly_price']); ?></td>
                <td><?php echo $apt_row['bedrooms']; ?></td>
                <td><?php echo $apartmentAvailability; ?></td>
                <td><?php echo number_format($apt_row['area']); ?></td>
                <?if (!$isMobile) {?>
                <td><?php echo $apt_row['bath_rooms']; ?></td>
                <?}?>
            </tr>
            <?php
                        }
                    } else {
                        ?>
            <tr id="<?php echo $apt_row['apartment_id']; ?>" <?php if ($default == '0') {
                                                                                echo 'class="active"';
                                                                                $default = '1';
                                                                            } ?>>
                <td style="white-space:nowrap"><?php echo $apt_row['unit_number']; ?><?php echo $furnishedDisplay; ?>
                </td>
                <td><?php echo number_format($apt_row['monthly_price']); ?></td>
                <td><?php echo $apt_row['bedrooms']; ?></td>
                <td><?php echo $apartmentAvailability; ?></td>
                <td><?php echo number_format($apt_row['area']); ?></td>
                <?if (!$isMobile) {?>
                <td><?php echo $apt_row['bath_rooms']; ?></td>
                <?}?>
            </tr>
            <?php
                    }
                } // if ($DB_apt->isUnitShowed($apt_row['apartment_id'])) {
            }
            ?>
        </tbody>
    </table>
</div>