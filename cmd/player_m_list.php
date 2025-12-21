<?php
session_start();

include 'setting.php';

try {
    
    $sql = "
    select mercenary_id
        , name
        , position
        , phone
        , memo
    from fbm_mercenary_player p
    order by p.name
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $rows = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'count' => count($rows),
        'data' => $rows
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

?>