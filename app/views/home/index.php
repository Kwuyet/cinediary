<?php
$title = 'Home';
require __DIR__ . '/../layouts/header.php';

$heroMovie = $movies[0] ?? null;
$fallbackPoster = 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=900&q=80';
$heroPoster = url('/public/assets/img/hero.jpg');
$heroTitle = $heroMovie['title_en'] ?? $heroMovie['title_vi'] ?? 'Featured film';
$heroMeta = trim((string) (($heroMovie['year'] ?? '') . ' · ' . ($heroMovie['genre'] ?? '')));
$heroRating = $heroMovie['avg_rating'] ?? '';
$heroSynopsis = $heroMovie['synopsis'] ?? '';

if (!function_exists('format_vnd')) {
  function format_vnd($value) {
    return number_format((float) $value, 0, ',', '.') . ' đ';
  }
}
?>

<main class="imdb-home">
  <section class="hero-simple">
    <div class="hero-backdrop" style="background-image: url('<?php echo e($heroPoster); ?>');"></div>
    <div class="hero-overlay"></div>
    <div class="container hero-container">
      <div class="hero-content">
        <p class="hero-director">Nơi mỗi bộ phim trở thành một phần ký ức của bạn</p>
        <a class="btn hero-cta" href="<?php echo url('/discover'); ?>">Khám phá ngay</a>
      </div>
    </div>
  </section>

  <section class="imdb-section">
    <div class="container">
      <div class="imdb-section-head">
        <div>
          <h2 class="h4">CHỌN LỌC CHO BẠN</h2>
        </div>
      </div>
      <div class="imdb-row">
        <?php foreach (array_slice($movies, 0, 6) as $movie): ?>
          <?php $poster = $movie['poster_url'] ?: $fallbackPoster; ?>
          <?php $avg = number_format((float) ($movie['avg_rating'] ?? 0), 1); $rc = (int) ($movie['review_count'] ?? 0); ?>
          <?php $ticketPrice = (int) ($movie['ticket_price'] ?? 0); ?>
          <?php $movieId = (int) $movie['id']; ?>
          <article class="imdb-card">
            <button class="imdb-card-btn" type="button"
              data-id="<?php echo $movieId; ?>"
              data-title="<?php echo e($movie['title_en'] ?: $movie['title_vi']); ?>"
              data-year="<?php echo e((string) $movie['year']); ?>"
              data-genre="<?php echo e($movie['genre']); ?>"
              data-duration="<?php echo e((string) ($movie['duration_min'] ?? '')); ?>"
              data-director="<?php echo e($movie['director']); ?>"
              data-ticket-price="<?php echo e((string) $ticketPrice); ?>"
              data-trailer="<?php echo e($movie['trailer_url'] ?? ''); ?>"
              data-cast="<?php echo e($movie['cast_list']); ?>"
              data-synopsis="<?php echo e($movie['synopsis']); ?>"
              data-poster="<?php echo e($poster); ?>"
              data-rating="<?php echo $avg; ?>"
              data-reviews="<?php echo $rc; ?>"
              data-review-items="<?php echo e(json_encode($movieReviews[$movieId] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>"
            >
              <div class="imdb-card-poster poster" style="background-image: url('<?php echo e($poster); ?>');"></div>
              <div class="imdb-card-overlay">
                <div class="imdb-card-overlay-inner">
                  <div class="imdb-card-title"><?php echo e($movie['title_en'] ?: $movie['title_vi']); ?></div>
                  <div class="imdb-card-meta"><?php echo e((string) $movie['year']); ?> · <?php echo e($movie['genre']); ?></div>
                  <div class="imdb-card-price">Giá vé: <?php echo format_vnd($ticketPrice); ?></div>
                  <div class="imdb-card-rating">
                    <div class="stars" data-rating="<?php echo $avg; ?>"></div>
                    <div class="rating-text"><?php echo $avg; ?>/5 · <?php echo $rc; ?> đánh giá</div>
                  </div>
                </div>
              </div>
            </button>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="imdb-section">
    <div class="container">
      <div class="imdb-section-head">
        <div>
          <h2 class="h4">ĐANG HOT TỪ CỘNG ĐỒNG</h2>
        </div>
      </div>
      <div class="imdb-grid">
        <?php foreach (array_slice($movies, 0, 8) as $movie): ?>
          <?php $poster = $movie['poster_url'] ?: $fallbackPoster; ?>
          <?php $ticketPrice = (int) ($movie['ticket_price'] ?? 0); ?>
          <a class="imdb-list-card" href="<?php echo url('/movies/' . (int) $movie['id']); ?>">
            <div class="imdb-list-poster" style="background-image: url('<?php echo e($poster); ?>');"></div>
            <div>
              <h4><?php echo e($movie['title_en'] ?: $movie['title_vi']); ?></h4>
              <p><?php echo e((string) $movie['year']); ?> · <?php echo e($movie['genre']); ?></p>
              <p>Giá vé: <?php echo format_vnd($ticketPrice); ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>

<script>
  window.CINE_DIARY_BASE_URL = <?php echo json_encode(rtrim(url('/'), '/')); ?>;
</script>

  <!-- Movie detail modal -->
  <div id="movieDetailModal" class="movie-modal" aria-hidden="true" style="display:none;">
    <div class="movie-modal-backdrop"></div>
    <div class="movie-modal-dialog">
      <button class="movie-modal-close" aria-label="Close">×</button>
      <div class="movie-modal-body">
        <div class="movie-modal-poster" style="background-image: url('');"></div>
        <div class="movie-modal-info">
          <h2 class="movie-modal-title"></h2>
          <div class="movie-modal-meta"></div>
          <div class="movie-modal-director"><strong>Đạo diễn:</strong> <span class="movie-modal-director-name"></span></div>
          <div class="movie-modal-price"><strong>Giá Vé:</strong> <span class="movie-modal-ticket-price"></span></div>
          <div class="movie-modal-rating"><div class="stars" data-rating="0"></div><div class="movie-modal-rating-num"></div></div>
          <p class="movie-modal-synopsis"></p>
          <div class="movie-modal-community">
            <div class="movie-modal-community-head">
              <h3>Đánh giá từ cộng đồng</h3>
              <span class="movie-modal-community-count"></span>
            </div>
            <div class="movie-modal-community-list"></div>
          </div>
          <div class="movie-modal-actions">
            <a class="btn imdb-btn-outline movie-modal-trailer-link" href="#" target="_blank" style="display:none;">Xem trailer</a>
            <a class="btn imdb-btn-primary movie-detail-link" href="#">Xem chi tiết đầy đủ</a>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
