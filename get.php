<?php

include 'setting.php';

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
FROM football_tpm_view
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
        $sql = "SELECT id, match_date, win_ab
                FROM football_match
                WHERE team_id = '".$pid."' order by match_date desc";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('match_id'=>$row['id'],'match_date'=>$row['match_date']
            ,'win_ab'=>$row['win_ab']));
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
                SUM(team_b_yn) team_b
            FROM football_tpm_view
            WHERE player_id in (".$pids.")
                AND play_yn = 1
            GROUP BY match_date
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