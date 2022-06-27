<?php

include 'setting.php';
  
$pid = $_REQUEST["id"];
$pcmd = $_REQUEST["cmd"];

$notinplayer1 = " and id not in ('79','80','81','82') ";
$notinplayer = " and player_id not in ('79','80','81','82') ";

$array = array();
if($pcmd == "info_formation"){
    if($conn){
        $formation = $_REQUEST["formation"];
        $sql = "SELECT * FROM football_best_info_formation where formation = ".$formation;
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        array_push($array, array(
              'pos_lw'=>$row['pos_lw']
             ,'pos_st'=>$row['pos_st']
             ,'pos_rw'=>$row['pos_rw']
             ,'pos_lm'=>$row['pos_lm']
             ,'pos_am'=>$row['pos_am']
             ,'pos_cm'=>$row['pos_cm']
             ,'pos_dm'=>$row['pos_dm']
             ,'pos_rm'=>$row['pos_rm']
             ,'pos_lwb'=>$row['pos_lwb']
             ,'pos_rwb'=>$row['pos_rwb']
             ,'pos_lb'=>$row['pos_lb']
             ,'pos_cb'=>$row['pos_cb']
             ,'pos_rb'=>$row['pos_rb']
             ,'pos_gk'=>$row['pos_gk']
        ));
        echo json_encode($array);
    }
}
else if($pcmd == "vote"){
    if($conn){
		$sql = "INSERT INTO football_best_formation_2
        (team_id, reg_name, reg_date, reg_formation
        ,pos_lw
        ,pos_st
        ,pos_rw
        ,pos_lm
        ,pos_am
        ,pos_cm
        ,pos_dm
        ,pos_rm
        ,pos_lwb
        ,pos_rwb
        ,pos_lb
        ,pos_cb
        ,pos_rb
        ,pos_gk) 
        values (1, '".$_REQUEST["name"]."', NOW(), '".$_REQUEST["formation"]."'
        ,'".$_REQUEST["pos_lw"]."'
        ,'".$_REQUEST["pos_st"]."'
        ,'".$_REQUEST["pos_rw"]."'
        ,'".$_REQUEST["pos_lm"]."'
        ,'".$_REQUEST["pos_am"]."'
        ,'".$_REQUEST["pos_cm"]."'
        ,'".$_REQUEST["pos_dm"]."'
        ,'".$_REQUEST["pos_rm"]."'
        ,'".$_REQUEST["pos_lwb"]."'
        ,'".$_REQUEST["pos_rwb"]."'
        ,'".$_REQUEST["pos_lb"]."'
        ,'".$_REQUEST["pos_cb"]."'
        ,'".$_REQUEST["pos_rb"]."'
        ,'".$_REQUEST["pos_gk"]."'
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
else if($pcmd == "count_formation"){
    $sql = "select reg_formation, count(1) cnt from football_best_formation_2
	group by reg_formation
	order by cnt desc";
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_array($result)){
        array_push($array, array('formation'=>$row['reg_formation']
            ,'cnt'=>$row['cnt']
        ));
    }
    echo json_encode($array);
}
else if($pcmd == "count_player"){
    $sql = "SELECT player_id, player_name
    , sum(pos_lw ) pos_lw 
    , sum(pos_st ) pos_st 
    , sum(pos_rw ) pos_rw 
    , sum(pos_lm ) pos_lm 
    , sum(pos_am ) pos_am 
    , sum(pos_cm ) pos_cm 
    , sum(pos_dm ) pos_dm 
    , sum(pos_rm ) pos_rm 
    , sum(pos_lwb) pos_lwb
    , sum(pos_rwb) pos_rwb
    , sum(pos_lb ) pos_lb 
    , sum(pos_cb ) pos_cb 
    , sum(pos_rb ) pos_rb 
    , sum(pos_gk ) pos_gk 
    , sum(pos_lw + pos_st + pos_rw + pos_lm + pos_am + pos_cm + pos_dm + pos_rm + pos_lwb+ pos_rwb+ pos_lb + pos_cb + pos_rb + pos_gk) total_cnt
    FROM (
        select p.*
        ,(f.pos_lw like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_lw
        ,(f.pos_st like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_st
        ,(f.pos_rw like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_rw
        ,(f.pos_lm like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_lm
        ,(f.pos_am like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_am
        ,(f.pos_cm like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_cm
        ,(f.pos_dm like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_dm
        ,(f.pos_rm like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_rm
        ,(f.pos_lwb like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_lwb
        ,(f.pos_rwb like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_rwb
        ,(f.pos_lb like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_lb
        ,(f.pos_cb like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_cb
        ,(f.pos_rb like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_rb
        ,(f.pos_gk like convert(concat('%$', p.player_id,'$%') using utf8)) AS pos_gk
        from football_best_formation_2 f, 
        (
            select id player_id, player_name, concat('$', id ,'$') player_label
            from football_player
        ) p
    ) t1
    GROUP BY player_id, player_name
    ORDER BY total_cnt DESC";
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_array($result)){
        array_push($array, array('player_name'=>$row['player_name']
            ,'total_cnt'=>$row['total_cnt']
            ,'pos_lw'=>$row['pos_lw']
            ,'pos_st'=>$row['pos_st']
            ,'pos_rw'=>$row['pos_rw']
            ,'pos_lm'=>$row['pos_lm']
            ,'pos_am'=>$row['pos_am']
            ,'pos_cm'=>$row['pos_cm']
            ,'pos_dm'=>$row['pos_dm']
            ,'pos_rm'=>$row['pos_rm']
            ,'pos_lwb'=>$row['pos_lwb']
            ,'pos_rwb'=>$row['pos_rwb']
            ,'pos_lb'=>$row['pos_lb']
            ,'pos_cb'=>$row['pos_cb']
            ,'pos_rb'=>$row['pos_rb']
            ,'pos_gk'=>$row['pos_gk']
        ));
    }
    echo json_encode($array);
}
else if($pcmd == "list"){
    $sql = "SELECT id, reg_name FROM football_best_formation_2
	order by id";
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_array($result)){
        array_push($array, array('id'=>$row['id']
            ,'reg_name'=>$row['reg_name']
        ));
    }
    echo json_encode($array);
}
else if($pcmd == "voteinfo"){
    $sql = "SELECT * 
    FROM football_best_formation_2
	where id = ".$_REQUEST["voteid"];
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_array($result)){
        array_push($array, array('id'=>$row['id']
            ,'reg_name'=>$row['reg_name']
            ,'reg_date'=>$row['reg_date']
            ,'reg_formation'=>$row['reg_formation']
            ,'pos_lw'=>$row['pos_lw']
            ,'pos_st'=>$row['pos_st']
            ,'pos_rw'=>$row['pos_rw']
            ,'pos_lm'=>$row['pos_lm']
            ,'pos_am'=>$row['pos_am']
            ,'pos_cm'=>$row['pos_cm']
            ,'pos_dm'=>$row['pos_dm']
            ,'pos_rm'=>$row['pos_rm']
            ,'pos_lwb'=>$row['pos_lwb']
            ,'pos_rwb'=>$row['pos_rwb']
            ,'pos_lb'=>$row['pos_lb']
            ,'pos_cb'=>$row['pos_cb']
            ,'pos_rb'=>$row['pos_rb']
            ,'pos_gk'=>$row['pos_gk']
        ));
    }
    echo json_encode($array);
}
else{
    echo "fail-cmd";
}
?>