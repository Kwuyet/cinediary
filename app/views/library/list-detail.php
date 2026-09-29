<?php
$title = isset($list['title']) ? 'Danh sách: ' . $list['title'] : 'Chi tiết danh sách';
require __DIR__ . '/../layouts/header.php';

$list = $list ?? [];
$items = $items ?? [];
$movies = $movies ?? [];

if (!function_exists('listDetailMovieLabel')) {
  function listDetailMovieLabel(array $movie): string
  {
    $title = trim((string) ($movie['title_en'] ?? ''));
    if ($title === '') {
      $title = trim((string) ($movie['title_vi'] ?? ''));
    }
    $year = trim((string) ($movie['year'] ?? ''));
    return $year !== '' ? $title . ' (' . $year . ')' : $title;
  }
}

if (!function_exists('listDetailStars')) {
  function listDetailStars(float $rating): string
  {
    $full = (int) floor($rating);
    $half = ($rating - $full) >= 0.5;
    $out = '<span class="list-stars" aria-label="Rating ' . e(number_format($rating, 1)) . '">';
    for ($i = 1; $i <= 5; $i++) {
      if ($i <= $full) {
        $out .= '<span class="is-full">★</span>';
      } elseif ($i === $full + 1 && $half) {
        $out .= '<span class="is-half">★</span>';
      } else {
        $out .= '<span>★</span>';
      }
    }
    return $out . '</span>';
  }
}

if (!function_exists('listDetailPosterUrl')) {
  function listDetailPosterUrl(?string $path): string
  {
    $path = trim((string) $path);
    if ($path === '') {
      return '';
    }
    if (stripos($path, 'http://') === 0 || stripos($path, 'https://') === 0) {
      return $path;
    }
    if ($path[0] !== '/') {
      $path = '/uploads/posters/' . ltrim($path, '/');
    }
    return url($path);
  }
}

$listTitle = (string) ($list['title'] ?? 'Danh sách phim cá nhân');
$listDesc = (string) ($list['description'] ?? 'Không gian riêng để bạn sắp xếp phim theo gu xem của mình.');
if (trim($listDesc) === '') {
  $listDesc = 'Không gian riêng để bạn sắp xếp phim theo gu xem của mình.';
}
$visibility = (string) ($list['visibility'] ?? 'Riêng tư');
$count = count($items);
?>

