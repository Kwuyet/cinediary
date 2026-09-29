<?php
declare(strict_types=1);

class AdminController extends Controller
{
    public function dashboard(): void
    {
        Auth::requireRole('admin');

        $userModel = new User();
        $movieModel = new Movie();
        $reviewModel = new Review();
        $commentModel = new Comment();
        $listModel = new ListModel();

        $filter = $this->resolveDashboardFilter();
        [$rangeStart, $rangeEnd, $filterLabel, $trendLabels] = $this->buildDashboardRange($filter);

        $totalUsers = $userModel->countCreatedBetween($rangeStart, $rangeEnd);
        $activeUsers = $totalUsers;
        $totalMovies = $movieModel->countCreatedBetween($rangeStart, $rangeEnd);
        $totalReviews = $reviewModel->countCreatedBetween($rangeStart, $rangeEnd);
        $totalComments = $commentModel->countCreatedBetween($rangeStart, $rangeEnd);
        $totalLists = $listModel->countCreatedBetween($rangeStart, $rangeEnd);
        $reviewsNeedAttention = $reviewModel->countLowRatedBetween($rangeStart, $rangeEnd, 3.0);
        $newUsersToday = $totalUsers;

        $averageRating = $reviewModel->averageRatingBetween($rangeStart, $rangeEnd);

        $reviewTrend = [];
        $signupTrend = [];
        $movieTrend = [];

        $cursor = new DateTimeImmutable($filter['start'] . ' 00:00:00');
        $endDate = new DateTimeImmutable($filter['end'] . ' 00:00:00');
        while ($cursor <= $endDate) {
            $day = $cursor->format('Y-m-d');
            $trendLabels[] = $cursor->format('d/m');
            $reviewTrend[] = $reviewModel->countCreatedOn($day);
            $signupTrend[] = $userModel->countCreatedOn($day);
            $movieTrend[] = $movieModel->countCreatedOn($day);
            $cursor = $cursor->modify('+1 day');
        }

        $contentMix = [
            'Phim' => $totalMovies,
            'Đánh giá' => $totalReviews,
            'Bình luận' => $totalComments,
            'Danh sách' => $totalLists,
        ];

        $contentToReview = array_slice(
            $this->filterByRange($reviewModel->getRecentForModeration(20, 3.0), $rangeStart, $rangeEnd),
            0,
            3
        );
        $recentUsers = $this->filterByRange($userModel->getRecentRegistrations(8), $rangeStart, $rangeEnd);
        $recentReviews = $this->filterByRange($reviewModel->getRecentActivity(8), $rangeStart, $rangeEnd);
        $recentMovies = $this->filterByRange($movieModel->getRecentAdded(8), $rangeStart, $rangeEnd);

        $recentActivity = [];
        foreach ($recentUsers as $user) {
            $recentActivity[] = [
                'type' => 'user',
                'icon' => 'person_add',
                'title' => $user['display_name'] ?? 'Người dùng mới',
                'subtitle' => 'Đăng ký tài khoản mới',
                'meta' => !empty($user['created_at']) ? date('H:i, d/m', strtotime($user['created_at'])) : '',
                'ts' => !empty($user['created_at']) ? strtotime($user['created_at']) : 0,
            ];
        }
        foreach ($recentReviews as $review) {
            $recentActivity[] = [
                'type' => 'review',
                'icon' => 'rate_review',
                'title' => $review['title'] ?? 'Review mới',
                'subtitle' => ($review['display_name'] ?? 'Thành viên') . ' • ' . ($review['movie_title_vi'] ?? $review['movie_title_en'] ?? 'Phim'),
                'meta' => !empty($review['created_at']) ? date('H:i, d/m', strtotime($review['created_at'])) : '',
                'ts' => !empty($review['created_at']) ? strtotime($review['created_at']) : 0,
            ];
        }
        foreach ($recentMovies as $movie) {
            $recentActivity[] = [
                'type' => 'movie',
                'icon' => 'movie',
                'title' => $movie['title_vi'] ?: ($movie['title_en'] ?? 'Phim mới'),
                'subtitle' => 'Phim mới được thêm vào hệ thống',
                'meta' => !empty($movie['created_at']) ? date('H:i, d/m', strtotime($movie['created_at'])) : '',
                'ts' => !empty($movie['created_at']) ? strtotime($movie['created_at']) : 0,
            ];
        }

        usort($recentActivity, static function (array $left, array $right): int {
            return (int) ($right['ts'] ?? 0) <=> (int) ($left['ts'] ?? 0);
        });
        $recentActivity = array_slice($recentActivity, 0, 6);

        $this->view('admin/dashboard', [
            'filterStart' => $filter['start'],
            'filterEnd' => $filter['end'],
            'filterLabel' => $filterLabel,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'trendLabels' => $trendLabels,
            'reviewTrend' => $reviewTrend,
            'signupTrend' => $signupTrend,
            'movieTrend' => $movieTrend,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'totalMovies' => $totalMovies,
            'totalReviews' => $totalReviews,
            'totalComments' => $totalComments,
            'totalLists' => $totalLists,
            'reviewsNeedAttention' => $reviewsNeedAttention,
            'newUsersToday' => $newUsersToday,
            'averageRating' => $averageRating,
            'contentMix' => $contentMix,
            'contentToReview' => $contentToReview,
            'recentActivity' => $recentActivity,
        ]);
    }

