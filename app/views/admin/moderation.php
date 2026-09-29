<?php
$title = 'Quản lý Đánh giá & Bình luận';
require __DIR__ . '/../layouts/admin_head.php';
?>

<section class="max-w-7xl mx-auto space-y-6">
  <div class="rounded-2xl bg-surface p-6 lg:p-8 shadow-sm">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="font-headline text-3xl">Quản lý Đánh giá & Bình luận</h1>
        <p class="text-sm text-onSurfaceVariant">Duyệt, lọc và xử lý nhanh các nội dung do người dùng đăng.</p>
      </div>
      <div>
        <a href="<?php echo url('/admin'); ?>" class="inline-flex items-center gap-2 rounded-lg border border-white/6 px-3 py-2 hover:bg-white/2">← Quay lại</a>
      </div>
    </div>
  </div>

  <div class="rounded-2xl bg-surface p-6 shadow-sm">
    <!-- Tabs -->
    <?php $currentTab = $tab ?? 'reviews'; ?>
    <div class="flex items-center gap-3 mb-5">
      <?php $tabs = ['reviews'=>'Tất cả Đánh giá','comments'=>'Tất cả Bình luận']; ?>
      <?php foreach ($tabs as $key=>$label): ?>
        <a href="<?php echo url('/admin/moderation?tab=' . $key); ?>" class="px-4 py-2 text-sm font-medium rounded-md <?php echo ($tab===$key) ? 'bg-primaryContainer text-white shadow' : 'text-onSurfaceVariant hover:bg-white/3'; ?> ">
          <?php echo $label; ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Filters row -->
    <form id="filterForm" method="get" class="mb-4 grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
      <input type="hidden" name="tab" value="<?php echo e($tab); ?>">
      <div class="md:col-span-2">
        <label class="text-xs text-onSurfaceVariant mb-1 block">Tìm kiếm</label>
        <div class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-onSurfaceVariant">🔎</span>
          <input type="text" name="q" placeholder="<?php echo $currentTab === 'comments' ? 'Tìm theo tên bài đánh giá hoặc phim' : 'Tìm theo người dùng, phim hoặc từ khóa'; ?>" value="<?php echo e($q ?? ''); ?>" class="w-full pl-10 pr-4 py-2 rounded-xl bg-black/8 focus:outline-none" />
        </div>
      </div>

      <?php if ($currentTab !== 'comments'): ?>
      <div>
        <label class="text-xs text-onSurfaceVariant mb-1 block">Trạng thái</label>
        <select name="status" class="w-full rounded-xl py-2 bg-black/8">
          <option value="">Tất cả trạng thái</option>
          <option value="active" <?php echo (($status ?? '')==='active') ? 'selected' : ''; ?>>Công khai</option>
          <option value="hidden" <?php echo (($status ?? '')==='hidden') ? 'selected' : ''; ?>>Ẩn</option>
          <option value="pending" <?php echo (($status ?? '')==='pending') ? 'selected' : ''; ?>>Chờ duyệt</option>
        </select>
      </div>

      <div>
        <label class="text-xs text-onSurfaceVariant mb-1 block">Điểm</label>
        <select name="rating" class="w-full rounded-xl py-2 bg-black/8">
          <option value="">Tất cả</option>
          <?php for ($val = 5.0; $val >= 0.5; $val -= 0.5): $s = number_format($val, 1, '.', ''); ?>
            <option value="<?php echo $s; ?>" <?php echo ((string)($rating ?? '') === (string)$s) ? 'selected' : ''; ?>><?php echo $s; ?> ⭐</option>
          <?php endfor; ?>
        </select>
      </div>
      <?php endif; ?>

      <div>
        <label class="text-xs text-onSurfaceVariant mb-1 block">Thời gian</label>
        <select name="time" class="w-full rounded-xl py-2 bg-black/8">
          <option value="">Mọi lúc</option>
          <option value="24h" <?php echo (($time ?? '')==='24h') ? 'selected' : ''; ?>>24 giờ</option>
          <option value="7d" <?php echo (($time ?? '')==='7d') ? 'selected' : ''; ?>>7 ngày</option>
          <option value="30d" <?php echo (($time ?? '')==='30d') ? 'selected' : ''; ?>>30 ngày</option>
        </select>
      </div>

      <div class="flex gap-2 md:col-span-1">
        <button class="ml-auto w-full rounded-xl bg-primaryContainer text-white py-2">Lọc</button>
      </div>
    </form>

    <!-- Bulk actions -->
    <div class="flex items-center gap-3 mb-4">
      <div class="flex items-center gap-2">
        <input id="selectAll" type="checkbox" class="h-5 w-5" />
        <label for="selectAll" class="text-sm text-onSurfaceVariant">Chọn tất cả</label>
      </div>
      <div class="ml-auto flex items-center gap-2">
        <button id="bulkHide" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-500 text-white hover:brightness-105"><span class="material-symbols-outlined">visibility_off</span> Ẩn</button>
        <button id="bulkUnhide" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white hover:brightness-105"><span class="material-symbols-outlined">visibility</span> Bỏ Ẩn</button>
        <button id="bulkDelete" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white hover:brightness-95"><span class="material-symbols-outlined">delete</span> Xóa</button>
      </div>
    </div>

    <!-- Table / List (reviews only) -->
    <?php
      $items = ($currentTab === 'comments') ? ($comments ?? []) : ($reviews ?? []);

      function normalizeAvatarSrc($a) {
        if (empty($a)) return null;
        if (preg_match('#^https?://#i', $a)) return $a;
        // ensure leading slash
        if ($a[0] !== '/') $a = '/' . $a;
        // url() will prefix base_path
        return url($a);
      }

      // simple inline SVG fallback avatar (ensures image always shows)
      $defaultAvatar = 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="6" fill="#1f2937"/><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-6 1.67-6 4v1h12v-1c0-2.33-2.67-4-6-4z" fill="#9ca3af"/></svg>');

    ?>
    <div class="divide-y divide-white/5 rounded-xl overflow-hidden border border-white/4">
      <?php foreach ($items as $it): ?>
        <?php $itemStatus = strtolower(trim((string)($it['status'] ?? 'active'))); ?>
        <?php $reviewLinkId = $currentTab === 'comments' ? (int) ($it['review_id'] ?? 0) : (int) $it['id']; ?>
        <div class="flex items-start gap-4 p-4 hover:bg-white/3 transition-colors">
          <div class="flex-shrink-0">
            <input class="rowCheckbox h-5 w-5" data-id="<?php echo (int)$it['id']; ?>" type="checkbox">
          </div>
          <div class="w-12">
            <?php $src = normalizeAvatarSrc((string)($it['avatar_url'] ?? $it['anhDaiDien'] ?? '')); ?>
            <img src="<?php echo e($src ?: $defaultAvatar); ?>" alt="avatar" class="h-10 w-10 rounded-full object-cover">
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-3">
              <div class="min-w-0">
                <div class="text-sm font-medium">
                  <a href="<?php echo url('/profile/' . (int)($it['user_id'] ?? 0)); ?>" class="hover:underline"><?php echo e($it['display_name'] ?? 'Người dùng'); ?></a>
                  <span class="text-xs text-onSurfaceVariant"> • <?php echo e($it['movie_title_vi'] ?? ($it['movie_title_en'] ?? '')); ?></span>
                </div>
                <div class="mt-1 text-sm text-onSurfaceVariant truncate">
                  <?php if (isset($it['title']) && $it['title'] !== ''): ?>
                    <strong class="mr-2"><?php echo e($it['title']); ?></strong>
                  <?php endif; ?>
                  <?php echo e(mb_substr($it['body'] ?? ($it['excerpt'] ?? ''), 0, 220)); ?>
                </div>
              </div>
              <div class="text-right">
                <div class="text-sm font-semibold">ID <?php echo (int)$it['id']; ?></div>
                <div class="text-xs text-onSurfaceVariant mt-1"><?php echo !empty($it['created_at']) ? date('d/m/Y H:i', strtotime($it['created_at'])) : ''; ?></div>
              </div>
            </div>

            <div class="mt-3 flex items-center justify-between">
              <div class="flex items-center gap-2 text-xs text-onSurfaceVariant">
                <span class="px-2 py-1 rounded-full bg-white/6"><?php echo e($it['status'] ?? 'active'); ?></span>
                <?php if (isset($it['rating'])): ?>
                  <span class="px-2 py-1 rounded-full bg-white/6"><?php echo e((string)($it['rating'] ?? '')); ?> ⭐</span>
                <?php endif; ?>
              </div>
              <div class="flex items-center gap-2">
                <?php if ($reviewLinkId > 0): ?>
                  <a class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-white/6 text-xs hover:bg-white/10" href="<?php echo url('/reviews/' . $reviewLinkId); ?>" target="_blank" rel="noopener">Mở bài đánh giá</a>
                <?php endif; ?>
                <?php if ($itemStatus === 'hidden'): ?>
                  <button class="action-btn inline-flex items-center gap-2 px-3 py-1 rounded-md bg-emerald-600 text-white" data-action="unhide" data-type="<?php echo e($currentTab === 'comments' ? 'comment' : 'review'); ?>" data-id="<?php echo (int)$it['id']; ?>"><span class="material-symbols-outlined">visibility</span> Bỏ Ẩn</button>
                <?php else: ?>
                  <button class="action-btn inline-flex items-center gap-2 px-3 py-1 rounded-md bg-amber-500 text-white" data-action="hide" data-type="<?php echo e($currentTab === 'comments' ? 'comment' : 'review'); ?>" data-id="<?php echo (int)$it['id']; ?>"><span class="material-symbols-outlined">visibility_off</span> Ẩn</button>
                <?php endif; ?>
                <button class="action-btn inline-flex items-center gap-2 px-3 py-1 rounded-md bg-red-600 text-white" data-action="delete" data-type="<?php echo e($currentTab === 'comments' ? 'comment' : 'review'); ?>" data-id="<?php echo (int)$it['id']; ?>"><span class="material-symbols-outlined">delete</span> Xóa</button>
                <button class="view-full inline-flex items-center gap-2 px-3 py-1 rounded-md bg-white/6 text-xs" data-type="<?php echo e($currentTab === 'comments' ? 'comment' : 'review'); ?>" data-id="<?php echo (int)$it['id']; ?>">Xem đầy đủ</button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-end">
      <?php // simple pagination placeholder - reuse $page and $perPage if available ?>
      <nav class="inline-flex items-center gap-2">
        <?php $prev = max(1, ($page ?? 1)-1); $next = ($page ?? 1)+1; ?>
        <a href="<?php echo url('/admin/moderation?tab=' . e($tab) . '&page=' . $prev); ?>" class="px-3 py-1 rounded-md bg-white/6">‹ Trước</a>
        <span class="px-3 py-1 rounded-md bg-white/4">Trang <?php echo ($page ?? 1); ?></span>
        <a href="<?php echo url('/admin/moderation?tab=' . e($tab) . '&page=' . $next); ?>" class="px-3 py-1 rounded-md bg-white/6">Tiếp ›</a>
      </nav>
    </div>

  </div>
