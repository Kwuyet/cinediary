<?php
declare(strict_types=1);

class SampleData
{
    public static function demoUser(): array
    {
        return [
            'id' => 999,
            'email' => 'demo@cinediary.local',
            'display_name' => 'Demo User',
            'role' => 'admin',
        ];
    }

    public static function featuredMovies(): array
    {
        return array_slice(self::movies(), 0, 6);
    }

    public static function movies(): array
    {
        return [
            [
                'id' => 1,
                'title_vi' => 'Silent Harbor',
                'title_en' => 'Silent Harbor',
                'year' => 2025,
                'duration_min' => 118,
                'genre' => 'Drama, Mystery',
                'director' => 'Noah Glen',
                'cast_list' => 'Elena Park, Jonas Reed, Mina Soto',
                'country' => 'US',
                'synopsis' => 'Một thị trấn ven biển che giấu vụ mất tích mười năm khi người gác hải đăng mới đến.',
                'trailer_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'poster_url' => 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963f?auto=format&fit=crop&w=600&q=80',
                'avg_rating' => 4.6,
                'created_at' => '2026-05-01',
            ],
            [
                'id' => 2,
                'title_vi' => 'Night Circuit',
                'title_en' => 'Night Circuit',
                'year' => 2024,
                'duration_min' => 102,
                'genre' => 'Thriller, Sci-Fi',
                'director' => 'Lea Moreno',
                'cast_list' => 'Kai Harper, Rina Cho, Luis Ahn',
                'country' => 'KR',
                'synopsis' => 'Một tay đua ngầm phát hiện thí nghiệm giám sát quy mô thành phố.',
                'trailer_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'poster_url' => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=600&q=80',
                'avg_rating' => 4.2,
                'created_at' => '2026-04-18',
            ],
            [
                'id' => 3,
                'title_vi' => 'Golden Frame',
                'title_en' => 'Golden Frame',
                'year' => 2023,
                'duration_min' => 127,
                'genre' => 'Romance, History',
                'director' => 'Iris Nguyen',
                'cast_list' => 'Bao Le, Sophia Tran, Marcus Li',
                'country' => 'VN',
                'synopsis' => 'Một nhiếp ảnh gia trẻ nảy sinh tình cảm với người phục chế trong lần trùng tu cung điện.',
                'trailer_url' => '',
                'poster_url' => 'https://images.unsplash.com/photo-1524985069026-dd778a71c7b4?auto=format&fit=crop&w=600&q=80',
                'avg_rating' => 4.8,
                'created_at' => '2026-03-10',
            ],
            [
                'id' => 4,
                'title_vi' => 'Static Bloom',
                'title_en' => 'Static Bloom',
                'year' => 2025,
                'duration_min' => 96,
                'genre' => 'Horror',
                'director' => 'Avery Cole',
                'cast_list' => 'Nora Miles, Jae Kim',
                'country' => 'CA',
                'synopsis' => 'Một phát thanh viên nhận cuộc gọi từ một thị trấn không tồn tại.',
                'trailer_url' => '',
                'poster_url' => 'https://images.unsplash.com/photo-1502139214982-d0ad755818d8?auto=format&fit=crop&w=600&q=80',
                'avg_rating' => 4.0,
                'created_at' => '2026-02-12',
            ],
            [
                'id' => 5,
                'title_vi' => 'Parallel Draft',
                'title_en' => 'Parallel Draft',
                'year' => 2022,
                'duration_min' => 134,
                'genre' => 'Action, Adventure',
                'director' => 'Ravi Singh',
                'cast_list' => 'Jamie Cole, Priya Patel',
                'country' => 'UK',
                'synopsis' => 'Một kiến trúc sư tìm thấy bản thiết kế của thành phố ở dòng thời gian khác.',
                'trailer_url' => '',
                'poster_url' => 'https://images.unsplash.com/photo-1505685296765-3a2736de412f?auto=format&fit=crop&w=600&q=80',
                'avg_rating' => 3.9,
                'created_at' => '2026-01-05',
            ],
            [
                'id' => 6,
                'title_vi' => 'Paper Skies',
                'title_en' => 'Paper Skies',
                'year' => 2021,
                'duration_min' => 110,
                'genre' => 'Animation, Family',
                'director' => 'Hana Ito',
                'cast_list' => 'Voice Ensemble',
                'country' => 'JP',
                'synopsis' => 'Một đứa trẻ làm diều đưa ký ức về ông bà trở lại.',
                'trailer_url' => '',
                'poster_url' => 'https://images.unsplash.com/photo-1497032205916-ac775f0649ae?auto=format&fit=crop&w=600&q=80',
                'avg_rating' => 4.5,
                'created_at' => '2025-12-01',
            ],
        ];
    }

    public static function movieDetail(int $id): ?array
    {
        foreach (self::movies() as $movie) {
            if ((int) $movie['id'] === $id) {
                return $movie;
            }
        }
        return self::movies()[0] ?? null;
    }

