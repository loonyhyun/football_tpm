<?php

include 'setting_html.php';

$PAGE_NAME = $_REQUEST["page"];

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

try {
    $fileName = $SET_DIRECTORY.$PAGE_NAME.".html";
    if ( !file_exists($fileName) ) {
        throw new Exception('File not found.');
    }
    //body
    $fp = fopen($SET_DIRECTORY.$PAGE_NAME.".html", "r") or die("no found file.");
    while (!feof($fp)){
        echo fgets($fp);
    }
    fclose($fp);
} catch (Exception $th) {
    $fp = fopen($SET_DIRECTORY."error.html", "r") or die("no found file.");
    while (!feof($fp)){
        echo fgets($fp);
    }
    fclose($fp);
}

//footer
$fp = fopen($SET_DIRECTORY."footer.html", "r") or die("no found file.");
while (!feof($fp)){
    echo fgets($fp);
}
fclose($fp);

echo "</body></html>";
?>