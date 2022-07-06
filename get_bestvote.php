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
    $pfrom = $_REQUEST["from"];
    $pto = $_REQUEST["to"];
    $sql = "SELECT reg_formation, count(1) cnt from football_best_formation_2
    where date_format(reg_date, '%Y.%m.%d') between '".$pfrom."' and '".$pto."'
	GROUP BY reg_formation
	ORDER BY cnt desc";
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_array($result)){
        array_push($array, array('formation'=>$row['reg_formation']
            ,'cnt'=>$row['cnt']
        ));
    }
    echo json_encode($array);
}
else if($pcmd == "count_player"){
    $pfrom = $_REQUEST["from"];
    $pto = $_REQUEST["to"];
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
        where date_format(f.reg_date, '%Y.%m.%d') between '".$pfrom."' and '".$pto."'
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
    $pfrom = $_REQUEST["from"];
    $pto = $_REQUEST["to"];
    $sql = "SELECT id, reg_name FROM football_best_formation_2
    where date_format(reg_date, '%Y.%m.%d') between '".$pfrom."' and '".$pto."'
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
else if($pcmd == "top"){
    if($conn){
        $paramcount = $_REQUEST["cnt"];
        $pfrom = $_REQUEST["from"];
        $pto = $_REQUEST["to"];

        $sql = "SELECT player_id, player_name
            , SUM(goal_cnt) goal_cnt
            , SUM(asst_cnt) asst_cnt
            , SUM(play_yn) play_cnt
            , SUM(win_yn) win_point
            ,SUM(case when win_yn = 1.0 then 3 when win_yn = 0.5 then 1 ELSE 0 END) pts
        FROM football_tpm_view
        WHERE match_date BETWEEN '".$pfrom."' AND '".$pto."'
        AND team_id = ".$pid."
        GROUP BY player_id, player_name
        ORDER BY play_cnt desc";
        $result = mysqli_query($conn, $sql);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result)) && $cnt < $paramcount){
            array_push($array, array('player_name'=>$row['player_name']
                ,'play_cnt'=>$row['play_cnt']
                ,'type'=>'play')
            );

            $cnt = $cnt + 1;
        }
        $sql = "SELECT player_id, player_name
            , SUM(goal_cnt) goal_cnt
            , SUM(asst_cnt) asst_cnt
            , SUM(play_yn) play_cnt
            , SUM(win_yn) win_point
            ,SUM(case when win_yn = 1.0 then 3 when win_yn = 0.5 then 1 ELSE 0 END) pts
        FROM football_tpm_view
        WHERE match_date BETWEEN '".$pfrom."' AND '".$pto."'
        AND team_id = ".$pid."
        GROUP BY player_id, player_name
        ORDER BY goal_cnt desc";
        $result = mysqli_query($conn, $sql);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result)) && $cnt < $paramcount){
            array_push($array, array('player_name'=>$row['player_name']
                ,'goal_cnt'=>$row['goal_cnt']
                ,'type'=>'goal')
            );

            $cnt = $cnt + 1;
        }
        $sql = "SELECT player_id, player_name
            , SUM(goal_cnt) goal_cnt
            , SUM(asst_cnt) asst_cnt
            , SUM(play_yn) play_cnt
            , SUM(win_yn) win_point
            ,SUM(case when win_yn = 1.0 then 3 when win_yn = 0.5 then 1 ELSE 0 END) pts
        FROM football_tpm_view
        WHERE match_date BETWEEN '".$pfrom."' AND '".$pto."'
        AND team_id = ".$pid."
        GROUP BY player_id, player_name
        ORDER BY asst_cnt desc";
        $result = mysqli_query($conn, $sql);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result)) && $cnt < $paramcount){
            array_push($array, array('player_name'=>$row['player_name']
                ,'asst_cnt'=>$row['asst_cnt']
                ,'type'=>'asst')
            );

            $cnt = $cnt + 1;
        }
        $sql = "SELECT player_id, player_name
            , SUM(goal_cnt) goal_cnt
            , SUM(asst_cnt) asst_cnt
            , SUM(play_yn) play_cnt
            , SUM(win_yn) win_point
            ,SUM(case when win_yn = 1.0 then 3 when win_yn = 0.5 then 1 ELSE 0 END) pts
        FROM football_tpm_view
        WHERE match_date BETWEEN '".$pfrom."' AND '".$pto."'
        AND team_id = ".$pid."
        GROUP BY player_id, player_name
        ORDER BY pts desc";
        $result = mysqli_query($conn, $sql);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result)) && $cnt < $paramcount){
            array_push($array, array('player_name'=>$row['player_name']
                ,'pts'=>$row['pts']
                ,'type'=>'pts')
            );

            $cnt = $cnt + 1;
        }
        $sql = "SELECT player_id, player_name
                    , sum(quarters) t_cnt
                    , sum(ls_cnt) ls_cnt
                FROM football_tpm_scoreless
                WHERE team_id = ".$pid."
                AND match_date BETWEEN '".$pfrom."' AND '".$pto."'
                GROUP BY player_id, player_name
                ORDER BY ls_cnt desc, player_name";
        $result = mysqli_query($conn, $sql);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result)) && $cnt < $paramcount){
            array_push($array, array('player_id'=>$row['player_id']
                ,'player_name'=>$row['player_name']
                ,'t_cnt'=>$row['t_cnt']
                ,'ls_cnt'=>$row['ls_cnt']
                , 'type'=>'def')
            );

            $cnt = $cnt + 1;
        }
        echo json_encode($array);
    }
}
else{
    echo "fail-cmd";
}
?>