<?php
$title = 'Đăng nhập';
require __DIR__ . '/../layouts/header.php';
?>

<div class="auth-shell d-flex align-items-center justify-content-center" style="min-height:85vh;">
  <div class="auth-card card p-4 p-md-5 shadow-lg text-light mx-3">
    <div class="text-center mb-3">
      <a class="brand-link" href="<?php echo url('/'); ?>">
        <span class="brand-logo-small">
          <span class="badge-dot badge-1"></span>
          <span class="badge-dot badge-2"></span>
          <span class="badge-dot badge-3"></span>
        </span>
        <span class="brand-name">CINEDIARY</span>
      </a>
    </div>
    <h1 class="h2 mb-1 fw-bold">Chào mừng trở lại</h1>
    <p class="text-muted mb-4">Đăng nhập vào CineDiary để tiếp tục khám phá.</p>

    <?php if ($err = flash('error')): ?>
      <div class="alert alert-danger"><?php echo e($err); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo url('/login'); ?>" class="mb-3">
      <div class="mb-3">
        <label class="form-label visually-hidden">Email</label>
        <div class="input-group input-group-lg">
          <span class="input-group-text bg-dark text-light"><i class="bi bi-envelope-fill"></i></span>
          <input type="text" name="email" class="form-control form-control-lg dark-input" placeholder="Email hoặc tên đăng nhập" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label visually-hidden">Mật khẩu</label>
        <div class="input-group input-group-lg">
          <span class="input-group-text bg-dark text-light"><i class="bi bi-lock-fill"></i></span>
          <input type="password" name="password" class="form-control form-control-lg dark-input" placeholder="Mật khẩu" required>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
          <label class="form-check-label text-muted" for="remember">Ghi nhớ đăng nhập</label>
        </div>
        <div><a href="<?php echo url('/forgot'); ?>" class="text-decoration-none">Quên mật khẩu?</a></div>
      </div>

      <button class="btn btn-gradient w-100 btn-lg mb-3" type="submit">Đăng nhập</button>

      <a href="<?php echo url('/auth/google'); ?>" class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2 mb-2">
        <svg width="20" height="20" viewBox="0 0 533.5 544.3" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path fill="#4285F4" d="M533.5 278.4c0-18.5-1.6-36.3-4.6-53.5H272v101.3h147.4c-6.4 34.6-25.6 63.9-54.6 83.5v69.3h88.2c51.6-47.5 82.5-117.8 82.5-200.6z"/><path fill="#34A853" d="M272 544.3c73.7 0 135.6-24.4 180.8-66.2l-88.2-69.3c-24.6 16.6-56 26.5-92.6 26.5-71.1 0-131.4-48-153-112.4H30.9v70.6C75.8 483.6 168.5 544.3 272 544.3z"/><path fill="#FBBC05" d="M119 322.9c-10.7-31.9-10.7-66.2 0-98.1V154.2H30.9c-42.6 83.8-42.6 182.5 0 266.3L119 322.9z"/><path fill="#EA4335" d="M272 109.6c39.9 0 75.7 13.7 104 40.6l78-78C406.9 24.4 345 0 272 0 168.5 0 75.8 60.7 30.9 154.2l88.1 70.6C140.6 157.6 200.9 109.6 272 109.6z"/></svg>
        Đăng nhập bằng Google
      </a>
    </form>

    <div class="text-center text-muted small mb-2">Chưa có tài khoản? <a href="<?php echo url('/register'); ?>">Đăng ký</a></div>
    <div class="auth-footer text-center text-muted">© <?php echo date('Y'); ?> CineDiary</div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
