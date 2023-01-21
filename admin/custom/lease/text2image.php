<?php
// Set the content-type
// header('Content-type: image/png');

// Create the image
$im = imagecreatetruecolor(30, 30);

// Create some colors
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 128, 128, 128);
$black = imagecolorallocate($im, 0, 0, 0);
// Make the background transparent
// imagecolortransparent($im, $black);

imagefilledrectangle($im, 0, 0, 399, 29, $white);

// The text to draw
$text = $_GET['text'];
$image_name = $_GET['image_name'];
// $tid = $_GET['tid'];
// $lease_id = $_GET['lease_id'];
// $image_name = "../../files/lease_signs/tenant_sign_init_$rid" . "_l$lease_id" . ".png";
// Replace path by your own font path
$font = __DIR__ . '/Courgette-Regular.ttf';

// Add some shadow to the text
// imagettftext($im, 20, 0, 11, 21, $grey, $font, $text);

// Add the text
imagettftext($im, 12, 1, 1, 20, $black, $font, $text);

// Using imagepng() results in clearer text compared with imagejpeg()
imagepng($im, $image_name);
imagepng($im);
imagedestroy($im);