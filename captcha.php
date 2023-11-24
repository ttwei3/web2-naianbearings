<?php
session_start();

$random_str = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 6);

$_SESSION['captcha'] = $random_str;

$width = 120; 
$height = 50; 
$image = imagecreatetruecolor($width, $height);
$bg_color = imagecolorallocate($image, 255, 255, 255); 
$fg_color = imagecolorallocate($image, 0, 0, 0); 
$line_color = imagecolorallocate($image, 64, 64, 64); 

imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

for ($i = 0; $i < 5; $i++) {
    imageline($image, 0, rand() % $height, $width, rand() % $height, $line_color);
}


imagestring($image, 5, 10, 15, $random_str, $fg_color); 

header('Content-type: image/png');
imagepng($image);

imagedestroy($image);
?>
