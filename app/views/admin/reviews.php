<?php
$title = 'Duyệt đánh giá';
require __DIR__ . '/../layouts/admin_head.php';
?>

<section class="max-w-7xl mx-auto space-y-6">
  <div class="rounded-3xl border border-white/5 bg-white/5 p-6 lg:p-8">
    <p class="text-sm uppercase tracking-[0.25em] text-onSurfaceVariant">Kiểm duyệt</p>
    <h1 class="mt-2 font-headline text-4xl">Duyệt đánh giá</h1>
    <p class="mt-2 text-onSurfaceVariant">Các review điểm thấp gần đây để quản trị viên xem xét nhanh trước khi xử lý.</p>
  </div>

  <div class="rounded-3xl border border-white/5 bg-white/5 p-6">
    <div class="flex items-center justify-between text-sm text-onSurfaceVariant">
      <span><?php echo count($reviews ?? []); ?> mục đang chờ xem xét</span>
      <a class="rounded-full border border-white/10 px-4 py-2 text-sm" href="<?php echo url('/admin'); ?>">Quay lại dashboard</a>
    </div>

    <div class="mt-5 space-y-3">
      <?php foreach (($reviews ?? []) as $review): ?>
        <article class="rounded-2xl border border-white/5 bg-black/20 p-4">
          <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-2 text-sm text-onSurfaceVariant">
                <span class="rounded-full bg-white/5 px-2 py-1">Điểm <?php echo e((string) ($review['rating'] ?? '')); ?>/5</span>
                <span><?php echo e($review['display_name'] ?? 'Thành viên'); ?></span>
                <span>•</span>
                <span><?php echo e($review['movie_title_vi'] ?? $review['movie_title_en'] ?? 'Phim'); ?></span>
              </div>
              <h2 class="mt-2 font-semibold text-lg"><?php echo e($review['title'] ?? 'Review'); ?></h2>
              <p class="mt-2 text-sm leading-6 text-onSurfaceVariant"><?php echo e($review['excerpt'] ?? ''); ?></p>
            </div>
            <div class="flex flex-wrap gap-2">
              <a class="rounded-full bg-primaryContainer px-4 py-2 text-sm font-semibold text-white" href="#">Duyệt</a>
              <a class="rounded-full border border-red-500/30 bg-red-500/10 px-4 py-2 text-sm text-red-200" href="#">Xóa nhanh</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>

      <?php if (empty($reviews)): ?>
        <div class="rounded-2xl border border-white/5 bg-black/20 p-4 text-sm text-onSurfaceVariant">Chưa có review cần xử lý.</div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>