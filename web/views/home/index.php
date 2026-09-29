<?php
$title = 'Home';
require __DIR__ . '/../layouts/header.php';
?>

<main class="home-root">
  <section class="home-hero hero-compact">
    <div class="container">
      <div class="hero-flex">
        <div class="hero-left">
          <p class="section-title">CineDiary</p>
          <h1 class="hero-title">Ghi lại, đánh giá và khám phá phim theo cách của bạn</h1>
          <p class="text-muted">Tạo nhật ký xem, lưu list cá nhân và theo dõi hoạt động bạn bè.</p>

          <form class="nav-search" action="<?php echo url('/discover'); ?>" method="get" role="search">
            <input name="q" placeholder="Tìm phim, đạo diễn, danh sách..." aria-label="Tìm phim" />
          </form>

          <div class="hero-cta">
            <a class="btn btn-accent" href="<?php echo url('/register'); ?>">Bắt đầu miễn phí</a>
            <a class="btn btn-ghost" href="<?php echo url('/login'); ?>">Đăng nhập</a>
          </div>
        </div>
        <div class="hero-right">
          <div class="hero-card panel">
            <h4 class="mb-2">Phim gợi ý hôm nay</h4>
            <div class="poster-swatch">
              <?php if (!empty($movies)): ?>
                <?php foreach (array_slice($movies, 0, 6) as $m): ?>
                  <?php $p = $m['poster_url'] ?: 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=600&q=80'; ?>
                  <div class="swatch-item" style="background-image: url('<?php echo e($p); ?>');" title="<?php echo e($m['title_en'] ?: $m['title_vi']); ?>"></div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <p class="text-muted mt-2">Khám phá theo thể loại, điểm số và sở thích.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="container content-grid">
    <div class="grid-left">
      <div class="section-head">
        <h2 class="h3">Mới & Nổi</h2>
        <p class="text-muted">Bộ sưu tập poster — rê chuột để xem nhanh thông tin.</p>
      </div>

      <div class="poster-grid">
        <?php if (empty($movies)): ?>
          <div class="panel">Chưa có phim để hiển thị.</div>
        <?php else: ?>
          <?php foreach ($movies as $movie): ?>
            <?php $poster = $movie['poster_url'] ?: 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=600&q=80'; ?>
            <article class="poster-item">
              <a href="<?php echo url('/movies/' . $movie['id']); ?>" class="poster-link" aria-label="<?php echo e($movie['title_en'] ?: $movie['title_vi']); ?>">
                <div class="poster" style="background-image: url('<?php echo e($poster); ?>');"></div>
                <div class="poster-overlay">
                  <div class="overlay-left">
                    <h4 class="poster-title"><?php echo e($movie['title_en'] ?: $movie['title_vi']); ?></h4>
                    <div class="poster-meta"><?php echo e($movie['year']); ?> · <?php echo e($movie['genre']); ?></div>
                  </div>
                  <div class="overlay-right">
                    <span class="badge-rating"><?php echo e((string) $movie['avg_rating']); ?></span>
                  </div>
                </div>
              </a>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <aside class="grid-right">
      <div class="panel">
        <h4>Hoạt động gần đây</h4>
        <div class="timeline">
          <div class="timeline-item">Nova Gray đăng đánh giá 4.5 — Silent Harbor</div>
          <div class="timeline-item">Avery tạo danh sách: Midnight Thrillers</div>
          <div class="timeline-item">Kai cập nhật nhật ký: Night Circuit</div>
        </div>
      </div>

      <div class="panel mt-3">
        <h4>Danh sách nổi bật</h4>
        <ul class="list-links">
          <li><a href="<?php echo url('/lists'); ?>">Best of 2025</a></li>
          <li><a href="<?php echo url('/lists'); ?>">Neo-Noir Essentials</a></li>
          <li><a href="<?php echo url('/lists'); ?>">Weekend Watchlist</a></li>
        </ul>
      </div>
    </aside>
  </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
