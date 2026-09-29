<?php
declare(strict_types=1);

class Review extends Model
{
    private ?bool $imageColumnExists = null;
    private ?string $movieCountryColumn = null;

    private function hasImageColumn(): bool
    {
        if ($this->imageColumnExists !== null) {
            return $this->imageColumnExists;
        }

        $row = $this->db->fetch("SHOW COLUMNS FROM DanhGia LIKE 'anhMinhHoa'");
        $this->imageColumnExists = $row !== null;
        return $this->imageColumnExists;
    }


    private function movieCountryColumn(): ?string
    {
        if ($this->movieCountryColumn !== null) {
            return $this->movieCountryColumn;
        }

        $candidates = ['quocGia', 'quoc_gia', 'quocgia', 'country'];
        foreach ($candidates as $column) {
            $row = $this->db->fetch("SHOW COLUMNS FROM Phim LIKE '{$column}'");
            if ($row !== null) {
                $this->movieCountryColumn = $column;
                return $this->movieCountryColumn;
            }
        }

        $this->movieCountryColumn = null;
        return null;
    }

    private function buildFeedFilters(string $tab): array
    {
        $where = [];
        $having = [];
        $params = [];

        if ($tab === 'hot') {
            $having[] = '(MAX(r.diemSo) >= :hot_rating OR COUNT(DISTINCT c.maBinhLuan) >= :hot_comments)';
            $params['hot_rating'] = 4.0;
            $params['hot_comments'] = 3;
        }

        if ($tab === 'viet' || $tab === 'hollywood') {
            $countryColumn = $this->movieCountryColumn();
            if ($countryColumn !== null) {
                if ($tab === 'viet') {
                    $where[] = 'LOWER(p.' . $countryColumn . ') LIKE :country_viet';
                    $params['country_viet'] = '%viet%';
                } else {
                    $where[] = '(LOWER(p.' . $countryColumn . ') LIKE :country_us OR LOWER(p.' . $countryColumn . ') LIKE :country_usa OR LOWER(p.' . $countryColumn . ') LIKE :country_hollywood)';
                    $params['country_us'] = '%united states%';
                    $params['country_usa'] = '%usa%';
                    $params['country_hollywood'] = '%hollywood%';
                }
            }
        }

        return [$where, $having, $params];
    }

    private function buildDiscoverFilters(array $filters): array
    {
        $where = [];
        $params = [];

        $query = trim((string) ($filters['q'] ?? ''));
        $director = trim((string) ($filters['director'] ?? ''));
        $genre = trim((string) ($filters['genre'] ?? ''));
        $rating = trim((string) ($filters['rating'] ?? ''));

        if ($query !== '') {
            $where[] = '(p.tenPhimVN LIKE :q OR p.tenPhimEN LIKE :q)';
            $params['q'] = '%' . $query . '%';
        }

        if ($director !== '') {
            $where[] = 'p.daoDien LIKE :director';
            $params['director'] = '%' . $director . '%';
        }

        if ($genre !== '') {
            $where[] = 'p.theLoai LIKE :genre';
            $params['genre'] = '%' . $genre . '%';
        }

        if ($rating !== '') {
            $where[] = 'r.diemSo >= :rating';
            $params['rating'] = (float) $rating;
        }

        return [$where, $params];
    }

    public function getFeed(int $limit = 20): array
    {
        $limit = max(1, (int) $limit);
        $hasImageColumn = $this->hasImageColumn();
        $imageSelect = $hasImageColumn ? 'r.anhMinhHoa AS image_url, ' : "'' AS image_url, ";
        $sql = 'SELECT ' .
                'r.maDanhGia AS id, ' .
                'r.maPhim AS movie_id, ' .
                'r.maNguoiDung AS user_id, ' .
                'r.tieuDe AS title, ' .
                $imageSelect .
                'LEFT(r.noiDung, 220) AS excerpt, ' .
                'r.diemSo AS rating, ' .
                'r.ngayTao AS created_at, ' .
                'u.tenDangNhap AS display_name, ' .
                'u.anhDaiDien AS avatar_url, ' .
                'p.tenPhimVN AS movie_title_vi, ' .
                'p.tenPhimEN AS movie_title_en, ' .
                'p.duongDanPoster AS poster_url ' .
            'FROM DanhGia r ' .
            'JOIN NguoiDung u ON u.maNguoiDung = r.maNguoiDung ' .
            'JOIN Phim p ON p.maPhim = r.maPhim ';

        $params = [];
        $col = $this->availableStatusColumn();
        if ($col !== null) {
            $sql .= "WHERE TRIM(r.{$col}) = :active ";
            $params['active'] = 'active';
        }

        $sql .= 'ORDER BY r.ngayTao DESC LIMIT ' . (int) $limit;
        return $this->db->fetchAll($sql, $params);
    }


