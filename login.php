<?php

include './setting.php';

if(!empty($_REQUEST["id"]) && !empty($_REQUEST["pwd"])){

    if($conn){
        $sql = "SELECT t.id team_id, t.team_name
        , t.permission_yn
        , u.permission_ymd ymd
        , CURDATE() cur
        FROM football_team t, football_user u
        WHERE t.id = u.team_id
            AND user_id = '".$_REQUEST["id"]."'
            AND user_pwd = '".$_REQUEST["pwd"]."'
        ";
        $result = mysqli_query($conn, $sql);
        $ret_cnt = mysqli_num_rows($result);
        if($ret_cnt > 0){
            $row = mysqli_fetch_array($result);

            if($row['ymd'] < $row['cur']){
                echo 'expired user';
            }
            else if($row['permission_yn'] == 'Y'){
                $array = array();
                array_push($array, array(
                    'id'=>$row['id']
                    ,'team_id'=>$row['team_id']
                    ,'team_name'=>$row['team_name']
                    ,'session_ymd'=>$row['cur']
                    )
                );
                echo json_encode($array);
            }
            else{
                echo 'permission denied';
            }
        }else{
            echo 'fail';
        }
    }
    else{
        echo 'db error';   
    }
}
else{
    echo 'none parameter';
}

?>