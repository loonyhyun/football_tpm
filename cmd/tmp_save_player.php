<?php
session_start();

include 'setting.php';

try {
    
    $sql = "
        INSERT INTO fbm_player
        (name, birth_year, position, del_yn)
        VALUES
        (:name, :year, :position, 'N')
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'name' => $data['name'],
        'year' => $data['year'] ?? null,
        'position' => $data['position']
    ]);

    echo json_encode([
        'success' => true,
        'count' => $pdo->lastInsertId()
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

?>