    public function getFeedFiltered(int $page, int $perPage, string $sort, string $tab, array $filters = []): array
    {
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $offset = ($page - 1) * $perPage;

        $hasImageColumn = $this->hasImageColumn();
        $imageSelect = $hasImageColumn ? 'r.anhMinhHoa AS image_url, ' : "'' AS image_url, ";
        [$where, $having, $params] = $this->buildFeedFilters($tab);
        [$discoverWhere, $discoverParams] = $this->buildDiscoverFilters($filters);
        if (!empty($discoverWhere)) {
            $where = array_merge($where, $discoverWhere);
        }
        if (!empty($discoverParams)) {
            $params = array_merge($params, $discoverParams);
        }

        $orderBy = 'r.ngayTao DESC';
        if ($sort === 'top') {
            $orderBy = 'r.diemSo DESC, r.ngayTao DESC';
        } elseif ($sort === 'liked') {
            $orderBy = 'comment_count DESC, r.ngayTao DESC';
        }

        $sql = 'SELECT ' .
                'r.maDanhGia AS id, ' .
                'r.maPhim AS movie_id, ' .
                'r.maNguoiDung AS user_id, ' .
                'r.tieuDe AS title, ' .
                $imageSelect .
                'LEFT(r.noiDung, 220) AS excerpt, ' .
                'r.diemSo AS rating, ' .
                'r.ngayTao AS created_at, ' .
                'u.tenDangNhap AS display_name, ' .
                'u.anhDaiDien AS avatar_url, ' .
                'p.tenPhimVN AS movie_title_vi, ' .
                'p.tenPhimEN AS movie_title_en, ' .
                'p.duongDanPoster AS poster_url, ' .
                'COUNT(DISTINCT c.maBinhLuan) AS comment_count ' .
            'FROM DanhGia r ' .
            'JOIN NguoiDung u ON u.maNguoiDung = r.maNguoiDung ' .
            'JOIN Phim p ON p.maPhim = r.maPhim ' .
            'LEFT JOIN BinhLuan c ON c.maDanhGia = r.maDanhGia ';

        // ensure we only return active reviews when possible
        $col = $this->availableStatusColumn();
        if ($col !== null) {
            $where[] = "TRIM(r.{$col}) = :active";
            $params['active'] = 'active';
        }

        if (!empty($where)) {
            $sql .= 'WHERE ' . implode(' AND ', $where) . ' ';
        }

        $groupColumns = [
            'r.maDanhGia',
            'r.maPhim',
            'r.maNguoiDung',
            'r.tieuDe',
            'r.noiDung',
            'r.diemSo',
            'r.ngayTao',
            'u.tenDangNhap',
            'u.anhDaiDien',
            'p.tenPhimVN',
            'p.tenPhimEN',
            'p.duongDanPoster',
        ];
        if ($hasImageColumn) {
            $groupColumns[] = 'r.anhMinhHoa';
        }
        $sql .= 'GROUP BY ' . implode(', ', $groupColumns) . ' ';

        if (!empty($having)) {
            $sql .= 'HAVING ' . implode(' AND ', $having) . ' ';
        }

        $sql .= 'ORDER BY ' . $orderBy . ' LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset;

        return $this->db->fetchAll($sql, $params);
    }

    public function countFeedFiltered(string $tab, array $filters = []): int
    {
        [$where, $having, $params] = $this->buildFeedFilters($tab);
        [$discoverWhere, $discoverParams] = $this->buildDiscoverFilters($filters);
        if (!empty($discoverWhere)) {
            $where = array_merge($where, $discoverWhere);
        }
        if (!empty($discoverParams)) {
            $params = array_merge($params, $discoverParams);
        }

        // add active filter to count query as well
        $col = $this->availableStatusColumn();
        if ($col !== null) {
            $where[] = "TRIM(r.{$col}) = :active";
            $params['active'] = 'active';
        }

        $sql = 'SELECT COUNT(*) AS cnt FROM (' .
            'SELECT r.maDanhGia ' .
            'FROM DanhGia r ' .
            'JOIN NguoiDung u ON u.maNguoiDung = r.maNguoiDung ' .
            'JOIN Phim p ON p.maPhim = r.maPhim ' .
            'LEFT JOIN BinhLuan c ON c.maDanhGia = r.maDanhGia ';

        if (!empty($where)) {
            $sql .= 'WHERE ' . implode(' AND ', $where) . ' ';
        }

        $sql .= 'GROUP BY r.maDanhGia ';

        if (!empty($having)) {
            $sql .= 'HAVING ' . implode(' AND ', $having) . ' ';
        }

        $sql .= ') t';

        $row = $this->db->fetch($sql, $params);
        return (int) ($row['cnt'] ?? 0);
    }

