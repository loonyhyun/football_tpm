<?php 
    include 'httpsetting.php';

    function matchInfo(PDO $pdo, array $data) {
        $sql = "
        SELECT 
            m.match_id,
            date_format(m.match_date, '%Y-%m-%d') match_date,
            m.ground_id,
            g.name as ground_name,
            ht.name AS home_team,
            at.name AS away_team,
            m.home_score,
            m.away_score,
            m.home_own_score,
            m.away_own_score,
            m.match_yn
        FROM fbm_match_game m
        left outer JOIN fbm_team ht ON m.home_team_id = ht.team_id
        left outer JOIN fbm_team at ON m.away_team_id = at.team_id
        left outer join fbm_ground g
        on g.id = m.ground_id
        WHERE m.match_id = :match_id
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['match_id' => $data['match_id']]);

        return $stmt->fetch();
    }

    function player(PDO $pdo, array $data) {
        $sql = "select fmp.player_id
            , fmp.mercenary_id 
            , fp.name player_name
            , fp_m.name player_name_mer
            from fbm_match_player fmp
            join fbm_match_game fmg 
            on fmg.match_id = fmp.match_id 
            left outer join fbm_player fp 
            on fp.player_id = fmp.player_id 
            left outer join fbm_mercenary_player fp_m
            on fp_m.mercenary_id  = fmp.match_player_id 
            where fmp.match_id = :match_id
            and fmp.team_ab = :team_ab
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['match_id' => $data['match_id'], 'team_ab' => $data['team_ab']]);

        return $stmt->fetchAll();
    }

    function goals(PDO $pdo, array $data) {        
        $sql = "SELECT 
                g.goal_id,
                g.minute,
                p.name AS scorer,
                mp.name AS scorer_mp,
                g.is_penalty,
                g.is_own,
                g.quarter,
                (case when fga.assist_id is not null then 'Y' else 'N' end) asst_yn,
                pa.name as asst,
                mpa.name as asst_mp
            FROM fbm_goal g
            join fbm_match_game mg
            on g.match_id = mg.match_id
            left outer JOIN fbm_player p ON g.player_id = p.player_id
            left outer JOIN fbm_mercenary_player mp ON g.mercenary_id = mp.mercenary_id
            left outer join fbm_goal_assist fga ON g.goal_id = fga.goal_id 
            left outer JOIN fbm_player pa ON fga.player_id = pa.player_id
            left outer JOIN fbm_mercenary_player mpa ON fga.mercenary_id = mpa.mercenary_id
            WHERE g.match_id = :match_id
            and (
                (g.player_id in (
                    select fmp.player_id from fbm_match_player fmp 
                    where fmp.match_id = g.match_id
                    and fmp.team_ab = :team_ab
                    )
                    and g.is_own is null)
                or (g.mercenary_id in (
                    select fmp.mercenary_id from fbm_match_player fmp 
                    where fmp.match_id = g.match_id
                    and fmp.team_ab = :team_ab
                    )
                    and g.is_own is null)
                or (g.player_id in (
                    select fmp.player_id from fbm_match_player fmp 
                    where fmp.match_id = g.match_id
                    and fmp.team_ab <> :team_ab
                    ) and g.is_own is not null)
            )
            ORDER BY g.quarter, g.minute
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['match_id' => $data['match_id'], 'team_ab' => $data['team_ab']]);

        return $stmt->fetchAll();
    }
?>