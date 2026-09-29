<?php
declare(strict_types=1);

class User extends Model
{
    private function baseSelect(): string
    {
        $statusExpr = "'active' AS status, ";
        if ($this->columnExists('trangThai')) {
            $statusExpr = 'trangThai AS status, ';
        } elseif ($this->columnExists('status')) {
            $statusExpr = 'status AS status, ';
        } elseif ($this->columnExists('biKhoa')) {
            $statusExpr = "CASE WHEN biKhoa = 1 THEN 'locked' ELSE 'active' END AS status, ";
        } elseif ($this->columnExists('is_locked')) {
            $statusExpr = "CASE WHEN is_locked = 1 THEN 'locked' ELSE 'active' END AS status, ";
        }

        return 'SELECT ' .
            'maNguoiDung AS id, ' .
            'tenDangNhap AS display_name, ' .
            'email, ' .
            'matKhau AS password_hash, ' .
            'anhDaiDien AS avatar_url, ' .
            'tieuSu AS bio, ' .
            "CASE WHEN vaiTro = 'QuanTriVien' THEN 'admin' ELSE 'member' END AS role, " .
            $statusExpr .
            'ngayTao AS created_at, ' .
            'ngayCapNhat AS updated_at ' .
            'FROM NguoiDung';
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch($this->baseSelect() . ' WHERE email = :email LIMIT 1', [
            'email' => $email,
        ]);
    }

    public function findByDisplayName(string $displayName): ?array
    {
        return $this->db->fetch($this->baseSelect() . ' WHERE tenDangNhap = :display_name LIMIT 1', [
            'display_name' => $displayName,
        ]);
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch($this->baseSelect() . ' WHERE maNguoiDung = :id LIMIT 1', [
            'id' => $id,
        ]);
    }

    public function findAll(): array
    {
        return $this->db->fetchAll($this->baseSelect() . ' ORDER BY ngayTao DESC');
    }

    public function countAll(): int
    {
        $row = $this->db->fetch('SELECT COUNT(*) AS cnt FROM NguoiDung');
        return (int) ($row['cnt'] ?? 0);
    }

