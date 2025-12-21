<?php
session_start();

include 'setting.php';

function insertPlayer(PDO $pdo, array $data): int
{
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

    return (int)$pdo->lastInsertId();
}

function updatePlayer(PDO $pdo, array $data): int
{
    $sql = "
        UPDATE fbm_player
        SET
            name = :name,
            birth_year = :year,
            position = :position,
            del_yn = :delYn
        WHERE player_id = :playerId
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'playerId' => $data['playerId'],
        'name' => $data['name'],
        'year' => $data['year'] ?? null,
        'position' => $data['position'],
        'delYn' => $data['delYn'] ?? 'N'
    ]);

    return 1;
}

function deletePlayer(PDO $pdo, array $data): int
{
    $sql = "
        UPDATE fbm_player
        SET
            del_yn = :delYn
        WHERE player_id = :playerId
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'playerId' => $data['playerId'],
        'delYn' => $data['delYn'] ?? 'N'
    ]);

    return 1;
}

?>