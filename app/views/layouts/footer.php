</main>

<footer class="footer">
  <div class="container d-flex justify-content-between">
    <span>CineDiary</span>
    <span>2026</span>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php
  $homeJs = __DIR__ . '/../../../public/assets/js/home.js';
  $validateJs = __DIR__ . '/../../../public/assets/js/validate.js';
  $homeVer = file_exists($homeJs) ? filemtime($homeJs) : time();
  $valVer = file_exists($validateJs) ? filemtime($validateJs) : time();
?>
<script src="<?php echo url('/public/assets/js/home.js') . '?v=' . $homeVer; ?>"></script>
<script src="<?php echo url('/public/assets/js/validate.js') . '?v=' . $valVer; ?>"></script>
</body>
</html>
