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
    
    echo json_encode($array);
}
else if($pcmd == "get"){
    if($conn){
        $array1 = array();
        $array2 = array();
        $sql = "SELECT MAX(match_date) m_date
                    , m_name
                    , COUNT(1) tot
                FROM football_mercenary
                WHERE team_id = '".$pid."'
                GROUP BY m_name
                ORDER BY m_date DESC, m_name ASC";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array1, array('m_date'=>$row['m_date'], 'm_name'=>$row['m_name'], 'tot'=>$row['tot']));
        }
        
        $sql = "SELECT match_date
                    , m_name
                    , @rownum := IF(@prev_cate = t.m_name, @rownum + 1, 1) AS rnum
                    , @prev_cate := t.m_name
                FROM football_mercenary t
                JOIN (SELECT @rownum := 0, @prev_cate := NULL) vars
                WHERE team_id = '".$pid."'
                ORDER BY m_name ASC, match_date desc";
        $result = mysqli_query($conn, $sql);
        while($row = mysqli_fetch_array($result)){
            array_push($array2, array('m_name'=>$row['m_name'], 'm_date'=>$row['match_date'], 'rnum'=>$row['rnum']));
        }
        array_push($array, array('d1'=>$array1, 'd2'=>$array2));
    }
    
    echo json_encode($array);
}
else if($pcmd == "save"){
    $pchecker = $_REQUEST["checker"];
    if($pchecker == "baron"){
        if($conn){
            $sql = "DELETE FROM football_chul
            WHERE match_date = '".$_REQUEST["md"]."'
            ";
            
            $result = mysqli_query($conn, $sql);
            if($result){
                echo "ok";
            }
            else{
                echo "fail";
            }
        }
    }
}
else{
    
}

?>