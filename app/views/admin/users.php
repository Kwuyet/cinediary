<?php
$title = 'Quản lý người dùng';
require __DIR__ . '/../layouts/admin_head.php';
?>

<section class="max-w-7xl mx-auto space-y-6">
  <div class="rounded-3xl border border-white/5 bg-white/5 p-6 lg:p-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
      <div>
        <p class="text-sm uppercase tracking-[0.25em] text-onSurfaceVariant">Người dùng</p>
        <h1 class="mt-2 font-headline text-4xl">Quản lý người dùng</h1>
        <p class="mt-2 text-onSurfaceVariant">Chọn sidebar để sang trang khác. Trang này có phân trang riêng theo danh sách tài khoản.</p>
      </div>
      <div class="text-sm text-onSurfaceVariant">Tổng: <?php echo (int) ($totalUsers ?? 0); ?> người dùng</div>
    </div>
  </div>

  <div class="rounded-3xl border border-white/5 bg-white/5 p-6">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
      <div class="text-sm text-onSurfaceVariant">Hiển thị trang <?php echo (int) ($page ?? 1); ?> / <?php echo (int) ($totalPages ?? 1); ?></div>
      <form method="get" class="flex items-center gap-3 relative" id="userSearchForm">
        <div class="relative w-80">
          <input id="userSearchInput" name="q" value="<?php echo e($q ?? ''); ?>" autocomplete="off" class="rounded-2xl border border-white/5 bg-black/20 px-4 py-3 outline-none w-full" placeholder="Tìm người dùng...">
          <ul id="userSuggest" class="hidden absolute left-0 right-0 z-40 mt-1 max-h-48 overflow-auto rounded-xl border border-white/10 bg-[#0f0f0f] p-1 text-sm"></ul>
        </div>
        <select name="role" class="rounded-2xl border border-white/5 bg-black/20 px-4 py-3 outline-none">
          <option value="" <?php echo empty($role) ? 'selected' : ''; ?>>Tất cả vai trò</option>
          <option value="admin" <?php echo (($role ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
          <option value="member" <?php echo (($role ?? '') === 'member') ? 'selected' : ''; ?>>Member</option>
        </select>
        <button type="submit" class="rounded-2xl bg-primaryContainer px-4 py-3 text-sm font-semibold text-white">Tìm</button>
      </form>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/5">
      <table class="min-w-full text-left text-sm">
        <thead class="bg-black/20 text-onSurfaceVariant">
          <tr>
            <th class="px-4 py-3">Tên</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Vai trò</th><th class="px-4 py-3">Trạng thái</th><th class="px-4 py-3">Ngày tạo</th><th class="px-4 py-3">Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($users ?? []) as $user): ?>
            <tr class="border-t border-white/5 even:bg-white/5">
              <td class="px-4 py-3"><?php echo e($user['display_name'] ?? ''); ?></td>
              <td class="px-4 py-3"><?php echo e($user['email'] ?? ''); ?></td>
              <td class="px-4 py-3"><?php echo e($user['role'] ?? ''); ?></td>
              <td class="px-4 py-3"><?php echo e($user['status'] ?? ''); ?></td>
              <td class="px-4 py-3"><?php echo !empty($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : '—'; ?></td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <button data-id="<?php echo (int) ($user['id'] ?? 0); ?>" class="lock-btn rounded-full border border-white/10 px-3 py-1 text-xs">
                    <?php echo (($user['status'] ?? '') === 'locked') ? 'Mở' : 'Khóa'; ?>
                  </button>
                  <select class="role-select rounded-2xl border border-white/5 bg-black/10 px-3 py-1 text-xs" data-id="<?php echo (int) ($user['id'] ?? 0); ?>">
                    <option value="member" <?php echo (($user['role'] ?? '') === 'member') ? 'selected' : ''; ?>>Member</option>
                    <option value="admin" <?php echo (($user['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
                  </select>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-5 flex items-center justify-between text-sm text-onSurfaceVariant">
      <span>Trang <?php echo (int) ($page ?? 1); ?> / <?php echo (int) ($totalPages ?? 1); ?></span>
      <div class="flex gap-2">
        <?php if (($page ?? 1) > 1): ?>
          <a class="rounded-full border border-white/10 px-3 py-1" href="<?php echo url('/admin/users?page=' . ((int) $page - 1) . '&q=' . urlencode((string) ($q ?? '')) . '&role=' . urlencode((string) ($role ?? ''))); ?>">Trước</a>
        <?php endif; ?>
        <?php if (($page ?? 1) < ($totalPages ?? 1)): ?>
          <a class="rounded-full border border-white/10 px-3 py-1" href="<?php echo url('/admin/users?page=' . ((int) $page + 1) . '&q=' . urlencode((string) ($q ?? '')) . '&role=' . urlencode((string) ($role ?? ''))); ?>">Sau</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
<script>
(() => {
  const input = document.getElementById('userSearchInput');
  const suggest = document.getElementById('userSuggest');
  let timer = 0;

  const renderList = (items) => {
    if (!items || items.length === 0) {
      suggest.classList.add('hidden');
      suggest.innerHTML = '';
      return;
    }
    suggest.innerHTML = items.map(it => `
      <li class="cursor-pointer rounded-md px-3 py-2 hover:bg-white/5" data-id="${it.id}" data-name="${it.display_name}">
        <div class="font-semibold">${it.display_name}</div>
        <div class="text-xs text-onSurfaceVariant">${it.email || ''}</div>
      </li>
    `).join('');
    suggest.classList.remove('hidden');
  };

  input.addEventListener('input', (e) => {
    clearTimeout(timer);
    const q = input.value.trim();
    if (q.length < 1) { renderList([]); return; }
    timer = setTimeout(() => {
      fetch(`<?php echo url('/admin/users/suggest'); ?>?q=` + encodeURIComponent(q))
        .then(r => r.json())
        .then(renderList)
        .catch(() => renderList([]));
    }, 200);
  });

  suggest.addEventListener('click', (ev) => {
    const li = ev.target.closest('li[data-id]');
    if (!li) return;
    const name = li.getAttribute('data-name');
    input.value = name;
    suggest.classList.add('hidden');
    document.getElementById('userSearchForm').submit();
  });

  document.addEventListener('click', (e) => {
    if (!e.target.closest('#userSearchForm')) {
      suggest.classList.add('hidden');
    }
  });
})();
</script>
<script>
(() => {
  const postJson = async (url, body) => {
    const resp = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
      credentials: 'same-origin',
    });
    const text = await resp.text();
    try {
      return { status: resp.status, json: JSON.parse(text), text };
    } catch (e) {
      return { status: resp.status, json: null, text };
    }
  };

  document.querySelectorAll('.lock-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const id = btn.getAttribute('data-id');
      const current = btn.textContent.trim();
      const locking = current === 'Khóa';
      btn.disabled = true;
        try {
          const res = await postJson(`<?php echo url('/admin/users/'); ?>${id}/lock`, { locked: locking });
          if (res && res.json && res.json.success) {
            btn.textContent = locking ? 'Mở' : 'Khóa';
          } else {
            const msg = (res && res.json && res.json.message) ? res.json.message : res && res.text ? res.text : 'Không thể cập nhật trạng thái';
            alert(`Lỗi (${res && res.status ? res.status : 'n/a'}): ` + msg);
            console.log('Debug response:', res);
          }
      } catch (err) {
        alert('Lỗi mạng');
      } finally { btn.disabled = false; }
    });
  });

  document.querySelectorAll('.role-select').forEach(sel => {
    sel.addEventListener('change', async () => {
      const id = sel.getAttribute('data-id');
      const role = sel.value;
      sel.disabled = true;
      try {
        const res = await postJson(`<?php echo url('/admin/users/'); ?>${id}/role`, { role });
        if (!res || !(res.json && res.json.success)) {
          const msg = res && res.json && res.json.message ? res.json.message : 'Không thể cập nhật vai trò';
          alert(msg);
        }
      } catch (err) {
        alert('Lỗi mạng');
      } finally { sel.disabled = false; }
    });
  });
})();
</script>