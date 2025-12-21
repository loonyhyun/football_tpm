<?php
session_start();

include 'setting.php';

try {
    
    $sql = "
    select p.player_id
        , p.name
        , p.birth_year
        , group_concat(fpa.alias, ', ') as alias
    from fbm_player p
    left outer join fbm_player_alias fpa 
    on p.player_id = fpa.player_id 
    where p.del_yn = 'N'
    group by p.player_id, p.name, p.birth_year
    order by p.name, p.birth_year 
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