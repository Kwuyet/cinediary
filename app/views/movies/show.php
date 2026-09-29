<?php
$title = $movie['title_en'] ?: $movie['title_vi'];

if (!function_exists('movieNormalizeMediaUrl')) {
  function movieNormalizeMediaUrl(?string $path, string $fallbackFolder = ''): string
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

if (!function_exists('movieNormalizeAvatar')) {
  function movieNormalizeAvatar(?string $avatar): string
  {
    return avatar_url($avatar);
  }
}

require __DIR__ . '/../layouts/header.php';
$poster = movieNormalizeMediaUrl($movie['poster_url'] ?? '', '/uploads/posters/');
if ($poster === '') {
  $poster = 'https://via.placeholder.com/400x600?text=Poster';
}
$authUser = Auth::user() ?? [];
$currentUserId = (int) ($authUser['id'] ?? 0);

$movieReviewCount = (int) count($reviews ?? []);
$movieAverageRating = number_format((float) ($movie['avg_rating'] ?? 0), 1);
$featuredReview = $reviews[0] ?? null;
$featuredReviewComments = $featuredReview['comments'] ?? [];
$featuredReviewImage = $featuredReview ? movieNormalizeMediaUrl($featuredReview['image_url'] ?? '', '/uploads/reviews/') : '';

if (!function_exists('movieTimeAgo')) {
  function movieTimeAgo(?string $dateTime): string
  {
    if (empty($dateTime)) {
      return '';
    }
    try {
      $then = new DateTimeImmutable($dateTime);
      $diff = (new DateTimeImmutable('now'))->getTimestamp() - $then->getTimestamp();
      if ($diff < 60) return 'Vừa xong';
      if ($diff < 3600) return floor($diff / 60) . ' phút trước';
      if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
      if ($diff < 604800) return floor($diff / 86400) . ' ngày trước';
      return $then->format('d/m/Y');
    } catch (Throwable $e) {
      return (string) $dateTime;
    }
  }
}

if (!function_exists('renderMovieStars')) {
  function renderMovieStars(float $rating): string
  {
    $full = (int) floor($rating);
    $half = ($rating - $full) >= 0.5;
    $html = '<div class="movie-rating-stars" aria-label="Rating ' . e(number_format($rating, 1)) . '">';
    for ($i = 1; $i <= 5; $i++) {
      if ($i <= $full) {
        $html .= '<span class="movie-rating-star is-filled">★</span>';
      } elseif ($i === $full + 1 && $half) {
        $html .= '<span class="movie-rating-star is-half">★</span>';
      } else {
        $html .= '<span class="movie-rating-star">★</span>';
      }
    }
    return $html . '</div>';
  }
}

if (!function_exists('renderMovieReviewCard')) {
  function renderMovieReviewCard(array $comment, int $currentUserId, int $movieId, int $depth = 0): void
  {
    $isOwner = $currentUserId > 0 && (int) ($comment['user_id'] ?? 0) === $currentUserId;
    $avatarUrl = movieNormalizeAvatar($comment['avatar_url'] ?? '');
    $indentClass = $depth > 0 ? 'review-comment--reply' : '';
    $likeCount = (int) ($comment['like_count'] ?? 0);
    $liked = !empty($comment['liked']);
    $rating = isset($comment['rating']) ? (float) $comment['rating'] : 0.0;
    $body = trim((string) ($comment['body'] ?? ''));
    $bodyPreview = mb_strlen($body) > 220 ? mb_substr($body, 0, 220) . '...' : $body;
    ?>
    <article class="review-comment-card <?php echo e($indentClass); ?>" style="margin-left: <?php echo (int) min($depth * 22, 88); ?>px;">
      <div class="review-comment-avatar-wrap">
        <?php if ($avatarUrl !== ''): ?>
          <img class="review-comment-avatar" src="<?php echo e($avatarUrl); ?>" alt="Avatar">
        <?php else: ?>
          <div class="review-comment-avatar review-comment-avatar--empty"><?php echo strtoupper(substr((string) ($comment['display_name'] ?? 'U'), 0, 1)); ?></div>
        <?php endif; ?>
      </div>

      <div class="review-comment-content">
        <div class="review-comment-head">
          <div class="review-comment-author-block">
            <a class="review-comment-author" href="<?php echo url('/profile/' . (int) ($comment['user_id'] ?? 0)); ?>"><?php echo e($comment['display_name'] ?? 'Unknown'); ?></a>
            <span class="review-comment-badge">Member</span>
          </div>
          <div class="review-comment-time"><?php echo e(movieTimeAgo($comment['created_at'] ?? '')); ?></div>
        </div>

        <?php if ($rating > 0): ?>
          <div class="review-comment-rating">
            <?php echo renderMovieStars($rating); ?>
            <span class="review-comment-rating-value"><?php echo e(number_format($rating, 1)); ?>/5</span>
          </div>
        <?php endif; ?>

        <?php if (!empty($comment['title'])): ?>
          <div class="review-comment-title"><?php echo e($comment['title']); ?></div>
        <?php endif; ?>

        <div class="review-comment-body">
          <?php echo nl2br(e($bodyPreview)); ?>
        </div>

        <div class="review-comment-actions">
          <?php if ($currentUserId > 0): ?>
            <form method="post" action="<?php echo url('/comments/' . (int) $comment['id'] . '/like'); ?>" class="d-inline">
              <button class="review-action-btn <?php echo $liked ? 'is-liked' : ''; ?>" type="submit">
                <i class="bi bi-hand-thumbs-up"></i>
                <span><?php echo $liked ? 'Liked' : 'Like'; ?></span>
                <strong><?php echo (int) $likeCount; ?></strong>
              </button>
            </form>
            <button class="review-action-btn" type="button" data-bs-toggle="collapse" data-bs-target="#reply-<?php echo (int) $comment['id']; ?>">
              <i class="bi bi-reply"></i>
              <span>Reply</span>
            </button>
          <?php endif; ?>

          <?php if (!empty($comment['user_id'])): ?>
            <a class="review-action-btn" href="<?php echo url('/profile/' . (int) $comment['user_id']); ?>">
              <i class="bi bi-person"></i>
              <span>Xem profile</span>
            </a>
          <?php endif; ?>

          <?php if ($isOwner): ?>
            <button class="review-action-btn" type="button" data-bs-toggle="collapse" data-bs-target="#edit-<?php echo (int) $comment['id']; ?>">
              <i class="bi bi-pencil"></i>
              <span>Edit</span>
            </button>
            <form method="post" action="<?php echo url('/comments/' . (int) $comment['id'] . '/delete'); ?>" onsubmit="return confirm('Delete this comment?');" class="d-inline">
              <button class="review-action-btn review-action-btn--danger" type="submit">
                <i class="bi bi-trash"></i>
                <span>Delete</span>
              </button>
            </form>
          <?php endif; ?>
        </div>

        <?php if ($currentUserId > 0): ?>
          <div class="collapse review-inline-collapse" id="reply-<?php echo (int) $comment['id']; ?>">
            <form class="review-inline-form" method="post" action="<?php echo url('/comments/' . (int) $comment['id'] . '/reply'); ?>">
              <textarea class="form-control review-inline-textarea" name="body" rows="2" placeholder="Viết phản hồi ngắn..." minlength="10" required></textarea>
              <div class="d-flex justify-content-end mt-2">
                <button class="btn btn-sm btn-accent review-inline-submit" type="submit">Đăng phản hồi</button>
              </div>
            </form>
          </div>
        <?php endif; ?>

        <?php if ($isOwner): ?>
          <div class="collapse review-inline-collapse" id="edit-<?php echo (int) $comment['id']; ?>">
            <form class="review-inline-form" method="post" action="<?php echo url('/comments/' . (int) $comment['id'] . '/update'); ?>">
              <textarea class="form-control review-inline-textarea" name="body" rows="3" minlength="10" required><?php echo e($body); ?></textarea>
              <div class="d-flex justify-content-end mt-2">
                <button class="btn btn-sm btn-outline-light review-inline-submit" type="submit">Lưu thay đổi</button>
              </div>
            </form>
          </div>
        <?php endif; ?>

        <?php if (!empty($comment['children'])): ?>
          <div class="review-comment-children">
            <?php foreach ($comment['children'] as $child): ?>
              <?php renderMovieReviewCard($child, $currentUserId, $movieId, $depth + 1); ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </article>
    <?php
  }
}
?>

<div class="movie-detail-page">
  <section class="movie-hero-section">
    <div class="container">
      <div class="movie-hero-shell">
        <div class="movie-hero-poster-column">
          <div class="movie-hero-poster-frame">
            <div class="movie-hero-poster" style="background-image: url('<?php echo e($poster); ?>');"></div>
            <div class="movie-hero-poster-overlay">
              <span class="movie-hero-poster-chip">CineDiary</span>
              <span class="movie-hero-poster-chip"><?php echo e($movieAverageRating); ?>/5 • <?php echo (int) $movieReviewCount; ?> đánh giá</span>
            </div>
          </div>
        </div>

        <div class="movie-hero-content">
          <p class="movie-section-kicker">Nội Dung Đánh giá</p>
          <div class="movie-featured-review-shell">
            <div class="movie-featured-review-head">
              <div>
                <p class="movie-featured-review-kicker">Nội dung đánh giá</p>
                <h2 class="movie-featured-review-title"><?php echo e((string) ($featuredReview['title'] ?? 'Chưa có bài đánh giá nổi bật')); ?></h2>
              </div>
              <a class="movie-featured-review-link" href="#featured-comments">Đi tới bình luận</a>
            </div>

            <?php if ($featuredReview): ?>
              <div class="movie-featured-review-meta">
                <a class="movie-featured-review-user" href="<?php echo url('/profile/' . (int) ($featuredReview['user_id'] ?? 0)); ?>">
                  <?php echo render_avatar($featuredReview['display_name'] ?? 'U', $featuredReview['avatar_url'] ?? '', ['size' => 44, 'class' => 'movie-featured-review-avatar']); ?>
                  <span><?php echo e((string) ($featuredReview['display_name'] ?? 'Unknown')); ?></span>
                </a>
                <div class="movie-featured-review-time"><?php echo e(movieTimeAgo($featuredReview['created_at'] ?? '')); ?></div>
              </div>

              <div class="movie-featured-review-rating">
                <?php echo renderMovieStars((float) ($featuredReview['rating'] ?? 0)); ?>
                <span><?php echo e(number_format((float) ($featuredReview['rating'] ?? 0), 1)); ?>/5</span>
              </div>

              <?php if ($featuredReviewImage !== ''): ?>
                <div class="movie-featured-review-image">
                  <img src="<?php echo e($featuredReviewImage); ?>" alt="Review image" loading="lazy">
                </div>
              <?php endif; ?>

              <p class="movie-featured-review-body"><?php echo e((string) ($featuredReview['body'] ?? '')); ?></p>

            <?php else: ?>
              <div class="movie-featured-review-empty">Chưa có bài đánh giá nào để hiển thị ở hero.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="movie-reviews-section movie-reviews-section--spacious" id="featured-comments">
    <div class="container">
      <div class="movie-reviews-header">
        <div>
          <p class="movie-section-kicker">Bình luận</p>
          <div class="d-flex align-items-baseline gap-2 flex-wrap">
            <h2 class="movie-reviews-title">Bình luận cho bài đánh giá</h2>
            <span class="movie-reviews-count"><?php echo (int) count($featuredReviewComments); ?> bình luận</span>
          </div>
        </div>
        <div class="movie-reviews-header-side">
          <a class="btn movie-review-cta" href="#write-comment">
            <i class="bi bi-pencil-square"></i>
            <span>Viết bình luận</span>
          </a>
        </div>
      </div>

      <div class="movie-comment-compose-card" id="write-comment">
        <?php if ($featuredReview): ?>
          <?php if (Auth::check()): ?>
            <?php $composerAvatar = movieNormalizeAvatar($authUser['avatar_url'] ?? ''); ?>
            <div class="movie-comment-compose-avatar-wrap">
              <?php if ($composerAvatar !== ''): ?>
                <img class="movie-comment-compose-avatar" src="<?php echo e($composerAvatar); ?>" alt="Avatar">
              <?php else: ?>
                <div class="movie-comment-compose-avatar movie-comment-compose-avatar--empty"><?php echo strtoupper(substr((string) ($authUser['display_name'] ?? 'U'), 0, 1)); ?></div>
              <?php endif; ?>
            </div>
            <div class="movie-comment-compose-body">
              <div class="movie-comment-compose-top">
                <div>
                  <div class="movie-comment-compose-title"><?php echo e((string) ($authUser['display_name'] ?? 'Bạn')); ?></div>
                  <div class="movie-comment-compose-note">Bình luận cho bài đánh giá ở trên. Gọn, đúng ý và dễ đọc.</div>
                </div>
                <a class="movie-comment-compose-link" href="<?php echo url('/reviews/' . (int) $featuredReview['id']); ?>">Mở bài đánh giá</a>
              </div>

              <form class="movie-comment-compose-form" method="post" action="<?php echo url('/reviews/' . (int) $featuredReview['id'] . '/comments'); ?>" data-review-comment-form>
                <textarea class="form-control movie-comment-compose-textarea" name="body" rows="4" placeholder="Viết bình luận của bạn..." minlength="10" required></textarea>
                <div class="movie-comment-compose-actions">
                  <div class="movie-comment-compose-note">Đây là phần bình luận, không phải bài đánh giá mới.</div>
                  <button class="btn btn-accent movie-comment-compose-submit" type="submit">Đăng bình luận</button>
                </div>
              </form>
            </div>
          <?php else: ?>
            <div class="movie-comment-compose-login">
              <div>
                <div class="movie-comment-compose-title">Đăng nhập để bình luận</div>
                <div class="movie-comment-compose-note">Bạn cần tài khoản để tham gia thảo luận cho bài đánh giá này.</div>
              </div>
              <a class="btn btn-accent" href="<?php echo url('/login'); ?>">Đăng nhập</a>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <div class="review-empty-state">Chưa có bài đánh giá nào để bình luận.</div>
        <?php endif; ?>
      </div>

      <div class="movie-comment-list">
        <?php if (!$featuredReview): ?>
          <div class="review-empty-state">Chưa có bình luận nào vì chưa có bài đánh giá nổi bật.</div>
        <?php elseif (empty($featuredReviewComments)): ?>
          <div class="review-empty-state">Bài đánh giá này chưa có bình luận nào.</div>
        <?php else: ?>
          <?php foreach ($featuredReviewComments as $comment): ?>
            <?php renderMovieReviewCard($comment, $currentUserId, (int) $movie['id']); ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const ratingPickers = Array.from(document.querySelectorAll('[data-rating-picker]'));

  const clampRating = (value) => Math.min(5, Math.max(0.5, value));

  const setRating = (picker, value) => {
    const normalized = clampRating(Math.round(value * 2) / 2);
    const buttons = Array.from(picker.querySelectorAll('.review-rating-picker__star'));
    const inputs = Array.from(picker.querySelectorAll('input[name="rating"]'));

    const selectedInput = inputs.find((input) => parseFloat(input.value) === normalized);
    if (selectedInput) {
      selectedInput.checked = true;
    }

    picker.dataset.rating = normalized.toFixed(1);

    buttons.forEach((button) => {
      const star = parseInt(button.dataset.star || '0', 10);
      const fill = button.querySelector('.review-rating-picker__star-fill');
      if (!fill) return;

      let width = 0;
      if (normalized >= star) {
        width = 100;
      } else if (normalized >= star - 0.5) {
        width = 50;
      }

      fill.style.width = width + '%';
      button.classList.toggle('is-filled', normalized >= star);
      button.classList.toggle('is-half', normalized === star - 0.5);
    });
  };

  ratingPickers.forEach((picker) => {
    const buttons = Array.from(picker.querySelectorAll('.review-rating-picker__star'));
    buttons.forEach((button) => {
      button.addEventListener('click', (event) => {
        const star = parseInt(button.dataset.star || '0', 10);
        const rect = button.getBoundingClientRect();
        const isHalf = (event.clientX - rect.left) < (rect.width / 2);
        setRating(picker, isHalf ? star - 0.5 : star);
      });
    });

    setRating(picker, parseFloat(picker.dataset.rating || '5'));
  });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
