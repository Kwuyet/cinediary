<?php
$title = $title ?? 'CineDiary Admin';
$authUser = class_exists('Auth') && Auth::check() ? Auth::user() : null;
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$isAdminRoot = str_contains($currentPath, '/admin') && !str_contains($currentPath, '/admin/');
$isAdminUsers = str_contains($currentPath, '/admin/users');
$isAdminMovies = str_contains($currentPath, '/admin/movies');
$isAdminSettings = str_contains($currentPath, '/admin/settings');
$isAdminModeration = str_contains($currentPath, '/admin/moderation');

$navClass = static function (bool $active): string {
  return 'flex items-center gap-3 rounded-xl px-4 py-3 transition ' . ($active ? 'bg-white/5 text-primary' : 'hover:bg-white/5');
};
?>
<!doctype html>
<html lang="vi" class="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($title); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            background: '#131313',
            surface: '#1c1b1b',
            surface2: '#2a2a2a',
            primary: '#ffb4aa',
            primaryContainer: '#e50914',
            onSurface: '#e5e2e1',
            onSurfaceVariant: '#c8c6c5'
          },
          fontFamily: {
            headline: ['Montserrat', 'sans-serif'],
            body: ['Inter', 'sans-serif']
          }
        }
      }
    };
  </script>
  <style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-thumb { background: #353534; border-radius: 999px; }
  </style>
</head>
<body class="bg-background text-onSurface font-body min-h-screen">
  <div class="min-h-screen flex">
    <aside class="w-72 hidden lg:flex flex-col border-r border-white/5 bg-[#0f0f0f]">
      <div class="p-6 border-b border-white/5">
        <a href="<?php echo url('/'); ?>" class="flex items-center gap-3 transition hover:opacity-90">
          <div class="w-12 h-12 rounded-xl bg-primaryContainer/20 text-primary flex items-center justify-center font-bold">CD</div>
          <div>
            <div class="font-headline text-xl">CineDiary</div>
            <div class="text-sm text-onSurfaceVariant">Về trang chủ</div>
          </div>
        </a>
      </div>

      <nav class="p-3 space-y-1">
        <a href="<?php echo url('/admin'); ?>" class="<?php echo e($navClass($isAdminRoot)); ?>">
          <span class="material-symbols-outlined">dashboard</span><span>Tổng quan</span>
        </a>
        <a href="<?php echo url('/admin/users'); ?>" class="<?php echo e($navClass($isAdminUsers)); ?>">
          <span class="material-symbols-outlined">group</span><span>Người dùng</span>
        </a>
        <a href="<?php echo url('/admin/movies'); ?>" class="<?php echo e($navClass($isAdminMovies)); ?>">
          <span class="material-symbols-outlined">movie</span><span>Phim</span>
        </a>
        <a href="<?php echo url('/admin/moderation'); ?>" class="<?php echo e($navClass($isAdminModeration)); ?>">
          <span class="material-symbols-outlined">report</span><span>Review và Bình luận</span>
        </a>
        <a href="<?php echo url('/admin/settings'); ?>" class="<?php echo e($navClass($isAdminSettings)); ?>">
          <span class="material-symbols-outlined">settings</span><span>Cài đặt</span>
        </a>
      </nav>
    </aside>

    <div class="flex-1 min-w-0">
      <header class="sticky top-0 z-30 border-b border-white/5 bg-[#131313]/90 backdrop-blur">
        <div class="px-4 lg:px-8 h-16 flex items-center justify-between">
          <div>
            <div class="text-sm text-onSurfaceVariant">Bảng điều khiển quản trị</div>
            <div class="font-headline text-lg">CineDiary</div>
          </div>
          <div class="flex items-center gap-3">
            <div class="hidden md:flex items-center gap-2 rounded-full bg-white/5 px-4 py-2 text-sm text-onSurfaceVariant">
              <span class="material-symbols-outlined text-base">search</span>
              <span>Tìm kiếm</span>
            </div>
            <div class="flex items-center gap-2 text-sm">
              <?php if ($authUser): ?>
                <?php echo render_avatar($authUser['display_name'] ?? null, $authUser['avatar_url'] ?? null, ['class' => 'w-9 h-9 rounded-full border border-white/10']); ?>
                <span class="hidden sm:inline"><?php echo e($authUser['display_name'] ?? 'Admin'); ?></span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </header>
      <main class="p-4 lg:p-8">