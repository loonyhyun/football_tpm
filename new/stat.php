<?php include '_header.php'; ?>

<?php
include '../cmd/stat.php';

$stats = stats($pdo, ['from'=>$_REQUEST['from'] ?? '', 'to'=>$_REQUEST['to'] ?? '']);

?>
<table class="table table-striped table-hover">
<thead class="table-dark">
<tr>
  <th>이름</th>
  <th>경기</th>
  <th>득점</th>
  <th>어시스트</th>
  <th>승점</th>
  <th>승률</th>
</tr>
</thead>
<tbody>
<?php foreach($stats as $s): ?>
<tr>
  <td><?= $s['player_name'] ?></td>
  <td><?= $s['match_cnt'] ?></td>
  <td><?= $s['goal_cnt'] ?></td>
  <td><?= $s['assist_cnt'] ?></td>
  <td class="fw-bold"><?= $s['total_point'] ?></td>
  <td><?= $s['win_rate'] ?>%</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>


<?php include '_footer.php'; ?>
