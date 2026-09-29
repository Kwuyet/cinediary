<?php
$title = 'Cài đặt quản trị';
require __DIR__ . '/../layouts/admin_head.php';
?>

<section class="max-w-7xl mx-auto space-y-6">
  <div class="rounded-3xl border border-white/5 bg-white/5 p-6 lg:p-8">
    <p class="text-sm uppercase tracking-[0.25em] text-onSurfaceVariant">Cài đặt</p>
    <h1 class="mt-2 font-headline text-4xl">Cài đặt hệ thống</h1>
    <p class="mt-2 text-onSurfaceVariant">Trang riêng cho cấu hình admin, không còn nằm chung với dashboard.</p>
  </div>

  <div class="grid gap-6 lg:grid-cols-3">
    <div class="rounded-3xl border border-white/5 bg-white/5 p-6">
      <h2 class="font-headline text-2xl">Giao diện</h2>
      <p class="mt-2 text-sm text-onSurfaceVariant">Thiết lập logo, màu và theme.</p>
      <div class="mt-4 flex gap-3">
        <button class="rounded-full bg-primaryContainer px-4 py-2 text-sm font-semibold text-white">Đổi logo</button>
        <button class="rounded-full border border-white/10 px-4 py-2 text-sm">Theme tối</button>
      </div>
    </div>

    <div class="rounded-3xl border border-white/5 bg-white/5 p-6">
      <h2 class="font-headline text-2xl">Người dùng</h2>
      <p class="mt-2 text-sm text-onSurfaceVariant">Quyền và quản lý tài khoản.</p>
      <div class="mt-4">
        <a class="rounded-full bg-primaryContainer px-4 py-2 text-sm font-semibold text-white" href="<?php echo url('/admin/users'); ?>">Mở trang người dùng</a>
      </div>
    </div>

    <div class="rounded-3xl border border-white/5 bg-white/5 p-6">
      <h2 class="font-headline text-2xl">Sao lưu</h2>
      <p class="mt-2 text-sm text-onSurfaceVariant">Tác vụ bảo trì và backup.</p>
      <div class="mt-4 flex gap-3">
        <button class="rounded-full bg-primaryContainer px-4 py-2 text-sm font-semibold text-white">Chạy sao lưu</button>
        <button class="rounded-full border border-white/10 px-4 py-2 text-sm">Xem lịch</button>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>