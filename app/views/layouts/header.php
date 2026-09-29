<?php
$title = $title ?? 'CineDiary';
$bodyClass = trim((string) ($bodyClass ?? ''));
$isAuthed = class_exists('Auth') && Auth::check();
$authUser = $isAuthed ? Auth::user() : null;
$isAdmin = $authUser && ($authUser['role'] ?? '') === 'admin';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($title); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Cinzel:wght@500;600;700&family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <?php
  $cssFile = __DIR__ . '/../../../public/assets/css/style.css';
  $cssVer = file_exists($cssFile) ? filemtime($cssFile) : time();
  ?>
  <link rel="stylesheet" href="<?php echo url('/public/assets/css/style.css') . '?v=' . $cssVer; ?>">
</head>
<body<?php echo $bodyClass !== '' ? ' class="' . e($bodyClass) . '"' : ''; ?>>
<nav class="navbar navbar-expand-lg sticky-top main-nav navbar-dark">
  <div class="container">
    <a class="navbar-brand brand" href="<?php echo url('/'); ?>">
      <span class="logo" aria-label="CineDiary">
        <span class="logo-badges" aria-hidden="true">
          <span class="badge-dot badge-1"></span>
          <span class="badge-dot badge-2"></span>
          <span class="badge-dot badge-3"></span>
        </span>
        <span class="logo-text">CINEDIARY</span>
      </span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      
      <ul class="navbar-nav mx-auto nav-center">
        <li class="nav-item"><a class="nav-link" href="<?php echo url('/discover'); ?>">KHÁM PHÁ</a></li>
        <!-- Movies nav removed per request -->
        <li class="nav-item"><a class="nav-link" href="<?php echo url('/reviews'); ?>">VIẾT ĐÁNH GIÁ</a></li>
        <?php if ($isAuthed): ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo url('/lists'); ?>">TẠO DANH SÁCH</a></li>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo url('/admin'); ?>">QUẢN TRỊ</a></li>
        <?php endif; ?>
      </ul>
      <div class="nav-actions d-flex align-items-center gap-2">
        <?php if ($isAuthed): ?>
          <div class="user-menu-dropdown">
            <button class="btn btn-ghost btn-sm user-menu-trigger"><?php echo e($authUser['display_name']); ?></button>
            <div class="user-menu-panel">
              <a class="user-menu-item" href="<?php echo url('/profile'); ?>">Hồ sơ cá nhân</a>
              <a class="user-menu-item" href="<?php echo url('/profile/password'); ?>">Đổi mật khẩu</a>
              <?php if ($isAdmin): ?>
                <hr class="my-1">
                <a class="user-menu-item admin-only" href="<?php echo url('/admin'); ?>">Trang quản trị</a>
              <?php endif; ?>
            </div>
          </div>
          <form method="post" action="<?php echo url('/logout'); ?>">
            <button class="btn btn-sm btn-outline-dark">Đăng Xuất</button>
          </form>
        <?php else: ?>
          <a class="btn btn-ghost btn-sm" href="<?php echo url('/login'); ?>">Đăng nhập</a>
          <a class="btn btn-accent btn-sm" href="<?php echo url('/register'); ?>">Tạo tài khoản</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<div class="container mt-3">
  <?php if ($msg = flash('success')): ?>
    <div class="alert alert-success"><?php echo e($msg); ?></div>
  <?php endif; ?>
  <?php if ($msg = flash('error')): ?>
    <div class="alert alert-danger"><?php echo e($msg); ?></div>
  <?php endif; ?>
</div>

<main class="pb-4">
