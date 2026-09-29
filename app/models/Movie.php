<?php
declare(strict_types=1);

class Movie extends Model
{
    private function baseSelect(): string
    {
        return 'SELECT ' .
            'maPhim AS id, ' .
            'tenPhimVN AS title_vi, ' .
            'tenPhimEN AS title_en, ' .
            'namSanXuat AS year, ' .
            'thoiLuong AS duration_min, ' .
            'theLoai AS genre, ' .
            'daoDien AS director, ' .
            'dienVien AS cast_list, ' .
            'noiDung AS synopsis, ' .
            'giaVe AS ticket_price, ' .
            'duongDanTrailer AS trailer_url, ' .
            'duongDanPoster AS poster_url, ' .
            // compute real-time average rating from DanhGia and count of reviews
            '(SELECT IFNULL(ROUND(AVG(diemSo),1), 0) FROM DanhGia WHERE DanhGia.maPhim = Phim.maPhim) AS avg_rating, ' .
            '(SELECT COUNT(*) FROM DanhGia WHERE DanhGia.maPhim = Phim.maPhim) AS review_count, ' .
            'ngayTao AS created_at, ' .
            'ngayCapNhat AS updated_at ' .
            'FROM Phim';
    }

    public function getFeatured(): array
    {
        return $this->db->fetchAll(
            $this->baseSelect() . ' ORDER BY diemTrungBinh DESC, ngayTao DESC LIMIT 12'
        );
    }

    public function search(array $filters): array
    {
        $sql = $this->baseSelect();
        $where = [];
        $params = [];

        if ($filters['q'] !== '') {
            $where[] = '(tenPhimVN LIKE :q OR tenPhimEN LIKE :q OR daoDien LIKE :q OR dienVien LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }
        if ($filters['genre'] !== '') {
            $where[] = 'theLoai LIKE :genre';
            $params['genre'] = '%' . $filters['genre'] . '%';
        }
        if ($filters['year'] !== '') {
            $where[] = 'namSanXuat = :year';
            $params['year'] = (int) $filters['year'];
        }
        if ($filters['rating'] !== '') {
            $where[] = 'diemTrungBinh >= :rating';
            $params['rating'] = (float) $filters['rating'];
        }

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY ngayTao DESC LIMIT 50';

        return $this->db->fetchAll($sql, $params);
    }

    public function find(int $id): ?array
    {
        return $this->db->fetch($this->baseSelect() . ' WHERE maPhim = :id LIMIT 1', [
            'id' => $id,
        ]);
    }

    public function findAll(): array
    {
        return $this->db->fetchAll($this->baseSelect() . ' ORDER BY ngayTao DESC');
    }

    public function updateMovie(int $id, string $titleVi, string $titleEn, int $year, string $genre, string $director, string $castList, string $synopsis, int $duration, int $ticketPrice, string $trailerUrl, string $posterUrl): void
    {
        $this->db->query(
            'UPDATE Phim SET tenPhimVN = :title_vi, tenPhimEN = :title_en, namSanXuat = :year, theLoai = :genre, daoDien = :director, dienVien = :cast_list, noiDung = :synopsis, giaVe = :ticket_price, thoiLuong = :duration, duongDanTrailer = :trailer_url, duongDanPoster = :poster_url, ngayCapNhat = NOW() WHERE maPhim = :id',
            [
                'title_vi' => $titleVi,
                'title_en' => $titleEn,
                'year' => $year,
                'genre' => $genre,
                'director' => $director,
                'cast_list' => $castList,
                'synopsis' => $synopsis,
                'ticket_price' => $ticketPrice,
                'duration' => $duration,
                'trailer_url' => $trailerUrl,
                'poster_url' => $posterUrl,
                'id' => $id,
            ]
        );
    }

    public function createMovie(string $titleVi, string $titleEn, int $year, string $genre, string $director, string $castList, string $synopsis, int $duration, int $ticketPrice, string $trailerUrl, string $posterUrl): int
    {
        $this->db->query(
            'INSERT INTO Phim (tenPhimVN, tenPhimEN, namSanXuat, theLoai, daoDien, dienVien, noiDung, giaVe, thoiLuong, duongDanTrailer, duongDanPoster, ngayTao, ngayCapNhat) VALUES (:title_vi, :title_en, :year, :genre, :director, :cast_list, :synopsis, :ticket_price, :duration, :trailer_url, :poster_url, NOW(), NOW())',
            [
                'title_vi' => $titleVi,
                'title_en' => $titleEn,
                'year' => $year,
                'genre' => $genre,
                'director' => $director,
                'cast_list' => $castList,
                'synopsis' => $synopsis,
                'ticket_price' => $ticketPrice,
                'duration' => $duration,
                'trailer_url' => $trailerUrl,
                'poster_url' => $posterUrl,
            ]
        );

        return (int) $this->db->pdo()->lastInsertId();
    }

    public function delete(int $id): void
    {
        $this->db->query('DELETE FROM Phim WHERE maPhim = :id', ['id' => $id]);
    }

    public function countAll(): int
    {
        $row = $this->db->fetch('SELECT COUNT(*) AS cnt FROM Phim');
        return (int) ($row['cnt'] ?? 0);
    }

    public function countCreatedOn(string $date): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS cnt FROM Phim WHERE DATE(ngayTao) = :day',
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
            'SELECT COUNT(*) AS cnt FROM Phim WHERE ngayTao >= :start AND ngayTao < :end',
            ['start' => $start, 'end' => $end]
        );

        return (int) ($row['cnt'] ?? 0);
    }

    public function countCreatedAtHour(string $date, int $hour): int
    {
        $row = $this->db->fetch(
            'SELECT COUNT(*) AS cnt FROM Phim WHERE DATE(ngayTao) = :day AND HOUR(ngayTao) = :hour',
            ['day' => $date, 'hour' => $hour]
        );

        return (int) ($row['cnt'] ?? 0);
    }

    public function getRecentAdded(int $limit = 5): array
    {
        $limit = max(1, (int) $limit);

        return $this->db->fetchAll(
            'SELECT ' .
                'maPhim AS id, ' .
                'tenPhimVN AS title_vi, ' .
                'tenPhimEN AS title_en, ' .
                'ngayTao AS created_at ' .
            'FROM Phim ' .
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
}
