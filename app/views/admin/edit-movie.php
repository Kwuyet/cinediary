<?php
$title = 'Chỉnh sửa phim';
require __DIR__ . '/../layouts/admin_head.php';
?>

<section class="max-w-5xl mx-auto space-y-6">
  <div class="rounded-3xl border border-white/5 bg-white/5 p-6 lg:p-8">
    <p class="text-sm uppercase tracking-[0.25em] text-onSurfaceVariant">Quản trị CineDiary</p>
    <h1 class="mt-3 font-headline text-4xl">Chỉnh sửa phim</h1>
    <p class="mt-3 text-onSurfaceVariant">Biểu mẫu này dùng cùng hệ card và khoảng trắng như trang chủ để việc quản trị vẫn giữ cảm giác nhẹ, rõ và sang.</p>
    <div class="mt-6 flex flex-wrap gap-3">
      <a class="rounded-full border border-white/10 px-4 py-2 text-sm" href="<?php echo url('/admin'); ?>">Quay lại dashboard</a>
      <a class="rounded-full bg-primaryContainer px-4 py-2 text-sm font-semibold text-white" href="<?php echo url('/admin'); ?>#movies">Danh sách phim</a>
    </div>
  </div>

  <?php if ($error = flash('error')): ?>
    <div class="rounded-2xl border border-red-500/20 bg-red-500/10 p-4 text-red-200"><?php echo e($error); ?></div>
  <?php endif; ?>
  <?php if ($success = flash('success')): ?>
    <div class="rounded-2xl border border-green-500/20 bg-green-500/10 p-4 text-green-200"><?php echo e($success); ?></div>
  <?php endif; ?>

  <div class="rounded-3xl border border-white/5 bg-white/5 p-6">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h2 class="font-headline text-2xl">Thông tin phim</h2>
        <p class="text-sm text-onSurfaceVariant">Cập nhật nội dung hiển thị cho thư viện phim.</p>
      </div>
      <a class="text-sm text-onSurfaceVariant" href="<?php echo url('/admin'); ?>#movies">← Quay lại danh sách</a>
    </div>

    <form method="POST" action="<?php echo url('/admin/movies/' . (int) ($movie['id'] ?? 0) . '/update'); ?>" class="mt-6 space-y-4">
      <div class="grid gap-4 md:grid-cols-2">
        <input type="text" name="title_vi" class="rounded-2xl border border-white/5 bg-black/20 px-4 py-3 outline-none" value="<?php echo e($movie['title_vi'] ?? ''); ?>" placeholder="Tên phim (Tiếng Việt)" required>
        <input type="text" name="title_en" class="rounded-2xl border border-white/5 bg-black/20 px-4 py-3 outline-none" value="<?php echo e($movie['title_en'] ?? ''); ?>" placeholder="Tên phim (English)" required>
        <input type="number" name="year" class="rounded-2xl border border-white/5 bg-black/20 px-4 py-3 outline-none" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo e($movie['year'] ?? date('Y')); ?>" placeholder="Năm sản xuất" required>
        <input type="number" name="duration_min" class="rounded-2xl border border-white/5 bg-black/20 px-4 py-3 outline-none" min="1" value="<?php echo e($movie['duration_min'] ?? ''); ?>" placeholder="Thời lượng (phút)">
        <input type="number" name="ticket_price" class="rounded-2xl border border-white/5 bg-black/20 px-4 py-3 outline-none" min="0" step="1000" value="<?php echo e($movie['ticket_price'] ?? '0'); ?>" placeholder="Giá vé (VNĐ)">
        <input type="text" name="genre" class="rounded-2xl border border-white/5 bg-black/20 px-4 py-3 outline-none" value="<?php echo e($movie['genre'] ?? ''); ?>" placeholder="Thể loại">
        <input type="text" name="director" class="rounded-2xl border border-white/5 bg-black/20 px-4 py-3 outline-none" value="<?php echo e($movie['director'] ?? ''); ?>" placeholder="Đạo diễn">
      </div>

      <input type="text" name="cast_list" class="w-full rounded-2xl border border-white/5 bg-black/20 px-4 py-3 outline-none" value="<?php echo e($movie['cast_list'] ?? ''); ?>" placeholder="Diễn viên">
      <textarea name="synopsis" class="w-full rounded-2xl border border-white/5 bg-black/20 px-4 py-3 outline-none" rows="5" placeholder="Mô tả"><?php echo e($movie['synopsis'] ?? ''); ?></textarea>

      <div class="grid gap-4 md:grid-cols-2">
        <input type="url" name="trailer_url" class="w-full rounded-2xl border border-white/5 bg-black/20 px-4 py-3 outline-none" value="<?php echo e($movie['trailer_url'] ?? ''); ?>" placeholder="URL Trailer">
        <input type="url" name="poster_url" class="w-full rounded-2xl border border-white/5 bg-black/20 px-4 py-3 outline-none" value="<?php echo e($movie['poster_url'] ?? ''); ?>" placeholder="URL Poster">
        <div class="rounded-2xl border border-white/5 bg-black/20 p-4 flex items-center justify-center min-h-[160px]">
          <?php if (!empty($movie['poster_url'])): ?>
            <img src="<?php echo e($movie['poster_url']); ?>" alt="Poster" class="max-h-40 rounded-xl object-cover">
          <?php else: ?>
            <span class="text-onSurfaceVariant">Chưa có poster</span>
          <?php endif; ?>
        </div>
      </div>

      <div class="flex flex-wrap gap-3">
        <button type="submit" class="rounded-full bg-primaryContainer px-5 py-3 text-sm font-semibold text-white">Lưu thay đổi</button>
        <a href="<?php echo url('/admin'); ?>#movies" class="rounded-full border border-white/10 px-5 py-3 text-sm">Hủy</a>
      </div>
    </form>

    <?php if (!empty($movie['id'])): ?>
    <form method="POST" action="<?php echo url('/admin/movies/' . (int) ($movie['id'] ?? 0) . '/delete'); ?>" class="mt-4" onsubmit="return confirm('Bạn chắc chắn muốn xóa phim này?');">
      <button type="submit" class="rounded-full border border-red-500/30 bg-red-500/10 px-5 py-3 text-sm text-red-200">Xóa phim</button>
    </form>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