    public function countCreatedOn(string $date): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS cnt FROM NguoiDung WHERE DATE(ngayTao) = :day',
            ['day' => $date]
        );

        return (int) ($row['cnt'] ?? 0);
    }

    public function countCreatedToday(): int
    {
        return $this->countCreatedOn(date('Y-m-d'));
    }

    public function countCreatedBetween(string $start, string $end): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS cnt FROM NguoiDung WHERE ngayTao >= :start AND ngayTao < :end',
            ['start' => $start, 'end' => $end]
        );

        return (int) ($row['cnt'] ?? 0);
    }

    public function countCreatedAtHour(string $date, int $hour): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS cnt FROM NguoiDung WHERE DATE(ngayTao) = :day AND HOUR(ngayTao) = :hour',
            ['day' => $date, 'hour' => $hour]
        );

        return (int) ($row['cnt'] ?? 0);
    }

    public function getRecentRegistrations(int $limit = 5): array
    {
        $limit = max(1, (int) $limit);

        return $this->db->fetchAll(
            'SELECT ' .
                'maNguoiDung AS id, ' .
                'tenDangNhap AS display_name, ' .
                'email, ' .
                'ngayTao AS created_at ' .
            'FROM NguoiDung ' .
            'ORDER BY ngayTao DESC LIMIT ' . $limit
        );
    }

    public function findAllPaginated(int $page, int $perPage = 10): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $limit = (int) $perPage;
        $offset = (int) $offset;
        $sql = $this->baseSelect() . " ORDER BY ngayTao DESC LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql);
    }

    public function searchUsers(string $q, int $limit = 50, int $offset = 0): array
    {
        $q = trim($q);
        if ($q === '') {
            return [];
        }

        $like = '%' . $q . '%';
        $sql = $this->baseSelect();
        $sql .= ' WHERE (' . $this->buildUnaccentExpr('tenDangNhap') . ' LIKE :uq OR ' . $this->buildUnaccentExpr('email') . ' LIKE :uq) ';
        $sql .= ' ORDER BY ngayTao DESC LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;

        $uq = $this->normalizeVietnamese($q);
        return $this->db->fetchAll($sql, ['uq' => '%' . $uq . '%']);
    }

    public function countFiltered(string $q, string $role = ''): int
    {
        $q = trim($q);
        if ($q === '') {
            $row = $this->db->fetch('SELECT COUNT(*) AS cnt FROM NguoiDung');
            return (int) ($row['cnt'] ?? 0);
        }

        $sql = 'SELECT COUNT(*) AS cnt FROM NguoiDung WHERE (' . $this->buildUnaccentExpr('tenDangNhap') . ' LIKE :uq OR ' . $this->buildUnaccentExpr('email') . ' LIKE :uq)';
        $params = ['uq' => '%' . $this->normalizeVietnamese($q) . '%'];

        if ($role === 'admin') {
            $sql .= ' AND vaiTro = :admin_role';
            $params['admin_role'] = 'QuanTriVien';
        } elseif ($role === 'member') {
            $sql .= ' AND (vaiTro IS NULL OR vaiTro <> :admin_role)';
            $params['admin_role'] = 'QuanTriVien';
        }

        $row = $this->db->fetch($sql, $params);
        return (int) ($row['cnt'] ?? 0);
    }

    private function normalizeVietnamese(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $map = [
            '/[àáạảãâầấậẩẫăằắặẳẵ]/u' => 'a',
            '/[èéẹẻẽêềếệểễ]/u' => 'e',
            '/[ìíịỉĩ]/u' => 'i',
            '/[òóọỏõôồốộổỗơờớợởỡ]/u' => 'o',
            '/[ùúụủũưừứựửữ]/u' => 'u',
            '/[ỳýỵỷỹ]/u' => 'y',
            '/[đ]/u' => 'd',
            '/[^a-z0-9@\.\-_ ]/u' => '',
        ];
        return preg_replace(array_keys($map), array_values($map), $s);
    }

    private function buildUnaccentExpr(string $column): string
    {
        $expr = "LOWER({$column})";
        // apply chained REPLACE for common Vietnamese diacritics so SQL can compare without accents
        $replacements = [
            ['à','a'],['á','a'],['ạ','a'],['ả','a'],['ã','a'],['â','a'],['ầ','a'],['ấ','a'],['ậ','a'],['ẩ','a'],['ẫ','a'],['ă','a'],['ằ','a'],['ắ','a'],['ặ','a'],['ẳ','a'],['ẵ','a'],
            ['è','e'],['é','e'],['ẹ','e'],['ẻ','e'],['ẽ','e'],['ê','e'],['ề','e'],['ế','e'],['ệ','e'],['ể','e'],['ễ','e'],
            ['ì','i'],['í','i'],['ị','i'],['ỉ','i'],['ĩ','i'],
            ['ò','o'],['ó','o'],['ọ','o'],['ỏ','o'],['õ','o'],['ô','o'],['ồ','o'],['ố','o'],['ộ','o'],['ổ','o'],['ỗ','o'],['ơ','o'],['ờ','o'],['ớ','o'],['ợ','o'],['ở','o'],['ỡ','o'],
            ['ù','u'],['ú','u'],['ụ','u'],['ủ','u'],['ũ','u'],['ư','u'],['ừ','u'],['ứ','u'],['ự','u'],['ử','u'],['ữ','u'],
            ['ỳ','y'],['ý','y'],['ỵ','y'],['ỷ','y'],['ỹ','y'],
            ['đ','d'],
            ['À','a'],['Á','a'],['Ạ','a'],['Ả','a'],['Ã','a'],['Â','a'],['Ầ','a'],['Ấ','a'],['Ậ','a'],['Ẩ','a'],['Ẫ','a'],['Ă','a'],['Ằ','a'],['Ắ','a'],['Ặ','a'],['Ẳ','a'],['Ẵ','a'],
            ['È','e'],['É','e'],['Ẹ','e'],['Ẻ','e'],['Ẽ','e'],['Ê','e'],['Ề','e'],['Ế','e'],['Ệ','e'],['Ể','e'],['Ễ','e'],
            ['Ì','i'],['Í','i'],['Ị','i'],['Ỉ','i'],['Ĩ','i'],
            ['Ò','o'],['Ó','o'],['Ọ','o'],['Ỏ','o'],['Õ','o'],['Ô','o'],['Ồ','o'],['Ố','o'],['Ộ','o'],['Ổ','o'],['Ỗ','o'],['Ơ','o'],['Ờ','o'],['Ớ','o'],['Ợ','o'],['Ở','o'],['Ỡ','o'],
            ['Ù','u'],['Ú','u'],['Ụ','u'],['Ủ','u'],['Ũ','u'],['Ư','u'],['Ừ','u'],['Ứ','u'],['Ự','u'],['Ử','u'],['Ữ','u'],
            ['Ỳ','y'],['Ý','y'],['Ỵ','y'],['Ỷ','y'],['Ỹ','y'],
            ['Đ','d']
        ];

        foreach ($replacements as $pair) {
            $from = $pair[0];
            $to = $pair[1];
            $expr = "REPLACE({$expr}, '{$from}', '{$to}')";
        }

        return $expr;
    }

    public function create(string $email, string $password, string $displayName): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->db->query(
            'INSERT INTO NguoiDung (tenDangNhap, email, matKhau, vaiTro, ngayTao, ngayCapNhat) VALUES (:display_name, :email, :hash, :role, NOW(), NOW())',
            [
                'display_name' => $displayName,
                'email' => $email,
                'hash' => $hash,
                'role' => 'ThanhVien',
            ]
        );

        return (int) $this->db->pdo()->lastInsertId();
    }

    public function createWithHash(string $email, string $hash, string $displayName): int
    {
        $this->db->query(
            'INSERT INTO NguoiDung (tenDangNhap, email, matKhau, vaiTro, ngayTao, ngayCapNhat) VALUES (:display_name, :email, :hash, :role, NOW(), NOW())',
            [
                'display_name' => $displayName,
                'email' => $email,
                'hash' => $hash,
                'role' => 'ThanhVien',
            ]
        );

        return (int) $this->db->pdo()->lastInsertId();
    }

    public function updatePasswordHash(int $id, string $hash): void
    {
        $this->db->query(
            'UPDATE NguoiDung SET matKhau = :hash, ngayCapNhat = NOW() WHERE maNguoiDung = :id',
            [
                'hash' => $hash,
                'id' => $id,
            ]
        );
    }

    public function isDisplayNameTaken(string $displayName, int $excludeId): bool
    {
        $row = $this->db->fetch(
            'SELECT maNguoiDung FROM NguoiDung WHERE tenDangNhap = :display_name AND maNguoiDung <> :id LIMIT 1',
            [
                'display_name' => $displayName,
                'id' => $excludeId,
            ]
        );

        return $row !== null;
    }

    public function updateProfile(int $id, string $displayName, string $bio, ?string $avatarUrl): void
    {
        $sql = 'UPDATE NguoiDung SET tenDangNhap = :display_name, tieuSu = :bio';
        $params = [
            'display_name' => $displayName,
            'bio' => $bio,
            'id' => $id,
        ];

        if ($avatarUrl !== null) {
            $sql .= ', anhDaiDien = :avatar';
            $params['avatar'] = $avatarUrl;
        }

        $sql .= ', ngayCapNhat = NOW() WHERE maNguoiDung = :id';

        $this->db->query($sql, $params);
    }

    public function getReviewsByUser(int $userId, int $limit = 8): array
    {
        $limit = max(1, (int) $limit);
        return $this->db->fetchAll(
            'SELECT r.maDanhGia AS id, r.tieuDe AS title, r.diemSo AS rating, r.ngayTao AS created_at, ' .
                'p.maPhim AS movie_id, p.tenPhimVN AS title_vi, p.tenPhimEN AS title_en, ' .
                'p.duongDanPoster AS poster_url, ' .
                'LEFT(r.noiDung, 220) AS excerpt ' .
            'FROM DanhGia r JOIN Phim p ON p.maPhim = r.maPhim ' .
            'WHERE r.maNguoiDung = :id ORDER BY r.ngayTao DESC LIMIT ' . $limit,
            [
                'id' => $userId,
            ]
        );
    }

    public function getLoggedMoviesByUser(int $userId, int $limit = 8): array
    {
        $limit = max(1, (int) $limit);
        return $this->db->fetchAll(
            'SELECT p.maPhim AS id, p.tenPhimVN AS title_vi, p.tenPhimEN AS title_en, ' .
                'p.duongDanPoster AS poster_url, MAX(r.ngayTao) AS last_log ' .
            'FROM DanhGia r JOIN Phim p ON p.maPhim = r.maPhim ' .
            'WHERE r.maNguoiDung = :id ' .
            'GROUP BY p.maPhim, p.tenPhimVN, p.tenPhimEN, p.duongDanPoster ' .
            'ORDER BY last_log DESC LIMIT ' . $limit,
            [
                'id' => $userId,
            ]
        );
    }

    public function getListsByUser(int $userId, int $limit = 8): array
    {
        $limit = max(1, (int) $limit);
        return $this->db->fetchAll(
            'SELECT maDanhSach AS id, tenDanhSach AS name, moTa AS description, congKhai AS is_public, ngayTao AS created_at ' .
            'FROM DanhSachTuyChinh WHERE maNguoiDung = :id ORDER BY ngayTao DESC LIMIT ' . $limit,
            [
                'id' => $userId,
            ]
        );
    }

    private function columnExists(string $column): bool
    {
        $row = $this->db->fetch(
            "SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'NguoiDung' AND COLUMN_NAME = :col",
            ['col' => $column]
        );
        return (int) ($row['cnt'] ?? 0) > 0;
    }

    public function getStatus(int $id): string
    {
        // try known status columns
        if ($this->columnExists('trangThai')) {
            $row = $this->db->fetch('SELECT trangThai AS s FROM NguoiDung WHERE maNguoiDung = :id LIMIT 1', ['id' => $id]);
            return $row['s'] ?? 'active';
        }
        if ($this->columnExists('status')) {
            $row = $this->db->fetch('SELECT status AS s FROM NguoiDung WHERE maNguoiDung = :id LIMIT 1', ['id' => $id]);
            return $row['s'] ?? 'active';
        }
        if ($this->columnExists('biKhoa')) {
            $row = $this->db->fetch('SELECT biKhoa AS s FROM NguoiDung WHERE maNguoiDung = :id LIMIT 1', ['id' => $id]);
            return (!empty($row['s']) && (int)$row['s'] === 1) ? 'locked' : 'active';
        }
        if ($this->columnExists('is_locked')) {
            $row = $this->db->fetch('SELECT is_locked AS s FROM NguoiDung WHERE maNguoiDung = :id LIMIT 1', ['id' => $id]);
            return (!empty($row['s']) && (int)$row['s'] === 1) ? 'locked' : 'active';
        }

        return 'active';
    }

    public function availableStatusColumn(): ?string
    {
        $candidates = ['trangThai', 'status', 'biKhoa', 'is_locked'];
        foreach ($candidates as $c) {
            if ($this->columnExists($c)) {
                return $c;
            }
        }
        return null;
    }

    public function setLocked(int $id, bool $locked): bool
    {
        $val = $locked ? 'locked' : 'active';
        if ($this->columnExists('trangThai')) {
            $this->db->query('UPDATE NguoiDung SET trangThai = :val, ngayCapNhat = NOW() WHERE maNguoiDung = :id', ['val' => $val, 'id' => $id]);
            return true;
        }
        if ($this->columnExists('status')) {
            $this->db->query('UPDATE NguoiDung SET status = :val, ngayCapNhat = NOW() WHERE maNguoiDung = :id', ['val' => $val, 'id' => $id]);
            return true;
        }
        if ($this->columnExists('biKhoa')) {
            $this->db->query('UPDATE NguoiDung SET biKhoa = :val, ngayCapNhat = NOW() WHERE maNguoiDung = :id', ['val' => $locked ? 1 : 0, 'id' => $id]);
            return true;
        }
        if ($this->columnExists('is_locked')) {
            $this->db->query('UPDATE NguoiDung SET is_locked = :val, ngayCapNhat = NOW() WHERE maNguoiDung = :id', ['val' => $locked ? 1 : 0, 'id' => $id]);
            return true;
        }

        // fallback: no known status column, cannot lock at DB level
        return false;
    }

    public function setRole(int $id, string $role): bool
    {
        $v = $role === 'admin' ? 'QuanTriVien' : 'ThanhVien';
        $this->db->query('UPDATE NguoiDung SET vaiTro = :v, ngayCapNhat = NOW() WHERE maNguoiDung = :id', ['v' => $v, 'id' => $id]);
        return true;
    }
}
