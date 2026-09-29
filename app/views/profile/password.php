<?php
$title = 'Doi mat khau';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container profile-page">
  <div class="panel profile-hero">
    <div class="profile-header">
      <div class="profile-info">
        <h2 class="h4 mb-1">Đổi mật khẩu</h2>
        <p class="text-muted">Nhập mật khẩu hiện tại, sau đó đặt mật khẩu mới cho tài khoản của bạn.</p>
      </div>
    </div>
  </div>

  <div class="panel profile-section">
    <form class="profile-form" method="post" action="<?php echo url('/profile/password'); ?>">
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Mật khẩu hiện tại</label>
          <input type="password" class="form-control" name="current_password" autocomplete="current-password" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Mật khẩu mới</label>
          <input type="password" class="form-control" name="new_password" autocomplete="new-password" required>
          <div class="form-hint">Mật khẩu nên có ít nhất 8 ký tự và bao gồm chữ, số.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Xác nhận mật khẩu mới</label>
          <input type="password" class="form-control" name="confirm_password" autocomplete="new-password" required>
        </div>
      </div>
      <div class="mt-4 d-flex flex-wrap gap-2">
        <button class="btn btn-accent" type="submit">Lưu mật khẩu mới</button>
        <a class="btn btn-ghost" href="<?php echo url('/profile'); ?>">Quay lại hồ sơ</a>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
