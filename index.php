<?php

include 'setting_html.php';

$PAGE_NAME = "football";

echo "<!DOCTYPE html><html>";

//head
$fp = fopen($SET_DIRECTORY."head.html", "r") or die("no found file.");
while (!feof($fp)){
    echo fgets($fp);
}
fclose($fp);

echo "<body>";

//top
$fp = fopen($SET_DIRECTORY."top.html", "r") or die("no found file.");
while (!feof($fp)){
    echo fgets($fp);
}
fclose($fp);

//body
$fp = fopen($SET_DIRECTORY.$PAGE_NAME.".html", "r") or die("no found file.");
while (!feof($fp)){
    echo fgets($fp);
}
fclose($fp);

//footer
$fp = fopen($SET_DIRECTORY."footer.html", "r") or die("no found file.");
while (!feof($fp)){
    echo fgets($fp);
}
fclose($fp);

echo "</body></html>";
?>