<style>
  .list-v2-page {
    background: radial-gradient(1000px 480px at 15% 0%, rgba(16, 185, 129, 0.10), transparent 60%),
      radial-gradient(900px 420px at 85% 0%, rgba(56, 189, 248, 0.08), transparent 55%),
      #050b16;
    padding-bottom: 3rem;
  }
  .list-v2-shell {
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 24px;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.90), rgba(2, 6, 23, 0.92));
    box-shadow: 0 24px 54px rgba(2, 6, 23, 0.46);
  }
  .list-v2-hero {
    padding: 1.75rem;
  }
  .list-v2-title {
    margin: 0;
    font-size: clamp(2rem, 3vw, 3rem);
    font-weight: 800;
    color: #f8fafc;
    line-height: 1.08;
  }
  .list-v2-title[contenteditable='true']:focus {
    outline: 2px solid rgba(16, 185, 129, 0.45);
    border-radius: 10px;
    padding: 0.15rem 0.4rem;
  }
  .list-v2-sub {
    margin-top: 0.65rem;
    color: #cbd5e1;
    max-width: 74ch;
  }
  .list-v2-meta {
    display: flex;
    gap: 0.55rem;
    flex-wrap: wrap;
    margin-top: 1rem;
  }
  .list-v2-chip {
    border: 1px solid rgba(148, 163, 184, 0.3);
    background: rgba(15, 23, 42, 0.85);
    color: #e2e8f0;
    border-radius: 999px;
    padding: 0.4rem 0.8rem;
    font-size: 0.85rem;
    font-weight: 600;
  }
  .list-v2-chip.is-public {
    border-color: rgba(16, 185, 129, 0.5);
    color: #a7f3d0;
    background: rgba(16, 185, 129, 0.14);
  }
  .list-v2-actions {
    display: flex;
    gap: 0.65rem;
    flex-wrap: wrap;
    justify-content: flex-end;
  }
  .list-v2-btn {
    border-radius: 12px;
    padding: 0.62rem 1rem;
    font-weight: 700;
    border: 1px solid rgba(148, 163, 184, 0.35);
    color: #e2e8f0;
    background: transparent;
  }
  .list-v2-btn:hover {
    border-color: rgba(226, 232, 240, 0.5);
    color: #fff;
    background: rgba(30, 41, 59, 0.55);
  }
  .list-v2-btn.is-primary {
    border-color: transparent;
    background: #10b981;
    color: #041011;
    box-shadow: 0 16px 28px rgba(16, 185, 129, 0.28);
  }
  .list-v2-btn.is-primary:hover {
    background: #34d399;
    color: #021112;
  }
  .list-v2-quick {
    border-top: 1px solid rgba(148, 163, 184, 0.18);
    padding: 1rem 1.35rem 1.35rem;
  }
  .list-v2-quick-grid {
    display: grid;
    gap: 0.75rem;
    grid-template-columns: 1.4fr 1fr auto;
  }
  .list-v2-control {
    border-radius: 12px;
    border: 1px solid rgba(100, 116, 139, 0.55);
    background: rgba(2, 6, 23, 0.75);
    color: #f1f5f9;
    padding: 0.7rem 0.85rem;
  }
  .list-v2-section-head {
    margin: 1.4rem 0 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.6rem;
  }
  .list-v2-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
  .list-v2-card {
    border: 1px solid rgba(71, 85, 105, 0.5);
    border-radius: 18px;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.95), rgba(2, 6, 23, 0.96));
    box-shadow: 0 14px 30px rgba(2, 6, 23, 0.35);
    overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
  }
  .list-v2-card:hover {
    transform: translateY(-4px);
    border-color: rgba(148, 163, 184, 0.65);
    box-shadow: 0 20px 36px rgba(2, 6, 23, 0.44);
  }
  .list-v2-poster {
    height: 270px;
    background: #0b1220 center / cover no-repeat;
    position: relative;
  }
  .list-v2-rank {
    position: absolute;
    left: 10px;
    top: 10px;
    background: rgba(2, 6, 23, 0.86);
    color: #e2e8f0;
    border-radius: 10px;
    padding: 0.18rem 0.55rem;
    font-weight: 700;
    font-size: 0.82rem;
  }
  .list-v2-card-body {
    padding: 0.9rem;
  }
  .list-v2-movie-title {
    margin: 0;
    color: #fff;
    font-weight: 700;
    font-size: 1.1rem;
  }
  .list-v2-year {
    color: #94a3b8;
    font-size: 0.84rem;
  }
  .list-stars {
    display: inline-flex;
    gap: 1px;
    color: #64748b;
    font-size: 1.08rem;
    line-height: 1;
  }
  .list-stars .is-full,
  .list-stars .is-half {
    color: #fbbf24;
  }
  .list-v2-note {
    color: #cbd5e1;
    font-size: 0.92rem;
    min-height: 38px;
    margin: 0.55rem 0 0.7rem;
  }
  .list-v2-actions-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 0.45rem;
  }
  .list-v2-actions-row .btn {
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 700;
  }
  @media (max-width: 1199px) {
    .list-v2-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width: 767px) {
    .list-v2-quick-grid { grid-template-columns: 1fr; }
    .list-v2-grid { grid-template-columns: 1fr; }
    .list-v2-hero { padding: 1.15rem; }
    .list-v2-quick { padding: 0.95rem; }
  }
</style>

