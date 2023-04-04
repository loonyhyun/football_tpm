<?php
 
header("Progma:no-cache");

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");

header('Content-Type: application/json; charset=UTF-8');

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
  //'loonyhyun.cafe24.com',
  'localhost',
  'loonyhyun',
  'bodigad0',
  'loonyhyun');
// 컨텐츠 타입이 JSON 인지 확인한다
if(!in_array('application/json',explode(';',$_SERVER['CONTENT_TYPE']))){
echo json_encode(array('result_code' => '400'));
exit;
}
 
$__rawBody = file_get_contents("php://input"); // 본문을 불러옴
$__getData = array(json_decode($__rawBody)); // 데이터를 변수에 넣고
//$data = json_decode(json_encode('{"body": "bar", "id": 101, "title": "foo", "user_id": 2, "user_pw": "1234"}'));
//$getddd = array(json_decode($data)); // 데이터를 변수에 넣고

foreach($__getData as $row){
    $puid = $row->user_id;
    $pupwd = $row->user_pw;
    $pcmd = $row->cmd;
}
if($pcmd == "user_"){
    if($conn){
        $sql = "SELECT count(1) cnt
                FROM football_user t WHERE user_id = '".$puid."'
                    and user_pwd = '".$pupwd."'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_array($result);
        //array_push($array, array('result'=>''.$row['cnt']));
        //echo json_encode($array);
        echo json_encode(array('result_code' => '200', 'result'=> ''.$row['cnt']));
    }else{
        echo json_encode(array('result_code' => '200', 'result'=> 'fail'));
    }
}else{

    echo json_encode(array('result_code' => '200', 'result'=>$__getData));
}
 
  
?>