    public function users(): void
    {
        Auth::requireRole('admin');
        $userModel = new User();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $q = trim((string) ($_GET['q'] ?? ''));
        $role = trim((string) ($_GET['role'] ?? ''));

        if ($q !== '') {
            $offset = ($page - 1) * $perPage;
            $totalUsers = $userModel->countFiltered($q, $role);
            $totalPages = max(1, (int) ceil($totalUsers / $perPage));
            $users = $userModel->searchUsers($q, $perPage, $offset);
            // apply role filter on returned rows in case DB didn't handle -> filter PHP-side as fallback
            if ($role === 'admin' || $role === 'member') {
                $users = array_values(array_filter($users, static function ($u) use ($role) {
                    $r = ($u['role'] ?? 'member');
                    return $r === $role;
                }));
            }
        } else {
            $totalUsers = $userModel->countAll();
            $totalPages = max(1, (int) ceil($totalUsers / $perPage));
            $users = $userModel->findAllPaginated($page, $perPage);
            if ($role === 'admin' || $role === 'member') {
                $users = array_values(array_filter($users, static function ($u) use ($role) {
                    $r = ($u['role'] ?? 'member');
                    return $r === $role;
                }));
            }
        }

        $this->view('admin/users', [
            'users' => $users,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
            'perPage' => $perPage,
            'q' => $q,
            'role' => $role,
        ]);
    }

    public function toggleUserLock(): void
    {
        Auth::requireRole('admin');
        $id = (int) $this->param('id');

        // accept JSON or form-encoded
        $body = json_decode(file_get_contents('php://input'), true);
        $locked = null;
        if (is_array($body) && isset($body['locked'])) {
            $locked = (bool) $body['locked'];
        } elseif (isset($_POST['locked'])) {
            $locked = (bool) $_POST['locked'];
        }

        if ($locked === null) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(['success' => false, 'message' => 'Missing parameter locked']);
            return;
        }

