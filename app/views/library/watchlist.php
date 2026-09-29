<?php
$title = 'Danh sách xem';
require __DIR__ . '/../layouts/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <p class="section-title">Thư viện</p>
        <h2 class="h3 mb-0">Danh sách xem của bạn</h2>
      </div>
      <button class="btn btn-ghost">Sắp xếp mới nhất</button>
    </div>
  </div>
</section>

<div class="container">
  <div class="row g-3">
    <?php foreach ($movies as $movie): ?>
      <?php $poster = $movie['poster_url'] ?: 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=600&q=80'; ?>
      <div class="col-md-6">
        <div class="panel movie-card">
          <div class="movie-poster" style="background-image: url('<?php echo e($poster); ?>');"></div>
          <div>
            <h5 class="mb-1"><?php echo e($movie['title_en'] ?: $movie['title_vi']); ?></h5>
            <div class="movie-meta"><?php echo e($movie['year']); ?> · <?php echo e($movie['genre']); ?></div>
            <div class="d-flex gap-2 mt-2">
              <button class="btn btn-sm btn-outline-dark">Ghi log</button>
              <button class="btn btn-sm btn-ghost">Xóa</button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
