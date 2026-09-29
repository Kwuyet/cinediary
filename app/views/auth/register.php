<?php
$title = 'Đăng ký';
require __DIR__ . '/../layouts/header.php';
?>

<div class="auth-shell d-flex align-items-center justify-content-center" style="min-height:86vh;">
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

    <h1 class="h2 mb-1 fw-bold">Tạo tài khoản</h1>
    <p class="text-muted mb-4">Tham gia cộng đồng yêu phim và lưu giữ những cảm xúc của bạn.</p>

    <?php if ($err = flash('error')): ?>
      <div class="alert alert-danger"><?php echo e($err); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo url('/register'); ?>" data-validate="register">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <div class="input-group input-group-lg">
          <span class="input-group-text bg-dark text-light"><i class="bi bi-envelope-fill"></i></span>
          <input type="email" class="form-control form-control-lg dark-input" name="email" placeholder="you@email.com" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Tên hiển thị</label>
        <div class="input-group input-group-lg">
          <span class="input-group-text bg-dark text-light"><i class="bi bi-person-fill"></i></span>
          <input type="text" class="form-control form-control-lg dark-input" name="display_name" placeholder="Tên của bạn" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Mật khẩu</label>
        <div class="input-group input-group-lg">
          <span class="input-group-text bg-dark text-light"><i class="bi bi-lock-fill"></i></span>
          <input type="password" class="form-control form-control-lg dark-input" name="password" required>
        </div>
        <div class="form-hint text-muted small mt-2">Tối thiểu 8 ký tự, có chữ hoa, chữ thường và số.</div>
      </div>

      <div class="mb-4">
        <label class="form-label">Nhập lại mật khẩu</label>
        <div class="input-group input-group-lg">
          <span class="input-group-text bg-dark text-light"><i class="bi bi-shield-lock-fill"></i></span>
          <input type="password" class="form-control form-control-lg dark-input" name="password_confirm" required>
        </div>
      </div>

      <button class="btn btn-gradient w-100 btn-lg mb-2" type="submit">Tạo tài khoản</button>
    </form>

    <div class="text-center text-muted small mb-2">Đã có tài khoản? <a href="<?php echo url('/login'); ?>">Đăng nhập</a></div>
    <div class="auth-footer text-center text-muted">© <?php echo date('Y'); ?> CineDiary</div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
