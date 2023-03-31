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
else if($pcmd == "user_"){
    if($conn){
        $puid = $_REQUEST["user_id"];
        $pupwd = $_REQUEST["user_pwd"];
        $sql = "SELECT count(1) cnt
                FROM football_user t WHERE user_id = '".$puid."'
                    and user_pwd = '".$pupwd."'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        array_push($array, array('result'=>''.$row['cnt']));
        echo json_encode($array);
    }
}
else if($pcmd == "player"){
    if($conn){
        $pid = $_REQUEST["id"];
        $sql = "SELECT *
                FROM football_player t WHERE  team_id = ".$pid."";
        if( ! empty($_REQUEST["playerName"]) ){
            $pname = $_REQUEST["playerName"];
            $sql = $sql." and player_name = '".$pname."' ";
        }
        $result = mysqli_query($conn, $sql);
        if( ! empty($_REQUEST["playerName"]) ){
            $row = mysqli_fetch_array($result);
            array_push($array, 
                array('id'=>$row['id']
                , 'team_id'=>$row['team_id']
                , 'player_name'=>$row['player_name']
                , 'back_no'=>$row['back_no']
                , 'input'=>$row['input']
                , 'position'=>$row['position']
                , 'recommand_pos'=>$row['recommand_pos']
                , 'age'=>$row['age']
                )
            );
        }
        else{
            while($row = mysqli_fetch_array($result)){
                array_push($array,
                    array('id'=>$row['id']
                    , 'team_id'=>$row['team_id']
                    , 'player_name'=>$row['player_name']
                    , 'back_no'=>$row['back_no']
                    , 'input'=>$row['input']
                    , 'position'=>$row['position']
                    , 'recommand_pos'=>$row['recommand_pos']
                    , 'age'=>$row['age']
                    )
                );
            }
        }
        echo json_encode($array);
        //echo ''.$row['cnt'].'';
    }
}
else{
  array_push($array, array('result'=>'0'));
  echo json_encode($array);
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
