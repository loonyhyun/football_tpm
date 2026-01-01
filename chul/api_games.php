<?php

header('Content-Type: application/json; charset=utf-8');

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://soccer-fc.vercel.app/api/games");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // SSL 검증
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

$response = curl_exec($ch);

if ($response === false) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => curl_error($ch)
    ], JSON_UNESCAPED_UNICODE);
    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo $response;
?>