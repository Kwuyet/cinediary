<?php
declare(strict_types=1);

class ListModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureSchema();
    }

    private function ensureSchema(): void
    {
        $this->db->query(
            'CREATE TABLE IF NOT EXISTS DanhSachTuyChinh (' .
                'maDanhSach INT AUTO_INCREMENT PRIMARY KEY, ' .
                'maNguoiDung INT NOT NULL, ' .
                'tenDanhSach VARCHAR(255) NOT NULL, ' .
                'moTa TEXT DEFAULT NULL, ' .
                'congKhai TINYINT(1) NOT NULL DEFAULT 1, ' .
                'ngayTao DATETIME NOT NULL, ' .
                'ngayCapNhat DATETIME NOT NULL, ' .
                'INDEX idx_danhsach_user (maNguoiDung) ' .
            ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->query(
            'CREATE TABLE IF NOT EXISTS DanhSachPhim (' .
                'maMuc INT AUTO_INCREMENT PRIMARY KEY, ' .
                'maDanhSach INT NOT NULL, ' .
                'maPhim INT NOT NULL, ' .
                'ghiChu TEXT DEFAULT NULL, ' .
                'thuTu INT NOT NULL DEFAULT 1, ' .
                'ngayTao DATETIME NOT NULL, ' .
                'ngayCapNhat DATETIME NOT NULL, ' .
                'UNIQUE KEY uniq_danhsach_phim (maDanhSach, maPhim), ' .
                'INDEX idx_danhsachphim_list (maDanhSach), ' .
                'INDEX idx_danhsachphim_movie (maPhim), ' .
                'CONSTRAINT fk_danhsachphim_list FOREIGN KEY (maDanhSach) REFERENCES DanhSachTuyChinh(maDanhSach) ON DELETE CASCADE, ' .
                'CONSTRAINT fk_danhsachphim_movie FOREIGN KEY (maPhim) REFERENCES Phim(maPhim) ON DELETE CASCADE ' .
            ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    public function create(int $userId, string $title, string $description, bool $isPublic): int
    {
        $this->db->query(
            'INSERT INTO DanhSachTuyChinh (maNguoiDung, tenDanhSach, moTa, congKhai, ngayTao, ngayCapNhat) ' .
            'VALUES (:user_id, :title, :description, :is_public, NOW(), NOW())',
            [
                'user_id' => $userId,
                'title' => $title,
                'description' => $description,
                'is_public' => $isPublic ? 1 : 0,
            ]
        );

        return (int) $this->db->pdo()->lastInsertId();
    }

    public function countAll(): int
    {
        $row = $this->db->fetch('SELECT COUNT(*) AS cnt FROM DanhSachTuyChinh');
        return (int) ($row['cnt'] ?? 0);
    }

    public function countCreatedOn(string $date): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS cnt FROM DanhSachTuyChinh WHERE DATE(ngayTao) = :day',
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
            'SELECT COUNT(*) AS cnt FROM DanhSachTuyChinh WHERE ngayTao >= :start AND ngayTao < :end',
            ['start' => $start, 'end' => $end]
        );

        return (int) ($row['cnt'] ?? 0);
    }

    public function countCreatedAtHour(string $date, int $hour): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS cnt FROM DanhSachTuyChinh WHERE DATE(ngayTao) = :day AND HOUR(ngayTao) = :hour',
            ['day' => $date, 'hour' => $hour]
        );

        return (int) ($row['cnt'] ?? 0);
    }

    public function getByUser(int $userId): array
    {
        return $this->db->fetchAll(
            'SELECT ' .
                'maDanhSach AS id, ' .
                'tenDanhSach AS title, ' .
                'moTa AS description, ' .
                'congKhai AS is_public, ' .
                '(SELECT COUNT(*) FROM DanhSachPhim WHERE DanhSachPhim.maDanhSach = DanhSachTuyChinh.maDanhSach) AS item_count, ' .
                'CASE WHEN congKhai = 1 THEN "Công khai" ELSE "Riêng tư" END AS visibility ' .
            'FROM DanhSachTuyChinh ' .
            'WHERE maNguoiDung = :user_id ' .
            'ORDER BY ngayTao DESC',
            [
                'user_id' => $userId,
            ]
        );
    }

    public function findByIdAndUser(int $listId, int $userId): ?array
    {
        return $this->db->fetch(
            'SELECT ' .
                'maDanhSach AS id, ' .
                'tenDanhSach AS title, ' .
                'moTa AS description, ' .
                'congKhai AS is_public, ' .
                '(SELECT COUNT(*) FROM DanhSachPhim WHERE DanhSachPhim.maDanhSach = DanhSachTuyChinh.maDanhSach) AS item_count, ' .
                'CASE WHEN congKhai = 1 THEN "Công khai" ELSE "Riêng tư" END AS visibility ' .
            'FROM DanhSachTuyChinh ' .
            'WHERE maDanhSach = :list_id AND maNguoiDung = :user_id ' .
            'LIMIT 1',
            [
                'list_id' => $listId,
                'user_id' => $userId,
            ]
        );
    }

    public function getItems(int $listId): array
    {
        return $this->db->fetchAll(
            'SELECT ' .
                'item.maMuc AS id, ' .
                'item.maDanhSach AS list_id, ' .
                'item.maPhim AS movie_id, ' .
                'item.ghiChu AS note, ' .
                'item.thuTu AS position, ' .
                'item.ngayTao AS created_at, ' .
                'item.ngayCapNhat AS updated_at, ' .
                'Phim.tenPhimVN AS title_vi, ' .
                'Phim.tenPhimEN AS title_en, ' .
                'Phim.namSanXuat AS year, ' .
                'Phim.duongDanPoster AS poster_url, ' .
                '(SELECT IFNULL(ROUND(AVG(diemSo),1), 0) FROM DanhGia WHERE DanhGia.maPhim = Phim.maPhim) AS avg_rating, ' .
                '(SELECT COUNT(*) FROM DanhGia WHERE DanhGia.maPhim = Phim.maPhim) AS review_count ' .
            'FROM DanhSachPhim item ' .
            'INNER JOIN Phim ON Phim.maPhim = item.maPhim ' .
            'WHERE item.maDanhSach = :list_id ' .
            'ORDER BY item.thuTu ASC, item.ngayTao DESC',
            [
                'list_id' => $listId,
            ]
        );
    }

    public function findItem(int $itemId, int $listId): ?array
    {
        return $this->db->fetch(
            'SELECT ' .
                'item.maMuc AS id, ' .
                'item.maDanhSach AS list_id, ' .
                'item.maPhim AS movie_id, ' .
                'item.ghiChu AS note, ' .
                'item.thuTu AS position, ' .
                'item.ngayTao AS created_at, ' .
                'item.ngayCapNhat AS updated_at, ' .
                'Phim.tenPhimVN AS title_vi, ' .
                'Phim.tenPhimEN AS title_en, ' .
                'Phim.namSanXuat AS year, ' .
                'Phim.duongDanPoster AS poster_url ' .
            'FROM DanhSachPhim item ' .
            'INNER JOIN Phim ON Phim.maPhim = item.maPhim ' .
            'WHERE item.maMuc = :item_id AND item.maDanhSach = :list_id ' .
            'LIMIT 1',
            [
                'item_id' => $itemId,
                'list_id' => $listId,
            ]
        );
    }

    public function findItemByMovie(int $listId, int $movieId, ?int $excludeItemId = null): ?array
    {
        $sql =
            'SELECT item.maMuc AS id FROM DanhSachPhim item ' .
            'WHERE item.maDanhSach = :list_id AND item.maPhim = :movie_id';
        $params = [
            'list_id' => $listId,
            'movie_id' => $movieId,
        ];

        if ($excludeItemId !== null) {
            $sql .= ' AND item.maMuc <> :exclude_item_id';
            $params['exclude_item_id'] = $excludeItemId;
        }

        $sql .= ' LIMIT 1';

        return $this->db->fetch($sql, $params);
    }

    private function nextPosition(int $listId): int
    {
        $row = $this->db->fetch(
            'SELECT IFNULL(MAX(thuTu), 0) + 1 AS next_position FROM DanhSachPhim WHERE maDanhSach = :list_id',
            ['list_id' => $listId]
        );

        return (int) ($row['next_position'] ?? 1);
    }

    public function addMovieToList(int $listId, int $movieId, string $note, ?int $position = null): void
    {
        $position = $position !== null && $position > 0 ? $position : $this->nextPosition($listId);

        $this->db->query(
            'INSERT INTO DanhSachPhim (maDanhSach, maPhim, ghiChu, thuTu, ngayTao, ngayCapNhat) ' .
            'VALUES (:list_id, :movie_id, :note, :position, NOW(), NOW()) ' .
            'ON DUPLICATE KEY UPDATE ghiChu = VALUES(ghiChu), thuTu = VALUES(thuTu), ngayCapNhat = NOW()',
            [
                'list_id' => $listId,
                'movie_id' => $movieId,
                'note' => $note,
                'position' => $position,
            ]
        );
    }

    public function updateItem(int $itemId, int $listId, int $movieId, string $note, int $position): void
    {
        $this->db->query(
            'UPDATE DanhSachPhim SET maPhim = :movie_id, ghiChu = :note, thuTu = :position, ngayCapNhat = NOW() WHERE maMuc = :item_id AND maDanhSach = :list_id',
            [
                'item_id' => $itemId,
                'list_id' => $listId,
                'movie_id' => $movieId,
                'note' => $note,
                'position' => $position,
            ]
        );
    }

    public function deleteItem(int $itemId, int $listId): void
    {
        $this->db->query(
            'DELETE FROM DanhSachPhim WHERE maMuc = :item_id AND maDanhSach = :list_id',
            [
                'item_id' => $itemId,
                'list_id' => $listId,
            ]
        );
    }
}
