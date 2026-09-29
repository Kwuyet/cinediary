<?php
declare(strict_types=1);

class Comment extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureSchema();
    }

    private function ensureSchema(): void
    {
        $this->db->query(
            'CREATE TABLE IF NOT EXISTS BinhLuanThich (
                maBinhLuan INT NOT NULL,
                maNguoiDung INT NOT NULL,
                ngayTao DATETIME NOT NULL,
                PRIMARY KEY (maBinhLuan, maNguoiDung),
                CONSTRAINT fk_binhluanthich_binhluan FOREIGN KEY (maBinhLuan) REFERENCES BinhLuan(maBinhLuan) ON DELETE CASCADE,
                CONSTRAINT fk_binhluanthich_nguoidung FOREIGN KEY (maNguoiDung) REFERENCES NguoiDung(maNguoiDung) ON DELETE CASCADE
            ) ENGINE=InnoDB'
        );
    }

    public function getByReviewId(int $reviewId, ?int $viewerId = null): array
    {
        $viewerId = $viewerId ?? 0;
        return $this->db->fetchAll(
            'SELECT ' .
                'c.maBinhLuan AS id, ' .
                'c.maDanhGia AS review_id, ' .
                'c.maNguoiDung AS user_id, ' .
                'c.maBinhLuanCha AS parent_id, ' .
                'c.noiDung AS body, ' .
                "'active' AS status, " .
                'c.ngayTao AS created_at, ' .
                'c.ngayCapNhat AS updated_at, ' .
                'u.tenDangNhap AS display_name, ' .
                'u.anhDaiDien AS avatar_url, ' .
                "CASE WHEN u.vaiTro = 'QuanTriVien' THEN 'admin' ELSE 'member' END AS role, " .
                'COUNT(DISTINCT blt.maNguoiDung) AS like_count, ' .
                'CASE WHEN MAX(CASE WHEN blt_user.maNguoiDung IS NOT NULL THEN 1 ELSE 0 END) = 1 THEN 1 ELSE 0 END AS liked ' .
            'FROM BinhLuan c JOIN NguoiDung u ON u.maNguoiDung = c.maNguoiDung ' .
            'LEFT JOIN BinhLuanThich blt ON blt.maBinhLuan = c.maBinhLuan ' .
            'LEFT JOIN BinhLuanThich blt_user ON blt_user.maBinhLuan = c.maBinhLuan AND blt_user.maNguoiDung = :viewer_id ' .
            'WHERE c.maDanhGia = :review_id ' .
            'GROUP BY c.maBinhLuan, c.maDanhGia, c.maNguoiDung, c.maBinhLuanCha, c.noiDung, c.ngayTao, c.ngayCapNhat, u.tenDangNhap, u.anhDaiDien, u.vaiTro ' .
            'ORDER BY c.ngayTao ASC',
            [
                'review_id' => $reviewId,
                'viewer_id' => $viewerId,
            ]
        );
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            'SELECT ' .
                'c.maBinhLuan AS id, ' .
                'c.maDanhGia AS review_id, ' .
                'r.maPhim AS movie_id, ' .
                'c.maNguoiDung AS user_id, ' .
                'c.maBinhLuanCha AS parent_id, ' .
                'c.noiDung AS body, ' .
                'c.ngayTao AS created_at, ' .
                'c.ngayCapNhat AS updated_at, ' .
                'u.tenDangNhap AS display_name, ' .
                'u.anhDaiDien AS avatar_url, ' .
                "CASE WHEN u.vaiTro = 'QuanTriVien' THEN 'admin' ELSE 'member' END AS role " .
            'FROM BinhLuan c JOIN NguoiDung u ON u.maNguoiDung = c.maNguoiDung JOIN DanhGia r ON r.maDanhGia = c.maDanhGia ' .
            'WHERE c.maBinhLuan = :id LIMIT 1',
            [
                'id' => $id,
            ]
        );
    }

    public function create(int $reviewId, int $userId, string $body, ?int $parentId = null): void
    {
        $columns = 'maDanhGia, maNguoiDung, noiDung, ngayTao, ngayCapNhat';
        $placeholders = ':review_id, :user_id, :body, NOW(), NOW()';
        $params = [
            'review_id' => $reviewId,
            'user_id' => $userId,
            'body' => $body,
        ];

        if ($parentId !== null) {
            $columns .= ', maBinhLuanCha';
            $placeholders .= ', :parent_id';
            $params['parent_id'] = $parentId;
        }

        $this->db->query(
            'INSERT INTO BinhLuan (' . $columns . ') VALUES (' . $placeholders . ')',
            $params
        );
    }

    public function update(int $id, string $body): void
    {
        $this->db->query(
            'UPDATE BinhLuan SET noiDung = :body, ngayCapNhat = NOW() WHERE maBinhLuan = :id',
            [
                'body' => $body,
                'id' => $id,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->db->query(
            'DELETE FROM BinhLuan WHERE maBinhLuan = :id',
            [
                'id' => $id,
            ]
        );
    }

    public function availableStatusColumn(): ?string
    {
        $candidates = ['trangThai', 'status', 'trang_thai', 'tinhTrang'];
        foreach ($candidates as $c) {
            $row = $this->db->fetch("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'BinhLuan' AND COLUMN_NAME = :col", ['col' => $c]);
            if ((int) ($row['cnt'] ?? 0) > 0) return $c;
        }
        return null;
    }

    public function setStatus(int $id, string $status): bool
    {
        $col = $this->availableStatusColumn();
        if ($col === null) {
            try {
                $this->db->query("ALTER TABLE BinhLuan ADD COLUMN trangThai ENUM('active','pending','hidden') NOT NULL DEFAULT 'active'");
                $col = 'trangThai';
            } catch (Throwable $e) {
                return false;
            }
        }

        try {
            $sql = "UPDATE BinhLuan SET {$col} = :status, ngayCapNhat = NOW() WHERE maBinhLuan = :id";
            $this->db->query($sql, ['status' => $status, 'id' => $id]);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function countCreatedOn(string $date): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS cnt FROM BinhLuan WHERE DATE(ngayTao) = :day',
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
            'SELECT COUNT(*) AS cnt FROM BinhLuan WHERE ngayTao >= :start AND ngayTao < :end',
            ['start' => $start, 'end' => $end]
        );

        return (int) ($row['cnt'] ?? 0);
    }

    public function countCreatedAtHour(string $date, int $hour): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS cnt FROM BinhLuan WHERE DATE(ngayTao) = :day AND HOUR(ngayTao) = :hour',
            ['day' => $date, 'hour' => $hour]
        );

        return (int) ($row['cnt'] ?? 0);
    }

    public function countAll(): int
    {
        $row = $this->db->fetch('SELECT COUNT(*) AS cnt FROM BinhLuan');

        return (int) ($row['cnt'] ?? 0);
    }

    public function getRecentComments(int $limit = 5): array
    {
        $limit = max(1, (int) $limit);

        return $this->db->fetchAll(
            'SELECT ' .
                'c.maBinhLuan AS id, ' .
                'c.maDanhGia AS review_id, ' .
                'c.noiDung AS body, ' .
                'c.ngayTao AS created_at, ' .
                'u.tenDangNhap AS display_name, ' .
                'r.tieuDe AS review_title, ' .
                'p.tenPhimVN AS movie_title_vi ' .
            'FROM BinhLuan c ' .
            'JOIN NguoiDung u ON u.maNguoiDung = c.maNguoiDung ' .
            'JOIN DanhGia r ON r.maDanhGia = c.maDanhGia ' .
            'JOIN Phim p ON p.maPhim = r.maPhim ' .
            'ORDER BY c.ngayTao DESC LIMIT ' . $limit
        );
    }

    public function getForModeration(int $page, int $perPage, string $q = '', ?string $status = null, ?string $time = null): array
    {
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $offset = ($page - 1) * $perPage;

        $sql = 'SELECT c.maBinhLuan AS id, c.maDanhGia AS review_id, r.maPhim AS movie_id, c.maNguoiDung AS user_id, c.noiDung AS body, c.ngayTao AS created_at, u.tenDangNhap AS display_name, p.tenPhimVN AS movie_title_vi ';
        $statusColumn = $this->availableStatusColumn();
        if ($statusColumn !== null) {
            $sql .= ', c.' . $statusColumn . ' AS status ';
        } else {
            $sql .= ", 'active' AS status ";
        }
        $sql .= 'FROM BinhLuan c JOIN NguoiDung u ON u.maNguoiDung = c.maNguoiDung JOIN DanhGia r ON r.maDanhGia = c.maDanhGia JOIN Phim p ON p.maPhim = r.maPhim WHERE 1=1 ';
        $params = [];
        if ($q !== '') {
            $sql .= ' AND (u.tenDangNhap LIKE :q OR p.tenPhimVN LIKE :q OR p.tenPhimEN LIKE :q OR r.tieuDe LIKE :q OR c.noiDung LIKE :q) ';
            $params['q'] = '%' . $q . '%';
        }
        if ($status !== null && $status !== '') {
            $col = $this->availableStatusColumn();
            if ($col !== null) {
                $sql .= " AND TRIM(c.{$col}) = :st ";
                $params['st'] = $status;
            }
        }
        if ($time !== null && $time !== '') {
            if ($time === '24h') {
                $sql .= ' AND c.ngayTao >= DATE_SUB(NOW(), INTERVAL 1 DAY) ';
            } elseif ($time === '7d') {
                $sql .= ' AND c.ngayTao >= DATE_SUB(NOW(), INTERVAL 7 DAY) ';
            } elseif ($time === '30d') {
                $sql .= ' AND c.ngayTao >= DATE_SUB(NOW(), INTERVAL 30 DAY) ';
            }
        }

        $sql .= ' ORDER BY c.ngayTao DESC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset;

        return $this->db->fetchAll($sql, $params);
    }

    public function hasLike(int $commentId, int $userId): bool
    {
        $row = $this->db->fetch(
            'SELECT maBinhLuan FROM BinhLuanThich WHERE maBinhLuan = :comment_id AND maNguoiDung = :user_id LIMIT 1',
            [
                'comment_id' => $commentId,
                'user_id' => $userId,
            ]
        );

        return $row !== null;
    }

    public function addLike(int $commentId, int $userId): void
    {
        $this->db->query(
            'INSERT IGNORE INTO BinhLuanThich (maBinhLuan, maNguoiDung, ngayTao) VALUES (:comment_id, :user_id, NOW())',
            [
                'comment_id' => $commentId,
                'user_id' => $userId,
            ]
        );
    }

    public function removeLike(int $commentId, int $userId): void
    {
        $this->db->query(
            'DELETE FROM BinhLuanThich WHERE maBinhLuan = :comment_id AND maNguoiDung = :user_id',
            [
                'comment_id' => $commentId,
                'user_id' => $userId,
            ]
        );
    }
}
