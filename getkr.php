<?php

header("Progma:no-cache");
header("Content-Type:text/html;charset=utf8");

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
    'loonyhyun.cafe24.com',
    //'localhost',
  'loonyhyun',
  'bodigad0',
  'loonyhyun');

  
$pid = $_REQUEST["id"];
$pcmd = $_REQUEST["cmd"];

$array = array();
if($pcmd == "team"){
    if($conn){
        $sql = "SELECT team_name, (SELECT COUNT(1) FROM football_match WHERE team_id = t.id) total_cnt
                FROM football_team t WHERE id = '".$pid."'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        array_push($array, array('team_name'=>$row['team_name'], 'total_cnt'=>$row['total_cnt']));
    }
}
else if($pcmd == "player"){
    if($conn){
        $sql = "SELECT * FROM football_player where team_id = '".$pid."' and del_yn = 'N' order by player_name";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('player_id'=>$row['id'],'player_name'=>$row['player_name']));
        }
    }
}
else if($pcmd == "player_one"){
    if($conn){
        $playerId = $_REQUEST["playerId"];
        $sql = "SELECT * FROM football_player where team_id = '".$pid."' and id = '".$playerId."'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        array_push($array, array('player_id'=>$row['id'], 'player_name'=>$row['player_name'], 'back_no'=>$row['back_no'], 'desc'=>$row['input']));
    }
}
else if($pcmd == "tpm_view"){
    if($conn){
        $sql = "SELECT p.*, m.*
FROM
(SELECT player_id, max(player_name) player_name
    , SUM(play_yn) play_cnt
    , SUM(win_yn) win_cnt, SUM(goal_cnt) goal_cnt, SUM(asst_cnt) asst_cnt
FROM football_tpm_view
WHERE team_id = '".$pid."'
GROUP BY player_id) p,
(SELECT COUNT(1) match_total_cnt FROM football_match WHERE team_id = '".$pid."') m
ORDER BY player_name";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array,
                array('player_id'=>$row['player_id'],'player_name'=>$row['player_name']
                    , 'play_cnt'=>$row['play_cnt']
                    , 'win_cnt'=>$row['win_cnt']
                    , 'goal_cnt'=>$row['goal_cnt']
                    , 'asst_cnt'=>$row['asst_cnt']
                    , 'match_total_cnt'=>$row['match_total_cnt']
                )
                );
        }
    }
}
else if($pcmd == "tpm_view_search"){
    if($conn){
        $pst = $_REQUEST["st"];
        $ped = $_REQUEST["ed"];
        
        $sql = "SELECT p.*, m.*
FROM
(SELECT player_id, max(player_name) player_name
    , SUM(play_yn) play_cnt
    , SUM(win_yn) win_cnt, SUM(goal_cnt) goal_cnt, SUM(asst_cnt) asst_cnt
FROM football_tpm_view
WHERE team_id = '".$pid."' and match_date between '".$pst."' and '".$ped."'
GROUP BY player_id) p,
(SELECT COUNT(1) match_total_cnt FROM football_match WHERE team_id = '".$pid."') m
ORDER BY player_name";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array,
                array('player_id'=>$row['player_id'],'player_name'=>$row['player_name']
                    , 'play_cnt'=>$row['play_cnt']
                    , 'win_cnt'=>$row['win_cnt']
                    , 'goal_cnt'=>$row['goal_cnt']
                    , 'asst_cnt'=>$row['asst_cnt']
                    , 'match_total_cnt'=>$row['match_total_cnt']
                )
                );
        }
    }
}
else if($pcmd == "matchlist"){
    if($conn){
        $sql = "SELECT id, match_date
                FROM football_match
                WHERE team_id = '".$pid."' order by match_date desc";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('match_id'=>$row['id'],'match_date'=>$row['match_date']));
        }
    }
}
else if($pcmd == "match"){
    if($conn){
        $pmatchid = $_REQUEST["match_id"];
        $sql1 = "SELECT win_ab
                FROM football_match
                WHERE id = '".$pmatchid."'";
        $result1 = mysqli_query($conn, $sql1);
        $row1 = mysqli_fetch_array($result1);
        $win_ab = $row1["win_ab"];
        array_push($array, array('win_ab'=>$win_ab));
        
        $sql = "SELECT *
                FROM football_tpm_view
                WHERE match_id = '".$pmatchid."' and play_yn = 1 order by player_name";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('player_name'=>$row['player_name']
                ,'team_a_yn'=>$row['team_a_yn']
                ,'team_b_yn'=>$row['team_b_yn']
                ,'goal_cnt'=>$row['goal_cnt']
                ,'asst_cnt'=>$row['asst_cnt']
            ));
        }
    }
}
else{
    
}
echo json_encode($array, JSON_UNESCAPED_UNICODE);

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