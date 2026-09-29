<?php
$title = 'Profile';
require __DIR__ . '/../layouts/header.php';

$displayName = $user['display_name'] ?? 'Member';
$email = $user['email'] ?? '';
$role = $user['role'] ?? 'member';
$bio = $user['bio'] ?? '';
$reviewCount = (int) ($review_count ?? count($reviews ?? []));
$listCount = (int) ($list_count ?? count($lists ?? []));

if (!function_exists('profileTimeAgo')) {
  function profileTimeAgo(?string $dateTime): string
  {
    if (!$dateTime) {
      return 'Vừa xong';
    }

    $timestamp = strtotime($dateTime);
    if ($timestamp === false) {
      return (string) $dateTime;
    }

    $diff = time() - $timestamp;
    if ($diff < 60) {
      return 'Vừa xong';
    }
    if ($diff < 3600) {
      return floor($diff / 60) . ' phút trước';
    }
    if ($diff < 86400) {
      return floor($diff / 3600) . ' giờ trước';
    }
    if ($diff < 604800) {
      return floor($diff / 86400) . ' ngày trước';
    }

    return date('d/m/Y', $timestamp);
  }
}

if (!function_exists('profileRenderStars')) {
  function profileRenderStars(float $rating): string
  {
    $rating = max(0, min(5, $rating));
    $full = (int) floor($rating);
    $half = ($rating - $full) >= 0.5;
    $html = '<span class="profile-stars" aria-label="' . htmlspecialchars(number_format($rating, 1) . ' trên 5', ENT_QUOTES, 'UTF-8') . '">';
    for ($i = 0; $i < 5; $i++) {
      $class = 'profile-star';
      if ($i < $full) {
        $class .= ' is-full';
      } elseif ($half && $i === $full) {
        $class .= ' is-half';
      }
      $html .= '<i class="bi bi-star-fill ' . $class . '"></i>';
    }
    $html .= '</span>';
    return $html;
  }
}
?>

