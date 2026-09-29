<?php
// simple forgot password form
?>
<style>
/* minimal inline styles for auth pages (fallback if main CSS not loaded) */
.auth-shell{padding:2rem 0;min-height:80vh;display:flex;align-items:center;justify-content:center;background:#0f172a;color:#e9eef2}
.auth-card{width:100%;max-width:480px;padding:2rem;border-radius:12px;background:linear-gradient(180deg,#0b0f1a,#071021);box-shadow:0 20px 60px rgba(0,0,0,0.6)}
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
        <h2 class="auth-title">Quên mật khẩu</h2>
        <p class="auth-sub">Nhập email đã đăng ký để nhận mã xác thực (OTP).</p>
        <?php if ($m = flash('error')): ?>
            <div class="alert alert-danger"><?php echo e($m); ?></div>
        <?php endif; ?>
        <?php if ($m = flash('success')): ?>
            <div class="alert alert-success"><?php echo e($m); ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo url('/forgot'); ?>">
            <div class="mb-3 input-group">
                <span class="input-group-text bg-dark text-light" style="border-right:0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 8.5L12 13L21 8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <input name="email" type="email" class="form-control dark-input" placeholder="Email của bạn" required style="border-left:0;">
            </div>

            <div class="d-grid">
                <button class="btn btn-gradient">Gửi mã OTP</button>
            </div>

            <div class="mt-3 text-center small text-muted">
                Hoặc <a href="<?php echo url('/login'); ?>">quay lại đăng nhập</a>
            </div>
        </form>
    </div>
</div>
