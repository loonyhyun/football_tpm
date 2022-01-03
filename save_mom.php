<?php

include 'setting.php';
  
$pid = $_REQUEST["id"];
$pcmd = $_REQUEST["cmd"];

$array = array();

if($pcmd == "mom"){
    if($conn){
        $pmatch_id = $_REQUEST["match_id"];
        $pplayerid = $_REQUEST["player_id"];
        $pdesc = $_REQUEST["mom_reason"];
        
		$sql = "INSERT INTO football_mom
        (match_id, player_id, mom_reason, reg_date) 
        values (
		".$pmatch_id.",
		".$pplayerid.",
		'".$pdesc."',
        NOW()
        )";

		$result = mysqli_query($conn, $sql);
		if($result){
			echo "ok";
		}
		else{
			echo "fail";
		}
    }
}
else if($pcmd == "mom_one"){
    if($conn){
        $pmatch_id = $_REQUEST["match_id"];
        
		$sql = "SELECT T1.*, (SELECT player_name FROM football_player WHERE id = T1.player_id) player_name
        FROM (
        SELECT m.id match_id, mom.player_id,
           COUNT(1) mom_cnt,
           @RNUM := @RNUM + 1 rnum
        FROM football_match m, football_mom mom, (SELECT @RNUM := 0) tmp
        WHERE m.id = ".$pmatch_id."
        AND m.id = mom.match_id
        GROUP BY m.id, mom.player_id
        ORDER BY mom_cnt DESC
        ) T1
        WHERE rnum = 1";

		$result = mysqli_query($conn, $sql1);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('match_id'=>$row['match_id']
                ,'player_id'=>$row['player_id']
                ,'player_name'=>$row['player_name']
                ,'mom_cnt'=>$row['mom_cnt']
            ));
        }

        echo json_encode($array);
    }
}
else if($pcmd == "mom_view_cnt"){
    if($conn){
        $pmatch_id = $_REQUEST["match_id"];
        $sql1 = "SELECT m.id match_id, m.match_date, mom.player_id, p.player_name,
            COUNT(1) mom_cnt
        FROM football_match m, football_mom mom, football_player p
        WHERE m.id = ".$pmatch_id."
        AND m.id = mom.match_id
        AND mom.player_id = p.id
        GROUP BY m.id, m.match_date, mom.player_id, p.player_name
        ORDER BY mom_cnt desc, p.player_name";
        $result = mysqli_query($conn, $sql1);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('match_id'=>$row['match_id']
                ,'match_date'=>$row['match_date']
                ,'player_id'=>$row['player_id']
                ,'player_name'=>$row['player_name']
                ,'mom_cnt'=>$row['mom_cnt']
            ));
        }
    }

    echo json_encode($array);
}
else if($pcmd == "mom_view"){
    if($conn){
        $pmatch_id = $_REQUEST["match_id"];
        $sql1 = "SELECT m.id match_id, m.match_date, mom.player_id, p.player_name,
            mom.mom_reason, mom.reg_date
        FROM football_match m, football_mom mom, football_player p
        WHERE m.id = ".$pmatch_id."
        AND m.id = mom.match_id
        AND mom.player_id = p.id
        ORDER BY mom.reg_date, p.player_name";
        $result = mysqli_query($conn, $sql1);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('match_id'=>$row['match_id']
                ,'match_date'=>$row['match_date']
                ,'player_id'=>$row['player_id']
                ,'player_name'=>$row['player_name']
                ,'mom_reason'=>$row['mom_reason']
                ,'reg_date'=>$row['reg_date']
            ));
        }
    }

    echo json_encode($array);
}
?>