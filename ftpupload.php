<?php

$file = "./upload/1.jpg";
$info = getimagesize($file);

$w = 400;
$h = 400;

$width = $info[0];
$height = $info[1];

if ($info['mime'] == 'image/jpeg') 
    $image = imagecreatefromjpeg($file);

elseif ($info['mime'] == 'image/gif') 
    $image = imagecreatefromgif($file);

elseif ($info['mime'] == 'image/png') 
    $image = imagecreatefrompng($file);

$dst = imagecreatetruecolor($w, $h);
imagecopyresampled($dst, $image, 0, 0, 0, 0, $w, $h, $width, $height);
ob_start();
$result = imagejpeg($dst, $file, 100);
$output = base64_encode(ob_get_contents());
ob_end_clean();

echo "ok resize\n";
?>