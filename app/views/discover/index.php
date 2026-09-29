<?php
$title = 'Discover';
require __DIR__ . '/../layouts/header.php';
$sort = (string) ($sort ?? 'new');
$page = (int) ($page ?? 1);
$totalPages = (int) ($total_pages ?? 1);
$filters = (array) ($filters ?? []);
$authUser = Auth::user() ?? [];
$currentUserId = (int) ($authUser['id'] ?? 0);

$buildUrl = function (array $params = []) use ($sort, $filters): string {
  $query = [
    'sort' => $params['sort'] ?? $sort,
  ];
  foreach (['q', 'director', 'genre', 'rating'] as $key) {
    $query[$key] = $params[$key] ?? ($filters[$key] ?? '');
  }
  if (isset($params['page'])) {
    $query['page'] = (int) $params['page'];
  }
  $query = array_filter($query, fn ($value) => $value !== '' && $value !== null);
  $queryString = http_build_query($query);
  return url('/discover' . ($queryString ? '?' . $queryString : ''));
};
?>

<section class="page-hero">
  <div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
    </div>

    <form class="discover-filter-panel mt-4" method="get" action="<?php echo url('/discover'); ?>">
      <input type="hidden" name="sort" value="<?php echo e($sort); ?>">
      <div class="discover-filter-quick">
        <div class="discover-filter-field discover-filter-field--wide">
          <label class="discover-filter-label">Tên phim</label>
          <div class="discover-filter-input-wrap discover-filter-input-wrap--compact">
            <i class="bi bi-film"></i>
            <input class="form-control discover-filter-input discover-filter-input--compact" type="text" name="q" value="<?php echo e((string) ($filters['q'] ?? '')); ?>" placeholder="Tìm tên phim...">
          </div>
        </div>

        <div class="discover-filter-field discover-filter-field--rating">
          <label class="discover-filter-label">Điểm đánh giá</label>
          <div class="discover-filter-input-wrap discover-filter-input-wrap--compact">
            <i class="bi bi-star-fill"></i>
            <select class="form-select discover-filter-input discover-filter-input--compact" name="rating">
              <option value="">Tất cả</option>
              <?php for ($score = 5.0; $score >= 1.0; $score -= 0.5): ?>
                <?php $scoreValue = number_format($score, 1, '.', ''); ?>
                <option value="<?php echo e($scoreValue); ?>" <?php echo ((string) ($filters['rating'] ?? '')) === $scoreValue ? 'selected' : ''; ?>>
                  ★ Từ <?php echo e(rtrim(rtrim($scoreValue, '0'), '.')); ?>
                </option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <div class="discover-filter-toolbar">
          <div class="discover-filter-chips tag-list">
            <a class="chip <?php echo $sort === 'new' ? 'chip-active' : ''; ?>" href="<?php echo $buildUrl(['sort' => 'new', 'page' => 1]); ?>">Mới nhất</a>
            <a class="chip <?php echo $sort === 'top' ? 'chip-active' : ''; ?>" href="<?php echo $buildUrl(['sort' => 'top', 'page' => 1]); ?>">Điểm cao</a>
          </div>

          <div class="discover-filter-toolbar-actions">
            <button class="btn btn-accent discover-filter-submit discover-filter-submit--compact" type="submit">Lọc</button>
            <a class="btn btn-ghost discover-filter-reset discover-filter-reset--compact" href="<?php echo url('/discover'); ?>">Đặt lại</a>
          </div>
        </div>
      </div>

      <div class="discover-filter-grid discover-filter-grid--compact mt-3">
        <div class="discover-filter-field">
          <label class="discover-filter-label">Tên đạo diễn</label>
          <div class="discover-filter-input-wrap discover-filter-input-wrap--compact">
            <i class="bi bi-person-badge"></i>
            <input class="form-control discover-filter-input discover-filter-input--compact" type="text" name="director" value="<?php echo e((string) ($filters['director'] ?? '')); ?>" placeholder="Ví dụ: James Cameron">
          </div>
        </div>
        <div class="discover-filter-field">
          <label class="discover-filter-label">Thể loại</label>
          <div class="discover-filter-input-wrap discover-filter-input-wrap--compact">
            <i class="bi bi-tags"></i>
            <input class="form-control discover-filter-input discover-filter-input--compact" type="text" name="genre" value="<?php echo e((string) ($filters['genre'] ?? '')); ?>" placeholder="Ví dụ: Action, Drama">
          </div>
        </div>
      </div>

    </form>
  </div>
