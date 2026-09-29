  </main>

<footer class="footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <h5 class="mb-2">CineDiary</h5>
        <p class="mb-0">Cộng đồng review phim, nhật ký xem phim và danh sách tuyển chọn.</p>
      </div>
      <div class="col-md-4">
        <h6 class="text-uppercase">Khám phá</h6>
        <div class="d-flex flex-column gap-1">
          <a href="<?php echo url('/discover'); ?>">Khám phá</a>
          <a href="<?php echo url('/movies'); ?>">Phim</a>
          <a href="<?php echo url('/reviews'); ?>">Đánh giá</a>
        </div>
      </div>
      <div class="col-md-4">
        <h6 class="text-uppercase">Tài khoản</h6>
        <div class="d-flex flex-column gap-1">
          <a href="<?php echo url('/profile'); ?>">Hồ sơ</a>
          <a href="<?php echo url('/watchlist'); ?>">Danh sách xem</a>
          <a href="<?php echo url('/diary'); ?>">Nhật ký</a>
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-between mt-4">
      <span>2026</span>
      <span>Xây dựng bằng PHP + MySQL + Bootstrap</span>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo url('/assets/js/home.js'); ?>"></script>
<script src="<?php echo url('/assets/js/validate.js'); ?>"></script>
</body>
</html>