    public function getByMovieId(int $movieId): array
    {
        $imageSelect = $this->hasImageColumn() ? 'r.anhMinhHoa AS image_url, ' : "'' AS image_url, ";
        $sql = 'SELECT ' .
                'r.maDanhGia AS id, ' .
                'r.maPhim AS movie_id, ' .
                'r.maNguoiDung AS user_id, ' .
                'r.tieuDe AS title, ' .
                $imageSelect .
                'r.noiDung AS body, ' .
                'r.diemSo AS rating, ' .
                'r.ngayTao AS created_at, ' .
                'r.ngayCapNhat AS updated_at, ' .
                'u.tenDangNhap AS display_name, ' .
                'u.anhDaiDien AS avatar_url ' .
            'FROM DanhGia r JOIN NguoiDung u ON u.maNguoiDung = r.maNguoiDung WHERE r.maPhim = :movie_id ';

        $params = ['movie_id' => $movieId];
        $col = $this->availableStatusColumn();
        if ($col !== null) {
            $sql .= "AND TRIM(r.{$col}) = :active ";
            $params['active'] = 'active';
        }

        $sql .= 'ORDER BY r.ngayTao DESC';
        return $this->db->fetchAll($sql, $params);
    }

    public function countAll(): int
    {
        $row = $this->db->fetch('SELECT COUNT(*) AS cnt FROM DanhGia');
        return (int) ($row['cnt'] ?? 0);
    }