</section>

<div class="container">
  <div class="row g-4 discover-grid">
    <?php foreach (($reviews ?? []) as $review): ?>
      <?php
        $posterUrl = (string) ($review['poster_url'] ?? '');
        if ($posterUrl !== '' && stripos($posterUrl, 'http') !== 0) {
          if ($posterUrl[0] !== '/') {
            $posterUrl = '/uploads/posters/' . ltrim($posterUrl, '/');
          }
          $posterUrl = url($posterUrl);
        }
        if ($posterUrl === '') {
          $posterUrl = 'https://via.placeholder.com/300x450?text=Poster';
        }
        $ratingValue = (float) ($review['rating'] ?? 0);
        if ($ratingValue >= 4.0) {
          $ratingClass = 'rating-good';
        } elseif ($ratingValue >= 2.5) {
          $ratingClass = 'rating-mid';
        } else {
          $ratingClass = 'rating-low';
        }
      ?>
      <?php $isOwner = $currentUserId > 0 && (int) ($review['user_id'] ?? 0) === $currentUserId; ?>
      <div class="col-lg-4">
        <div class="review-card review-card--compact h-100 d-flex flex-column">
          <a class="text-decoration-none text-reset" href="<?php echo url('/movies/' . (int) $review['movie_id']); ?>">
            <div class="review-card-top">
              <div class="review-poster" style="background-image: url('<?php echo e($posterUrl); ?>');"></div>
              <span class="rating-badge <?php echo $ratingClass; ?>"><?php echo e((string) $review['rating']); ?></span>
            </div>
            <div class="review-card-body">
              <div class="review-movie-title"><?php echo e($review['movie_title_en'] ?: $review['movie_title_vi']); ?></div>
              <div class="review-title"><?php echo e($review['title']); ?></div>
              <p class="review-excerpt">
                <?php echo e($review['excerpt']); ?><?php echo strlen((string) $review['excerpt']) >= 220 ? '...' : ''; ?>
              </p>
              <div class="review-meta">Bởi <?php echo e($review['display_name']); ?> · <?php echo e($review['created_at']); ?></div>
            </div>
          </a>
          <div class="d-flex flex-wrap gap-2 mt-auto pt-3">
            <a class="btn btn-sm btn-outline-dark" href="<?php echo url('/movies/' . (int) $review['movie_id']); ?>">Xem phim</a>
            <?php if ($isOwner): ?>
              <a class="btn btn-sm btn-outline-secondary" href="<?php echo url('/reviews/' . (int) $review['id'] . '/edit'); ?>">Sửa</a>
              <form method="post" action="<?php echo url('/reviews/' . (int) $review['id'] . '/delete'); ?>" onsubmit="return confirm('Bạn chắc chắn muốn xóa đánh giá này?');">
                <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php if ($totalPages > 1): ?>
    <div class="d-flex justify-content-center align-items-center gap-2 mt-4 discover-pagination">
      <a class="btn btn-outline-dark <?php echo $page <= 1 ? 'disabled' : ''; ?>" href="<?php echo $buildUrl(['page' => max(1, $page - 1)]); ?>">Truoc</a>
      <span class="page-indicator">Trang <?php echo (int) $page; ?> / <?php echo (int) $totalPages; ?></span>
      <a class="btn btn-outline-dark <?php echo $page >= $totalPages ? 'disabled' : ''; ?>" href="<?php echo $buildUrl(['page' => min($totalPages, $page + 1)]); ?>">Sau</a>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
