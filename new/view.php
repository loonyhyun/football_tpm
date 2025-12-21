<?php include '_header.php'; ?>

<?php
include '../cmd/view.php';

$matchInfo = matchInfo($pdo, ['match_id' => $_REQUEST['match_id']]);
$homePlayers = player($pdo, ['match_id' => $_REQUEST['match_id'], 'team_ab' => 'a']);
$awayPlayers = player($pdo, ['match_id' => $_REQUEST['match_id'], 'team_ab' => 'b']);
$homeGoals = goals($pdo, ['match_id' => $_REQUEST['match_id'], 'team_ab' => 'a']);
$awayGoals = goals($pdo, ['match_id' => $_REQUEST['match_id'], 'team_ab' => 'b']);
?>

<div class="card mb-4">
  <div class="card-body text-center">
    <h5><?= $matchInfo['home_team'] ?>
      <span class="mx-3 display-6 fw-bold">
        <?= $matchInfo['home_score'] + $matchInfo['home_own_score'] ?? 0 ?> : <?= $matchInfo['away_score'] + $matchInfo['away_own_score'] ?? 0 ?>
      </span>
      <?= $matchInfo['away_team'] ?>
    </h5>
    <div class="text-muted">
      <?= $matchInfo['match_date'] ?> / <?= $matchInfo['ground_name'] ?>
    </div>
  </div>
</div>

<!-- 출전 선수 -->
<div class="row">
  <div class="col-md-6">
    <h5><?= $matchInfo['match_yn'] == 'Y' ? $matchInfo['home_team'] : '레드' ?> 출전 선수</h5>
    <ul class="list-group mb-3">
      <?php foreach($homePlayers as $p): ?>
        <li class="list-group-item"><?= $p['player_name'] ?? $p['player_name_mer'] ?? 'Unknown' ?></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="col-md-6">
    <h5><?= $matchInfo['match_yn'] == 'Y' ? '상대' : '블루' ?>  출전 선수</h5>
    <ul class="list-group mb-3">
      <?php foreach($awayPlayers as $p): ?>
        <li class="list-group-item"><?= $p['player_name'] ?? $p['player_name_mer'] ?? 'Unknown' ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<!-- 골 기록 -->
<h5 class="mt-4">골 기록</h5>
<div class="row">
  <div class="col-md-6">
    <table class="table table-sm">
      <thead>
        <tr>
          <th>쿼터</th>
          <th>시간</th>
          <th>득점자</th>
          <th>PK</th>
          <th>자책</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($homeGoals as $g): ?>
        <tr>
          <td><?= $g['quarter'] ?></td>
          <td><?= $g['minute'] ?>'</td>
          <td>
              <?= $g['scorer'] ?? $g['scorer_mp'] ?? 'Unknown' ?>
              <?= $g['asst_yn']=='Y' ? ('(어시스트: '.($g['asst'] ?? $g['asst_mp'] ?? 'Unknown').')') : '' ?>
          </td>
          <td><?= $g['is_penalty']?'✔':'' ?></td>
          <td><?= $g['is_own']?'✔':'' ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="col-md-6">
    <table class="table table-sm">
      <thead>
        <tr>
          <th>쿼터</th>
          <th>시간</th>
          <th>득점자</th>
          <th>PK</th>
          <th>자책</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($awayGoals as $g): ?>
        <tr>
          <td><?= $g['quarter'] ?></td>
          <td><?= $g['minute'] ?>'</td>
          <td>
              <?= $g['scorer'] ?? $g['scorer_mp'] ?? 'Unknown' ?>
              <?= $g['asst_yn']=='Y' ? ('(어시스트: '.($g['asst'] ?? $g['asst_mp'] ?? 'Unknown').')') : '' ?>
          </td>
          <td><?= $g['is_penalty']?'✔':'' ?></td>
          <td><?= $g['is_own']?'✔':'' ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>


<?php include '_footer.php'; ?>
