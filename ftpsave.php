<?php

$ftp_server = "iup.cdn1.cafe24.com";
$ftp_port = 21;
$ftp_user_name = "loonyhyun";
$ftp_user_pass = "bodigad0";
$ftp_send_file = "./upload/" + $_REQUEST["filename"];
//$ftp_send_file = "/www/tpm";
$ftp_remote_file = $_REQUEST["filename"];

try{

    // FTP서버 접속
    $conn_id = ftp_connect($ftp_server, $ftp_port);

    // FTP서버 접속 실패한 경우 Exception 처리
    if($conn_id == false){
        throw new Exception("[IP:".$ftp_server.":".$ftp_port ."] FTP 서버 접속 실패");
    }else{
        print_r("FTP 서버 접속 성공.<br>");
    }

    // FTP서버 로그인
    $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

    // FTP서버 로그인 실패한 경우 Exception 처리
    if($login_result == false){
        throw new Exception("[IP:".$ftp_server.":".$ftp_port ."], [USER:".$ftp_user_name."] 로그인 실패");
    }else{
        print_r("FTP 서버 로그인 성공.<br>");
    }

    if(ftp_chdir($conn_id, "/www/tpm/")){
        print_r("FTP 폴더 이동. <br>");
    }else{
        print_r("FTP 폴더 이동 실패.<br>");
    }

    // 패시브 모드 설정
    ftp_pasv($conn_id, true);

    // FTP 서버에 파일 전송
    if (ftp_put($conn_id, $ftp_remote_file, $ftp_send_file, FTP_BINARY)) {
        print_r("파일 전송 (ftp) -> UPLOAD 성공");
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

?>