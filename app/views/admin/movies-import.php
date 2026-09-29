<?php
$title = 'Import phim CSV';
require __DIR__ . '/../layouts/admin_head.php';
?>

<section class="max-w-4xl mx-auto space-y-6">
  <div class="rounded-3xl border border-white/5 bg-white/5 p-6 lg:p-8">
    <h1 class="font-headline text-2xl">Nhập phim từ CSV</h1>
    <p class="mt-2 text-onSurfaceVariant">Upload file CSV có header: title_vi,title_en,year,duration_min,genre,director,cast_list,synopsis,poster_url</p>
  </div>

  <?php if ($error = flash('error')): ?>
    <div class="rounded-2xl border border-red-500/20 bg-red-500/10 p-4 text-red-200"><?php echo e($error); ?></div>
  <?php endif; ?>
  <?php if ($success = flash('success')): ?>
    <div class="rounded-2xl border border-green-500/20 bg-green-500/10 p-4 text-green-200"><?php echo e($success); ?></div>
  <?php endif; ?>

  <div class="rounded-3xl border border-white/5 bg-white/5 p-6">
    <form method="post" action="<?php echo url('/admin/movies/import'); ?>" enctype="multipart/form-data">
      <div class="space-y-3">
        <input type="file" name="csv_file" accept=".csv" required />
        <div class="text-sm text-onSurfaceVariant">Lưu ý: kiểm tra định dạng trước khi upload. Mỗi file lớn nên chia nhỏ.</div>
      </div>

      <div class="mt-4">
        <button type="submit" class="rounded-full bg-primaryContainer px-5 py-3 text-sm font-semibold text-white">Upload & Import</button>
        <a href="<?php echo url('/admin/movies'); ?>" class="ml-2 rounded-full border border-white/10 px-5 py-3 text-sm">Hủy</a>
      </div>
    </form>
  </div>
</section>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
