<?php
$title = 'Dang review';
require __DIR__ . '/../layouts/header.php';
$authUser = Auth::user() ?? [];
$currentUserId = (int) ($authUser['id'] ?? 0);
?>

<div class="container review-page">
  <div class="review-header">
    <div>
      <p class="section-title">Review</p>
      <h2 class="review-title-lg">Danh gia phim</h2>
    </div>
    <a class="btn btn-ghost" href="<?php echo url('/discover'); ?>">Xem bai dang</a>
  </div>

  <?php if (Auth::check()): ?>
    <div class="review-layout">
      <aside class="review-sidebar">
        <div class="review-empty" id="review-empty-state">
          <div class="review-empty-icon" aria-hidden="true">
            <svg viewBox="0 0 64 64" role="img" aria-label="Film icon">
              <rect x="10" y="16" width="44" height="32" rx="6" fill="currentColor" opacity="0.2" />
              <rect x="14" y="20" width="36" height="24" rx="4" fill="currentColor" />
              <rect x="18" y="24" width="6" height="6" rx="2" fill="#111111" />
              <rect x="28" y="24" width="6" height="6" rx="2" fill="#111111" />
              <rect x="38" y="24" width="6" height="6" rx="2" fill="#111111" />
              <rect x="18" y="34" width="6" height="6" rx="2" fill="#111111" />
              <rect x="28" y="34" width="6" height="6" rx="2" fill="#111111" />
              <rect x="38" y="34" width="6" height="6" rx="2" fill="#111111" />
            </svg>
          </div>
          <div class="review-empty-title">Chua chon phim</div>
          <div class="review-empty-text">Vui long chon mot bo phim de viet danh gia</div>
          <button class="btn review-empty-btn" type="button" id="review-pick-movie">Chon phim ngay</button>
        </div>
        <div class="review-poster-shell">
          <span class="review-poster-badge">PHIM DA CHON</span>
          <img id="review-movie-poster" src="https://via.placeholder.com/400x600?text=Poster" alt="Poster" />
        </div>
        <div class="review-movie-info">
          <div id="review-movie-title" class="review-movie-title">Chon phim de xem thong tin</div>
          <div id="review-movie-meta" class="review-movie-meta">Nam san xuat • The loai</div>
        </div>
        <button class="btn review-change-btn" type="button">Thay doi phim</button>
      </aside>

      <div class="review-form-card">
        <div class="review-form-head mb-4">
          <p class="section-title mb-1">Tạo đánh giá</p>
          <h3 class="review-form-title mb-2">Viết cảm nhận của bạn</h3>
          <p class="text-muted mb-0">Chọn phim ở bên trái, sau đó nhập tiêu đề, nội dung và điểm số.</p>
        </div>
        <form id="review-form" method="post" action="#" enctype="multipart/form-data">
          <div class="review-field">
            <label class="form-label">Chon phim</label>
            <select class="form-select" name="movie_id" id="review-movie-select" required>
              <option value="">-- Chon phim --</option>
              <?php foreach (($movieOptions ?? []) as $movie): ?>
                <?php
                  $poster = $movie['poster_url'] ?? '';
                  if ($poster !== '' && stripos($poster, 'http') !== 0) {
                    if ($poster[0] !== '/') {
                      $poster = '/uploads/posters/' . ltrim($poster, '/');
                    }
                    $poster = url($poster);
                  }
                  if ($poster === '') {
                    $poster = 'https://via.placeholder.com/400x600?text=Poster';
                  }
                  $title = $movie['title_en'] ?: $movie['title_vi'];
                  $meta = trim((string) (($movie['year'] ?? '') . ' • ' . ($movie['genre'] ?? '')));
                ?>
                <option
                  value="<?php echo e((string) $movie['id']); ?>"
                  data-poster="<?php echo e($poster); ?>"
                  data-title="<?php echo e($title); ?>"
                  data-meta="<?php echo e($meta); ?>"
                >
                  <?php echo e($title); ?> (<?php echo e((string) $movie['year']); ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="review-field">
            <label class="form-label">Tieu de danh gia</label>
            <input class="form-control review-input-title" type="text" name="title" placeholder="Tieu de danh gia cua ban..." required>
          </div>
          <div class="review-field">
            <label class="form-label">Noi dung</label>
            <textarea class="form-control review-input-body" name="body" rows="7" placeholder="Hay chia se cam nhan cua ban ve bo phim nay..." required></textarea>
          </div>
          <div class="review-field">
            <label class="form-label">Diem so</label>
            <div class="rating-stars rating-stars--lg">
              <?php for ($i = 10; $i >= 1; $i--): ?>
                <?php $value = $i / 2; ?>
                <input type="radio" id="rating-<?php echo $i; ?>" name="rating" value="<?php echo $value; ?>" <?php echo $i === 10 ? 'checked' : ''; ?> required>
                <label class="<?php echo $i % 2 === 0 ? 'star' : 'star half'; ?>" for="rating-<?php echo $i; ?>">&#9733;</label>
              <?php endfor; ?>
            </div>
            <div class="small text-muted">Ho tro 0.5 buoc (vi du: 4.5).</div>
          </div>
          <div class="review-field">
            <label class="form-label">Hinh minh hoa (tuy chon)</label>
            <label class="btn btn-outline-dark review-upload-btn" for="review-image-input">Them hinh anh</label>
            <input class="review-upload-input" id="review-image-input" type="file" name="image" accept="image/*">
            <div class="small text-muted">Ho tro JPG, PNG, WEBP, GIF.</div>
            <div class="review-preview" id="review-image-preview" hidden>
              <img src="" alt="Preview" />
            </div>
          </div>
          <div class="review-actions">
            <button class="btn btn-accent" type="submit">Dang danh gia</button>
            <a class="btn btn-outline-dark" href="<?php echo url('/discover'); ?>">Huy</a>
          </div>
        </form>
      </div>
    </div>
  <?php else: ?>
    <div class="panel">
      <p class="text-muted">Dang nhap de tao review.</p>
      <a class="btn btn-ghost" href="<?php echo url('/login'); ?>">Dang nhap</a>
    </div>
  <?php endif; ?>
</div>

<script>
const reviewForm = document.getElementById('review-form');
const movieSelect = document.getElementById('review-movie-select');
const moviePoster = document.getElementById('review-movie-poster');
const movieTitle = document.getElementById('review-movie-title');
const movieMeta = document.getElementById('review-movie-meta');
const reviewSidebar = document.querySelector('.review-sidebar');
const emptyState = document.getElementById('review-empty-state');
const pickMovieBtn = document.getElementById('review-pick-movie');
const imageInput = document.getElementById('review-image-input');
const imagePreview = document.getElementById('review-image-preview');

movieSelect?.addEventListener('change', function () {
  const option = this.selectedOptions[0];
  if (!option) {
    return;
  }
  if (reviewSidebar) {
    reviewSidebar.classList.add('has-movie');
  }
  const poster = option.dataset.poster || 'https://via.placeholder.com/400x600?text=Poster';
  const title = option.dataset.title || 'Chon phim de xem thong tin';
  const meta = option.dataset.meta || 'Nam san xuat • The loai';
  if (moviePoster) {
    moviePoster.src = poster;
  }
  if (movieTitle) {
    movieTitle.textContent = title;
  }
  if (movieMeta) {
    movieMeta.textContent = meta;
  }
});

pickMovieBtn?.addEventListener('click', function () {
  movieSelect?.focus();
});

imageInput?.addEventListener('change', function () {
  const file = this.files && this.files[0];
  if (!file || !imagePreview) {
    return;
  }
  const img = imagePreview.querySelector('img');
  if (!img) {
    return;
  }
  img.src = URL.createObjectURL(file);
  imagePreview.hidden = false;
});

reviewForm?.addEventListener('submit', function () {
  const select = this.querySelector('select[name="movie_id"]');
  if (!select || !select.value) {
    return;
  }
  this.action = '<?php echo url('/movies/'); ?>' + select.value + '/reviews';
});
</script>

<script>
document.querySelectorAll('.review-card--feed').forEach((card) => {
  card.addEventListener('click', (event) => {
    if (event.target.closest('a, button, form')) {
      return;
    }
  });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
