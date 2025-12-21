<?php include '_header.php'; ?>

<h4>경기 등록</h4>

<form method="post" action="write_action.php">
<div class="card mb-3">
  <div class="card-header">기본 정보</div>
  <div class="card-body row g-3">
    <div class="col-md-4">
      <label class="form-label">경기 날짜</label>
      <input type="date" name="match_date" class="form-control" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">홈팀</label>
      <select name="home_team_id" class="form-select">
        <?php foreach($teams as $t): ?>
          <option value="<?= $t['team_id'] ?>"><?= $t['team_name'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">어웨이팀</label>
      <select name="away_team_id" class="form-select">
        <?php foreach($teams as $t): ?>
          <option value="<?= $t['team_id'] ?>"><?= $t['team_name'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
</div>

<div class="text-end">
  <button class="btn btn-primary">경기 생성</button>
</div>
</form>

<?php include '_footer.php'; ?>
