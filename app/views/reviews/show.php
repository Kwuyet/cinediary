<?php
$title = $review['title'] ?: 'Review';
require __DIR__ . '/../layouts/header.php';

if (!function_exists('normalizeReviewMediaUrl')) {
  function normalizeReviewMediaUrl(?string $path, string $fallbackFolder = ''): string
  {
    $path = trim((string) $path);
    if ($path === '') {
      return '';
    }

    if (stripos($path, 'http://') === 0 || stripos($path, 'https://') === 0) {
      return $path;
    }

    if ($path[0] !== '/') {
      $path = $fallbackFolder !== '' ? $fallbackFolder . ltrim($path, '/') : '/' . ltrim($path, '/');
    }

    return url($path);
  }
}

$poster = normalizeReviewMediaUrl($movie['poster_url'] ?? '', '/uploads/posters/');
if ($poster === '') {
  $poster = 'https://via.placeholder.com/400x600?text=Poster';
}

$reviewImage = normalizeReviewMediaUrl($review['image_url'] ?? '', '/uploads/reviews/');
?>

<div class="container py-4">
  <div class="panel review-detail-shell">
    <div class="row g-4 align-items-start">
      <div class="col-lg-4">
        <div class="review-detail-poster" style="background-image: url('<?php echo e($poster); ?>');"></div>
      </div>
      <div class="col-lg-8">
        <p class="section-title">Đánh giá từ cộng đồng</p>
        <h1 class="review-detail-title"><?php echo e($review['title']); ?></h1>
        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
          <a class="review-detail-user" href="<?php echo url('/profile/' . (int) ($review['user_id'] ?? 0)); ?>">
            <?php echo render_avatar($review['display_name'] ?? 'U', $review['avatar_url'] ?? '', ['size' => 42, 'class' => 'review-detail-avatar']); ?>
            <span><?php echo e($review['display_name']); ?></span>
          </a>
          <span class="badge badge-rating"><?php echo e((string) $review['rating']); ?>/5</span>
          <span class="text-muted"><?php echo e((string) ($review['created_at'] ?? '')); ?></span>
        </div>
        <div class="mb-3 text-muted">
          <div><strong>Phim:</strong> <?php echo e($movie['title_en'] ?: $movie['title_vi']); ?></div>
          <div><strong>Năm:</strong> <?php echo e((string) $movie['year']); ?> · <strong>Thể loại:</strong> <?php echo e((string) $movie['genre']); ?></div>
        </div>
        <?php if ($reviewImage !== ''): ?>
          <div class="review-detail-image mb-3">
            <img src="<?php echo e($reviewImage); ?>" alt="Review image">
          </div>
        <?php endif; ?>
        <div class="review-detail-body"><?php echo nl2br(e((string) ($review['body'] ?? ''))); ?></div>
        <div class="d-flex flex-wrap gap-2 mt-4">
          <a class="btn imdb-btn-primary" href="<?php echo url('/movies/' . (int) ($movie['id'] ?? 0)); ?>">Xem phim</a>
          <a class="btn imdb-btn-outline" href="<?php echo url('/movies/' . (int) ($movie['id'] ?? 0)); ?>#reviews">Về danh sách review</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
