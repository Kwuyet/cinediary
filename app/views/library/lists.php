<?php
$title = 'Danh sách của bạn';
$bodyClass = 'lists-page';
require __DIR__ . '/../layouts/header.php';

$lists = $lists ?? [];

if (!function_exists('listPageCoverUrl')) {
  function listPageCoverUrl(string $title): string
  {
    $palette = [
      'linear-gradient(135deg, #1f2937, #0f172a)',
      'linear-gradient(135deg, #111827, #1f2937)',
      'linear-gradient(135deg, #1c1917, #3f3f46)',
      'linear-gradient(135deg, #1b1b1b, #27272a)',
      'linear-gradient(135deg, #0f172a, #312e81)',
      'linear-gradient(135deg, #111111, #3b2f2f)',
    ];
    $index = abs(crc32($title)) % count($palette);
    return $palette[$index];
  }
}
?>

<main class="lists-page-shell">
  <section class="lists-hero">
    <div class="container">
      <div class="lists-hero-banner">
        <div class="lists-hero-copy lists-hero-copy--center">
          <p class="lists-kicker">Bộ sưu tập cá nhân</p>
          <h1 class="lists-title">Danh sách của bạn</h1>
          <p class="lists-description">Tổ chức bộ sưu tập phim yêu thích của bạn một cách thanh lịch và cá nhân hóa.</p>
          <div class="lists-hero-actions lists-hero-actions--center">
            <a class="btn btn-accent lists-primary-cta lists-primary-cta--neon" href="#create-list">
              <i class="bi bi-plus-lg"></i>
              <span>Tạo danh sách mới</span>
            </a>
            <a class="btn btn-ghost lists-secondary-cta" href="<?php echo url('/discover'); ?>">
              <i class="bi bi-compass"></i>
              <span>Khám phá phim</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="lists-main-section">
    <div class="container">
      <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?php echo e($error); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?php if ($success = flash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?php echo e($success); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="lists-create-card" id="create-list">
        <div class="lists-create-header">
          <div>
            <h2 class="lists-create-title">Tạo danh sách mới</h2>
          </div>
        </div>

        <form class="lists-create-form" method="POST" action="<?php echo url('/lists'); ?>">
          <div class="lists-form-field">
            <label class="form-label lists-form-label" for="list-title">Tên danh sách</label>
            <input id="list-title" class="form-control lists-input" type="text" name="title" placeholder="Ví dụ: Phim xem cuối tuần, Neo-noir favourites..." required>
          </div>

          <div class="lists-form-field">
            <label class="form-label lists-form-label" for="list-description">Mô tả danh sách</label>
            <textarea id="list-description" class="form-control lists-textarea" name="description" rows="4" placeholder="Ví dụ: Những phim dịu dàng cho một đêm yên tĩnh..."></textarea>
          </div>

          <div class="lists-form-grid">
            <div class="lists-form-field">
              <label class="form-label lists-form-label" for="list-visibility">Độ riêng tư</label>
              <div class="lists-select-wrap">
                <i class="bi bi-shield-lock lists-select-icon"></i>
                <select id="list-visibility" class="form-select lists-select" name="visibility">
                  <option value="public">Công khai</option>
                  <option value="private">Riêng tư</option>
                </select>
              </div>
            </div>
          </div>

          <div class="lists-form-actions">
            <button class="btn btn-accent lists-submit" type="submit">
              <i class="bi bi-plus-lg"></i>
              <span>Tạo danh sách mới</span>
            </button>
          </div>
        </form>
      </div>

      <div class="lists-grid-head">
        <div>
          <p class="lists-section-kicker">Bộ sưu tập đã tạo</p>
          <h2 class="lists-grid-title">Danh sách hiện có</h2>
        </div>
        <div class="lists-grid-meta"><?php echo count($lists); ?> danh sách</div>
      </div>

      <?php if (!empty($lists)): ?>
        <div class="lists-grid">
          <?php foreach ($lists as $list): ?>
            <?php
              $coverStyle = listPageCoverUrl((string) ($list['title'] ?? ''));
              $titleInitial = mb_strtoupper(mb_substr((string) ($list['title'] ?? 'L'), 0, 1));
              $itemCount = (int) ($list['item_count'] ?? 0);
              $visibility = (string) ($list['visibility'] ?? '');
            ?>
            <article class="list-card">
              <a class="list-card-cover" href="<?php echo url('/lists/' . (int) $list['id']); ?>" style="background: <?php echo e($coverStyle); ?>;">
                <div class="list-card-cover-overlay"></div>
                <div class="list-card-cover-chip"><?php echo e($visibility); ?></div>
                <div class="list-card-cover-initial"><?php echo e($titleInitial); ?></div>
              </a>
              <div class="list-card-body">
                <div class="list-card-top">
                  <div>
                    <h3 class="list-card-title"><?php echo e($list['title']); ?></h3>
                    <p class="list-card-desc"><?php echo e($list['description'] ?: 'Chưa có mô tả cho danh sách này.'); ?></p>
                  </div>
                  <span class="list-card-lock">
                    <i class="bi bi-<?php echo $visibility === 'Riêng tư' ? 'lock' : 'globe2'; ?>"></i>
                  </span>
                </div>
                <div class="list-card-meta">
                  <span><strong><?php echo $itemCount; ?></strong> phim</span>
                  <span><?php echo e($visibility); ?></span>
                </div>
                <div class="list-card-actions">
                  <a class="btn btn-sm btn-outline-light list-card-open" href="<?php echo url('/lists/' . (int) $list['id']); ?>">Mở danh sách</a>
                  <a class="btn btn-sm btn-ghost list-card-share" href="#create-list">Chỉnh tạo mới</a>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="lists-empty">
          <div class="lists-empty-icon"><i class="bi bi-collection"></i></div>
          <h3>Chưa có danh sách nào</h3>
          <p>Tạo danh sách đầu tiên để lưu lại các bộ phim bạn yêu thích.</p>
          <a class="btn btn-accent" href="#create-list">Tạo danh sách đầu tiên</a>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