<div class="list-v2-page">
<section class="pt-3">
  <div class="container">
    <div class="list-v2-shell">
      <div class="list-v2-hero">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
          <div>
            <p class="section-title mb-1">Chi tiết danh sách phim</p>
            <h1 class="list-v2-title" contenteditable="true" spellcheck="false"><?php echo e($listTitle); ?></h1>
            <p class="list-v2-sub"><?php echo e($listDesc); ?></p>
            <div class="list-v2-meta">
              <span class="list-v2-chip <?php echo stripos($visibility, 'công') !== false || stripos($visibility, 'cong') !== false ? 'is-public' : ''; ?>"><?php echo e($visibility); ?></span>
              <span class="list-v2-chip"><?php echo (int) $count; ?> phim</span>
            </div>
          </div>
        </div>
      </div>

      <div class="list-v2-quick" id="add-movie">
        <form method="POST" action="<?php echo url('/lists/' . (int) ($list['id'] ?? 0) . '/items'); ?>" class="list-v2-quick-grid">
          <select class="list-v2-control" name="movie_id" required>
            <option value="">Tìm hoặc chọn phim...</option>
            <?php foreach ($movies as $movie): ?>
              <option value="<?php echo (int) $movie['id']; ?>"><?php echo e(listDetailMovieLabel($movie)); ?></option>
            <?php endforeach; ?>
          </select>
          <input class="list-v2-control" type="text" name="note" placeholder="Ghi chú ngắn (tuỳ chọn)">
          <button class="list-v2-btn is-primary" type="submit">+ Thêm</button>
        </form>
      </div>
    </div>
  </div>
</section>

<section class="pt-4">
  <div class="container">
    <div class="list-v2-section-head">
      <h2 class="h3 mb-0 text-white">Phim trong danh sách</h2>
      <span class="list-v2-chip"><?php echo (int) $count; ?> mục</span>
    </div>

    <?php if (!empty($items)): ?>
      <div class="list-v2-grid" id="movie-grid">
        <?php foreach ($items as $movie): ?>
              <?php
                $posterUrl = trim((string) ($movie['poster_url'] ?? ''));
                $movieTitle = trim((string) ($movie['title_en'] ?? ''));
                if ($movieTitle === '') {
                  $movieTitle = trim((string) ($movie['title_vi'] ?? ''));
                }
                $movieYear = (string) ($movie['year'] ?? '');
                $movieInitial = mb_strtoupper(mb_substr($movieTitle !== '' ? $movieTitle : 'M', 0, 1));
                $rating = (float) ($movie['avg_rating'] ?? 0);
                $position = (int) ($movie['position'] ?? 1);
                $posterResolved = listDetailPosterUrl($posterUrl);
              ?>
              <article class="list-v2-card" draggable="true" data-card>
                <div class="list-v2-poster"<?php echo $posterResolved !== '' ? ' style="background-image: url(' . e($posterResolved) . ');"' : ''; ?>>
                  <span class="list-v2-rank">#<?php echo (int) $position; ?></span>
                  <?php if ($posterResolved === ''): ?>
                    <div class="h-100 d-flex align-items-center justify-content-center" style="font-size:2rem;color:#cbd5e1;"><?php echo e($movieInitial); ?></div>
                  <?php endif; ?>
                </div>
                <div class="list-v2-card-body">
                  <h3 class="list-v2-movie-title"><?php echo e($movieTitle); ?></h3>
                  <div class="list-v2-year"><?php echo e($movieYear); ?></div>

                  <div class="d-flex align-items-center gap-2 mt-2">
                    <?php echo listDetailStars($rating); ?>
                    <span class="text-warning fw-semibold"><?php echo e(number_format($rating, 1)); ?>/5</span>
                  </div>

                  <p class="list-v2-note"><?php echo e((string) ($movie['note'] ?: 'Chưa có ghi chú cá nhân.')); ?></p>

                  <div class="list-v2-actions-row">
                    <form method="POST" action="<?php echo url('/lists/' . (int) ($list['id'] ?? 0) . '/items/' . (int) $movie['id'] . '/delete'); ?>" onsubmit="return confirm('Xóa phim này khỏi danh sách?');">
                      <button class="btn btn-outline-danger w-100" type="submit">Xóa</button>
                    </form>
                  </div>
                </div>
              </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="lists-empty list-detail-empty list-v2-shell p-4">
        <div class="lists-empty-icon"><i class="bi bi-film"></i></div>
        <h3>Chưa có phim nào trong danh sách này</h3>
        <p>Chọn một phim ở khối bên trên để bắt đầu xây dựng danh sách của bạn.</p>
      </div>
    <?php endif; ?>
  </div>
</section>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
