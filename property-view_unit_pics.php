<div class="row">
    <div class="col-lg-12"><?php
if ($apt_row['pictures']) {
?>

        <div class="owl-wrapper">
            <div id="carousel-post-card" class="gallery unit-pics">
                <?php
 //   print_r($apt_row['pictures']);
$apt_pic_arr = explode("|", $apt_row['pictures']);
    foreach ($apt_pic_arr as $apt_pic) {
        $apt_pic=str_replace(" ","%20",$apt_pic);
        //  echo count($apt_pic_arr);?>
                <div class="item col-sm-12" style="padding: 2px">
                    <div class="item-wrap">
                        <div class="post-card-item" style="padding: 0px">
                            <div class="figure-block">
                                <figure class="item-thumb">
                                    <a href="<?php echo "admin/files/apartment_pictures/$apt_pic"; ?>"
                                        class="hover-effect"
                                        data-gal="prettyPhoto[gallery<?=$apt_row['apartment_id']?>]">
                                        <div class="unitPictures">
                                            <img alt="Unit Picture" class="unitPictures"
                                                src="<?php echo "admin/files/apartment_pictures/$apt_pic" ?>"
                                                style="height: 120px;">
                                        </div>
                                    </a>
                                </figure>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
    } ?>
            </div>
        </div>
        <?php
} else {
?><p>Sorry, there is no available pictures for this
            unit
            now.</p><?php
    }
?>
        <?php if (!empty($apt_row['video'])) {?>
        <div>
            <iframe width="420" height="315" src="https://www.youtube.com/embed/<?=$apt_row['video']?>">
            </iframe>
        </div>
        <? }?>
    </div>
</div>