</section>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>

<script>
(() => {
  const postJson = async (url, body) => {
    const resp = await fetch(url, {
      method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(body), credentials:'same-origin'
    });
    return resp.json();
  };

  const getSelectedIds = () => Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(i=>i.getAttribute('data-id'));

  // Delegate action button clicks to handle hide/delete for reviews
  document.addEventListener('click', async (ev) => {
    const btn = ev.target.closest && ev.target.closest('.action-btn');
    if (!btn) return;
    const id = btn.getAttribute('data-id');
    const type = btn.getAttribute('data-type') || 'review';
    const action = btn.getAttribute('data-action');
    if (!id || !action) return;

    if (action === 'delete') {
      if (!confirm('Xác nhận xóa đánh giá này?')) return;
    }

    try {
      btn.disabled = true;
      const res = await postJson('<?php echo url('/admin/moderation/action'); ?>', { action, type, ids:[id] });
      btn.disabled = false;
      if (res && res.success) {
        location.reload();
      } else {
        alert(res && res.message ? res.message : 'Hành động thất bại');
      }
    } catch (e) {
      btn.disabled = false;
      alert('Lỗi kết nối');
    }
  });

  document.getElementById('bulkHide').addEventListener('click', async ()=>{
    const ids = getSelectedIds(); if (!ids.length) return alert('Chọn ít nhất một mục');
    try {
      const res = await postJson('<?php echo url('/admin/moderation/action'); ?>', { action:'hide', type:'<?php echo e($currentTab === 'comments' ? 'comment' : 'review'); ?>', ids });
      if (res && res.success) location.reload(); else alert(res && res.message ? res.message : 'Hành động thất bại');
    } catch (e) { alert('Lỗi kết nối'); }
  });
  document.getElementById('bulkUnhide').addEventListener('click', async ()=>{
    const ids = getSelectedIds(); if (!ids.length) return alert('Chọn ít nhất một mục');
    try {
      const res = await postJson('<?php echo url('/admin/moderation/action'); ?>', { action:'unhide', type:'<?php echo e($currentTab === 'comments' ? 'comment' : 'review'); ?>', ids });
      if (res && res.success) location.reload(); else alert(res && res.message ? res.message : 'Hành động thất bại');
    } catch (e) { alert('Lỗi kết nối'); }
  });
  document.getElementById('bulkDelete').addEventListener('click', async ()=>{
    const ids = getSelectedIds(); if (!ids.length) return alert('Chọn ít nhất một mục');
    if(!confirm('Xác nhận xóa các đánh giá đã chọn?')) return;
    try {
      const res = await postJson('<?php echo url('/admin/moderation/action'); ?>', { action:'delete', type:'<?php echo e($currentTab === 'comments' ? 'comment' : 'review'); ?>', ids });
      if (res && res.success) location.reload(); else alert(res && res.message ? res.message : 'Hành động thất bại');
    } catch (e) { alert('Lỗi kết nối'); }
  });

  document.getElementById('selectAll').addEventListener('change', (e)=>{
    const checked = e.target.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = checked);
  });

  document.querySelectorAll('.view-full').forEach(b=>b.addEventListener('click', async ()=>{
    const id = b.getAttribute('data-id');
    const type = b.getAttribute('data-type');
    const res = await postJson('<?php echo url('/admin/moderation/detail'); ?>?type=' + encodeURIComponent(type) + '&id=' + encodeURIComponent(id));
    if (!res || !res.success) { alert('Không tìm thấy nội dung'); return; }
    const item = res.item;
    // render into modal
    const modal = document.getElementById('detailModal');
    modal.querySelector('.modal-title').textContent = type === 'review' ? (item.title || 'Review') : 'Bình luận';
    let bodyHtml = '';
    if (type === 'review') {
      bodyHtml += `<div class="mb-2"><strong>Người đăng:</strong> <a href="<?php echo url('/profile/'); ?>${item.user_id}">${item.display_name}</a></div>`;
      bodyHtml += `<div class="mb-2"><strong>Phim:</strong> <a href="<?php echo url('/movies/'); ?>${item.movie_id}">${item.movie_title_vi || item.movie_title_en || ''}</a></div>`;
      bodyHtml += `<div class="mb-2"><strong>Điểm:</strong> ${item.rating}</div>`;
      if (item.poster_url) bodyHtml += `<div class="mb-4"><img src="${item.poster_url}" class="max-h-48 rounded"></div>`;
      bodyHtml += `<div>${item.body}</div>`;
      bodyHtml += `<div class="mt-4"><a class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-primaryContainer text-white" href="<?php echo url('/reviews/'); ?>${item.id}" target="_blank" rel="noopener">Mở bài đánh giá</a></div>`;
    } else {
      bodyHtml += `<div class="mb-2"><strong>Người đăng:</strong> <a href="<?php echo url('/profile/'); ?>${item.user_id}">${item.display_name}</a></div>`;
      bodyHtml += `<div class="mb-2"><strong>Phim:</strong> <a href="<?php echo url('/movies/'); ?>${item.movie_id}">${item.movie_title_vi || ''}</a></div>`;
      bodyHtml += `<div>${item.body}</div>`;
      if (item.review_id) {
        bodyHtml += `<div class="mt-4"><a class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-primaryContainer text-white" href="<?php echo url('/reviews/'); ?>${item.review_id}" target="_blank" rel="noopener">Mở bài đánh giá</a></div>`;
      }
    }
    modal.querySelector('.modal-body').innerHTML = bodyHtml;
    modal.classList.remove('hidden');
  }));

  // modal close
  document.addEventListener('click', (e)=>{
    const modal = document.getElementById('detailModal');
    if (!modal) return;
    if (e.target.matches('.modal-close') || e.target.closest('.modal-close')) {
      modal.classList.add('hidden');
    }
  });
})();
</script>

<!-- Detail modal -->
<div id="detailModal" class="fixed inset-0 z-50 flex items-start justify-center p-6 bg-black/60 hidden">
  <div class="bg-surface rounded-2xl max-w-3xl w-full p-6">
    <div class="flex items-center justify-between mb-4">
      <h3 class="modal-title font-headline text-xl">Chi tiết</h3>
      <button class="modal-close rounded-full border px-3 py-1">Đóng</button>
    </div>
    <div class="modal-body text-sm text-onSurface"></div>
    <div class="mt-4 text-right">
      <button class="modal-close rounded-full border px-4 py-2">Đóng</button>
    </div>
  </div>
</div>