    public static function reviewsForMovie(int $movieId): array
    {
        return [
            [
                'id' => 11,
                'movie_id' => $movieId,
                'display_name' => 'Nova Gray',
                'title' => 'Cơn bão cảm xúc dịu êm',
                'body' => 'Nhịp phim có chủ ý nhưng không chậm. Cảnh cuối lắng đọng và còn đọng lại rất lâu.',
                'rating' => 4.5,
                'created_at' => '2026-05-10',
            ],
            [
                'id' => 12,
                'movie_id' => $movieId,
                'display_name' => 'Kai Tran',
                'title' => 'Khung hình sắc nét',
                'body' => 'Hình ảnh đẹp, diễn xuất chính nổi bật. Phần giữa có thể gọn hơn.',
                'rating' => 4.0,
                'created_at' => '2026-05-09',
            ],
        ];
    }

    public static function reviewFeed(): array
    {
        return [
            [
                'id' => 2011,
                'movie' => 'Silent Harbor',
                'author' => 'Nova Gray',
                'title' => 'Slow burn đúng cách',
                'excerpt' => 'Phim tin vào sự im lặng như đối thoại, và không khí luôn liền mạch.',
                'rating' => 4.5,
                'likes' => 128,
                'created_at' => '2026-05-13',
            ],
            [
                'id' => 2012,
                'movie' => 'Night Circuit',
                'author' => 'Kai Tran',
                'title' => 'Đường đua neon',
                'excerpt' => 'Các pha rượt đuổi sắc gọn, nhạc synth là điểm sáng.',
                'rating' => 4.0,
                'likes' => 74,
                'created_at' => '2026-05-12',
            ],
            [
                'id' => 2013,
                'movie' => 'Golden Frame',
                'author' => 'Avery Stone',
                'title' => 'Lãng mạn phủ sắc vàng',
                'excerpt' => 'Diễn xuất tinh tế, màu sắc tuyệt đẹp. Một phim yêu thích mới.',
                'rating' => 5.0,
                'likes' => 156,
                'created_at' => '2026-05-10',
            ],
        ];
    }

    public static function commentsForReview(int $reviewId): array
    {
        return [
            [
                'id' => 101,
                'review_id' => $reviewId,
                'display_name' => 'Mia L.',
                'body' => 'Mình đồng ý về đoạn kết. Nó đọng lại vài ngày.',
                'created_at' => '2026-05-11',
            ],
            [
                'id' => 102,
                'review_id' => $reviewId,
                'display_name' => 'Eli R.',
                'body' => 'Thiết kế âm thanh cũng rất đáng khen.',
                'created_at' => '2026-05-12',
            ],
        ];
    }

    public static function profile(): array
    {
        return [
            'display_name' => 'Avery Stone',
            'bio' => 'Người mê phim, thiên về neo-noir và drama nhân vật.',
            'avatar_url' => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=200&q=80',
            'favorites' => array_slice(self::movies(), 0, 4),
            'stats' => [
                'reviews' => 38,
                'logs' => 112,
                'watchlist' => 27,
                'lists' => 5,
            ],
        ];
    }

    public static function activity(): array
    {
        return [
            ['type' => 'review', 'title' => 'Đánh giá Silent Harbor 4.5', 'time' => '2 giờ trước'],
            ['type' => 'log', 'title' => 'Ghi log Night Circuit', 'time' => '1 ngày trước'],
            ['type' => 'list', 'title' => 'Cập nhật list: Midnight Thrillers', 'time' => '3 ngày trước'],
        ];
    }

    public static function watchlist(): array
    {
        return array_slice(self::movies(), 0, 4);
    }

    public static function diary(): array
    {
        return [
            ['date' => '2026-05-12', 'movie' => 'Silent Harbor', 'rating' => 4.5, 'note' => 'Đoạn độc thoại cuối rất hay.'],
            ['date' => '2026-05-08', 'movie' => 'Night Circuit', 'rating' => 4.0, 'note' => 'Nhịp tốt, hơi có vài lỗ hổng.'],
            ['date' => '2026-05-02', 'movie' => 'Golden Frame', 'rating' => 5.0, 'note' => 'Hình ảnh rất đẹp.'],
        ];
    }

    public static function lists(): array
    {
        return [
            ['id' => 201, 'title' => 'Midnight Thrillers', 'count' => 8, 'visibility' => 'Công khai'],
            ['id' => 202, 'title' => 'Festival Winners', 'count' => 12, 'visibility' => 'Riêng tư'],
        ];
    }

    public static function listItems(int $listId): array
    {
        return array_slice(self::movies(), 0, 5);
    }

    public static function adminStats(): array
    {
        return [
            'total_movies' => 186,
            'total_reviews' => 1243,
            'active_users' => 512,
            'avg_rating' => 4.2,
        ];
    }

    public static function reports(): array
    {
        return [
            ['id' => 1, 'type' => 'Đánh giá', 'reason' => 'Spoiler', 'reported_by' => 'user23', 'status' => 'Chờ xử lý'],
            ['id' => 2, 'type' => 'Bình luận', 'reason' => 'Spam', 'reported_by' => 'user81', 'status' => 'Chờ xử lý'],
        ];
    }

    public static function users(): array
    {
        return [
            ['id' => 1, 'name' => 'Avery Stone', 'email' => 'avery@email.com', 'role' => 'member', 'status' => 'active'],
            ['id' => 2, 'name' => 'Nova Gray', 'email' => 'nova@email.com', 'role' => 'admin', 'status' => 'active'],
            ['id' => 3, 'name' => 'Kai Tran', 'email' => 'kai@email.com', 'role' => 'member', 'status' => 'locked'],
        ];
    }

    public static function adminMovies(): array
    {
        return array_slice(self::movies(), 0, 5);
    }
}
