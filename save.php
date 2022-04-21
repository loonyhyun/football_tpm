<?php

include 'setting.php';
  
$pid = $_REQUEST["id"];
$pcmd = $_REQUEST["cmd"];

if($pcmd == "team"){
    if($conn){
        echo "fail team";
    }
}
else if($pcmd == "player"){
    if($conn){
        $pname = $_REQUEST["player_name"];
        $pbackno = $_REQUEST["back_no"];
        $pposition = $_REQUEST["position"];
        $pdesc = $_REQUEST["desc"];
        
		$sql = "INSERT INTO football_player
        (team_id, player_name, back_no, input, position) 
        values (
		".$pid.",
		'".$pname."',
		'".$pbackno."',
		'".$pdesc."',
		'".$pposition."'
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
else if($pcmd == "player_update"){
    if($conn){
        $pid = $_REQUEST["player_id"];
        $pname = $_REQUEST["player_name"];
        $pbackno = $_REQUEST["back_no"];
        $pposition = $_REQUEST["position"];
        $pdesc = $_REQUEST["desc"];
        
        $sql = "UPDATE football_player
        SET
		  player_name = '".$pname."',
		  back_no = '".$pbackno."',
		  input = '".$pdesc."',
		  position = '".$pposition."'
        WHERE id = '".$pid."'";
        
        $result = mysqli_query($conn, $sql);
        if($result){
            $sql = "INSERT INTO football_player_hist
            (id, team_id, player_name, back_no, input, position, regdate) 
            SELECT id, team_id, player_name, back_no, input, position, NOW()
            FROM football_player WHERE id = ".$pid."";
    
            $result2 = mysqli_query($conn, $sql);
            echo "ok";
        }
        else{
            echo "fail";
        }
    }
}
else if($pcmd == "match"){
    if($conn){
        $pDate = $_REQUEST["match_date"];
        $pWinAB = $_REQUEST["winAB"];
        $pTeamA = $_REQUEST["teamA"];
        $pTeamB = $_REQUEST["teamB"];
        $pGoalA = $_REQUEST["goalA"];
        $pGoalB = $_REQUEST["goalB"];
        $pAsstA = $_REQUEST["asstA"];
        $pAsstB = $_REQUEST["asstB"];

        $pGround = $_REQUEST["groundId"];
        
        $sql = "INSERT INTO football_match
        (team_id, match_date, team_a, team_b, win_ab, goal_a, goal_b, asst_a, asst_b, ground_id)
        values (
		".$pid.",
		'".$pDate."',
		'".$pTeamA."',
		'".$pTeamB."',
        '".$pWinAB."',
		'".$pGoalA."',
		'".$pGoalB."',
		'".$pAsstA."',
		'".$pAsstB."',
        '".$pGround."'
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
else if($pcmd == "match_d"){
    if($conn){
        $pmid = $_REQUEST["match_id"];
        $pcnt = $_REQUEST["cnt"];
        
        $sql = "UPDATE football_match SET
                quarters = $pcnt
            WHERE id = ".$pmid."";
        
        $result = mysqli_query($conn, $sql);
        if($result){
            echo "ok";
        }
        else{
            echo "fail-1";
        }
    }
}
else if($pcmd == "match_scoreless_init"){
    if($conn){
        $pmid = $_REQUEST["match_id"];
        
        $sql = "DELETE FROM football_match_scoreless
                WHERE match_id = ".$pmid."";
        
        $result = mysqli_query($conn, $sql);
        if($result){
            echo "ok";
        }
        else{
            echo "fail-1";
        }
    }
}
else if($pcmd == "match_scoreless"){
    if($conn){
        $pmid = $_REQUEST["match_id"];
        $pq = $_REQUEST["q"];
        $pteam = $_REQUEST["team"];
        
        $sql = "INSERT INTO football_match_scoreless
                (match_id, team_ab, m_quarter)
                values ( ".$pmid.", '".$pteam."', ".$pq." )";
        
        $result = mysqli_query($conn, $sql);
        if($result){
            echo "ok";
        }
        else{
            echo "fail-1";
        }
    }
}
else if($pcmd == "match_reply"){
    if($conn){
        $pmid = $_REQUEST["match_id"];
        $ptxt = $_REQUEST["reply"];
        
        $sql = "INSERT INTO football_match_reply
            (match_id, reply, regdate)
            values ( ".$pmid.", '".$ptxt."', NOW() )";
        
        $result = mysqli_query($conn, $sql);
        if($result){
            echo "ok";
        }
        else{
            echo "fail";
        }
    }
}
else if($pcmd == "best11"){
    if($conn){
        $pname = $_REQUEST["reg_name"];
        $pformation = $_REQUEST["reg_formation"];
        $pp0 = $_REQUEST["player_0"];
        $pp1 = $_REQUEST["player_1"];
        $pp2 = $_REQUEST["player_2"];
        $pp3 = $_REQUEST["player_3"];
        $pp4 = $_REQUEST["player_4"];
        $pp5 = $_REQUEST["player_5"];
        $pp6 = $_REQUEST["player_6"];
        $pp7 = $_REQUEST["player_7"];
        $pp8 = $_REQUEST["player_8"];
        $pp9 = $_REQUEST["player_9"];
        $pp10 = $_REQUEST["player_10"];

        $sql = "INSERT INTO football_best
            (team_id, reg_date, reg_name, reg_formation
            , player_0, player_1, player_2
            , player_3, player_4, player_5
            , player_6, player_7, player_8, player_9
            , player_10)
            values ( ".$pid.", NOW(), '".$pname."', ".$pformation." 
            , ".$pp0.", ".$pp1.", ".$pp2."
            , ".$pp3.", ".$pp4.", ".$pp5."
            , ".$pp6.", ".$pp7.", ".$pp8.", ".$pp9."
            , ".$pp10."
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
else if($pcmd == "best_formation"){
    if($conn){
        $pname = $_REQUEST["reg_name"];
        $pformation = $_REQUEST["reg_formation"];
        $patt = $_REQUEST["player_att"];
        $pmid = $_REQUEST["player_mid"];
        $pdef = $_REQUEST["player_def"];
        $pkeep = $_REQUEST["player_keep"];

        $sql = "INSERT INTO football_best_formation
            (team_id, reg_date, reg_name, reg_formation
            , player_att, player_mid, player_def, player_keep)
            values ( ".$pid.", NOW(), '".$pname."', ".$pformation." 
            , '".$patt."', '".$pmid."', '".$pdef."', '".$pkeep."'
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
else if($pcmd == "attend"){
    if($conn){
        $ppid = $_REQUEST["player_id"];
        $pteamtype = $_REQUEST["teamtype"];

        $sql = "INSERT INTO football_attend
            (team_type, player_id, reg_date)
            values ( '".$pteamtype."', ".$ppid.", NOW() )";
        
        $result = mysqli_query($conn, $sql);
        if($result){
            echo "ok";
        }
        else{
            echo "fail";
        }
    }
}
else if($pcmd == "attend_init"){
    if($conn){
        $pchecker = $_REQUEST["checker"];
        if($pchecker == "baron"){
            $sql = "DELETE FROM football_attend";
            
            $result = mysqli_query($conn, $sql);
            if($result){
                echo "ok";
            }
            else{
                echo "fail";
            }
        }else{
            echo "fail-1";
        }
    }
}
else if($pcmd == "attend_del"){
    if($conn){
        $pchecker = $_REQUEST["checker"];
        if($pchecker == "baron"){
            $ppid = $_REQUEST["player_id"];
    
            $sql = "DELETE FROM football_attend WHERE player_id = ".$ppid." ";
            
            $result = mysqli_query($conn, $sql);
            if($result){
                echo "ok";
            }
            else{
                echo "fail";
            }
        }else{
            echo "fail-1";
        }
    }
}
else if($pcmd == "attend_quater"){
    if($conn){
        
        $team_a = $_REQUEST["team_a"];
        $team_b = $_REQUEST["team_b"];
        $quarters = $_REQUEST["quarters"];
        $def_a = $_REQUEST["def_a"];
        $def_b = $_REQUEST["def_b"];
        
        $sql = "DELETE FROM football_attend_quater";
        $result = mysqli_query($conn, $sql);

        $sql = "INSERT INTO football_attend_quater
            (team_a, team_b, quarters, def_a, def_b)
            values ( '".$team_a."', '".$team_b."', ".$quarters."
            , '".$def_a."', '".$def_b."')";
        
        $result = mysqli_query($conn, $sql);
        if($result){
            echo "ok";
        }
        else{
            echo "fail";
        }
    }
}
else{
    echo "fail else";
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