        $userModel = new User();
        $col = $userModel->availableStatusColumn();
        if ($col === null) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(['success' => false, 'message' => 'CSDL không có cột trạng thái để cập nhật (trangThai/status/biKhoa/is_locked).']);
            return;
        }

        $ok = $userModel->setLocked($id, $locked);

        header('Content-Type: application/json');
        if ($ok) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái người dùng, kiểm tra quyền ghi DB hoặc tên cột.']);
        }
    }

    public function changeUserRole(): void
    {
        Auth::requireRole('admin');
        $id = (int) $this->param('id');

        $body = json_decode(file_get_contents('php://input'), true);
        $role = null;
        if (is_array($body) && isset($body['role'])) {
            $role = $body['role'];
        } elseif (isset($_POST['role'])) {
            $role = $_POST['role'];
        }

        if (!in_array($role, ['admin', 'member'], true)) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(['success' => false, 'message' => 'Invalid role']);
            return;
        }

        $userModel = new User();
        $ok = $userModel->setRole($id, $role);

        header('Content-Type: application/json');
        echo json_encode(['success' => $ok]);
    }

    public function usersSuggest(): void
    {
        Auth::requireRole('admin');

        $q = trim((string) ($_GET['q'] ?? ''));
        $role = trim((string) ($_GET['role'] ?? ''));
        if ($q === '') {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        $userModel = new User();
        $matches = $userModel->searchUsers($q, 10, 0);
        if ($role === 'admin' || $role === 'member') {
            $matches = array_values(array_filter($matches, static function ($u) use ($role) { return ($u['role'] ?? 'member') === $role; }));
        }
        $out = [];
        foreach ($matches as $m) {
            $out[] = ['id' => $m['id'], 'display_name' => $m['display_name'], 'email' => $m['email'] ?? ''];
        }

        header('Content-Type: application/json');
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    }

    public function reviews(): void
    {
        Auth::requireRole('admin');

        $reviewModel = new Review();
        $reviews = $reviewModel->getRecentForModeration(12, 3.0);

        $this->view('admin/reviews', [
            'reviews' => $reviews,
        ]);
    }

    public function moderationPanel(): void
    {
        Auth::requireRole('admin');

        $tab = (string) ($_GET['tab'] ?? 'reviews'); // reviews|comments|flagged
        $q = trim((string) ($_GET['q'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));
        $rating = trim((string) ($_GET['rating'] ?? ''));
        $time = trim((string) ($_GET['time'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;

        $reviewModel = new Review();
        $commentModel = new Comment();

        $reviews = [];
        $comments = [];
        $total = 0;

        if ($tab === 'comments') {
            $comments = $commentModel->getForModeration($page, $perPage, $q, $status, $time);
        } else {
            $reviews = $reviewModel->getForModeration($page, $perPage, $q, $status, $rating);
        }

        $this->view('admin/moderation', [
            'tab' => $tab,
            'q' => $q,
            'status' => $status,
            'rating' => $rating,
            'time' => $time,
            'reviews' => $reviews,
            'comments' => $comments,
            'page' => $page,
            'perPage' => $perPage,
        ]);
    }

    public function moderationAction(): void
    {
        Auth::requireRole('admin');
        $body = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $action = $body['action'] ?? '';
        $type = $body['type'] ?? 'review'; // review|comment
        $ids = $body['ids'] ?? (!empty($body['id']) ? [$body['id']] : []);

        header('Content-Type: application/json');
        if (empty($action) || empty($ids) || !is_array($ids)) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            return;
        }

        $reviewModel = new Review();
        $commentModel = new Comment();
        $res = ['success' => true, 'results' => []];

        foreach ($ids as $id) {
            $id = (int) $id;
            if ($type === 'review') {
                if ($action === 'delete') {
                    $ok = $reviewModel->deleteReview($id);
                } elseif ($action === 'hide') {
                    $ok = $reviewModel->setStatus($id, 'hidden');
                } elseif ($action === 'unhide') {
                    $ok = $reviewModel->setStatus($id, 'active');
                } else {
                    $ok = false;
                }
            } else {
                if ($action === 'delete') {
                    $commentModel->delete($id);
                    $ok = true;
                } elseif ($action === 'hide') {
                    $ok = $commentModel->setStatus($id, 'hidden');
                } elseif ($action === 'unhide') {
                    $ok = $commentModel->setStatus($id, 'active');
                } else {
                    $ok = false;
                }
            }
            $res['results'][$id] = $ok ? 'ok' : 'error';
            if (!$ok) $res['success'] = false;
        }

        echo json_encode($res);
    }

    public function moderationDetail(): void
    {
        Auth::requireRole('admin');
        $type = trim((string) ($_GET['type'] ?? 'review'));
        $id = (int) ($_GET['id'] ?? 0);

        header('Content-Type: application/json');
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid id']);
            return;
        }

        if ($type === 'comment') {
            $commentModel = new Comment();
            $c = $commentModel->findById($id);
            if ($c === null) { echo json_encode(['success'=>false,'message'=>'Not found']); return; }
            echo json_encode(['success' => true, 'item' => $c], JSON_UNESCAPED_UNICODE);
            return;
        }

        $reviewModel = new Review();
        $r = $reviewModel->findById($id);
        if ($r === null) { echo json_encode(['success'=>false,'message'=>'Not found']); return; }
        echo json_encode(['success' => true, 'item' => $r], JSON_UNESCAPED_UNICODE);
    }

    public function movies(): void
    {
        Auth::requireRole('admin');

        $movieModel = new Movie();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $totalMovies = $movieModel->countAll();
        $totalPages = max(1, (int) ceil($totalMovies / $perPage));
        $movies = $movieModel->findAllPaginated($page, $perPage);

        $this->view('admin/movies', [
            'movies' => $movies,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalMovies' => $totalMovies,
            'perPage' => $perPage,
        ]);
    }

    public function moviesImportForm(): void
    {
        Auth::requireRole('admin');
        $this->view('admin/movies-import', []);
    }

    public function moviesImportProcess(): void
    {
        Auth::requireRole('admin');

        if (empty($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Vui lòng chọn file CSV hợp lệ.');
            redirect('/admin/movies/import');
        }

        $tmp = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($tmp, 'r');
        if ($handle === false) {
            flash('error', 'Không thể mở file CSV.');
            redirect('/admin/movies/import');
        }

        $movieModel = new Movie();
        $row = 0;
        $headers = [];
        $imported = 0;
        $errors = [];
        $nowYear = (int) date('Y');

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            if ($row === 1) {
                // normalize headers
                $headers = array_map(function ($h) { return mb_strtolower(trim($h), 'UTF-8'); }, $data);
                continue;
            }

            if (count($data) === 0 || (count($data) === 1 && trim($data[0]) === '')) {
                continue;
            }

            $item = [];
            foreach ($headers as $i => $col) {
                $item[$col] = isset($data[$i]) ? trim($data[$i]) : '';
            }

            // map possible header names
            $titleVi = $item['title_vi'] ?? $item['tenphimvn'] ?? $item['ten_vn'] ?? ($item['title'] ?? '');
            $titleEn = $item['title_en'] ?? $item['tenphimen'] ?? $item['ten_en'] ?? ($item['title_en'] ?? '');
            $year = isset($item['year']) ? (int) $item['year'] : (int) ($item['nam'] ?? 0);
            $duration = isset($item['duration_min']) ? (int) $item['duration_min'] : (int) ($item['duration'] ?? 0);
            $genre = $item['genre'] ?? $item['theloai'] ?? '';
            $director = $item['director'] ?? $item['daodien'] ?? '';
            $castList = $item['cast_list'] ?? $item['dienvien'] ?? '';
            $synopsis = $item['synopsis'] ?? $item['mota'] ?? '';
            $ticketPrice = isset($item['ticket_price']) ? (int) $item['ticket_price'] : (int) ($item['giave'] ?? 0);
            $trailerUrl = $item['trailer_url'] ?? $item['trailer'] ?? '';
            $posterUrl = $item['poster_url'] ?? $item['poster'] ?? '';

            $rowErrors = [];
            if ($titleVi === '') $rowErrors[] = 'Thiếu tiêu đề VN';
            if ($titleEn === '') $rowErrors[] = 'Thiếu tiêu đề EN';
            if ($year < 1900 || $year > $nowYear) $rowErrors[] = 'Năm không hợp lệ';
            if ($duration < 0) $rowErrors[] = 'Thời lượng không hợp lệ';

            // validate poster URL if provided
            if ($posterUrl !== '') {
                if (!$this->isImageUrlReachable($posterUrl)) {
                    $rowErrors[] = 'Poster URL không truy cập được hoặc không phải ảnh';
                }
            }

            if (!empty($rowErrors)) {
                $errors[] = "Dòng {$row}: " . implode('; ', $rowErrors);
                continue;
            }

            try {
                $movieModel->createMovie($titleVi, $titleEn, $year, $genre, $director, $castList, $synopsis, $duration, $ticketPrice, $trailerUrl, $posterUrl);
                $imported++;
            } catch (Exception $e) {
                $errors[] = "Dòng {$row}: Lỗi khi chèn ({$e->getMessage()})";
            }
        }

        fclose($handle);

        $msg = "Đã import {$imported} phim.";
        if (!empty($errors)) {
            flash('error', $msg . ' Một số dòng bị bỏ: ' . implode(' | ', array_slice($errors, 0, 10)));
        } else {
            flash('success', $msg);
        }

        redirect('/admin/movies');
    }

    private function isImageUrlReachable(string $url): bool
    {
        // basic check using get_headers
        $opts = ['http' => ['method' => 'HEAD', 'timeout' => 5, 'follow_location' => 1]];
        $context = stream_context_create($opts);
        $headers = @get_headers($url, 1, $context);
        if ($headers === false) return false;
        // get_headers may return string or array first element
        $statusLine = $headers[0] ?? '';
        if (stripos($statusLine, '200') === false) return false;
        $ctype = '';
        if (is_array($headers)) {
            if (isset($headers['Content-Type'])) {
                $ctype = is_array($headers['Content-Type']) ? $headers['Content-Type'][0] : $headers['Content-Type'];
            }
        }
        if ($ctype === '') return false;
        return stripos($ctype, 'image/') === 0;
    }

    public function settings(): void
    {
        Auth::requireRole('admin');

        $this->view('admin/settings', []);
    }

    public function editMovie(): void
    {
        Auth::requireRole('admin');
        
        $movieId = (int) $this->param('id');
        $movieModel = new Movie();
        if ($movieId === 0) {
            // create new empty movie structure for the form
            $movie = [
                'id' => 0,
                'title_vi' => '',
                'title_en' => '',
                'year' => date('Y'),
                'genre' => '',
                'director' => '',
                'cast_list' => '',
                'synopsis' => '',
                'ticket_price' => 0,
                'duration_min' => 0,
                'trailer_url' => '',
                'poster_url' => '',
            ];
        } else {
            $movie = $movieModel->find($movieId);
            if (!$movie) {
                redirect('/admin');
            }
        }

        $this->view('admin/edit-movie', ['movie' => $movie]);
    }

    public function updateMovie(): void
    {
        Auth::requireRole('admin');
        
        $movieId = (int) $this->param('id');
        $movieModel = new Movie();
        $movie = $movieModel->find($movieId);
        if ($movieId !== 0 && !$movie) {
            flash('error', 'Phim không tồn tại');
            redirect('/admin');
        }
        
        $titleVi = trim($_POST['title_vi'] ?? '');
        $titleEn = trim($_POST['title_en'] ?? '');
        $year = (int) ($_POST['year'] ?? 0);
        $genre = trim($_POST['genre'] ?? '');
        $director = trim($_POST['director'] ?? '');
        $castList = trim($_POST['cast_list'] ?? '');
        $synopsis = trim($_POST['synopsis'] ?? '');
        $ticketPrice = max(0, (int) ($_POST['ticket_price'] ?? 0));
        $duration = (int) ($_POST['duration_min'] ?? 0);
        $trailerUrl = trim($_POST['trailer_url'] ?? '');
        $posterUrl = trim($_POST['poster_url'] ?? '');
        
        $errors = [];
        if (empty($titleVi)) $errors[] = 'Tên phim (VN) là bắt buộc';
        if (empty($titleEn)) $errors[] = 'Tên phim (EN) là bắt buộc';
        if ($year < 1900 || $year > date('Y')) $errors[] = 'Năm sản xuất không hợp lệ';
        
        if (!empty($errors)) {
            flash('error', implode(', ', $errors));
            redirect("/admin/movies/{$movieId}/edit");
        }

        if ($movieId === 0) {
            $newId = $movieModel->createMovie($titleVi, $titleEn, $year, $genre, $director, $castList, $synopsis, $duration, $ticketPrice, $trailerUrl, $posterUrl);
            flash('success', '✅ Thêm phim thành công');
            redirect('/admin/movies');
        }

        $movieModel->updateMovie($movieId, $titleVi, $titleEn, $year, $genre, $director, $castList, $synopsis, $duration, $ticketPrice, $trailerUrl, $posterUrl);

        flash('success', '✅ Cập nhật phim thành công');
        redirect('/admin/movies');
    }

    public function deleteMovie(): void
    {
        Auth::requireRole('admin');
        
        $movieId = (int) $this->param('id');
        $movieModel = new Movie();
        $movie = $movieModel->find($movieId);
        
        if (!$movie) {
            flash('error', 'Phim không tồn tại');
            redirect('/admin');
        }
        
        $movieTitle = $movie['title_vi'] ?? 'Phim';
        $movieModel->delete($movieId);
        
        flash('success', "🗑️ Đã xóa phim '{$movieTitle}' thành công");
        redirect('/admin');
    }

    private function resolveDashboardFilter(): array
    {
        $today = new DateTimeImmutable('today');

        $start = (string) ($_GET['start'] ?? $today->modify('-6 days')->format('Y-m-d'));
        $end = (string) ($_GET['end'] ?? $today->format('Y-m-d'));

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start)) {
            $start = $today->modify('-6 days')->format('Y-m-d');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
            $end = $today->format('Y-m-d');
        }

        if (strtotime($start) > strtotime($end)) {
            [$start, $end] = [$end, $start];
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    private function buildDashboardRange(array $filter): array
    {
        $start = new DateTimeImmutable($filter['start'] . ' 00:00:00');
        $end = (new DateTimeImmutable($filter['end'] . ' 00:00:00'))->modify('+1 day');
        $label = 'Từ ' . date('d/m/Y', strtotime($filter['start'])) . ' đến ' . date('d/m/Y', strtotime($filter['end']));

        return [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s'), $label, $this->buildTrendLabels($filter['start'], $filter['end'])];
    }

    private function buildTrendLabels(string $startDate, string $endDate): array
    {
        $labels = [];
        $cursor = new DateTimeImmutable($startDate . ' 00:00:00');
        $end = new DateTimeImmutable($endDate . ' 00:00:00');

        while ($cursor <= $end) {
            $labels[] = $cursor->format('d/m');
            $cursor = $cursor->modify('+1 day');
        }

        return $labels;
    }

    private function filterByRange(array $items, string $start, string $end): array
    {
        $startTs = strtotime($start);
        $endTs = strtotime($end);

        return array_values(array_filter($items, static function (array $item) use ($startTs, $endTs): bool {
            if (empty($item['created_at'])) {
                return false;
            }

            $itemTs = strtotime((string) $item['created_at']);
            return $itemTs >= $startTs && $itemTs < $endTs;
        }));
    }
}
