<?php

include 'setting_html.php';

$PAGE_NAME = "attend";

echo "<!DOCTYPE html><html>";

echo "";

//body
$fp = fopen($SET_DIRECTORY.$PAGE_NAME.".html", "r") or die("no found file.");
while (!feof($fp)){
    echo fgets($fp);
}
fclose($fp);

echo "";
echo "</html>";
?>