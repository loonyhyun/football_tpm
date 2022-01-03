<?php

include 'setting.php';
  
$pid = $_REQUEST["id"];
$pcmd = $_REQUEST["cmd"];

$array = array();
if($pcmd == "matchlist"){
    if($conn){
        $sql = "SELECT t.id, t.match_date, ts.a_cnt, ts.b_cnt
                FROM football_match t, 
                (
                    SELECT match_id
                    , sum(case when team_ab = 'a' then 1 ELSE 0 end) a_cnt
                    , sum(case when team_ab = 'b' then 1 ELSE 0 end) b_cnt
                    FROM football_match_scoreless
                    GROUP BY match_id
                ) ts
                WHERE t.team_id = '".$pid."'
                AND t.id = ts.match_id
                ORDER BY match_date desc";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('match_id'=>$row['id'], 'match_date'=>$row['match_date']
            , 'a_cnt'=>$row['a_cnt']
            , 'b_cnt'=>$row['b_cnt']));
        }
    }
}
else if($pcmd == "match_scoreless"){
    if($conn){
        $sql = "SELECT * FROM football_tpm_scoreless_2
                WHERE team_id = '".$pid."' order by player_name";
        $result = mysqli_query($conn, $sql);
        //$row = mysqli_fetch_array($result);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('player_id'=>$row['player_id']
            ,'player_name'=>$row['player_name']
            ,'m_cnt'=>$row['m_cnt']
            ,'ls_cnt'=>$row['ls_cnt']));
        }
    }
}
else if($pcmd == "player_scoreless"){
    if($conn){
        $sql = "SELECT player_id, player_name
                    , sum(quarters) t_cnt
                    , sum(ls_cnt) ls_cnt
                FROM football_tpm_scoreless
                WHERE team_id = '".$pid."' ";
        if( ! empty($_REQUEST["st"]) && ! empty($_REQUEST["ed"]) ){
            $pst = $_REQUEST["st"];
            $ped = $_REQUEST["ed"];
            $sql = $sql." AND match_date between '".$pst."' and '".$ped."' ";
        }
        else if( ! empty($_REQUEST["st"]) ){
            $pst = $_REQUEST["st"];
            $sql = $sql." AND match_date >= '".$pst."' ";
        }
        else if( ! empty($_REQUEST["ed"]) ){
            $ped = $_REQUEST["ed"];
            $sql = $sql." AND match_date <= '".$ped."' ";
        }
        $sql = $sql." GROUP BY player_id, player_name
            ORDER BY ls_cnt desc, player_name";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('player_id'=>$row['player_id']
            ,'player_name'=>$row['player_name']
            ,'t_cnt'=>$row['t_cnt']
            ,'ls_cnt'=>$row['ls_cnt']));
        }
    }
}
else{
    
}
echo json_encode($array);
?>