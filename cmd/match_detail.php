<?php

include 'setting.php';

try {
    $matchId = $_REQUEST["matchId"];
    
    $sql = "SELECT 
            g.goal_id,
            g.minute,
            p.name AS scorer,
            mp.name AS scorer_mp,
            g.is_penalty,
            g.is_own,
            g.quarter
        FROM fbm_goal g
        join fbm_match_game mg
        on g.match_id = mg.match_id
        left outer JOIN fbm_player p ON g.player_id = p.player_id
        left outer JOIN fbm_mercenary_player mp ON g.mercenary_id = mp.mercenary_id
        WHERE g.match_id = :match_id
        ORDER BY g.minute
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['match_id' => $matchId]);

    $rows1 = $stmt->fetchAll();

    $sql = "SELECT 
            g.assist_id
            , g.goal_id
            , p.name as p_name
            , mp.name AS mp_name
            , g.quarter
        FROM fbm_goal_assist g
        join fbm_match_game mg
        on g.match_id = mg.match_id
        left outer JOIN fbm_player p ON g.player_id = p.player_id
        left outer JOIN fbm_mercenary_player mp ON g.mercenary_id = mp.mercenary_id
        WHERE g.match_id = :match_id
        order by g.goal_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['match_id' => $matchId]);

    $rows1 = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'goal_data' => $rows1,
        'asst_data' => $rows2
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

?>