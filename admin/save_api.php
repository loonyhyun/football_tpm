<?php

$host = 'loonyhyun.cafe24.com';
//$host = 'localhost';
$db   = 'loonyhyun';
$user = 'loonyhyun';
$pass = 'bodigad0';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

$pdo = new PDO($dsn, $user, $pass, $options);
  
$pid = $_REQUEST["id"];
$pcmd = $_REQUEST["cmd"];

if($pcmd == "team"){
    echo "fail team";
}
else if($pcmd == "games"){
	$sql = "SELECT count(1) cnt FROM api_games
	WHERE game_id = '".$_REQUEST["game_id"]."'
	";

	$stmt = $pdo->prepare($sql);
	$stmt->execute();

	$result = $stmt->fetch();
	if($result['cnt'] === 0){
		$sql = "INSERT INTO api_games
		(game_id, game_value)
		values (
		'".$_REQUEST["game_id"]."',
		'".$_REQUEST["game_value"]."'
		)";

		$stmt = $pdo->prepare($sql);
		$stmt->execute();

		$result = $stmt->execute();
		if($result){
			echo "ok";
		}
		else{
			echo "fail";
		}
	}
	else{
		$sql = "UPDATE api_games
		SET
			game_value = '".$_REQUEST["game_value"]."'
		WHERE game_id = '".$_REQUEST["game_id"]."'
		";

		$stmt = $pdo->prepare($sql);
		$result = $stmt->execute();
		if($result){
			echo "update_ok";
		}
		else{
			echo "update_fail";
		}
	}
}
else if($pcmd == "attends"){
	$sql = "SELECT count(1) cnt FROM api_attendances
	WHERE game_id = '".$_REQUEST["game_id"]."'
	";

	$stmt = $pdo->prepare($sql);
	$stmt->execute();

	$result = $stmt->fetch();
	if($result['cnt'] === 0){
		$sql = "INSERT INTO api_attendances
		(game_id, attendances_value)
		values (
		'".$_REQUEST["game_id"]."',
		'".$_REQUEST["attend_value"]."'
		)";

		$stmt = $pdo->prepare($sql);
		$result = $stmt->execute();
		if($result){
			echo "ok";
		}
		else{
			echo "fail";
		}
	}
	else{
		$sql = "UPDATE api_attendances
		SET
		    attendances_value = '".$_REQUEST["attend_value"]."'
		WHERE game_id = '".$_REQUEST["game_id"]."'
		";

		$stmt = $pdo->prepare($sql);
		$result = $stmt->execute();
		echo ($result);
		if($result){
			echo "update_ok";
		}
		else{
			echo "update_fail";
		}
	}
}
else if($pcmd == "quarter_events"){
	$sql = "SELECT count(1) cnt FROM api_quarter_events
	WHERE game_id = '".$_REQUEST["game_id"]."'
	";

	$stmt = $pdo->prepare($sql);
	$stmt->execute();

	$result = $stmt->fetch();
	if($result['cnt'] === 0){
		$sql = "INSERT INTO api_quarter_events
		(game_id, quarter_events_value)
		values (
		'".$_REQUEST["game_id"]."',
		'".$_REQUEST["quarter_events_value"]."'
		)";

		$stmt = $pdo->prepare($sql);
		$result = $stmt->execute();
		if($result){
			echo "ok";
		}
		else{
			echo "fail";
		}
	}
	else{
		$sql = "UPDATE api_quarter_events
		SET
		    quarter_events_value = '".$_REQUEST["quarter_events_value"]."'
		WHERE game_id = '".$_REQUEST["game_id"]."'
		";

		$stmt = $pdo->prepare($sql);
		$result = $stmt->execute();
		if($result){
			echo "update_ok";
		}
		else{
			echo "update_fail";
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