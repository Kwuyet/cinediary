<?php
$formMode = (string) ($formMode ?? 'create');
$isEdit = $formMode === 'edit';
$review = $review ?? null;
$currentRating = (float) ($review['rating'] ?? 5);
$title = $isEdit ? 'Edit Review' : 'Write Review';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
  <div class="card p-4">
    <h2 class="h4"><?php echo $isEdit ? 'Edit review for ' : 'Write review for '; ?><?php echo e($movie['title_en'] ?: $movie['title_vi']); ?></h2>
    <form method="post" action="<?php echo $isEdit ? url('/reviews/' . (int) $review['id'] . '/update') : url('/movies/' . $movie['id'] . '/reviews'); ?>" data-validate="review" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input class="form-control" type="text" name="title" value="<?php echo e((string) ($review['title'] ?? '')); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Review</label>
        <textarea class="form-control" name="body" rows="6" required><?php echo e((string) ($review['body'] ?? '')); ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Rating</label>
        <div class="review-rating-picker rating-stars--lg" data-rating-picker data-rating="<?php echo e(number_format($currentRating, 1, '.', '')); ?>">
          <div class="review-rating-picker__stars" aria-label="Rating control">
            <?php for ($star = 5; $star >= 1; $star--): ?>
              <button
                type="button"
                class="review-rating-picker__star"
                data-star="<?php echo $star; ?>"
                aria-label="Chọn <?php echo $star; ?> sao"
              >
                <span class="review-rating-picker__star-base" aria-hidden="true">&#9733;</span>
                <span class="review-rating-picker__star-fill" aria-hidden="true">&#9733;</span>
              </button>
            <?php endfor; ?>
          </div>
          <div class="review-rating-picker__inputs" hidden>
            <?php for ($i = 10; $i >= 1; $i--): ?>
              <?php $value = $i / 2; ?>
              <input type="radio" id="rating-<?php echo $i; ?>" name="rating" value="<?php echo $value; ?>" <?php echo $currentRating === $value ? 'checked' : ($i === 10 && !$isEdit ? 'checked' : ''); ?> required>
            <?php endfor; ?>
          </div>
        </div>
        <div class="small text-muted">Chon diem 0.5 buoc (vi du: 4.5).</div>
      </div>
      <div class="mb-3">
        <label class="form-label">Hinh minh hoa (tuy chon)</label>
        <input class="form-control" type="file" name="image" accept="image/*">
        <div class="small text-muted">Ho tro JPG, PNG, WEBP, GIF.</div>
        <?php if ($isEdit && !empty($review['image_url'])): ?>
          <div class="mt-2 small text-muted">Anh hien tai:</div>
          <img src="<?php echo e((string) $review['image_url']); ?>" alt="Current review image" style="max-width: 160px; border-radius: 12px;" />
        <?php endif; ?>
      </div>
      <button class="btn btn-accent" type="submit"><?php echo $isEdit ? 'Save changes' : 'Submit review'; ?></button>
      <a class="btn btn-outline-dark" href="<?php echo url('/movies/' . $movie['id']); ?>">Cancel</a>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const ratingPicker = document.querySelector('[data-rating-picker]');
  if (!ratingPicker) {
    return;
  }

  const ratingButtons = Array.from(ratingPicker.querySelectorAll('.review-rating-picker__star'));
  const ratingInputs = Array.from(ratingPicker.querySelectorAll('input[name="rating"]'));

  const clampRating = (value) => Math.min(5, Math.max(0.5, value));

  const setRating = (value) => {
    const normalized = clampRating(Math.round(value * 2) / 2);
    const selectedInput = ratingInputs.find((input) => parseFloat(input.value) === normalized);
    if (selectedInput) {
      selectedInput.checked = true;
    }

    ratingPicker.dataset.rating = normalized.toFixed(1);

    ratingButtons.forEach((button) => {
      const star = parseInt(button.dataset.star || '0', 10);
      const fill = button.querySelector('.review-rating-picker__star-fill');
      if (!fill) {
        return;
      }

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

  ratingButtons.forEach((button) => {
    button.addEventListener('click', (event) => {
      const star = parseInt(button.dataset.star || '0', 10);
      const rect = button.getBoundingClientRect();
      const isHalf = (event.clientX - rect.left) < (rect.width / 2);
      setRating(isHalf ? star - 0.5 : star);
    });
  });

  setRating(parseFloat(ratingPicker.dataset.rating || '5'));
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
