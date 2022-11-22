<?php

$SET_DIRECTORY = "./html/";

if(!isset($_SESSION["team_id"])){
    echo "<script>
    var TEAM_ID = 1;
    var TEAM_NAME = '';
    var PLAY_CNT = 0;
    </script>";
}else{
    echo "<script>
    var TEAM_ID = ".$_SESSION["team_id"].";
    var TEAM_NAME = '".$_SESSION["team_name"]."';
    var PLAY_CNT = 0;
    </script>";
}


?>