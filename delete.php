<?php

header("Progma:no-cache");
header("Content-Type:text/html;charset=utf-8");

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'loonyhyun' );

/** MySQL database username */
define( 'DB_USER', 'loonyhyun' );

/** MySQL database password */
define( 'DB_PASSWORD', 'bodigad0' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

$conn = mysqli_connect(
  //'loonyhyun.cafe24.com',
  'localhost',
  'loonyhyun',
  'bodigad0',
  'loonyhyun');
  
$pid = $_REQUEST["id"];
$pcmd = $_REQUEST["cmd"];

if($pcmd == "team"){
    if($conn){
        echo "fail team";
    }
}
else if($pcmd == "match"){
    if($conn){
        $pmatchid = $_REQUEST["match_id"];
        $psecret = $_REQUEST["match_secret"];
        
        $sql1 = "SELECT count(1) cnt
                FROM football_team
                WHERE id = '".$pid."' and team_admin = '".$psecret."'";
        $result1 = mysqli_query($conn, $sql1);
        $row1 = mysqli_fetch_array($result1);
        $chk_cnt = $row1["cnt"];
        
        if($chk_cnt > 0){
            $sql = "DELETE FROM football_match where id = ".$pmatchid." and team_id = ".$pid;
            
            $result = mysqli_query($conn, $sql);
            if($result){
                echo "ok";
            }
            else{
                echo "fail";
            }
        }
        else {
            echo "fail secret";
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