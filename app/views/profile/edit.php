<?php
$title = 'Chinh sua ho so';
require __DIR__ . '/../layouts/header.php';

$avatar = $user['avatar_url'] ?? '';
$avatarSrc = '';
if ($avatar !== '') {
    $avatarSrc = strpos($avatar, 'http') === 0 ? $avatar : url($avatar);
}
?>

<div class="container profile-page">
  <div class="panel profile-hero">
    <div class="profile-header">
      <div class="profile-avatar">
        <?php if ($avatarSrc !== ''): ?>
          <img class="avatar" src="<?php echo e($avatarSrc); ?>" alt="Avatar">
        <?php else: ?>
          <div class="avatar placeholder">?</div>
        <?php endif; ?>
      </div>
      <div class="profile-info">
        <h2 class="h4 mb-1">Chinh sua ho so</h2>
        <p class="text-muted">Cap nhat anh dai dien, ten hien thi va bio (toi da 500 ky tu).</p>
      </div>
    </div>
  </div>

  <div class="panel profile-section">
    <form class="profile-form" method="post" action="<?php echo url('/profile/update'); ?>" enctype="multipart/form-data">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Ten hien thi</label>
          <input type="text" class="form-control" name="display_name" value="<?php echo e($user['display_name'] ?? ''); ?>" maxlength="50" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" value="<?php echo e($user['email'] ?? ''); ?>" disabled>
        </div>
        <div class="col-12">
          <label class="form-label">Bio</label>
          <textarea class="form-control" name="bio" rows="5" maxlength="500" placeholder="Gioi thieu ngan ve ban..."><?php echo e($user['bio'] ?? ''); ?></textarea>
          <div class="form-hint">Toi da 500 ky tu.</div>
        </div>
        <div class="col-12">
          <label class="form-label">Anh dai dien</label>
          <input type="file" class="form-control" name="avatar" accept="image/png, image/jpeg, image/webp">
          <div class="form-hint">Dinh dang JPG, PNG, WEBP. De trong neu khong muon doi.</div>
        </div>
      </div>
      <div class="mt-4 d-flex flex-wrap gap-2">
        <button class="btn btn-accent" type="submit">Luu thay doi</button>
        <a class="btn btn-ghost" href="<?php echo url('/profile'); ?>">Huy</a>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
