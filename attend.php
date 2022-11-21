<?php

include 'setting_html.php';

$PAGE_NAME = "attend";

echo "<!DOCTYPE html><html>";

//head
$fp = fopen($SET_DIRECTORY."head.html", "r") or die("no found file.");
while (!feof($fp)){
    echo fgets($fp);
}
fclose($fp);

echo "<body>";

//body
$fp = fopen($SET_DIRECTORY.$PAGE_NAME.".html", "r") or die("no found file.");
while (!feof($fp)){
    echo fgets($fp);
}
fclose($fp);

echo "</body></html>";
?>