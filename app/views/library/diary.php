<?php
$title = 'Nhật ký';
require __DIR__ . '/../layouts/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <p class="section-title">Nhật ký</p>
        <h2 class="h3 mb-0">Log gần đây</h2>
      </div>
      <button class="btn btn-accent">Thêm log</button>
    </div>
  </div>
</section>

<div class="container">
  <div class="panel mb-4">
    <h5 class="mb-3">Ghi nhanh</h5>
    <form class="row g-2">
      <div class="col-md-4">
        <input class="form-control" type="text" placeholder="Tên phim">
      </div>
      <div class="col-md-3">
        <input class="form-control" type="date">
      </div>
      <div class="col-md-2">
        <select class="form-select">
          <option>5.0</option>
          <option>4.5</option>
          <option>4.0</option>
        </select>
      </div>
      <div class="col-md-3">
        <button class="btn btn-ghost w-100" type="button">Lưu log</button>
      </div>
    </form>
  </div>

  <div class="timeline">
    <?php foreach ($entries as $entry): ?>
      <div class="timeline-item">
        <div class="d-flex justify-content-between">
          <strong><?php echo e($entry['movie']); ?></strong>
          <span class="rating-pill"><?php echo e((string) $entry['rating']); ?>/5</span>
        </div>
        <div class="small text-muted"><?php echo e($entry['date']); ?></div>
        <div><?php echo e($entry['note']); ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
