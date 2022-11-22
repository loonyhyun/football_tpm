<?php
session_start();

include '../setting.php';

$POST_DATA = json_decode(file_get_contents('php://input'), true);
  
$pid = !empty($_REQUEST["id"]) ? $_REQUEST["id"] : $POST_DATA["id"];
$pcmd = !empty($_REQUEST["cmd"]) ? $_REQUEST["cmd"] : $POST_DATA["cmd"];

$notinplayer1 = " and id not in ('79','80','81','82','87','88') ";
$notinplayer = " and player_id not in ('79','80','81','82','87','88') ";

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
else if($pcmd == "match_info"){
    if($conn){
        $sql = "SELECT * FROM (
            SELECT p.*
                , (`m`.`team_a` like convert(concat('%', player_label, '%') USING utf8)) AS `team_a_yn`
                , (`m`.`team_b` like convert(concat('%', player_label, '%') USING utf8)) AS `team_b_yn`
                , m.match_date, m.quarters, m.ground_id
            FROM 
            ( select id player_id, CONCAT('$', id, '$') player_label, player_name
              from football_player
            ) p, football_match_before_info m
            WHERE m.team_id = ".$pid."
        ) t
        WHERE team_a_yn + team_b_yn > 0
        ";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'player_id'=>$row['player_id']
                ,'player_name'=>$row['player_name']
                ,'team_a_yn'=>$row['team_a_yn']
                ,'team_b_yn'=>$row['team_b_yn']
                ,'match_date'=>$row['match_date']
                ,'quarters'=>$row['quarters'] 
                ,'ground_id'=>$row['ground_id'] 
                )
            );
        }
    }
}
else if($pcmd == "match_goal"){
    if($conn){
        $sql = "SELECT player_id, player_label, player_name, m_quarter
        , if(goal_a_idx > 0, substring_index(substr(goal_a, goal_a_idx + length(player_label)),'$',1), 0) goal_a
        , if(goal_b_idx > 0, substring_index(substr(goal_b, goal_b_idx + length(player_label)),'$',1), 0) goal_b
        , if(asst_a_idx > 0, substring_index(substr(asst_a, asst_a_idx + length(player_label)),'$',1), 0) asst_a
        , if(asst_b_idx > 0, substring_index(substr(asst_b, asst_b_idx + length(player_label)),'$',1), 0) asst_b
        FROM (
            SELECT p.*
                , (goal_a like convert(concat('%', player_label, '%') USING utf8)) AS goal_a_yn
                , (goal_b like convert(concat('%', player_label, '%') USING utf8)) AS goal_b_yn
                , (asst_a like convert(concat('%', player_label, '%') USING utf8)) AS asst_a_yn
                , (asst_b like convert(concat('%', player_label, '%') USING utf8)) AS asst_b_yn
                , locate(convert(player_label using utf8),goal_a) AS goal_a_idx
                , locate(convert(player_label using utf8),goal_b) AS goal_b_idx
                , locate(convert(player_label using utf8),asst_a) AS asst_a_idx
                , locate(convert(player_label using utf8),asst_b) AS asst_b_idx
                , m.goal_a, m.goal_b, m.asst_a, m.asst_b, m_quarter
            FROM 
            ( select id player_id, CONCAT('$', id, '|') player_label, player_name
              from football_player
            ) p, football_match_before_goal m
        ) t
        WHERE goal_a_yn + goal_b_yn + asst_a_yn + asst_b_yn > 0";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'player_id'=>$row['player_id']
                ,'player_name'=>$row['player_name']
                ,'m_quarter'=>$row['m_quarter']
                ,'goal_a'=>$row['goal_a']
                ,'goal_b'=>$row['goal_b']
                ,'asst_a'=>$row['asst_a']
                ,'asst_b'=>$row['asst_b']
                )
            );
        }
    }
}
else if($pcmd == "yeyak"){
    if($conn){
        $sql = "SELECT *
        FROM football_yeyak
        WHERE del_yn = 'N' AND team_id = ".$pid ;
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'yeyak_id'=>$row['id']
                ,'txt'=>$row['txt']
                ,'start'=>$row['txtstart']
                ,'desc'=>$row['txtdesc']
                ,'color'=>$row['color']
                )
            );
        }
    }
}
else{
    
}
echo json_encode($array);

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