<?php
session_start();

include '../setting.php';
  
$pid = $_REQUEST["id"];
$pcmd = $_REQUEST["cmd"];

if($pcmd == "team"){
    if($conn){
        echo "fail team";
    }
}
else if($pcmd == "match_info"){
    if($conn){
		$sql = "DELETE FROM football_match_before_info WHERE team_id = ".$pid."";
		$result = mysqli_query($conn, $sql);
		$sql = "DELETE FROM football_match_before_goal WHERE team_id = ".$pid."";
		$result = mysqli_query($conn, $sql);

		$sql = "INSERT INTO football_match_before_info
        (team_id, match_date, team_a, team_b, quarters, ground_id) 
        values (
		".$pid.",
		'".$_REQUEST["match_date"]."',
		'".$_REQUEST["team_a"]."',
		'".$_REQUEST["team_b"]."',
		".$_REQUEST["quarters"].",
		".$_REQUEST["ground_id"]."
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
else if($pcmd == "match_goal_del"){
    if($conn){
		$sql = "DELETE FROM football_match_before_goal WHERE team_id = ".$pid."";
		$result = mysqli_query($conn, $sql);
		if($result){
			echo "ok";
		}
		else{
			echo "fail";
		}
    }
}
else if($pcmd == "match_goal"){
    if($conn){
		$sql = "DELETE FROM football_match_before_goal WHERE team_id = ".$pid." AND m_quarter = ".$_REQUEST["quarter"]."";
		$result = mysqli_query($conn, $sql);

		$sql = "INSERT INTO football_match_before_goal
        (team_id, m_quarter, goal_a, goal_b, asst_a, asst_b) 
        values (
		".$pid.",
		".$_REQUEST["quarter"].",
		'".$_REQUEST["goal_a"]."',
		'".$_REQUEST["goal_b"]."',
		'".$_REQUEST["asst_a"]."',
		'".$_REQUEST["asst_b"]."'
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
else if($pcmd == "yeyak"){
    if($conn){
		$sql = "INSERT INTO football_yeyak
        (team_id, txt, txtstart, txtdesc, color) 
        values (
		".$pid.",
		'".$_REQUEST["txt"]."',
		'".$_REQUEST["start"]."',
		'".$_REQUEST["desc"]."',
		'".$_REQUEST["color"]."'
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
else if($pcmd == "yeyak_update"){
    if($conn){
		$sql = "UPDATE football_yeyak SET
			team_id = ".$pid.",
			txt = '".$_REQUEST["txt"]."',
			txtstart = '".$_REQUEST["start"]."',
			txtdesc = '".$_REQUEST["desc"]."',
			color = '".$_REQUEST["color"]."'
		WHERE id = ".$_REQUEST["prevId"];

		$result = mysqli_query($conn, $sql);
		if($result){
			echo "ok";
		}
		else{
			echo "fail";
		}
    }
}
else if($pcmd == "yeyak_delete"){
    if($conn){
		$sql = "UPDATE football_yeyak SET
			del_yn = 'Y'
		WHERE id = ".$_REQUEST["prevId"];

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