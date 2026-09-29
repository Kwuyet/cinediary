<?php
$title = 'Quản lý phim';
require __DIR__ . '/../layouts/admin_head.php';
?>

<section class="max-w-7xl mx-auto space-y-6">
  <div class="rounded-3xl border border-white/5 bg-white/5 p-6 lg:p-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
      <div>
        <p class="text-sm uppercase tracking-[0.25em] text-onSurfaceVariant">Phim</p>
        <h1 class="mt-2 font-headline text-4xl">Quản lý phim</h1>
        <p class="mt-2 text-onSurfaceVariant">Sidebar đã tách thành từng trang riêng, nên khi bấm Phim sẽ vào trang list riêng này.</p>
      </div>
      <div class="flex gap-3">
        <a class="rounded-full border border-white/10 px-4 py-2 text-sm" href="<?php echo url('/admin/movies/import'); ?>">Nhập CSV</a>
        <a class="rounded-full bg-primaryContainer px-4 py-2 text-sm font-semibold text-white" href="<?php echo url('/admin/movies/0/edit'); ?>">+ Thêm phim</a>
      </div>
    </div>
  </div>

  <div class="rounded-3xl border border-white/5 bg-white/5 p-6">
    <div class="flex items-center justify-between text-sm text-onSurfaceVariant">
      <span>Tổng: <?php echo (int) ($totalMovies ?? 0); ?> phim</span>
      <span>Trang <?php echo (int) ($page ?? 1); ?> / <?php echo (int) ($totalPages ?? 1); ?></span>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/5">
      <table class="min-w-full text-left text-sm">
        <thead class="bg-black/20 text-onSurfaceVariant">
          <tr>
            <th class="px-4 py-3">Tên phim</th><th class="px-4 py-3">Năm</th><th class="px-4 py-3">Thể loại</th><th class="px-4 py-3">Giá vé</th><th class="px-4 py-3">Điểm</th><th class="px-4 py-3">Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($movies ?? []) as $movie): ?>
            <tr class="border-t border-white/5 even:bg-white/5">
              <td class="px-4 py-3"><?php echo e($movie['title_en'] ?: $movie['title_vi']); ?></td>
              <td class="px-4 py-3"><?php echo e((string) ($movie['year'] ?? '')); ?></td>
              <td class="px-4 py-3"><?php echo e($movie['genre'] ?? ''); ?></td>
              <td class="px-4 py-3"><?php echo number_format((float) ($movie['ticket_price'] ?? 0), 0, ',', '.'); ?> đ</td>
              <td class="px-4 py-3"><?php echo e((string) ($movie['avg_rating'] ?? '')); ?>/5</td>
              <td class="px-4 py-3 flex gap-2">
                <a class="rounded-full border border-white/10 px-3 py-1 text-xs" href="<?php echo url('/admin/movies/' . (int) ($movie['id'] ?? 0) . '/edit'); ?>">Sửa</a>
                <form method="post" action="<?php echo url('/admin/movies/' . (int) ($movie['id'] ?? 0) . '/delete'); ?>">
                  <button class="rounded-full bg-primaryContainer px-3 py-1 text-xs text-white">Xóa</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-5 flex items-center justify-between text-sm text-onSurfaceVariant">
      <span>Trang <?php echo (int) ($page ?? 1); ?> / <?php echo (int) ($totalPages ?? 1); ?></span>
      <div class="flex gap-2">
        <?php if (($page ?? 1) > 1): ?>
          <a class="rounded-full border border-white/10 px-3 py-1" href="<?php echo url('/admin/movies?' . http_build_query(array_filter([
            'page' => (int) $page - 1,
            'q' => $filters['q'] ?? '',
            'genre' => $filters['genre'] ?? '',
            'rating' => $filters['rating'] ?? '',
          ], static fn ($value) => $value !== ''))); ?>">Trước</a>
        <?php endif; ?>
        <?php if (($page ?? 1) < ($totalPages ?? 1)): ?>
          <a class="rounded-full border border-white/10 px-3 py-1" href="<?php echo url('/admin/movies?' . http_build_query(array_filter([
            'page' => (int) $page + 1,
            'q' => $filters['q'] ?? '',
            'genre' => $filters['genre'] ?? '',
            'rating' => $filters['rating'] ?? '',
          ], static fn ($value) => $value !== ''))); ?>">Sau</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>