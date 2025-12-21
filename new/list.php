<?php include '_header.php'; ?>
<?php

include '../cmd/httpsetting.php';

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
    WHERE 1=1
    order by m.match_id desc
    ";

    if(! empty($_REQUEST["from"]) ){
        $sql = $sql." AND match_date >= '".$_REQUEST["from"]."' ";
    }
    if(! empty($_REQUEST["to"]) ){
        $sql = $sql." AND match_date <= '".$_REQUEST["to"]."' ";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $matchList = $stmt->fetchAll();
?>

<h4 class="mb-3">경기 목록</h4>

<table class="table table-hover align-middle">
  <thead class="table-dark">
    <tr>
      <th>날짜</th>
      <th>홈</th>
      <th>스코어</th>
      <th>어웨이</th>
      <th>상태</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($matchList as $m): ?>
    <tr>
      <td><?= $m['match_date'] ?></td>
      <td><?= $m['home_team'] ?></td>
      <td class="fw-bold text-center">
        <?= $m['home_score'] + $m['home_own_score'] ?? 0 ?> : <?= $m['away_score'] + $m['away_own_score'] ?? 0 ?>
      </td>
      <td><?= $m['away_team'] ?></td>
      <td>
        <span class="badge bg-<?= $m['match_yn'] == 'Y'?'danger':'info' ?>">
          <?= $m['match_yn']=='Y'?'매치':'자체' ?>
        </span>
      </td>
      <td>
        <a class="btn btn-sm btn-outline-primary"
           href="view.php?match_id=<?= $m['match_id'] ?>">상세</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php include '_footer.php'; ?>