    public function countCreatedOn(string $date): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS cnt FROM DanhGia WHERE DATE(ngayTao) = :day',
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
            'SELECT COUNT(*) AS cnt FROM DanhGia WHERE ngayTao >= :start AND ngayTao < :end',
            ['start' => $start, 'end' => $end]
        );

        return (int) ($row['cnt'] ?? 0);
    }

    public function countCreatedAtHour(string $date, int $hour): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS cnt FROM DanhGia WHERE DATE(ngayTao) = :day AND HOUR(ngayTao) = :hour',
            ['day' => $date, 'hour' => $hour]
        );

        return (int) ($row['cnt'] ?? 0);
    }

    public function countLowRated(float $threshold = 3.0): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS cnt FROM DanhGia WHERE diemSo <= :threshold',
            ['threshold' => $threshold]
        );

        return (int) ($row['cnt'] ?? 0);
    }

    public function countLowRatedBetween(string $start, string $end, float $threshold = 3.0): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS cnt FROM DanhGia WHERE diemSo <= :threshold AND ngayTao >= :start AND ngayTao < :end',
            ['threshold' => $threshold, 'start' => $start, 'end' => $end]
        );

        return (int) ($row['cnt'] ?? 0);
    }

    public function averageRatingBetween(string $start, string $end): float
    {
        $row = $this->db->fetch(
            'SELECT IFNULL(ROUND(AVG(diemSo), 1), 0) AS avg_rating FROM DanhGia WHERE ngayTao >= :start AND ngayTao < :end',
            ['start' => $start, 'end' => $end]
        );

        return (float) ($row['avg_rating'] ?? 0);
    }

    public function getRecentForModeration(int $limit = 5, float $threshold = 3.0): array
    {
        $limit = max(1, (int) $limit);

        return $this->db->fetchAll(
            'SELECT ' .
                'r.maDanhGia AS id, ' .
                'r.tieuDe AS title, ' .
                'LEFT(r.noiDung, 170) AS excerpt, ' .
                'r.diemSo AS rating, ' .
                'r.ngayTao AS created_at, ' .
                'u.tenDangNhap AS display_name, ' .
                'p.tenPhimVN AS movie_title_vi, ' .
                'p.tenPhimEN AS movie_title_en ' .
            'FROM DanhGia r ' .
            'JOIN NguoiDung u ON u.maNguoiDung = r.maNguoiDung ' .
            'JOIN Phim p ON p.maPhim = r.maPhim ' .
            'WHERE r.diemSo <= :threshold ' .
            'ORDER BY r.ngayTao DESC LIMIT ' . $limit,
            ['threshold' => $threshold]
        );
    }

    public function getForModeration(int $page, int $perPage, string $q = '', ?string $status = null, $rating = null): array
    {
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $offset = ($page - 1) * $perPage;

        $hasImageColumn = $this->hasImageColumn();
        $imageSelect = $hasImageColumn ? 'r.anhMinhHoa AS image_url, ' : "'' AS image_url, ";

        $sql = 'SELECT r.maDanhGia AS id, r.maPhim AS movie_id, r.maNguoiDung AS user_id, r.tieuDe AS title, ' . $imageSelect . 'LEFT(r.noiDung,200) AS excerpt, r.diemSo AS rating, r.ngayTao AS created_at, u.tenDangNhap AS display_name, p.tenPhimVN AS movie_title_vi ';
        $statusColumn = $this->availableStatusColumn();
        if ($statusColumn !== null) {
            $sql .= ', r.' . $statusColumn . ' AS status ';
        } else {
            $sql .= ", 'active' AS status ";
        }
        $sql .= 'FROM DanhGia r JOIN NguoiDung u ON u.maNguoiDung = r.maNguoiDung JOIN Phim p ON p.maPhim = r.maPhim WHERE 1=1 ';
        $params = [];
        if ($q !== '') {
            $sql .= ' AND (u.tenDangNhap LIKE :q OR p.tenPhimVN LIKE :q OR r.noiDung LIKE :q OR r.tieuDe LIKE :q) ';
            $params['q'] = '%' . $q . '%';
        }
        if ($status !== null && $status !== '') {
            $col = $this->availableStatusColumn();
            if ($col !== null) {
                $sql .= " AND TRIM(r.{$col}) = :st ";
                $params['st'] = $status;
            }
        }
        if ($rating !== null && $rating !== '') {
            $sql .= ' AND r.diemSo = :rating ';
            $params['rating'] = (float) $rating;
        }

        $sql .= ' ORDER BY r.ngayTao DESC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset;

        return $this->db->fetchAll($sql, $params);
    }

    public function getRecentActivity(int $limit = 5): array
    {
        $limit = max(1, (int) $limit);

        return $this->db->fetchAll(
            'SELECT ' .
                'r.maDanhGia AS id, ' .
                'r.tieuDe AS title, ' .
                'LEFT(r.noiDung, 170) AS excerpt, ' .
                'r.diemSo AS rating, ' .
                'r.ngayTao AS created_at, ' .
                'u.tenDangNhap AS display_name, ' .
                'p.tenPhimVN AS movie_title_vi, ' .
                'p.tenPhimEN AS movie_title_en ' .
            'FROM DanhGia r ' .
            'JOIN NguoiDung u ON u.maNguoiDung = r.maNguoiDung ' .
            'JOIN Phim p ON p.maPhim = r.maPhim ' .
            'ORDER BY r.ngayTao DESC LIMIT ' . $limit
        );
    }

    public function findById(int $id): ?array
    {
        $hasImageColumn = $this->hasImageColumn();
        $imageSelect = $hasImageColumn ? 'r.anhMinhHoa AS image_url, ' : "'' AS image_url, ";

        return $this->db->fetch(
            'SELECT ' .
                'r.maDanhGia AS id, ' .
                'r.maPhim AS movie_id, ' .
                'r.maNguoiDung AS user_id, ' .
                'r.tieuDe AS title, ' .
                $imageSelect .
                'r.noiDung AS body, ' .
                'r.diemSo AS rating, ' .
                'r.ngayTao AS created_at, ' .
                'r.ngayCapNhat AS updated_at, ' .
                'u.tenDangNhap AS display_name, ' .
                'u.anhDaiDien AS avatar_url, ' .
                'p.tenPhimVN AS movie_title_vi, ' .
                'p.tenPhimEN AS movie_title_en, ' .
                'p.duongDanPoster AS poster_url ' .
            'FROM DanhGia r ' .
            'JOIN NguoiDung u ON u.maNguoiDung = r.maNguoiDung ' .
            'JOIN Phim p ON p.maPhim = r.maPhim ' .
            'WHERE r.maDanhGia = :id LIMIT 1',
            [
                'id' => $id,
            ]
        );
    }

    public function availableStatusColumn(): ?string
    {
        $candidates = ['trangThai', 'status', 'trang_thai', 'tinhTrang'];
        foreach ($candidates as $c) {
            $row = $this->db->fetch("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'DanhGia' AND COLUMN_NAME = :col", ['col' => $c]);
            if ((int) ($row['cnt'] ?? 0) > 0) return $c;
        }
        return null;
    }

    public function setStatus(int $id, string $status): bool
    {
        $col = $this->availableStatusColumn();
        if ($col === null) {
            // try to add a standard column 'trangThai' if missing
            try {
                $this->db->query("ALTER TABLE DanhGia ADD COLUMN trangThai ENUM('active','pending','hidden') NOT NULL DEFAULT 'active'");
                $col = 'trangThai';
            } catch (Throwable $e) {
                return false;
            }
        }

        try {
            $sql = "UPDATE DanhGia SET {$col} = :status, ngayCapNhat = NOW() WHERE maDanhGia = :id";
            $this->db->query($sql, ['status' => $status, 'id' => $id]);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function deleteReview(int $id): bool
    {
        try {
            $this->db->query('DELETE FROM DanhGia WHERE maDanhGia = :id', ['id' => $id]);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function create(int $movieId, int $userId, string $title, string $body, float $rating, ?string $imageUrl = null): void
    {
        if ($imageUrl !== null && !$this->hasImageColumn()) {
            try {
                $this->db->query('ALTER TABLE DanhGia ADD COLUMN anhMinhHoa VARCHAR(255) DEFAULT NULL');
                $this->imageColumnExists = true;
            } catch (Throwable $e) {
                $this->imageColumnExists = false;
            }
        }

        if ($this->hasImageColumn()) {
            $this->db->query(
                'INSERT INTO DanhGia (maPhim, maNguoiDung, tieuDe, noiDung, diemSo, anhMinhHoa, ngayTao, ngayCapNhat) VALUES (:movie_id, :user_id, :title, :body, :rating, :image_url, NOW(), NOW())',
                [
                    'movie_id' => $movieId,
                    'user_id' => $userId,
                    'title' => $title,
                    'body' => $body,
                    'rating' => $rating,
                    'image_url' => $imageUrl,
                ]
            );
            return;
        }

        $this->db->query(
            'INSERT INTO DanhGia (maPhim, maNguoiDung, tieuDe, noiDung, diemSo, ngayTao, ngayCapNhat) VALUES (:movie_id, :user_id, :title, :body, :rating, NOW(), NOW())',
            [
                'movie_id' => $movieId,
                'user_id' => $userId,
                'title' => $title,
                'body' => $body,
                'rating' => $rating,
            ]
        );
    }

    public function update(int $id, string $title, string $body, float $rating, ?string $imageUrl = null): void
    {
        if ($imageUrl !== null && $this->hasImageColumn()) {
            $this->db->query(
                'UPDATE DanhGia SET tieuDe = :title, noiDung = :body, diemSo = :rating, anhMinhHoa = :image_url, ngayCapNhat = NOW() WHERE maDanhGia = :id',
                [
                    'id' => $id,
                    'title' => $title,
                    'body' => $body,
                    'rating' => $rating,
                    'image_url' => $imageUrl,
                ]
            );
            return;
        }

        $this->db->query(
            'UPDATE DanhGia SET tieuDe = :title, noiDung = :body, diemSo = :rating, ngayCapNhat = NOW() WHERE maDanhGia = :id',
            [
                'id' => $id,
                'title' => $title,
                'body' => $body,
                'rating' => $rating,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->db->query(
            'DELETE FROM DanhGia WHERE maDanhGia = :id',
            [
                'id' => $id,
            ]
        );
    }

    public function findPaginated(int $page, int $perPage = 10): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $limit = (int) $perPage;
        $offset = (int) $offset;
        $sql = 'SELECT ' .
                'r.maDanhGia AS id, ' .
                'r.maPhim AS movie_id, ' .
                'r.maNguoiDung AS user_id, ' .
                'r.tieuDe AS title, ' .
                'LEFT(r.noiDung, 220) AS excerpt, ' .
                'r.diemSo AS rating, ' .
                'r.ngayTao AS created_at, ' .
                'u.tenDangNhap AS display_name, ' .
                'p.tenPhimVN AS movie_title_vi, ' .
                'p.tenPhimEN AS movie_title_en ' .
            'FROM DanhGia r ' .
            'JOIN NguoiDung u ON u.maNguoiDung = r.maNguoiDung ' .
            'JOIN Phim p ON p.maPhim = r.maPhim ' .
            "ORDER BY r.ngayTao DESC LIMIT {$limit} OFFSET {$offset}";

        return $this->db->fetchAll($sql);
    }
}
