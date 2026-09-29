<?php
$title = 'Tổng quan hệ thống CineDiary';
require __DIR__ . '/../layouts/admin_head.php';

$trendLabels = $trendLabels ?? [];
$reviewTrend = $reviewTrend ?? [];
$signupTrend = $signupTrend ?? [];
$movieTrend = $movieTrend ?? [];
$contentToReview = $contentToReview ?? [];
$recentActivity = $recentActivity ?? [];

$chartLabels = $trendLabels ?: [];
$chartReviews = $reviewTrend ?: [];
$chartSignups = $signupTrend ?: [];
$chartMovies = $movieTrend ?: [];
$pendingItems = array_slice($contentToReview, 0, 3);
?>

<section class="max-w-7xl mx-auto space-y-6">
  <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
    <div>
      <h1 class="font-headline text-4xl lg:text-5xl tracking-tight">Tổng quan hệ thống CineDiary</h1>
      <p class="mt-3 text-lg text-onSurfaceVariant">Chào mừng trở lại, hôm nay có <?php echo (int) ($reviewsNeedAttention ?? 0); ?> review mới cần phê duyệt.</p>
    </div>

    <a href="#" class="inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-5 py-3 text-sm font-semibold text-onSurface transition hover:bg-white/10">
      <span class="material-symbols-outlined text-base">download</span>
      Xuất báo cáo
    </a>
  </div>

  <form method="get" action="<?php echo url('/admin'); ?>" class="rounded-3xl border border-white/5 bg-white/5 p-5 shadow-[0_16px_48px_rgba(0,0,0,0.18)]">
    <div class="grid gap-4 lg:grid-cols-[1fr_1fr_auto] lg:items-end">
      <label>
        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-onSurfaceVariant">Từ ngày</span>
        <input type="date" name="start" value="<?php echo e($filterStart ?? date('Y-m-d')); ?>" class="w-full rounded-2xl border border-white/10 bg-[#1b1b1b] px-4 py-3 text-sm text-onSurface outline-none transition focus:border-primaryContainer">
      </label>

      <label>
        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.22em] text-onSurfaceVariant">Đến ngày</span>
        <input type="date" name="end" value="<?php echo e($filterEnd ?? date('Y-m-d')); ?>" class="w-full rounded-2xl border border-white/10 bg-[#1b1b1b] px-4 py-3 text-sm text-onSurface outline-none transition focus:border-primaryContainer">
      </label>

      <button type="submit" class="inline-flex h-[50px] items-center justify-center rounded-2xl bg-primaryContainer px-6 text-sm font-semibold text-white transition hover:brightness-110">Áp dụng</button>
    </div>
    <p class="mt-3 text-sm text-onSurfaceVariant">Đang xem: <?php echo e($filterLabel ?? 'Từ ngày ... đến ngày ...'); ?></p>
  </form>

  <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <article class="rounded-3xl border border-white/5 bg-white/5 p-5 shadow-[0_16px_48px_rgba(0,0,0,0.2)]">
      <div class="flex items-start justify-between">
        <div class="rounded-2xl bg-[#2b1212] p-3 text-[#ff4d5f]"><span class="material-symbols-outlined">movie</span></div>
        <span class="text-sm font-semibold text-green-400">+4% ↗</span>
      </div>
      <div class="mt-5 text-sm text-onSurfaceVariant">Tổng số phim</div>
      <div class="mt-2 text-4xl font-extrabold tracking-tight"><?php echo (int) ($totalMovies ?? 0); ?></div>
    </article>

    <article class="rounded-3xl border border-white/5 bg-white/5 p-5 shadow-[0_16px_48px_rgba(0,0,0,0.2)]">
      <div class="flex items-start justify-between">
        <div class="rounded-2xl bg-[#2a2410] p-3 text-[#ffd54a]"><span class="material-symbols-outlined">rate_review</span></div>
        <span class="text-sm font-semibold text-green-400">+12% ↗</span>
      </div>
      <div class="mt-5 text-sm text-onSurfaceVariant">Tổng lượt Review</div>
      <div class="mt-2 text-4xl font-extrabold tracking-tight"><?php echo number_format((int) ($totalReviews ?? 0) + (int) ($totalComments ?? 0), 0, ',', '.'); ?></div>
    </article>

    <article class="rounded-3xl border border-white/5 bg-white/5 p-5 shadow-[0_16px_48px_rgba(0,0,0,0.2)]">
      <div class="flex items-start justify-between">
        <div class="rounded-2xl bg-[#1f1f1f] p-3 text-[#d9d9d9]"><span class="material-symbols-outlined">person_add</span></div>
        <span class="text-sm font-semibold text-red-400">-2% ↘</span>
      </div>
      <div class="mt-5 text-sm text-onSurfaceVariant">Người dùng mới</div>
      <div class="mt-2 text-4xl font-extrabold tracking-tight"><?php echo (int) ($newUsersToday ?? 0); ?></div>
    </article>

    <article class="rounded-3xl border border-white/5 bg-white/5 p-5 shadow-[0_16px_48px_rgba(0,0,0,0.2)]">
      <div class="flex items-start justify-between">
        <div class="rounded-2xl bg-[#2a1616] p-3 text-[#ff9ea7]"><span class="material-symbols-outlined">star</span></div>
        <span class="text-sm font-semibold text-onSurfaceVariant">Rating ổn định</span>
      </div>
      <div class="mt-5 text-sm text-onSurfaceVariant">Rating trung bình</div>
      <div class="mt-2 flex items-end gap-2">
        <div class="text-4xl font-extrabold tracking-tight"><?php echo number_format((float) ($averageRating ?? 0), 1, ',', '.'); ?></div>
        <span class="text-2xl text-yellow-300">★</span>
      </div>
    </article>
  </div>

  <div class="grid gap-6 xl:grid-cols-[1.65fr_1fr]">
    <section class="rounded-3xl border border-white/5 bg-white/5 p-6">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h2 class="font-headline text-2xl">Xu hướng thảo luận</h2>
          <p class="mt-1 text-sm text-onSurfaceVariant">Biểu đồ tổng hợp review, đăng nhập và lượt xem phim trong <?php echo e($filterLabel ?? 'khoảng thời gian đã chọn'); ?>.</p>
        </div>
        <div class="inline-flex rounded-2xl border border-white/5 bg-black/20 p-1 text-sm font-semibold">
          <span class="rounded-xl bg-white/5 px-4 py-2 text-onSurfaceVariant">Khoảng lọc</span>
          <span class="rounded-xl bg-primaryContainer px-4 py-2 text-white"><?php echo e($filterLabel ?? 'Tổng quan'); ?></span>
        </div>
      </div>

      <div class="mt-5 h-[410px] rounded-2xl border border-white/5 bg-[#191919] p-4">
        <canvas id="activityTrendChart"></canvas>
      </div>
    </section>

    <aside class="rounded-3xl border border-white/5 bg-white/5 p-6">
      <div class="flex items-center justify-between gap-3">
        <div>
          <h2 class="font-headline text-2xl">Cần phê duyệt</h2>
          <p class="mt-1 text-sm text-onSurfaceVariant">Các nội dung có rủi ro hoặc cần xem nhanh.</p>
        </div>
      </div>

      <div class="mt-5 space-y-4">
        <?php foreach ($pendingItems as $item): ?>
          <article class="rounded-2xl border border-white/5 bg-[#1b1b1b] p-4 shadow-[0_10px_30px_rgba(0,0,0,0.2)]">
            <div class="flex items-start gap-3">
              <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-white/10">
                <div class="flex h-full w-full items-center justify-center text-xs font-bold text-onSurfaceVariant">CD</div>
              </div>
              <div class="min-w-0 flex-1">
                <div class="truncate font-semibold text-onSurface"><?php echo e($item['display_name'] ?? 'Thành viên'); ?></div>
                <div class="mt-1 text-sm font-medium text-[#d8b1a7]"><?php echo e($item['title'] ?? 'Review cần duyệt'); ?></div>
                <p class="mt-2 line-clamp-3 text-sm leading-6 text-onSurfaceVariant"><?php echo e($item['excerpt'] ?? ''); ?></p>
                <div class="mt-4 flex gap-2">
                    <a href="<?php echo url('/admin/reviews'); ?>" class="rounded-full bg-emerald-600/25 px-4 py-2 text-xs font-semibold text-emerald-300">DUYỆT</a>
                    <a href="<?php echo url('/admin/reviews'); ?>" class="rounded-full bg-red-600/25 px-4 py-2 text-xs font-semibold text-red-300">XÓA</a>
                </div>
              </div>
            </div>
          </article>
        <?php endforeach; ?>

        <?php if (empty($pendingItems)): ?>
          <div class="rounded-2xl border border-white/5 bg-[#1b1b1b] p-4 text-sm text-onSurfaceVariant">Không có nội dung chờ phê duyệt.</div>
        <?php endif; ?>
      </div>
    </aside>
  </div>

  <div class="grid gap-6 lg:grid-cols-[1.25fr_0.95fr]">
    <section class="rounded-3xl border border-white/5 bg-white/5 p-6">
      <div class="flex items-center justify-between gap-3">
        <div>
          <h2 class="font-headline text-2xl">Hoạt động gần đây</h2>
          <p class="mt-1 text-sm text-onSurfaceVariant">Log người dùng mới đăng ký, review mới được đăng, phim mới được thêm.</p>
        </div>
      </div>

      <div class="mt-5 space-y-3">
        <?php foreach (array_slice($recentActivity, 0, 4) as $item): ?>
          <div class="flex items-center gap-3 rounded-2xl border border-white/5 bg-[#1a1a1a] p-4">
            <span class="material-symbols-outlined rounded-xl bg-white/5 p-2 text-primaryContainer"><?php echo e($item['icon'] ?? 'circle'); ?></span>
            <div class="min-w-0 flex-1">
              <div class="flex items-center justify-between gap-3">
                <h3 class="truncate font-semibold"><?php echo e($item['title'] ?? 'Hoạt động'); ?></h3>
                <span class="text-xs text-onSurfaceVariant"><?php echo e($item['meta'] ?? ''); ?></span>
              </div>
              <p class="mt-1 text-sm text-onSurfaceVariant"><?php echo e($item['subtitle'] ?? ''); ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="rounded-3xl border border-white/5 bg-white/5 p-6">
      <div class="flex items-center justify-between gap-3">
        <div>
          <h2 class="font-headline text-2xl">Lối tắt nhanh</h2>
          <p class="mt-1 text-sm text-onSurfaceVariant">Các nút lớn cho những thao tác thường dùng nhất.</p>
        </div>
      </div>

      <div class="mt-5 grid gap-3">
        <a class="rounded-2xl border border-white/10 bg-[#1b1b1b] px-5 py-4 transition hover:bg-white/5" href="<?php echo url('/admin/reviews'); ?>">
          <div class="text-lg font-semibold">Duyệt đánh giá</div>
          <div class="mt-1 text-sm text-onSurfaceVariant">Xử lý nội dung cần kiểm tra nhanh</div>
        </a>
        <a class="rounded-2xl border border-white/10 bg-[#1b1b1b] px-5 py-4 transition hover:bg-white/5" href="<?php echo url('/admin/movies/0/edit'); ?>">
          <div class="text-lg font-semibold">Thêm phim mới</div>
          <div class="mt-1 text-sm text-onSurfaceVariant">Tạo nội dung cho thư viện phim</div>
        </a>
        <a class="rounded-2xl border border-white/10 bg-[#1b1b1b] px-5 py-4 transition hover:bg-white/5" href="<?php echo url('/admin/users'); ?>">
          <div class="text-lg font-semibold">Quản lý người dùng</div>
          <div class="mt-1 text-sm text-onSurfaceVariant">Phân trang, vai trò và trạng thái</div>
        </a>
        <a class="rounded-2xl border border-white/10 bg-[#1b1b1b] px-5 py-4 transition hover:bg-white/5" href="<?php echo url('/admin/reviews'); ?>">
          <div class="text-lg font-semibold">Xem báo cáo</div>
          <div class="mt-1 text-sm text-onSurfaceVariant">Theo dõi nội dung vi phạm / điểm thấp</div>
        </a>
        <a class="rounded-2xl border border-white/10 bg-[#1b1b1b] px-5 py-4 transition hover:bg-white/5" href="<?php echo url('/admin/settings'); ?>">
          <div class="text-lg font-semibold">Sao lưu dữ liệu</div>
          <div class="mt-1 text-sm text-onSurfaceVariant">Bảo trì và lưu trữ hệ thống</div>
        </a>
      </div>
    </section>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(() => {
  Chart.defaults.color = '#d9d7d6';
  Chart.defaults.font.family = 'Inter, sans-serif';
  Chart.defaults.borderColor = 'rgba(255,255,255,0.08)';

  const labels = <?php echo json_encode($chartLabels, JSON_UNESCAPED_UNICODE); ?>;
  const reviews = <?php echo json_encode($chartReviews, JSON_UNESCAPED_UNICODE); ?>;
  const signups = <?php echo json_encode($chartSignups, JSON_UNESCAPED_UNICODE); ?>;
  const movies = <?php echo json_encode($chartMovies, JSON_UNESCAPED_UNICODE); ?>;

  if (!labels.length) {
    return;
  }

  const ctx = document.getElementById('activityTrendChart');
  if (!ctx) return;

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          label: 'Review',
          data: reviews,
          backgroundColor: '#a31b26',
          borderRadius: 8,
          maxBarThickness: 28,
        },
        {
          label: 'Đăng nhập',
          data: signups,
          backgroundColor: '#cf2230',
          borderRadius: 8,
          maxBarThickness: 28,
        },
        {
          label: 'Lượt xem phim',
          data: movies,
          backgroundColor: '#7d1820',
          borderRadius: 8,
          maxBarThickness: 28,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          align: 'end',
          labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'rectRounded' }
        },
        tooltip: {
          backgroundColor: '#111111',
          borderColor: 'rgba(255,255,255,0.08)',
          borderWidth: 1,
          padding: 12
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { maxRotation: 0, autoSkip: true }
        },
        y: {
          beginAtZero: true,
          ticks: { precision: 0 },
          grid: { color: 'rgba(255,255,255,0.06)' }
        }
      }
    }
  });
})();
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>