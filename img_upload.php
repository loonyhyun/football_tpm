<?php

$__rawBody = file_get_contents("php://input"); // 본문을 불러옴
$__getData = array(json_decode($__rawBody)); // 데이터를 변수에 넣고

foreach($__getData as $row){
    $base64 = $row->base64;
    $player_id = $row->player_id;
}
$filename = "./upload/".$player_id.".jpg";
$decode = base64_decode($base64);
file_put_contents($filename, $decode);
echo "ok_img\n";

//resize start
$info = getimagesize($filename);

$w = 400;
$h = 400;

$width = $info[0];
$height = $info[1];

if ($info['mime'] == 'image/jpeg') 
    $image = imagecreatefromjpeg($filename);

elseif ($info['mime'] == 'image/gif') 
    $image = imagecreatefromgif($filename);

elseif ($info['mime'] == 'image/png') 
    $image = imagecreatefrompng($filename);

$dst = imagecreatetruecolor($w, $h);
imagecopyresampled($dst, $image, 0, 0, 0, 0, $w, $h, $width, $height);
ob_start();
$result = imagejpeg($dst, $filename, 100);
$output = base64_encode(ob_get_contents());
ob_end_clean();

echo "ok resize\n";


//send ftp start
$ftp_server = "iup.cdn1.cafe24.com";
$ftp_port = 21;
$ftp_user_name = "loonyhyun";
$ftp_user_pass = "bodigad0";
$ftp_send_file = $filename;
//$ftp_send_file = "/www/tpm";
$ftp_remote_file = $player_id.".jpg";

try{

    // FTP서버 접속
    $conn_id = ftp_connect($ftp_server, $ftp_port);

    // FTP서버 접속 실패한 경우 Exception 처리
    if($conn_id == false){
        throw new Exception("[IP:".$ftp_server.":".$ftp_port ."] FTP 서버 접속 실패");
    }else{
        print_r("FTP 서버 접속 성공.\n");
    }

    // FTP서버 로그인
    $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

    // FTP서버 로그인 실패한 경우 Exception 처리
    if($login_result == false){
        throw new Exception("[IP:".$ftp_server.":".$ftp_port ."], [USER:".$ftp_user_name."] 로그인 실패");
    }else{
        print_r("FTP 서버 로그인 성공.\n");
    }

    if(ftp_chdir($conn_id, "/www/tpm/player/")){
        print_r("FTP 폴더 이동. \n");
    }else{
        print_r("FTP 폴더 이동 실패.\n");
    }

    // 패시브 모드 설정
    ftp_pasv($conn_id, true);

    // FTP 서버에 파일 전송
    if (ftp_put($conn_id, $ftp_remote_file, $ftp_send_file, FTP_BINARY)) {
        print_r("파일 전송 (ftp) -> UPLOAD 성공\n");
    } else {
    // FTP서버 파일 전송 실패한 경우 Exception 처리
        throw new Exception("파일 전송 (ftp:".$conn_id.",".$ftp_remote_file.",".$ftp_send_file.") -> UPLOAD 실패");
    }

    // FTP 서버와 연결 끊음
    ftp_close($conn_id);
    fclose($ftp_send_file); 

} catch(Exception $e) {

    fclose($ftp_send_file); 
    // FTP 서버와 연결 끊음
    ftp_close($conn_id);
    print_r($e->getMessage());
}

//unlink($filename);

?>