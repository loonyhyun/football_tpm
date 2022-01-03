<?php

include 'setting.php';
  
$pid = $_REQUEST["id"];
$pcmd = $_REQUEST["cmd"];

$notinplayer1 = " and id not in ('79','80','81','82') ";
$notinplayer = " and player_id not in ('79','80','81','82') ";

$array = array();
if($pcmd == "user"){
    if($conn){
        $puid = $_REQUEST["user_id"];
        $pupwd = $_REQUEST["user_pwd"];
        $sql = "SELECT count(1) cnt
                FROM football_user t WHERE user_id = '".$puid."'
                    and user_pwd = '".$pupwd."'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        //array_push($array, array('result'=>''.$row['cnt']));
        //echo json_encode($array);
        echo ''.$row['cnt'].'';
    }
}
else{
    
}


#$sql = "SELECT * FROM pubg_match";
#$rows = mysqli_query($conn, $sql);
#if($conn){
#	while($row = mysqli_fetch_array($rows)){
#		echo $row['match_id'];
#	}
#}else{
#	echo "fail"
#}
?>