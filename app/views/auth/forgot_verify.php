<?php
// OTP verify + new password form
?>
<style>
/* minimal inline styles fallback for auth verify page */
.auth-shell{padding:2rem 0;min-height:80vh;display:flex;align-items:center;justify-content:center;background:#0f172a;color:#e9eef2}
.auth-card{width:100%;max-width:520px;padding:2rem;border-radius:12px;background:linear-gradient(180deg,#0b0f1a,#071021);box-shadow:0 20px 60px rgba(0,0,0,0.6)}
.auth-title{font-family:Arial,Helvetica,sans-serif;font-size:1.5rem;margin:0 0 .5rem}
.auth-sub{color:#9aa3af;margin-bottom:1rem}
.dark-input{width:100%;padding:.9rem;border-radius:8px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);color:#fff}
.btn-gradient{display:inline-block;width:100%;padding:.85rem;border-radius:10px;background:linear-gradient(90deg,#20bf55,#7b2cbf);color:#fff;border:0;font-weight:700}
.logo--mini{font-weight:800;letter-spacing:.12em;color:#fff;margin-bottom:.4rem}
.input-group{display:flex;gap:0}
.input-group-text{display:flex;align-items:center;justify-content:center;padding:.6rem;border-radius:8px 0 0 8px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06)}
</style>
<div class="auth-shell">
    <div class="auth-card">
        <div class="text-center mb-3">
            <div class="logo--mini">CINEDIARY</div>
        </div>
        <h2 class="auth-title">Xác thực khôi phục</h2>
        <p class="auth-sub">Nhập mã OTP và đặt mật khẩu mới cho tài khoản của bạn.</p>
        <?php if ($m = flash('error')): ?>
            <div class="alert alert-danger"><?php echo e($m); ?></div>
        <?php endif; ?>
        <?php if ($m = flash('success')): ?>
            <div class="alert alert-success"><?php echo e($m); ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo url('/forgot/verify'); ?>">
            <div class="mb-3 input-group">
                <span class="input-group-text bg-dark text-light" style="border-right:0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5v7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 12v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <input name="otp" type="text" class="form-control dark-input" placeholder="Mã OTP" required style="border-left:0;">
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text bg-dark text-light" style="border-right:0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 15v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 11V8a5 5 0 0 0-10 0v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <input name="current_password" type="password" class="form-control dark-input" placeholder="Mật khẩu hiện tại" required style="border-left:0;">
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text bg-dark text-light" style="border-right:0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 15v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 11V8a5 5 0 0 0-10 0v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <input name="password" type="password" class="form-control dark-input" placeholder="Mật khẩu mới" required style="border-left:0;">
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text bg-dark text-light" style="border-right:0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 15v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 11V8a5 5 0 0 0-10 0v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <input name="password_confirm" type="password" class="form-control dark-input" placeholder="Xác nhận mật khẩu" required style="border-left:0;">
            </div>

            <div class="d-grid">
                <button class="btn btn-gradient">Đặt lại mật khẩu</button>
            </div>
        </form>

        <form method="post" action="<?php echo url('/forgot/resend'); ?>" class="mt-3 text-center">
            <button class="btn btn-link">Gửi lại mã (Resend OTP)</button>
        </form>

        <div class="mt-3 text-center small text-muted">
            <a href="<?php echo url('/login'); ?>">Quay lại đăng nhập</a>
        </div>
    </div>
</div>
