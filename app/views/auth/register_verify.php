<?php
$title = 'Xác thực Email';
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

    <h1 class="h2 mb-1 fw-bold">Xác thực Email</h1>
    <p class="text-muted mb-4">Chúng tôi đã gửi mã xác thực (OTP) tới email của bạn. Nhập mã để hoàn tất đăng ký.</p>

    <?php if ($err = flash('error')): ?>
      <div class="alert alert-danger"><?php echo e($err); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo url('/register/verify'); ?>">
      <div class="mb-3">
        <label class="form-label">Mã xác thực (OTP)</label>
        <input type="text" name="otp" class="form-control form-control-lg dark-input" placeholder="Nhập mã 6 chữ số" required>
      </div>

      <button class="btn btn-gradient w-100 btn-lg mb-2" type="submit">Xác thực và tạo tài khoản</button>
    </form>

    <div class="text-center text-muted small mb-2">
      <form method="post" action="<?php echo url('/register/resend'); ?>" style="display:inline;">
        <button type="submit" class="btn btn-link text-decoration-none">Gửi lại mã (Resend OTP)</button>
      </form>
      • Nếu không nhận được email, kiểm tra file log tạm thời trên server.
    </div>
    <div class="auth-footer text-center text-muted">© <?php echo date('Y'); ?> CineDiary</div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
