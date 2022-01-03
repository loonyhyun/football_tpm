<?php

include 'setting.php';
  
$pid = $_REQUEST["id"];
$pcmd = $_REQUEST["cmd"];

$notinplayer1 = " and id not in ('79','80','81','82') ";
$notinplayer = " and player_id not in ('79','80','81','82') ";

$array = array();
if($pcmd == "best2021_cnt"){
    if($conn){
        $sql = "SELECT count(1) cnt
                FROM football_best_formation t WHERE reg_date < STR_TO_DATE('20220101', '%Y%m%d')";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $cnt = $row['cnt'];
        $sql = "SELECT reg_formation, count(1) cnt
                FROM football_best_formation t WHERE reg_date < STR_TO_DATE('20220101', '%Y%m%d')
                GROUP BY reg_formation
                ORDER BY cnt desc";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        $formation = $row['reg_formation'];
        echo ''.$cnt.'$'.$formation.'';
    }
}
else if($pcmd == "best2021_list"){
    if($conn){
        $ptype = $_REQUEST["type"];
        $sql = "SELECT T.player_name, 
            SUM(att_yn + mid_yn + def_yn + keep_yn) total_cnt,
            sum(att_yn) att_cnt,
            sum(mid_yn) mid_cnt,
            sum(def_yn) def_cnt,
            sum(keep_yn) keep_cnt
        FROM (
            select p.id AS player_id,p.player_name AS player_name
            ,(f.player_att like CONCAT('%$',p.id,'$%')) AS att_yn
            ,(f.player_mid like concat('%$',p.id,'$%')) AS mid_yn
            ,(f.player_def like concat('%$',p.id,'$%')) AS def_yn
            ,(f.player_keep like concat('%$',p.id,'$%')) AS keep_yn 
            , locate(concat('$',p.id,'$'), player_def) lb_yn
            from football_player p, football_best_formation f
            WHERE f.reg_date < STR_TO_DATE('20220101', '%Y%m%d')
        ) T
        GROUP BY player_name ";
        if($ptype == "1"){
            $sql = $sql." ORDER BY 2 DESC ";
        }else if($ptype == "2"){
            $sql = $sql." ORDER BY 3 DESC ";
        }else if($ptype == "3"){
            $sql = $sql." ORDER BY 4 DESC ";
        }else if($ptype == "4"){
            $sql = $sql." ORDER BY 5 DESC ";
        }else if($ptype == "5"){
            $sql = $sql." ORDER BY 6 DESC ";
        }
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('player_name'=>$row['player_name']
            ,'total_cnt'=>$row['total_cnt']
            ,'att_cnt'=>$row['att_cnt']
            ,'mid_cnt'=>$row['mid_cnt']
            ,'def_cnt'=>$row['def_cnt']
            ,'keep_cnt'=>$row['keep_cnt']));
        }
        echo json_encode($array);
    }
}
else if($pcmd == "best2021_list_formation"){
    if($conn){
        $sql1 = "SELECT T.player_name, 
            SUM(att_yn + mid_yn + def_yn + keep_yn) total_cnt,
            sum(att_yn) att_cnt,
            sum(mid_yn) mid_cnt,
            sum(def_yn) def_cnt,
            sum(keep_yn) keep_cnt
        FROM (
            select p.id AS player_id,p.player_name AS player_name
            ,(f.player_att like CONCAT('%$',p.id,'$%')) AS att_yn
            ,(f.player_mid like concat('%$',p.id,'$%')) AS mid_yn
            ,(f.player_def like concat('%$',p.id,'$%')) AS def_yn
            ,(f.player_keep like concat('%$',p.id,'$%')) AS keep_yn 
            , locate(concat('$',p.id,'$'), player_def) lb_yn
            from football_player p, football_best_formation f
            WHERE f.reg_date < STR_TO_DATE('20220101', '%Y%m%d')
        ) T
        GROUP BY player_name 
        ORDER BY att_cnt desc, player_name";

        $result1 = mysqli_query($conn, $sql1);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result1)) && $cnt < 6){
            array_push($array, array('player_name'=>$row['player_name']
                ,'total_cnt'=>$row['total_cnt']
                ,'att_cnt'=>$row['att_cnt']
                ,'mid_cnt'=>$row['mid_cnt']
                ,'def_cnt'=>$row['def_cnt']
                ,'keep_cnt'=>$row['keep_cnt']
                ,'type'=>'att'
            ));

            $cnt = $cnt + 1;
        }

        $sql2 = "SELECT T.player_name, 
            SUM(att_yn + mid_yn + def_yn + keep_yn) total_cnt,
            sum(att_yn) att_cnt,
            sum(mid_yn) mid_cnt,
            sum(def_yn) def_cnt,
            sum(keep_yn) keep_cnt
        FROM (
            select p.id AS player_id,p.player_name AS player_name
            ,(f.player_att like CONCAT('%$',p.id,'$%')) AS att_yn
            ,(f.player_mid like concat('%$',p.id,'$%')) AS mid_yn
            ,(f.player_def like concat('%$',p.id,'$%')) AS def_yn
            ,(f.player_keep like concat('%$',p.id,'$%')) AS keep_yn 
            , locate(concat('$',p.id,'$'), player_def) lb_yn
            from football_player p, football_best_formation f
            WHERE f.reg_date < STR_TO_DATE('20220101', '%Y%m%d')
        ) T
        GROUP BY player_name 
        ORDER BY mid_cnt desc, player_name";

        $result2 = mysqli_query($conn, $sql2);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result2)) && $cnt < 6){
            array_push($array, array('player_name'=>$row['player_name']
                ,'total_cnt'=>$row['total_cnt']
                ,'att_cnt'=>$row['att_cnt']
                ,'mid_cnt'=>$row['mid_cnt']
                ,'def_cnt'=>$row['def_cnt']
                ,'keep_cnt'=>$row['keep_cnt']
                ,'type'=>'mid'
            ));

            $cnt = $cnt + 1;
        }

        $sql3 = "SELECT T.player_name, 
            SUM(att_yn + mid_yn + def_yn + keep_yn) total_cnt,
            sum(att_yn) att_cnt,
            sum(mid_yn) mid_cnt,
            sum(def_yn) def_cnt,
            sum(keep_yn) keep_cnt
        FROM (
            select p.id AS player_id,p.player_name AS player_name
            ,(f.player_att like CONCAT('%$',p.id,'$%')) AS att_yn
            ,(f.player_mid like concat('%$',p.id,'$%')) AS mid_yn
            ,(f.player_def like concat('%$',p.id,'$%')) AS def_yn
            ,(f.player_keep like concat('%$',p.id,'$%')) AS keep_yn 
            , locate(concat('$',p.id,'$'), player_def) lb_yn
            from football_player p, football_best_formation f
            WHERE f.reg_date < STR_TO_DATE('20220101', '%Y%m%d')
        ) T
        GROUP BY player_name 
        ORDER BY def_cnt desc, player_name";

        $result3 = mysqli_query($conn, $sql3);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result3)) && $cnt < 6){
            array_push($array, array('player_name'=>$row['player_name']
                ,'total_cnt'=>$row['total_cnt']
                ,'att_cnt'=>$row['att_cnt']
                ,'mid_cnt'=>$row['mid_cnt']
                ,'def_cnt'=>$row['def_cnt']
                ,'keep_cnt'=>$row['keep_cnt']
                ,'type'=>'def'
            ));

            $cnt = $cnt + 1;
        }

        $sql4 = "SELECT T.player_name, 
            SUM(att_yn + mid_yn + def_yn + keep_yn) total_cnt,
            sum(att_yn) att_cnt,
            sum(mid_yn) mid_cnt,
            sum(def_yn) def_cnt,
            sum(keep_yn) keep_cnt
        FROM (
            select p.id AS player_id,p.player_name AS player_name
            ,(f.player_att like CONCAT('%$',p.id,'$%')) AS att_yn
            ,(f.player_mid like concat('%$',p.id,'$%')) AS mid_yn
            ,(f.player_def like concat('%$',p.id,'$%')) AS def_yn
            ,(f.player_keep like concat('%$',p.id,'$%')) AS keep_yn 
            , locate(concat('$',p.id,'$'), player_def) lb_yn
            from football_player p, football_best_formation f
            WHERE f.reg_date < STR_TO_DATE('20220101', '%Y%m%d')
        ) T
        GROUP BY player_name 
        ORDER BY keep_cnt desc, player_name";

        $result4 = mysqli_query($conn, $sql4);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result4)) && $cnt < 2){
            array_push($array, array('player_name'=>$row['player_name']
                ,'total_cnt'=>$row['total_cnt']
                ,'att_cnt'=>$row['att_cnt']
                ,'mid_cnt'=>$row['mid_cnt']
                ,'def_cnt'=>$row['def_cnt']
                ,'keep_cnt'=>$row['keep_cnt']
                ,'type'=>'keep'
            ));

            $cnt = $cnt + 1;
        }

        echo json_encode($array);
    }
}
else if($pcmd == "best2021_player"){
    if($conn){
        $ptype = $_REQUEST["type"];
        $sql = "SELECT T.player_name, 
            SUM(att_yn + mid_yn + def_yn + keep_yn) total_cnt,
            sum(att_yn) att_cnt,
            sum(mid_yn) mid_cnt,
            sum(def_yn) def_cnt,
            sum(keep_yn) keep_cnt
        FROM (
            select p.id AS player_id,p.player_name AS player_name
            ,(f.player_att like CONCAT('%$',p.id,'$%')) AS att_yn
            ,(f.player_mid like concat('%$',p.id,'$%')) AS mid_yn
            ,(f.player_def like concat('%$',p.id,'$%')) AS def_yn
            ,(f.player_keep like concat('%$',p.id,'$%')) AS keep_yn 
            , locate(concat('$',p.id,'$'), player_def) lb_yn
            from football_player p, football_best_formation f
            WHERE f.reg_date < STR_TO_DATE('20220101', '%Y%m%d')
        ) T
        GROUP BY player_name ";
        if($ptype == "1"){
            $sql = $sql." ORDER BY 2 DESC ";
        }else if($ptype == "2"){
            $sql = $sql." ORDER BY 3 DESC ";
        }else if($ptype == "3"){
            $sql = $sql." ORDER BY 4 DESC ";
        }else if($ptype == "4"){
            $sql = $sql." ORDER BY 5 DESC ";
        }else if($ptype == "5"){
            $sql = $sql." ORDER BY 6 DESC ";
        }
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array, array('player_name'=>$row['player_name']
            ,'total_cnt'=>$row['total_cnt']
            ,'att_cnt'=>$row['att_cnt']
            ,'mid_cnt'=>$row['mid_cnt']
            ,'def_cnt'=>$row['def_cnt']
            ,'keep_cnt'=>$row['keep_cnt']));
        }
        echo json_encode($array);
    }
}
if($pcmd == "best_2021_result"){
    if($conn){
        $sql = "SELECT player_id, player_name
            , SUM(goal_cnt) goal_cnt
            , SUM(asst_cnt) asst_cnt
            , SUM(play_yn) play_cnt
            , SUM(win_yn) win_point
            ,SUM(case when win_yn = 1.0 then 3 when win_yn = 0.5 then 1 ELSE 0 END) pts
        FROM football_tpm_view
        WHERE match_date BETWEEN '2021.01.01' AND '2021.12.31'
        AND team_id = 1
        GROUP BY player_id, player_name
        ORDER BY play_cnt desc";
        $result = mysqli_query($conn, $sql);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result)) && $cnt < 5){
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
        WHERE match_date BETWEEN '2021.01.01' AND '2021.12.31'
        AND team_id = 1
        GROUP BY player_id, player_name
        ORDER BY goal_cnt desc";
        $result = mysqli_query($conn, $sql);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result)) && $cnt < 5){
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
        WHERE match_date BETWEEN '2021.01.01' AND '2021.12.31'
        AND team_id = 1
        GROUP BY player_id, player_name
        ORDER BY asst_cnt desc";
        $result = mysqli_query($conn, $sql);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result)) && $cnt < 5){
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
        WHERE match_date BETWEEN '2021.01.01' AND '2021.12.31'
        AND team_id = 1
        GROUP BY player_id, player_name
        ORDER BY pts desc";
        $result = mysqli_query($conn, $sql);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result)) && $cnt < 5){
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
                WHERE team_id = '".$pid."' 
                AND match_date BETWEEN '2021.01.01' AND '2021.12.31'
                GROUP BY player_id, player_name
                ORDER BY ls_cnt desc, player_name";
        $result = mysqli_query($conn, $sql);
        $cnt = 0;
        while(($row = mysqli_fetch_array($result)) && $cnt < 5){
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