<?php
session_start();

include 'setting.php';

$POST_DATA = json_decode(file_get_contents('php://input'), true);

$pid = 0;
if(isset($_SESSION["team_id"])){
    $pid = $_SESSION["team_id"];
}else{
    $pid = !empty($_REQUEST["id"]) ? $_REQUEST["id"] : $POST_DATA["id"];
}
$pcmd = !empty($_REQUEST["cmd"]) ? $_REQUEST["cmd"] : $POST_DATA["cmd"];

$notinplayer1 = " and id not in ('79','80','81','82','87','88','128','129','130','131','132','133','134','135','136') ";
$notinplayer = " and player_id not in ('79','80','81','82','87','88','128','129','130','131','132','133','134','135','136') ";

$array = array();
if($pcmd == "team"){
    if($conn){
        $sql = "SELECT id team_id, team_name, (SELECT COUNT(1) FROM football_match WHERE team_id = t.id) total_cnt
                    , team_img
                FROM football_team t WHERE id = '".$pid."'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        array_push($array, array('team_name'=>$row['team_name'], 'total_cnt'=>$row['total_cnt']));
        
        $_SESSION["team_id"] = $row['team_id'];
        $_SESSION["team_name"] = $row['team_name'];
        $_SESSION["team_img"] = $row['team_img'];
    }
}
else if($pcmd == "play_cnt"){
    if($conn){
        $sql = "SELECT COUNT(1) total_cnt FROM football_match WHERE team_id = '".$pid."'";
        
        if( ! empty($_REQUEST["st"]) && ! empty($_REQUEST["ed"]) ){
            $pst = $_REQUEST["st"];
            $ped = $_REQUEST["ed"];
            $sql = $sql." AND match_date BETWEEN '".$pst."' AND '".$ped."' ";
        }
        else if( ! empty($_REQUEST["st"]) ){
            $pst = $_REQUEST["st"];
            $sql = $sql." AND match_date >= '".$pst."' ";
        }
        else if( ! empty($_REQUEST["ed"]) ){
            $ped = $_REQUEST["ed"];
            $sql = $sql." AND match_date <= '".$ped."' ";
        }

        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        array_push($array, array('total_cnt'=>$row['total_cnt']));
    }
}
else if($pcmd == "player"){
    if($conn){
        $sql = "SELECT * FROM football_player where team_id = '".$pid."' 
        and del_yn = 'N'
        order by player_name";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('player_id'=>$row['id'],'player_name'=>$row['player_name']));
        }
    }
}
else if($pcmd == "player_chul"){
    if($conn){
        $sql = "SELECT * FROM football_player where team_id = '".$pid."' 
        and del_yn = 'N'
        and chul_yn = 'Y'
        order by player_name";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('player_id'=>$row['id'],'player_name'=>$row['player_name']));
        }
    }
}
else if($pcmd == "chul"){
    if($conn){
        $sql = "SELECT * FROM football_chul where 1=1
        AND match_date = '".$_REQUEST["md"]."'
        ";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'team_a'=>$row['team_a'],'team_b'=>$row['team_b']
                ,'yong_a'=>$row['yong_a'],'yong_b'=>$row['yong_b']
                ,'aq1'=>$row['aq1'],'aq2'=>$row['aq2'],'aq3'=>$row['aq3'],'aq4'=>$row['aq4'],'aq5'=>$row['aq5']
                ,'bq1'=>$row['bq1'],'bq2'=>$row['bq2'],'bq3'=>$row['bq3'],'bq4'=>$row['bq4'],'bq5'=>$row['bq5']
            ));
        }
    }
}
else if($pcmd == "player_matches"){
    if($conn){
        $playerId = $_REQUEST["playerId"];
        $sql = "SELECT * FROM football_tpm_view
        where team_id = '".$pid."' 
        and player_id = '".$playerId."' and play_yn = 1
        ".$notinplayer."
        order by match_date";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('match_date'=>$row['match_date']
                ,'team_a_yn'=>$row['team_a_yn'],'team_b_yn'=>$row['team_b_yn']
                ,'play_yn'=>$row['play_yn'],'win_yn'=>$row['win_yn']
                ,'goal_cnt'=>$row['goal_cnt'],'asst_cnt'=>$row['asst_cnt']));
        }
    }
}
else if($pcmd == "player_sum"){
    if($conn){
        $playerId = $_REQUEST["playerId"];
        $sql = "SELECT 
          SUM(goal_cnt) goal_cnt
        , SUM(asst_cnt) asst_cnt
        , SUM(play_yn) play_cnt
        , SUM(case win_yn when 1 then 1 ELSE 0 end) win_cnt
        , SUM(case win_yn when 0.5 then 1 ELSE 0 end) tie_cnt
        FROM football_tpm_view
        where team_id = '".$pid."' 
        and player_id = '".$playerId."'";
        
        if( ! empty($_REQUEST["st"]) && ! empty($_REQUEST["ed"]) ){
            $pst = $_REQUEST["st"];
            $ped = $_REQUEST["ed"];
            $sql = $sql." AND match_date BETWEEN '".$pst."' AND '".$ped."' ";
        }
        else if( ! empty($_REQUEST["st"]) ){
            $pst = $_REQUEST["st"];
            $sql = $sql." AND match_date >= '".$pst."' ";
        }
        else if( ! empty($_REQUEST["ed"]) ){
            $ped = $_REQUEST["ed"];
            $sql = $sql." AND match_date <= '".$ped."' ";
        }
        
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'goal_cnt'=>$row['goal_cnt']
                ,'asst_cnt'=>$row['asst_cnt']
                ,'play_cnt'=>$row['play_cnt']
                ,'win_cnt'=>$row['win_cnt']
                ,'tie_cnt'=>$row['tie_cnt']
            ));
        }
    }
}
else if($pcmd == "player_one"){
    if($conn){
        $playerId = $_REQUEST["playerId"];
        $sql = "SELECT * FROM football_player where team_id = '".$pid."' and id = '".$playerId."'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        array_push($array, array('player_id'=>$row['id']
        , 'player_name'=>$row['player_name']
        , 'back_no'=>$row['back_no']
        , 'position'=>$row['position']
        , 'desc'=>$row['input']));
    }
}
else if($pcmd == "players"){
    if($conn){
        $sql = "SELECT * FROM football_player where team_id = '".$pid."' 
        and del_yn = 'N'
        ".$notinplayer1."
        order by player_name";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('player_id'=>$row['id']
            , 'player_name'=>$row['player_name']
            , 'back_no'=>$row['back_no']
            , 'position'=>$row['position']
            , 'desc'=>$row['input']));
        }
    }
}
else if($pcmd == "tpm_view"){
    if($conn){
        $sql = "SELECT p.*, m.*
FROM
(SELECT player_id, max(player_name) player_name
    , SUM(play_yn) play_cnt
    , SUM(win_yn) win_cnt, SUM(goal_cnt) goal_cnt, SUM(asst_cnt) asst_cnt
    , SUM(case when win_yn = 1.0 then 3 when win_yn = 0.5 then 1 else 0 end) pts
FROM football_tpm_view
WHERE team_id = '".$pid."'
".$notinplayer."
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
                    , 'pts'=>$row['pts']
                )
                );
        }
    }
}
else if($pcmd == "tpm_view_search"){
    if($conn){
        $sql = "SELECT p.*, m.*
FROM
(SELECT player_id, max(player_name) player_name
    , SUM(play_yn) play_cnt
    , SUM(win_yn) win_cnt, SUM(goal_cnt) goal_cnt, SUM(asst_cnt) asst_cnt
    , SUM(case when win_yn = 1.0 then 3 when win_yn = 0.5 then 1 else 0 end) pts
    , SUM(case when t.team_a_yn = 1 then s_cnt_a
        when t.team_b_yn = 1 then s_cnt_b
        else 0 end) s_cnt
FROM football_tpm_view t LEFT OUTER JOIN (
    SELECT match_id
    	, sum(case when team_ab = 'a' then 1 ELSE 0 END) s_cnt_a
    	, sum(case when team_ab = 'b' then 1 ELSE 0 END) s_cnt_b
    FROM football_match_scoreless
    GROUP BY match_id
) s
ON t.match_id = s.match_id
WHERE team_id = '".$pid."'";
    
    if( ! empty($_REQUEST["st"]) && ! empty($_REQUEST["ed"]) ){
        $pst = $_REQUEST["st"];
        $ped = $_REQUEST["ed"];
        $sql = $sql." AND match_date BETWEEN '".$pst."' AND '".$ped."' ";
    }
    else if( ! empty($_REQUEST["st"]) ){
        $pst = $_REQUEST["st"];
        $sql = $sql." AND match_date >= '".$pst."' ";
    }
    else if( ! empty($_REQUEST["ed"]) ){
        $ped = $_REQUEST["ed"];
        $sql = $sql." AND match_date <= '".$ped."' ";
    }
    
    if( ! empty($_REQUEST["ground_id"]) ){
        $pground = $_REQUEST["ground_id"];
        $sql = $sql." AND ground_id = '".$pground."' ";
    }

        $sql = $sql." ".$notinplayer."
GROUP BY player_id) p,
(SELECT COUNT(1) match_total_cnt FROM football_match WHERE team_id = '".$pid."' ";

    if( ! empty($_REQUEST["st"]) && ! empty($_REQUEST["ed"]) ){
        $pst = $_REQUEST["st"];
        $ped = $_REQUEST["ed"];
        $sql = $sql." AND match_date between '".$pst."' AND '".$ped."' ";
    }
    else if( ! empty($_REQUEST["st"]) ){
        $pst = $_REQUEST["st"];
        $sql = $sql." AND match_date >= '".$pst."' ";
    }
    else if( ! empty($_REQUEST["ed"]) ){
        $ped = $_REQUEST["ed"];
        $sql = $sql." AND match_date <= '".$ped."' ";
    }
    

        $sql = $sql."    ) m
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
                    , 'pts'=>$row['pts']
                    , 's_cnt'=>$row['s_cnt']
                )
                );
        }
    }
}
else if($pcmd == "rank"){
    if($conn){
        $ptype = $_REQUEST["type"];
        $pvalue = "play_cnt";
        if($ptype == "win"){
            $pvalue = "win_cnt";
        }
        else if($ptype == "goal"){
            $pvalue = "goal_cnt";
        }
        else if($ptype == "asst"){
            $pvalue = "asst_cnt";
        }
        else if($ptype == "play"){
            $pvalue = "play_cnt";
        }
        $sql = "SELECT *
        FROM (
            SELECT m.*, 

CASE WHEN @PREV_MONTH <> match_month THEN @SAME_CNT := 1 END V1,
CASE WHEN @PREV_MONTH <> match_month THEN @RANK := 0 END V2,
CASE WHEN @PREV_MONTH <> match_month THEN @PREV_SAME_CNT := NULL END V3,
CASE WHEN @PREV_MONTH <> match_month THEN @PREV_RANK := NULL END V4,

CASE
WHEN @PREV_MONTH = match_month THEN @PREV_MONTH
WHEN @PREV_MONTH := match_month THEN @PREV_MONTH END MMM,

CASE WHEN @PREV_RANK <> ".$pvalue." THEN @TEMP_RANK := ".$pvalue." END S1,
CASE WHEN @PREV_RANK = ".$pvalue." THEN @SAME_CNT := @SAME_CNT + 1 END T_SAME,

IFNULL(
CASE WHEN @PREV_RANK = ".$pvalue." THEN @RANK 
WHEN @PREV_RANK:= ".$pvalue." THEN @RANK := @RANK + @SAME_CNT END 
, @RANK := @RANK + @SAME_CNT
) T_RANK

            FROM football_tpm_month m, 
                (SELECT 
                    @RANK := 0, @PREV_RANK := NULL, @TEMP_RANK = NULL,
                    @SAME_CNT := 1, @PREV_SAME_CNT:= NULL, 
                    @PREV_MONTH := '2021.01') tmp
            WHERE team_id = '".$pid."' ".$notinplayer." ";
        if( ! empty($_REQUEST["st"]) && ! empty($_REQUEST["ed"]) ){
            $pst = $_REQUEST["st"];
            $ped = $_REQUEST["ed"];
            $sql = $sql." AND match_month between '".$pst."' and '".$ped."' ";
        }
        else if( ! empty($_REQUEST["st"]) ){
            $pst = $_REQUEST["st"];
            $sql = $sql." AND match_month >= '".$pst."' ";
        }
        else if( ! empty($_REQUEST["ed"]) ){
            $ped = $_REQUEST["ed"];
            $sql = $sql." AND match_month <= '".$ped."' ";
        }

        $sql = $sql." ORDER BY match_month, ".$pvalue." desc, player_name 
        ) t ";
        
        if( ! empty($_REQUEST["players"]) ){
            $pps = $_REQUEST["players"];
            $sql = $sql." WHERE player_id in (".$pps.")";
        }
        $sql = $sql." ORDER BY match_month ";

        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array,
                array('player_id'=>$row['player_id'],'player_name'=>$row['player_name']
                    , 'play_cnt'=>$row['play_cnt']
                    , 'win_cnt'=>$row['win_cnt']
                    , 'goal_cnt'=>$row['goal_cnt']
                    , 'asst_cnt'=>$row['asst_cnt']
                    , 'month'=>$row["match_month"]
                    , 'rank'=>$row["T_RANK"]
                )
                );
        }
    }
}
else if($pcmd == "tpm_view_month"){
    if($conn){
        $ptype = $_REQUEST["type"];
        $sql = "SELECT f.*
        , win_cnt / play_cnt win_per
        , goal_cnt / play_cnt goal_per
        , asst_cnt / play_cnt asst_per
        FROM football_tpm_month f
        WHERE team_id = '".$pid."' ".$notinplayer." ";
        if( ! empty($_REQUEST["st"]) && ! empty($_REQUEST["ed"]) ){
            $pst = $_REQUEST["st"];
            $ped = $_REQUEST["ed"];
            $sql = $sql." AND match_month between '".$pst."' and '".$ped."' ";
        }
        else if( ! empty($_REQUEST["st"]) ){
            $pst = $_REQUEST["st"];
            $sql = $sql." AND match_month >= '".$pst."' ";
        }
        else if( ! empty($_REQUEST["ed"]) ){
            $ped = $_REQUEST["ed"];
            $sql = $sql." AND match_month <= '".$ped."' ";
        }
        if( ! empty($_REQUEST["players"]) ){
            $pps = $_REQUEST["players"];
            $sql = $sql." AND player_id in (".$pps.")";
        }
        $sql = $sql." ORDER BY match_month, ";
        if($ptype == "win"){
            $sql = $sql." win_per desc, win_cnt desc ";
        }
        else if($ptype == "goal"){
            $sql = $sql." goal_cnt desc, goal_per desc ";
        }
        else if($ptype == "asst"){
            $sql = $sql." asst_cnt desc, asst_per desc ";
        }
        else if($ptype == "play"){
            $sql = $sql." play_cnt desc ";
        }

        #echo $_REQUEST["players"]."\n";
        #echo $sql;

        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array,
                array('player_id'=>$row['player_id'],'player_name'=>$row['player_name']
                    , 'play_cnt'=>$row['play_cnt']
                    , 'win_cnt'=>$row['win_cnt']
                    , 'goal_cnt'=>$row['goal_cnt']
                    , 'asst_cnt'=>$row['asst_cnt']
                    , 'month'=>$row["match_month"]
                    , 'win_per'=>$row["win_per"]
                    , 'goal_per'=>$row["goal_per"]
                    , 'asst_per'=>$row["asst_per"]
                )
                );
        }
    }
}
else if($pcmd == "tpm_view_month_rank"){
    if($conn){
        $ptype = $_REQUEST["type"];
        $sql = "SELECT match_month, COUNT(1) cnt ";
        if($ptype == "win"){
            $sql = $sql." , win_cnt / play_cnt win_per ";
        }
        else if($ptype == "goal"){
            $sql = $sql." , goal_cnt / play_cnt goal_per ";
        }
        else if($ptype == "asst"){
            $sql = $sql." , asst_cnt / play_cnt asst_per ";
        }
        else if($ptype == "play"){
            $sql = $sql." , play_cnt ";
        }
        $sql = $sql." FROM football_tpm_month f WHERE team_id = '".$pid."' ".$notinplayer." ";
        if( ! empty($_REQUEST["st"]) && ! empty($_REQUEST["ed"]) ){
            $pst = $_REQUEST["st"];
            $ped = $_REQUEST["ed"];
            $sql = $sql." AND match_month between '".$pst."' and '".$ped."' ";
        }
        else if( ! empty($_REQUEST["st"]) ){
            $pst = $_REQUEST["st"];
            $sql = $sql." AND match_month >= '".$pst."' ";
        }
        else if( ! empty($_REQUEST["ed"]) ){
            $ped = $_REQUEST["ed"];
            $sql = $sql." AND match_month <= '".$ped."' ";
        }
        if( ! empty($_REQUEST["players"]) ){
            $pps = $_REQUEST["players"];
            $sql = $sql." AND player_id in (".$pps.")";
        }
        $sql = $sql." GROUP BY match_month, ";
        if($ptype == "win"){
            $sql = $sql." win_cnt, win_per ";
        }
        else if($ptype == "goal"){
            $sql = $sql." goal_cnt, goal_per ";
        }
        else if($ptype == "asst"){
            $sql = $sql." asst_cnt, asst_per ";
        }
        else if($ptype == "play"){
            $sql = $sql." play_cnt ";
        }
        $sql = $sql." ORDER BY match_month, ";
        if($ptype == "win"){
            $sql = $sql." win_per desc, win_cnt desc ";
        }
        else if($ptype == "goal"){
            $sql = $sql." goal_cnt desc, goal_per desc ";
        }
        else if($ptype == "asst"){
            $sql = $sql." asst_cnt desc, asst_per desc ";
        }
        else if($ptype == "play"){
            $sql = $sql." play_cnt desc ";
        }

        #echo $sql;

        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            if($ptype == "win"){
                array_push($array,
                array('month'=>$row["match_month"]
                    , 'cnt'=>$row["cnt"]
                    , 'win_per'=>$row["win_per"]
                )
                );
            }
            else if($ptype == "goal"){
                array_push($array,
                array('month'=>$row["match_month"]
                    , 'cnt'=>$row["cnt"]
                    , 'goal_per'=>$row["goal_per"]
                )
                );
            }
            else if($ptype == "asst"){
                array_push($array,
                array('month'=>$row["match_month"]
                    , 'cnt'=>$row["cnt"]
                    , 'asst_per'=>$row["asst_per"]
                )
                );
            }
            else if($ptype == "play"){
                array_push($array,
                array('month'=>$row["match_month"]
                    , 'cnt'=>$row["cnt"]
                    , 'play_cnt'=>$row["play_cnt"]
                )
                );
            }
        }
    }
}
else if($pcmd == "matchlist"){
    if($conn){
        $sql = "SELECT id, match_date, win_ab, match_yn, other_team
                FROM football_match
                WHERE team_id = '".$pid."' order by match_date desc";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('match_id'=>$row['id'],'match_date'=>$row['match_date']
            ,'win_ab'=>$row['win_ab'],'match_yn'=>$row['match_yn']
            ,'other_team'=>$row['other_team']));
        }
    }
}
else if($pcmd == "match"){
    if($conn){
        $pmatchid = $_REQUEST["match_id"];
        $sql1 = "SELECT win_ab
                    , (select name from football_ground where id = ground_id) g_name
                    , quarters
                    , match_yn
                    , IFNULL(other_team, '-') other_team
                    , IFNULL(own_goal_a, 0) own_goal_a
                    , IFNULL(own_goal_b, 0) own_goal_b
                    , (SELECT GROUP_CONCAT(m_quarter SEPARATOR  ';') FROM football_match_scoreless WHERE match_id = m.id AND team_ab = 'a') a_q
                    , (SELECT GROUP_CONCAT(m_quarter SEPARATOR  ';') FROM football_match_scoreless WHERE match_id = m.id AND team_ab = 'b') b_q
                FROM football_match m
                WHERE id = '".$pmatchid."'";
        $result1 = mysqli_query($conn, $sql1);
        $row1 = mysqli_fetch_array($result1);
        $win_ab = $row1["win_ab"];
        $g_name = $row1["g_name"];
        array_push($array, array('win_ab'=>$win_ab, 'g_name'=>$g_name
        ,'quarters'=>$row1["quarters"]
        ,'a_q'=>$row1["a_q"]
        ,'b_q'=>$row1["b_q"]
        ,'match_yn'=>$row1["match_yn"]
        ,'other_team'=>$row1["other_team"]
        ,'own_goal_a'=>$row1["own_goal_a"]
        ,'own_goal_b'=>$row1["own_goal_b"]
        ));
        
        $sql = "SELECT *
                FROM football_tpm_view2
                WHERE match_id = '".$pmatchid."' and play_yn = 1 order by player_name";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('player_name'=>$row['player_name']
                ,'player_id'=>$row['player_id']
                ,'team_a_yn'=>$row['team_a_yn']
                ,'team_b_yn'=>$row['team_b_yn']
                ,'goal_cnt'=>$row['goal_cnt']
                ,'asst_cnt'=>$row['asst_cnt']
                ,'recommand_pos'=>$row['recommand_pos']
            ));
        }
    }
}
else if($pcmd == "match_nextid"){
    if($conn){
        $sql = "SELECT auto_increment
        FROM information_schema.tables
        WHERE TABLE_NAME = 'football_match'";
        
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('match_id'=>$row[0]));
        }
    }
}
else if($pcmd == "match_scoreless"){
    if($conn){
        $sql = "SELECT team_ab, m_quarter
        FROM football_match_scoreless
        WHERE match_id = ".$_REQUEST["match_id"]."";
        
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'team_ab'=>$row['team_ab']
                ,'quarter'=>$row['m_quarter']
            ));
        }
    }
}
else if($pcmd == "match_reply"){
    if($conn){
        $pmatchid = $_REQUEST["match_id"];
        $sql1 = "SELECT reply, regdate
                FROM football_match_reply
                WHERE match_id = '".$pmatchid."'
                ORDER BY regdate desc";
        $result = mysqli_query($conn, $sql1);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('reply'=>$row['reply']
                ,'regdate'=>$row['regdate']
            ));
        }
    }
}
else if($pcmd == "ground"){
    if($conn){
        $sql1 = "SELECT *
                FROM football_ground
                ORDER BY name";
        $result = mysqli_query($conn, $sql1);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('ground_id'=>$row['id']
                ,'name'=>$row['name']
                ,'width'=>$row['width']
                ,'height'=>$row['height']
                ,'ground_type'=>$row['ground_type']
                ,'recommend_shoe'=>$row['recommend_shoe']
                ,'map_url'=>$row['map_url']
                ,'latitude'=>$row['latitude']
                ,'longitude'=>$row['longitude']
            ));
        }
    }
}
else if($pcmd == "best11stat"){
    if($conn){
        $sql = "SELECT *
        FROM (
        SELECT reg_formation, COUNT(1) CNT
        FROM football_best
        WHERE team_id = ".$pid."
        GROUP BY reg_formation
        ) T
        ORDER BY CNT DESC";
        
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $regFormation = $row['reg_formation'];
        
        $sql = "SELECT count(1) total_cnt
        FROM football_best
        WHERE team_id = ".$pid."";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $totalCnt = $row['total_cnt'];
        array_push($array, array('reg_formation'=>$regFormation, 'total_cnt'=>$totalCnt));

        $sql1 = "SELECT
            id player_id
            , player_name
            , (SELECT COUNT(1) FROM football_best t
            WHERE player_0 = p.id 
                OR player_1 = p.id
                OR player_2 = p.id
                OR player_3 = p.id
                OR player_4 = p.id
                OR player_5 = p.id
                OR player_6 = p.id
                OR player_7 = p.id
                OR player_8 = p.id
                OR player_9 = p.id
                OR player_10 = p.id
            ) cnt
        FROM football_player p
        WHERE team_id = ".$pid." 
        AND id IN (
            SELECT player_0 FROM football_best UNION all
            SELECT player_1 FROM football_best UNION all
            SELECT player_2 FROM football_best UNION all
            SELECT player_3 FROM football_best UNION all
            SELECT player_4 FROM football_best UNION all
            SELECT player_5 FROM football_best UNION all
            SELECT player_6 FROM football_best UNION all
            SELECT player_7 FROM football_best UNION all
            SELECT player_8 FROM football_best UNION all
            SELECT player_9 FROM football_best UNION all
            SELECT player_10 FROM football_best
        )
        ORDER BY cnt desc";
        $result = mysqli_query($conn, $sql1);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('player_name'=>$row['player_name']
                ,'player_id'=>$row['player_id']
                ,'cnt'=>$row['cnt']
            ));
        }
    }
}
else if($pcmd == "best_formation_view"){
    if($conn){
        $pfid = $_REQUEST["fid"];
        $sql = "SELECT *
        FROM football_best_formation
        WHERE id = ".$pfid."";
        
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        array_push($array, array('reg_formation'=>$row['reg_formation']
        , 'player_att'=>$row['player_att']
        , 'player_mid'=>$row['player_mid']
        , 'player_def'=>$row['player_def']
        , 'player_keep'=>$row['player_keep']
        , 'reg_name'=>$row['reg_name']
        ));
    }
}
else if($pcmd == "best_formation_view_list"){
    if($conn){
        $sql = "SELECT id, reg_name
        FROM football_best_formation
        WHERE team_id = ".$pid."
        ORDER BY id";
        
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('fid'=>$row['id']
            , 'reg_name'=>$row['reg_name']
            ));
        }
    }
}
else if($pcmd == "attend"){
    if($conn){
        $sql = "SELECT a.team_type, a.player_id, b.player_name, a.reg_date, b.recommand_pos
        FROM football_attend a, football_player b 
        where a.player_id = b.id
        order by a.reg_date";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, 
                array('player_id'=>$row['player_id'],'player_name'=>$row['player_name']
                ,'team_type'=>$row['team_type']
                ,'reg_date'=>$row['reg_date']
                ,'recommand_pos'=>$row['recommand_pos']
                )
            );
        }
    }
}
else if($pcmd == "attend_quater"){
    if($conn){
        $sql = "SELECT * FROM football_attend_quater";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'team_a'=>$row['team_a']
                ,'team_b'=>$row['team_b']
                ,'quarters'=>$row['quarters']
                ,'def_a'=>$row['def_a']
                ,'def_b'=>$row['def_b']
                )
            );
        }
    }
}
else if($pcmd == "max_match_id"){
    if($conn){
        $sql = "SELECT max(id) match_id FROM football_match where team_id = ".$pid."";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        array_push($array, array(
            'match_id'=>$row['match_id']
            )
        );
    }
}
else if($pcmd == "match_together"){
    if($conn){
        $pids = $_REQUEST["players"];
        $sql = "
        SELECT * FROM (
            SELECT match_date, SUM(play_yn) play_yn,
                SUM(case when win_yn = 1 then 1 ELSE 0 end) win_yn,
                SUM(case when win_yn = 0 then 1 ELSE 0 end) lose_yn,
                SUM(team_a_yn) team_a,
                SUM(team_b_yn) team_b,
                SUM(goal_cnt) goal_cnt,
                SUM(asst_cnt) asst_cnt
            FROM football_tpm_view
            WHERE player_id in (".$pids.")
                AND play_yn = 1
        ";
        if( ! empty($_REQUEST["from"]) ){
            $pfrom = $_REQUEST["from"];
            $sql = $sql." AND match_date >= '".$pfrom."' ";
        }
        if( ! empty($_REQUEST["to"]) ){
            $pto = $_REQUEST["to"];
            $sql = $sql." AND match_date <= '".$pto."' ";
        }
        if( ! empty($_REQUEST["groundId"]) ){
            $pGround = $_REQUEST["groundId"];
            $sql = $sql." AND ground_id = '".$pGround."' ";
        }
        $sql = $sql."    GROUP BY match_date
        ) t
        ORDER BY match_date desc
        ";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'match_date'=>$row['match_date']
                ,'play_yn'=>$row['play_yn']
                ,'win_yn'=>$row['win_yn']
                ,'lose_yn'=>$row['lose_yn']
                ,'team_a'=>$row['team_a']
                ,'team_b'=>$row['team_b']
                ,'goal_cnt'=>$row['goal_cnt']
                ,'asst_cnt'=>$row['asst_cnt']
                )
            );
        }
    }
}
else if($pcmd == "ground_attend"){
    if($conn){
        $sql1 = "SELECT * FROM football_ground g1, football_attend_ground g2
        WHERE g1.id = g2.ground_id";
        $result = mysqli_query($conn, $sql1);
        $row = mysqli_fetch_array($result);
        array_push($array, array('ground_id'=>$row['id']
            ,'name'=>$row['name']
            ,'width'=>$row['width']
            ,'height'=>$row['height']
            ,'ground_type'=>$row['ground_type']
            ,'recommend_shoe'=>$row['recommend_shoe']
            ,'map_url'=>$row['map_url']
            ,'latitude'=>$row['latitude']
            ,'longitude'=>$row['longitude']
            ,'max_distance'=>$row['max_distance']
        ));
    }
}
else if($pcmd == "ground_attend_player"){
    if($conn){
        $sql1 = "SELECT * FROM football_attend_gps
        ORDER BY reg_date";
        $result = mysqli_query($conn, $sql1);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'player_name'=>$row['player_name']
                ,'team_type'=>$row['team_type']
                ,'reg_date'=>$row['reg_date']
                ,'gps_distance'=>$row['gps_distance']
                )
            );
        }
    }
}
else if($pcmd == "team_sta"){
    if($conn){
        $sql1 = "SELECT tt.player_name
        , goal_score
        , ROUND((goal_score / plays), 2) goal_per_game
        , lost_score
        , ROUND((lost_score / plays), 2) lost_per_game
        , goal_score - lost_score as goallost
        , ROUND(goal_score / plays - lost_score / plays, 2) goallost_per_game
        , plays
        , win
        , draw
        , lose
        , win * 3 + draw as pts
        , goals
        , ROUND(goals/plays, 2) AS goal_per_play
        , assts
        , ROUND(assts/plays, 2) AS asst_per_play
    FROM 
    (
    SELECT
        t2.player_name
        , SUM(play_yn) plays
        , SUM(goal_cnt) goals
        , SUM(asst_cnt) assts
        , SUM(case when win_yn = 1 then 1 ELSE 0 END) win
        , SUM(case when win_yn = 0.5 then 1 ELSE 0 END) draw
        , SUM(case when win_yn = 0 then 1 ELSE 0 END) lose
        , SUM(case when team_a_yn = 1 then a_goal ELSE 0 END + case when team_b_yn = 1 then b_goal ELSE 0 END) goal_score
        , SUM(case when team_a_yn = 1 then b_goal ELSE 0 END + case when team_b_yn = 1 then a_goal ELSE 0 END) lost_score
    FROM 
    (
        SELECT match_date
            , SUM(case when team_a_yn = 1 then goal_cnt ELSE 0 END) a_goal
            , SUM(case when team_b_yn = 1 then goal_cnt ELSE 0 END) b_goal
        FROM football_tpm_view
    GROUP BY match_date
    ) t1, (
        SELECT *
        FROM football_tpm_view
        WHERE team_id = ".$pid."
            AND play_yn = 1
            AND player_name NOT LIKE '_용병%'
    ) t2
    WHERE t1.match_date = t2.match_date
    AND t1.match_date like '".$_REQUEST["searchYear"].".%'
    GROUP BY player_id, player_name
    ) tt
    ORDER BY pts desc";
        $result = mysqli_query($conn, $sql1);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'player_name'=>$row['player_name']
                ,'goal_score'=>$row['goal_score']
                ,'goal_per_game'=>$row['goal_per_game']
                ,'lost_score'=>$row['lost_score']
                ,'lost_per_game'=>$row['lost_per_game']
                ,'goallost'=>$row['goallost']
                ,'goallost_per_game'=>$row['goallost_per_game']
                ,'plays'=>$row['plays']
                ,'win'=>$row['win']
                ,'draw'=>$row['draw']
                ,'lose'=>$row['lose']
                ,'pts'=>$row['pts']
                ,'goals'=>$row['goals']
                ,'goal_per_play'=>$row['goal_per_play']
                ,'assts'=>$row['assts']
                ,'asst_per_play'=>$row['asst_per_play']
                )
            );
        }
    }
}
else if($pcmd == "attend2"){
    if($conn){
        $sql = "SELECT 
            match_key, match_date
            , team_a, team_b
            , a_q1, a_q2, a_q3, a_q4, a_q5, a_q6
            , b_q1, b_q2, b_q3, b_q4, b_q5, b_q6
            , outs, hires
        FROM football_attend a
        where match_key = '".$_REQUEST["match_key"]."'
        ";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, 
                array('match_key'=>$row['match_key']
                , 'match_date'=>$row['match_date']
                , 'team_a'=>$row['team_a']
                , 'team_b'=>$row['team_b']
                , 'a_q1'=>$row['a_q1']
                , 'a_q2'=>$row['a_q2']
                , 'a_q3'=>$row['a_q3']
                , 'a_q4'=>$row['a_q4']
                , 'a_q5'=>$row['a_q5']
                , 'a_q6'=>$row['a_q6']
                , 'b_q1'=>$row['b_q1']
                , 'b_q2'=>$row['b_q2']
                , 'b_q3'=>$row['b_q3']
                , 'b_q4'=>$row['b_q4']
                , 'b_q5'=>$row['b_q5']
                , 'b_q6'=>$row['b_q6']
                , 'outs'=>$row['outs']
                , 'hires'=>$row['hires']
                )
            );
        }
    }
}
else if($pcmd == "attend2_set"){
    if($conn){
        $sql = "SELECT 
            *
        FROM football_attend_set a
        WHERE id = (SELECT MAX(id) FROM football_attend_set)
        ";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, 
                array('g_name'=>$row['g_name']
                , 'longitude'=>$row['longitude']
                , 'latitude'=>$row['latitude']
                , 'range'=>$row['g_range']
                )
            );
        }
    }
}
else if($pcmd == "tpm_view_all"){
    if($conn){
        $sql = "SELECT player_id, player_name, match_date, win_yn
        FROM football_tpm_view
        where team_id = '".$pid."'
        and play_yn = 1
        ";
        if( ! empty($_REQUEST["year"]) ){
            $sql = $sql." and match_date like '".$_REQUEST["year"]."%' ";
        }
        $sql = $sql."
        order by player_id, match_date
        ";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, 
                array('player_id'=>$row['player_id']
                , 'player_name'=>$row['player_name']
                , 'match_date'=>$row['match_date']
                , 'win_yn'=>$row['win_yn']
                )
            );
        }
    }
}
else if($pcmd == "match_duo"){
    if($conn){
        $sql = "SELECT tt.*
                , win_cnt / play_cnt dif
            FROM (
                SELECT t2_player player_name
                    , COUNT(1) play_cnt
                    , SUM(case when t1_win = 1 then 3 when t1_win = 0.5 then 1 ELSE 0 end) pts
                    , IFNULL(SUM(case when t1_win = 1 then 1 end), 0) win_cnt
                    , IFNULL(SUM(case when t1_win = 0.5 then 1 END), 0) draw_cnt
                    , IFNULL(SUM(case when t1_win = 0 then 1 END), 0) lose_cnt
                    , IFNULL(SUM(t1_goal + t2_goal), 0) goal_cnt
                    , IFNULL(SUM(t1_asst + t2_asst), 0) asst_cnt
                FROM (
                SELECT t1.player_name t1_player, t1.match_date, t2.player_name t2_player
                , t1.win_yn t1_win, t2.win_yn t2_win
                , t1.goal_cnt t1_goal, t2.goal_cnt t2_goal
                , t1.asst_cnt t1_asst, t2.asst_cnt t2_asst
                , case when t1.team_a_yn = 1 then 'a'
                    when t1.team_b_yn = 1 then 'b'
                    END t1_team
                , case when t2.team_a_yn = 1 then 'a'
                    when t2.team_b_yn = 1 then 'b'
                    END t2_team
                , case when t1.team_a_yn = t2.team_a_yn then 1
                    when t1.team_b_yn = t2.team_b_yn then 1
                    ELSE 0 END team_yn
                FROM football_tpm_view t1, football_tpm_view t2
                WHERE 1=1
                AND t1.player_id = '".$_REQUEST["player_id"]."'
                AND t1.match_date = t2.match_date
                AND t1.player_id <> t2.player_id
                AND t1.play_yn = 1
                AND t2.play_yn = 1
                AND t2.player_name NOT LIKE '_용병%'";
                
        if( ! empty($_REQUEST["from"]) ){
            $pfrom = $_REQUEST["from"];
            $sql = $sql." AND t1.match_date >= '".$pfrom."' ";
        }
        if( ! empty($_REQUEST["to"]) ){
            $pto = $_REQUEST["to"];
            $sql = $sql." AND t1.match_date <= '".$pto."' ";
        }
        $sql = $sql.") t
                WHERE team_yn = 1
                GROUP BY t2_player
            ) tt
            ORDER BY pts DESC";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, 
                array('player_name'=>$row['player_name']
                , 'play_cnt'=>$row['play_cnt']
                , 'pts'=>$row['pts']
                , 'win_cnt'=>$row['win_cnt']
                , 'draw_cnt'=>$row['draw_cnt']
                , 'lose_cnt'=>$row['lose_cnt']
                , 'goal_cnt'=>$row['goal_cnt']
                , 'asst_cnt'=>$row['asst_cnt']
                )
            );
        }
    }
}
else if($pcmd == "match_together_vs"){
    if($conn){
        $sql = "
        SELECT m.match_date, m.win_ab
            , a_cnt, b_cnt
            , t1.goal_cnt a_goal
            , t2.goal_cnt b_goal
            , t1.asst_cnt a_asst
            , t2.asst_cnt b_asst
        FROM (
            SELECT match_date
                , SUM(team_a_yn) a_cnt
                , SUM(goal_cnt) goal_cnt
                , SUM(asst_cnt) asst_cnt
            FROM football_tpm_view
            WHERE player_id IN (".$_REQUEST["players_a"].")
            AND play_yn = 1
            GROUP BY match_date
        ) t1,(
            SELECT match_date
                , SUM(team_b_yn) b_cnt
                , SUM(goal_cnt) goal_cnt
                , SUM(asst_cnt) asst_cnt
            FROM football_tpm_view
            WHERE player_id IN (".$_REQUEST["players_b"].")
            AND play_yn = 1
            GROUP BY match_date
        ) t2,
        football_match m
        WHERE 1=1
        AND a_cnt = ".$_REQUEST["cnt_a"]."
        AND b_cnt = ".$_REQUEST["cnt_b"]."
        ";
        if( ! empty($_REQUEST["from"]) ){
            $pfrom = $_REQUEST["from"];
            $sql = $sql." AND m.match_date >= '".$pfrom."' ";
        }
        if( ! empty($_REQUEST["to"]) ){
            $pto = $_REQUEST["to"];
            $sql = $sql." AND m.match_date <= '".$pto."' ";
        }
        if( ! empty($_REQUEST["groundId"]) ){
            $pGround = $_REQUEST["groundId"];
            $sql = $sql." AND m.ground_id = '".$pGround."' ";
        }
        $sql = $sql."
        AND m.match_date = t1.match_date 
        AND m.match_date = t2.match_date
        UNION ALL
        SELECT m1.match_date
            , case m1.win_ab when 'a' then 'b' when 'b' then 'a' ELSE '-' END win_ab
            , a_cnt, b_cnt
            , t22.goal_cnt a_goal
            , t11.goal_cnt b_goal
            , t22.asst_cnt a_asst
            , t11.asst_cnt b_asst
        FROM (
            SELECT match_date
                , SUM(team_a_yn) b_cnt
                , SUM(goal_cnt) goal_cnt
                , SUM(asst_cnt) asst_cnt
            FROM football_tpm_view
            WHERE player_id IN (".$_REQUEST["players_b"].")
            AND play_yn = 1
            GROUP BY match_date
        ) t11,(
            SELECT match_date
                , SUM(team_b_yn) a_cnt
                , SUM(goal_cnt) goal_cnt
                , SUM(asst_cnt) asst_cnt
            FROM football_tpm_view
            WHERE player_id IN (".$_REQUEST["players_a"].")
            AND play_yn = 1
            GROUP BY match_date
        ) t22,
        football_match m1
        WHERE 1=1
        AND a_cnt = ".$_REQUEST["cnt_a"]."
        AND b_cnt = ".$_REQUEST["cnt_b"]."
        ";
        if( ! empty($_REQUEST["from"]) ){
            $pfrom = $_REQUEST["from"];
            $sql = $sql." AND m1.match_date >= '".$pfrom."' ";
        }
        if( ! empty($_REQUEST["to"]) ){
            $pto = $_REQUEST["to"];
            $sql = $sql." AND m1.match_date <= '".$pto."' ";
        }
        if( ! empty($_REQUEST["groundId"]) ){
            $pGround = $_REQUEST["groundId"];
            $sql = $sql." AND m1.ground_id = '".$pGround."' ";
        }
        $sql = $sql."
        AND t11.match_date = t22.match_date
        AND t11.match_date = m1.match_date
        ORDER BY match_date desc
        ";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'match_date'=>$row['match_date']
                ,'win_ab'=>$row['win_ab']
                ,'a_goal'=>$row['a_goal']
                ,'b_goal'=>$row['b_goal']
                ,'a_asst'=>$row['a_asst']
                ,'b_asst'=>$row['b_asst']
                )
            );
        }
    }
}
else if($pcmd == "match_vs_total"){
    if($conn){
        $sql = "SELECT tt.*
                , win_cnt / play_cnt dif
            FROM (
                SELECT t2_player player_name
                    , COUNT(1) play_cnt
                    , SUM(case when t1_win = 1 then 3 when t1_win = 0.5 then 1 ELSE 0 end) pts
                    , IFNULL(SUM(case when t1_win = 1 then 1 end), 0) win_cnt
                    , IFNULL(SUM(case when t1_win = 0.5 then 1 END), 0) draw_cnt
                    , IFNULL(SUM(case when t1_win = 0 then 1 END), 0) lose_cnt
                    , IFNULL(SUM(t1_goal), 0) goal_cnt
                    , IFNULL(SUM(t1_asst), 0) asst_cnt
                    , IFNULL(SUM(t2_goal), 0) goal_vs_cnt
                    , IFNULL(SUM(t2_asst), 0) asst_vs_cnt
                FROM (
                SELECT t1.player_name t1_player, t1.match_date, t2.player_name t2_player
                , t1.win_yn t1_win, t2.win_yn t2_win
                , t1.goal_cnt t1_goal, t2.goal_cnt t2_goal
                , t1.asst_cnt t1_asst, t2.asst_cnt t2_asst
                , case when t1.team_a_yn = 1 AND t2.team_b_yn = 1 then 1 ELSE 0 END
					   + case when t2.team_a_yn = 1 and t1.team_b_yn = 1 then 1 ELSE 0 END AS vs_yn
                , case when t1.team_a_yn = 1 then 'a'
                    when t1.team_b_yn = 1 then 'b'
                    END t1_team
                , case when t2.team_a_yn = 1 then 'a'
                    when t2.team_b_yn = 1 then 'b'
                    END t2_team
                , case when t1.team_a_yn = t2.team_a_yn then 1
                    when t1.team_b_yn = t2.team_b_yn then 1
                    ELSE 0 END team_yn
                FROM football_tpm_view t1, football_tpm_view t2
                WHERE 1=1
                AND t1.player_id = '".$_REQUEST["player_id"]."'
                AND t1.match_date = t2.match_date
                AND t1.player_id <> t2.player_id
                AND t1.play_yn = 1
                AND t2.play_yn = 1
                AND t2.player_name NOT LIKE '_용병%'";
                
        if( ! empty($_REQUEST["from"]) ){
            $pfrom = $_REQUEST["from"];
            $sql = $sql." AND t1.match_date >= '".$pfrom."' ";
        }
        if( ! empty($_REQUEST["to"]) ){
            $pto = $_REQUEST["to"];
            $sql = $sql." AND t1.match_date <= '".$pto."' ";
        }
        $sql = $sql.") t
                WHERE team_yn = 0
                GROUP BY t2_player
            ) tt
            ORDER BY pts DESC";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, 
                array('player_name'=>$row['player_name']
                , 'play_cnt'=>$row['play_cnt']
                , 'pts'=>$row['pts']
                , 'win_cnt'=>$row['win_cnt']
                , 'draw_cnt'=>$row['draw_cnt']
                , 'lose_cnt'=>$row['lose_cnt']
                , 'goal_cnt'=>$row['goal_cnt']
                , 'asst_cnt'=>$row['asst_cnt']
                , 'goal_vs_cnt'=>$row['goal_vs_cnt']
                , 'asst_vs_cnt'=>$row['asst_vs_cnt']
                )
            );
        }
    }
}
else if($pcmd == "play_week"){
    if($conn){
        $sql = "SELECT 
            player_id
            , player_name
            , round(avg(play_cnt), 2) avg_cnt
            , sum(total_cnt) total_cnt
        FROM (
            SELECT v.player_id, v.player_name
                , myear, mweek
                , sum(v.play_yn) play_cnt
                , count(1) total_cnt
            FROM football_tpm_view v,
            (
                SELECT fm.*
                    , STR_TO_DATE(match_date, '%Y.%m.%d') mdate
                    , WEEKOFYEAR(STR_TO_DATE(match_date, '%Y.%m.%d')) mweek
                    , SUBSTRING(match_date, 1, 4) myear
                FROM football_match fm
                WHERE team_id = '".$pid."'
        ";
        if( ! empty($_REQUEST["st"]) && ! empty($_REQUEST["ed"]) ){
            $sql = $sql." AND match_date BETWEEN '".$_REQUEST["st"]."' AND '".$_REQUEST["ed"]."' ";
        }
        else if( ! empty($_REQUEST["st"])  ){
            $sql = $sql." AND match_date >= '".$_REQUEST["st"]."' ";
        }
        else if( ! empty($_REQUEST["ed"]) ){
            $sql = $sql." AND match_date <= '".$_REQUEST["ed"]."' ";
        }
        $sql = $sql.") m
            WHERE m.id = v.match_id
            group by v.player_id, v.player_name, myear, mweek
        ) t
        group by player_id, player_name
        order by avg_cnt desc, player_name
        ";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'player_id'=>$row['player_id'],
                'player_name'=>$row['player_name'],
                'avg_cnt'=>$row['avg_cnt'],
                'total_cnt'=>$row['total_cnt']
            ));
        }
    }
}
else if($pcmd == "play_month"){
    if($conn){
        $sql = "SELECT 
            player_id
            , player_name
            , round(avg(play_cnt), 2) avg_cnt
            , sum(total_cnt) total_cnt
        FROM (
            SELECT v.player_id, v.player_name
                , mmonth
                , sum(v.play_yn) play_cnt
                , count(1) total_cnt
            FROM football_tpm_view v,
            (
                SELECT fm.*
                    , STR_TO_DATE(match_date, '%Y.%m.%d') mdate
                    , SUBSTRING(match_date, 1, 7) mmonth
                FROM football_match fm
                WHERE team_id = '".$pid."'
        ";
        if( ! empty($_REQUEST["st"]) && ! empty($_REQUEST["ed"]) ){
            $sql = $sql." AND match_date BETWEEN '".$_REQUEST["st"]."' AND '".$_REQUEST["ed"]."' ";
        }
        else if( ! empty($_REQUEST["st"])  ){
            $sql = $sql." AND match_date >= '".$_REQUEST["st"]."' ";
        }
        else if( ! empty($_REQUEST["ed"]) ){
            $sql = $sql." AND match_date <= '".$_REQUEST["ed"]."' ";
        }
        $sql = $sql.") m
            WHERE m.id = v.match_id
            group by v.player_id, v.player_name, mmonth
        ) t
        group by player_id, player_name
        order by avg_cnt desc, player_name
        ";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'player_id'=>$row['player_id'],
                'player_name'=>$row['player_name'],
                'avg_cnt'=>$row['avg_cnt'],
                'total_cnt'=>$row['total_cnt']
            ));
        }
    }
}
else if($pcmd == "play_days"){
    if($conn){
        $sql = "SELECT 
            player_id
            , player_name
            , round(week0/cnt0 * 100, 2) week0
            , round(week1/cnt1 * 100, 2) week1
            , round(week2/cnt2 * 100, 2) week2
            , round(week3/cnt3 * 100, 2) week3
            , round(week4/cnt4 * 100, 2) week4
            , round(week5/cnt5 * 100, 2) week5
            , round(week6/cnt6 * 100, 2) week6
            , total_cnt
        FROM (
            SELECT v.player_id, v.player_name
                , sum(case when mweekday = 0 then v.play_yn end) week0
                , sum(case when mweekday = 1 then v.play_yn end) week1
                , sum(case when mweekday = 2 then v.play_yn end) week2
                , sum(case when mweekday = 3 then v.play_yn end) week3
                , sum(case when mweekday = 4 then v.play_yn end) week4
                , sum(case when mweekday = 5 then v.play_yn end) week5
                , sum(case when mweekday = 6 then v.play_yn end) week6
                , sum(case when mweekday = 0 then 1 end) cnt0
                , sum(case when mweekday = 1 then 1 end) cnt1
                , sum(case when mweekday = 2 then 1 end) cnt2
                , sum(case when mweekday = 3 then 1 end) cnt3
                , sum(case when mweekday = 4 then 1 end) cnt4
                , sum(case when mweekday = 5 then 1 end) cnt5
                , sum(case when mweekday = 6 then 1 end) cnt6
                , count(1) total_cnt
            FROM football_tpm_view v,
            (
                SELECT fm.*
                    , weekday(STR_TO_DATE(match_date, '%Y.%m.%d')) mweekday
                FROM football_match fm
                WHERE team_id = '".$pid."'
        ";
        if( ! empty($_REQUEST["st"]) && ! empty($_REQUEST["ed"]) ){
            $sql = $sql." AND match_date BETWEEN '".$_REQUEST["st"]."' AND '".$_REQUEST["ed"]."' ";
        }
        else if( ! empty($_REQUEST["st"])  ){
            $sql = $sql." AND match_date >= '".$_REQUEST["st"]."' ";
        }
        else if( ! empty($_REQUEST["ed"]) ){
            $sql = $sql." AND match_date <= '".$_REQUEST["ed"]."' ";
        }
        $sql = $sql.") m
            WHERE m.id = v.match_id
                ".$notinplayer."
            group by v.player_id, v.player_name
        ) t
        order by player_name
        ";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'player_id'=>$row['player_id'],
                'player_name'=>$row['player_name'],
                'week0'=>$row['week0'],
                'week1'=>$row['week1'],
                'week2'=>$row['week2'],
                'week3'=>$row['week3'],
                'week4'=>$row['week4'],
                'week5'=>$row['week5'],
                'week6'=>$row['week6'],
                'total_cnt'=>$row['total_cnt']
            ));
        }
    }
}
else if($pcmd == "team_record"){
    if($conn){
        $sql = "SELECT player_id, player_name
        , SUM(IFNULL(play_yn, 0)) play_cnt
        , SUM(IFNULL(win_yn, 0)) win_cnt
        , SUM(IFNULL(goal_cnt, 0)) goal_cnt
        , SUM(IFNULL(asst_cnt, 0)) asst_cnt
      FROM football_tpm_view2
      WHERE 1=1
      AND team_id = 1
      AND match_date IS NOT NULL
      AND player_name NOT LIKE '_용병%'
      ";
        if( ! empty($_REQUEST["searchYear"]) ){
            $sql = $sql." AND match_date BETWEEN '".$_REQUEST["searchYear"].".01.01' AND '".$_REQUEST["searchYear"].".12.31' ";
        }
        $sql = $sql."
      AND play_yn = 1
      GROUP BY player_id, player_name";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'player_name'=>$row['player_name']
                ,'play_cnt'=>$row['play_cnt']
                ,'win_cnt'=>$row['win_cnt']
                ,'goal_cnt'=>$row['goal_cnt']
                ,'asst_cnt'=>$row['asst_cnt']
                )
            );
        }
    }
}
else if($pcmd == "team_record1"){
    if($conn){
        $sql = "SELECT player_id, player_name
        , match_date
        , SUM(IFNULL(goal_cnt, 0)) goal_cnt
        , SUM(IFNULL(asst_cnt, 0)) asst_cnt
      FROM football_tpm_view2
      WHERE 1=1
      AND team_id = 1
      AND match_date IS NOT NULL
      ";
        if( ! empty($_REQUEST["searchYear"]) ){
            $sql = $sql." AND match_date BETWEEN '".$_REQUEST["searchYear"].".01.01' AND '".$_REQUEST["searchYear"].".12.31' ";
        }
        $sql = $sql."
      AND play_yn = 1
      and player_name not like '_용병%'
      GROUP BY player_id, player_name, match_date";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'player_name'=>$row['player_name']
                ,'match_date'=>$row['match_date']
                ,'goal_cnt'=>$row['goal_cnt']
                ,'asst_cnt'=>$row['asst_cnt']
                )
            );
        }
    }
}
else if($pcmd == "team_record2"){
    if($conn){
        $sql = "SELECT match_date
        , SUM(case when player_name like '_용병%' then 0 else play_yn end) play_cnt
        , SUM(team_a_yn) team_a_cnt
        , SUM(team_b_yn) team_b_cnt
        , SUM(goal_cnt) goal_cnt
        , SUM(asst_cnt) asst_cnt
        , SUM(case when team_a_yn = 1  then goal_cnt ELSE 0 END) team_a_goal
        , SUM(case when team_b_yn = 1  then goal_cnt ELSE 0 END) team_b_goal
        , SUM(case when team_a_yn = 1  then asst_cnt ELSE 0 END) team_a_asst
        , SUM(case when team_b_yn = 1  then asst_cnt ELSE 0 END) team_b_asst
        , ABS(SUM(case when team_a_yn = 1  then goal_cnt ELSE 0 END) - SUM(case when team_b_yn = 1  then goal_cnt ELSE 0 END)) c_goal
    FROM football_tpm_view2
    WHERE 1=1
    AND team_id = 1
    AND match_date IS NOT NULL
      ";
        if( ! empty($_REQUEST["searchYear"]) ){
            $sql = $sql." AND match_date BETWEEN '".$_REQUEST["searchYear"].".01.01' AND '".$_REQUEST["searchYear"].".12.31' ";
        }
        $sql = $sql."
      AND play_yn = 1
      GROUP BY match_date";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array(
                'match_date'=>$row['match_date']
                ,'play_cnt'=>$row['play_cnt']
                ,'team_a_cnt'=>$row['team_a_cnt']
                ,'team_b_cnt'=>$row['team_b_cnt']
                ,'goal_cnt'=>$row['goal_cnt']
                ,'asst_cnt'=>$row['asst_cnt']
                ,'team_a_goal'=>$row['team_a_goal']
                ,'team_b_goal'=>$row['team_b_goal']
                ,'team_a_asst'=>$row['team_a_asst']
                ,'team_b_asst'=>$row['team_b_asst']
                ,'c_goal'=>$row['c_goal']
                ,'team_max_goal'=>$row['team_a_goal']>$row['team_b_goal']?$row['team_a_goal']:$row['team_b_goal']
                ,'team_max_asst'=>$row['team_a_asst']>$row['team_b_asst']?$row['team_a_asst']:$row['team_b_asst']
                )
            );
        }
    }
}
else if($pcmd == "team_record3"){
    if($conn){
        $sql = "SELECT player_id, player_name, match_date, play_yn, win_yn, goal_cnt, asst_cnt
        FROM football_tpm_view
        where team_id = '".$pid."'
      AND player_name NOT LIKE '_용병%'
      ";
        if( ! empty($_REQUEST["searchYear"]) ){
            $sql = $sql." AND match_date BETWEEN '".$_REQUEST["searchYear"].".01.01' AND '".$_REQUEST["searchYear"].".12.31' ";
        }
        if( ! empty($_REQUEST["searchPlay"]) ){
            $sql = $sql." AND play_yn = 1 ";
        }
        $sql = $sql."
        order by player_id, match_date
        ";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, 
                array('player_id'=>$row['player_id']
                , 'player_name'=>$row['player_name']
                , 'match_date'=>$row['match_date']
                , 'play_yn'=>$row['play_yn']
                , 'win_yn'=>$row['win_yn']
                , 'goal_cnt'=>$row['goal_cnt']
                , 'asst_cnt'=>$row['asst_cnt']
                )
            );
        }
    }
}
else{
    
}
echo json_encode($array);

$dateString = date("Ymd", time());
$timeString =date("Y-m-d H:i:s", time());
$filepath = "./logs/get_".$dateString.".log";
$logfile = fopen($filepath, "a");
fwrite($logfile, "[team=".$pid."][cmd=".$pcmd."]".$timeString."\n");
fclose($logfile);

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