<div class="container profile-page">
  <section class="profile-hero profile-hero--cinematic">
    <div class="profile-hero-top">
      <div class="profile-hero-left">
        <div class="profile-avatar profile-avatar--glow">
          <?php echo render_avatar($displayName, $user['avatar_url'] ?? '', ['size' => 132, 'class' => 'user-avatar--xl']); ?>
        </div>

        <div class="profile-hero-copy">
          <div class="profile-title-row">
            <h1 class="profile-name"><?php echo e($displayName); ?></h1>
            <span class="profile-badge"><?php echo e($role === 'admin' ? 'Admin' : 'Member'); ?></span>
          </div>
          <div class="profile-meta profile-meta--email"><?php echo e($email); ?></div>
          <p class="profile-bio">
            <?php echo e($bio !== '' ? $bio : 'Chưa cập nhật bio.'); ?>
          </p>
        </div>
      </div>

      <?php if (!empty($is_owner)): ?>
        <div class="profile-actions">
          <a class="btn btn-profile-action" href="<?php echo url('/profile/edit'); ?>">
            <i class="bi bi-pencil-square"></i>
            <span>Chỉnh sửa hồ sơ</span>
          </a>
        </div>
      <?php endif; ?>
    </div>

    <div class="profile-stats profile-stats--cinematic">
      <div class="profile-stat">
        <div class="profile-stat-icon profile-stat-icon--review">
          <i class="bi bi-pencil-square"></i>
        </div>
        <span class="profile-stat-value"><?php echo (int) $reviewCount; ?></span>
        <span class="profile-stat-label">Review đã viết</span>
      </div>
      <div class="profile-stat">
        <div class="profile-stat-icon profile-stat-icon--list">
          <i class="bi bi-collection-play"></i>
        </div>
        <span class="profile-stat-value"><?php echo (int) $listCount; ?></span>
        <span class="profile-stat-label">Danh sách đã tạo</span>
      </div>
    </div>
  </section>

  <section class="profile-content-block">
    <div class="profile-section-head profile-section-head--spaced">
      <div>
        <p class="section-kicker">Recent activity</p>
        <h3>Review đã viết</h3>
        <p class="text-muted mb-0">Lịch sử đánh giá gần đây.</p>
      </div>
    </div>

    <div class="profile-review-grid">
      <?php if (empty($reviews)): ?>
        <div class="activity-empty">Chưa có review nào.</div>
      <?php else: ?>
        <?php foreach ($reviews as $review): ?>
          <?php
            $poster = trim((string) ($review['poster_url'] ?? ''));
            if ($poster !== '' && stripos($poster, 'http') !== 0) {
                if ($poster[0] !== '/') {
                    $poster = '/uploads/posters/' . ltrim($poster, '/');
                }
                $poster = url($poster);
            }
            if ($poster === '') {
                $poster = 'https://via.placeholder.com/240x360?text=Poster';
            }
            $movieTitle = trim((string) (($review['title_en'] ?? '') ?: ($review['title_vi'] ?? '')));
            $excerpt = trim((string) ($review['excerpt'] ?? ''));
            if ($excerpt === '') {
                $excerpt = trim((string) ($review['title'] ?? ''));
            }
          ?>
          <article class="profile-review-card">
            <a class="profile-review-poster" href="<?php echo url('/reviews/' . (int) $review['id']); ?>">
              <img src="<?php echo e($poster); ?>" alt="<?php echo e($movieTitle); ?>" loading="lazy">
            </a>
            <div class="profile-review-body">
              <div class="profile-review-topline">
                <div class="profile-review-movie"><?php echo e($movieTitle); ?></div>
                <div class="profile-review-time"><?php echo e(profileTimeAgo($review['created_at'] ?? '')); ?></div>
              </div>

              <div class="profile-review-title-row">
                <h4 class="profile-review-title"><?php echo e((string) ($review['title'] ?? '')); ?></h4>
                <div class="profile-review-score">
                  <?php echo profileRenderStars((float) ($review['rating'] ?? 0)); ?>
                  <span><?php echo e(number_format((float) ($review['rating'] ?? 0), 1)); ?>/5</span>
                </div>
              </div>

              <p class="profile-review-excerpt"><?php echo e(mb_strimwidth($excerpt, 0, 180, '...')); ?></p>

              <div class="profile-review-footer">
                <span class="profile-review-meta">Đánh giá phim</span>
                <a class="profile-review-link" href="<?php echo url('/reviews/' . (int) $review['id']); ?>">Xem chi tiết</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>

  <section class="profile-content-block">
    <div class="profile-section-head profile-section-head--spaced">
      <div>
        <p class="section-kicker">Recent collections</p>
        <h3>Danh sách đã tạo gần đây</h3>
        <p class="text-muted mb-0">Những bộ sưu tập bạn vừa xây dựng.</p>
      </div>
    </div>

    <div class="profile-list-grid">
      <?php if (empty($lists)): ?>
        <div class="activity-empty">Chưa có danh sách nào.</div>
      <?php else: ?>
        <?php foreach ($lists as $list): ?>
          <article class="profile-list-card">
            <div class="profile-list-icon">
              <i class="bi bi-collection-play"></i>
            </div>
            <div class="profile-list-body">
              <div class="profile-list-topline">
                <h4 class="profile-list-title"><?php echo e((string) ($list['name'] ?? '')); ?></h4>
                <span class="profile-list-pill"><?php echo !empty($list['is_public']) ? 'Công khai' : 'Riêng tư'; ?></span>
              </div>
              <p class="profile-list-description">
                <?php echo e(trim((string) ($list['description'] ?? '')) !== '' ? trim((string) ($list['description'] ?? '')) : 'Chưa có mô tả.'); ?>
              </p>
              <div class="profile-list-footer">
                <span class="profile-list-meta"><?php echo e(profileTimeAgo($list['created_at'] ?? '')); ?></span>
                <a class="profile-review-link" href="<?php echo url('/lists/' . (int) $list['id']); ?>">Xem chi tiết</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
