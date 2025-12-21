<?php 
    include 'httpsetting.php';

    function stats(PDO $pdo, array $data){
        $sql = "SELECT
            COALESCE(p.name, m.name) AS player_name,
            COUNT(DISTINCT mp.match_id) AS match_cnt,
            IFNULL(g.goal_cnt, 0) AS goal_cnt,
            IFNULL(a.assist_cnt, 0) AS assist_cnt,
            (
            SUM(
                CASE
                WHEN mg.home_team_id = mp.team_id AND mg.home_score > mg.away_score THEN 3
                WHEN mg.away_team_id = mp.team_id AND mg.away_score > mg.home_score THEN 3
                WHEN mg.home_score = mg.away_score THEN 1
                ELSE 0
                END
            )
            ) AS total_point,
            ROUND(
            SUM(
                CASE
                WHEN (mg.home_team_id = mp.team_id AND mg.home_score > mg.away_score)
                    OR (mg.away_team_id = mp.team_id AND mg.away_score > mg.home_score)
                THEN 1 ELSE 0
                END
            ) / COUNT(DISTINCT mp.match_id) * 100
            , 1) AS win_rate
        FROM fbm_match_player mp
        JOIN fbm_match_game mg ON mp.match_id = mg.match_id
        LEFT JOIN fbm_player p ON mp.player_id = p.player_id
        LEFT JOIN fbm_mercenary_player m ON mp.mercenary_id = m.mercenary_id
        LEFT JOIN (
            SELECT player_id, mercenary_id, COUNT(*) goal_cnt
            FROM fbm_goal
            GROUP BY player_id, mercenary_id
        ) g ON mp.player_id = g.player_id or mp.mercenary_id = g.mercenary_id
        LEFT JOIN (
            SELECT player_id, mercenary_id, COUNT(*) assist_cnt
            FROM fbm_goal_assist
            GROUP BY player_id, mercenary_id
        ) a ON mp.player_id = a.player_id or mp.mercenary_id = a.mercenary_id
        WHERE p.del_yn = 'N'
        ";

        if(! empty($data["from"]) ){
            $sql = $sql." AND match_date >= '".$data["from"]."' ";
        }
        if(! empty($data["to"]) ){
            $sql = $sql." AND match_date <= '".$data["to"]."' ";
        }

        $sql = $sql."
        GROUP BY mp.player_id, mp.mercenary_id
        ORDER BY player_name, total_point DESC, goal_cnt DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

?>