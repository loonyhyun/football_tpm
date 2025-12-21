<?php

include 'setting.php';

try {
    
    $sql = "
    SELECT 
        m.match_id,
        m.match_date,
        m.ground_id,
        g.name as ground_name,
        ht.name AS home_team,
        at.name AS away_team,
        m.home_score,
        m.away_score,
        m.home_own_score,
        m.away_own_score
    FROM fbm_match_game m
    left outer JOIN fbm_team ht ON m.home_team_id = ht.team_id
    left outer JOIN fbm_team at ON m.away_team_id = at.team_id
    left outer join fbm_ground g
    on g.id = m.ground_id
    WHERE 1=1
    ";

    if(! empty($_REQUEST["from"]) ){
        $sql = $sql." AND match_date >= '".$_REQUEST["from"]."' ";
    }
    if(! empty($_REQUEST["to"]) ){
        $sql = $sql." AND match_date <= '".$_REQUEST["to"]."' ";
    }

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