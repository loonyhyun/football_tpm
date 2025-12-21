<?php
session_start();

include 'setting.php';

function insertMatch(PDO $pdo, array $data): int
{
    $sql = "
        INSERT INTO fbm_match_game
        (match_date
        , ground_id
        , home_team_id
        , away_team_id
        , home_score
        , away_score
        , match_yn
        , home_own_score
        , away_own_score)
        VALUES
        (DATE_FORMAT(now(), '%Y-%m-%d')
        , :groundId
        , :teamId
        , :awayTeamId
        , 0, 0
        , :matchYn
        , 0, 0)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'groundId' => $data['groundId'] ?? null,
        'teamId' => $data['teamId'] ?? '1',
        'awayTeamId' => $data['awayTeamId'] ?? null,
        'matchYn' => $data['matchYn'] ?? 'N'
    ]);

    return (int)$pdo->lastInsertId();
}

function deleteMatch(PDO $pdo, array $data): void
{
    $sql = "
        DELETE FROM fbm_match_game
        WHERE match_id = :matchId
    ";

    $pdo->prepare($sql)->execute([
        'matchId' => $data['matchId'],
        'playerId' => $data['playerId']
    ]);
}

function insertMatchPlayer(PDO $pdo, array $data): void
{
    $sql = "
        INSERT INTO fbm_match_player
        (match_id
        , team_id
        , player_id
        , mercenary_id
        , team_ab)
        VALUES
        (:matchId, :teamId, :playerId, :mercenaryId, :team_ab)
    ";

    $pdo->prepare($sql)->execute([
        'matchId' => $data['matchId'],
        'teamId' => $data['teamId'] ?? '1',
        'playerId' => $data['playerId'] ?? null,
        'mercenaryId' => $data['mercenaryId'] ?? null,
        'team_ab' => $data['team_ab'] ?? 'a'
    ]);
}

function deleteMatchPlayer(PDO $pdo, array $data): void
{
    $sql = "
        DELETE FROM fbm_match_player
        WHERE match_player_id = :matchPlayerId
    ";

    $pdo->prepare($sql)->execute([
        'matchPlayerId' => $data['matchPlayerId']
    ]);
}

function insertGoal(PDO $pdo, array $data): int
{
    $sql = "
        INSERT INTO fbm_goal
        (match_id
        , team_id
        , player_id
        , mercenary_id
        , minute
        , quarter
        , is_penalty
        , is_own)
        VALUES
        (:matchId, :teamId, :playerId, :minute, :quarter, :isPenalty, :isOwn)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'matchId' => $data['matchId'],
        'teamId' => $data['teamId'] ?? '1',
        'playerId' => $data['playerId'] ?? null,
        'minute' => $data['minute'] ?? null,
        'quarter' => $data['quarter'] ?? '1',
        'isPenalty' => $data['isPenalty'] ?? null,
        'isOwn' => $data['isOwn'] ?? null
    ]);

    return (int)$pdo->lastInsertId();
}

function deleteGoal(PDO $pdo, array $data): void
{
    $sql = "
        DELETE FROM fbm_goal
        WHERE goal_id = :goalId
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'goalId' => $data['goalId']
    ]);
}


function insertAstt(PDO $pdo, array $data): int
{
    $sql = "
        INSERT INTO fbm_goal_assist
        (match_id
        , goal_id
        , player_id
        , mercenary_id
        , team_id
        , quarter)
        VALUES
        (:matchId, :goalId, :playerId, :mercenaryId, :teamId, :quarter)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'matchId' => $data['matchId'],
        'goalId' => $data['goalId'],
        'playerId' => $data['playerId'] ?? null,
        'mercenaryId' => $data['mercenaryId'] ?? null,
        'teamId' => $data['teamId'] ?? '1',
        'quarter' => $data['quarter'] ?? '1'
    ]);

    return (int)$pdo->lastInsertId();
}

function deleteAsst(PDO $pdo, array $data): void
{
    $sql = "
        DELETE FROM fbm_goal
        WHERE assist_id = :asstId
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'asstId' => $data['asstId']
    ]);
}
?>