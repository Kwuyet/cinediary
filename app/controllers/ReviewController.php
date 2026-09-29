<?php
declare(strict_types=1);

class ReviewController extends Controller
{
    public function show(): void
    {
        $reviewId = (int) $this->param('id');
        $reviewModel = new Review();
        $review = $reviewModel->findById($reviewId);

        if (!$review) {
            http_response_code(404);
            echo 'Review not found';
            return;
        }

        $movieModel = new Movie();
        $movie = $movieModel->find((int) ($review['movie_id'] ?? 0));

        if (!$movie) {
            http_response_code(404);
            echo 'Movie not found';
            return;
        }

        $this->view('reviews/show', [
            'review' => $review,
            'movie' => $movie,
        ]);
    }

    public function index(): void
    {
        $movieModel = new Movie();
        $reviewModel = new Review();
        $movieOptions = $movieModel->search([
            'q' => '',
            'genre' => '',
            'year' => '',
            'rating' => '',
        ]);
        $reviews = $reviewModel->getFeed(18);

        $this->view('reviews/index', [
            'movieOptions' => $movieOptions,
            'reviews' => $reviews,
        ]);
    }

    public function create(): void
    {
        Auth::requireLogin();
        $movieId = (int) $this->param('id');
        $movieModel = new Movie();
        $movie = $movieModel->find($movieId);
        if (!$movie) {
            http_response_code(404);
            echo 'Movie not found';
            return;
        }
        $this->view('reviews/form', ['movie' => $movie]);
    }

    public function edit(): void
    {
        Auth::requireLogin();
        $reviewId = (int) $this->param('id');

        $reviewModel = new Review();
        $review = $reviewModel->findById($reviewId);
        if (!$review) {
            http_response_code(404);
            echo 'Review not found';
            return;
        }

        $currentUser = Auth::user() ?? [];
        if ((int) ($review['user_id'] ?? 0) !== (int) ($currentUser['id'] ?? 0) && (($currentUser['role'] ?? '') !== 'admin')) {
            http_response_code(403);
            echo '403 Forbidden';
            return;
        }

        $movieModel = new Movie();
        $movie = $movieModel->find((int) $review['movie_id']);
        if (!$movie) {
            http_response_code(404);
            echo 'Movie not found';
            return;
        }

        $this->view('reviews/form', [
            'movie' => $movie,
            'review' => $review,
            'formMode' => 'edit',
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        $movieId = (int) $this->param('id');
        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $rating = (float) ($_POST['rating'] ?? 0);

        if ($title === '' || $body === '' || !Validator::rating($rating) || fmod($rating * 2, 1.0) !== 0.0) {
            flash('error', 'Please provide valid review data.');
            redirect('/movies/' . $movieId . '/reviews/create');
        }

        $imageUrl = null;
        if (!empty($_FILES['image']['name'] ?? '')) {
            $upload = $_FILES['image'];
            if (($upload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                flash('error', 'Image upload failed.');
                redirect('/movies/' . $movieId . '/reviews/create');
            }

            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $mime = mime_content_type($upload['tmp_name']);
            if (!in_array($mime, $allowed, true)) {
                flash('error', 'Unsupported image type.');
                redirect('/movies/' . $movieId . '/reviews/create');
            }

            $ext = pathinfo($upload['name'], PATHINFO_EXTENSION);
            $ext = $ext ? strtolower($ext) : 'jpg';
            $safeName = 'review_' . $movieId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $targetDir = __DIR__ . '/../../public/uploads/reviews';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0775, true);
            }
            $targetPath = $targetDir . '/' . $safeName;
            if (!move_uploaded_file($upload['tmp_name'], $targetPath)) {
                flash('error', 'Failed to save image.');
                redirect('/movies/' . $movieId . '/reviews/create');
            }

            $imageUrl = '/uploads/reviews/' . $safeName;
        }

        $reviewModel = new Review();
        $reviewModel->create($movieId, (int) Auth::user()['id'], $title, $body, $rating, $imageUrl);
        flash('success', 'Review posted.');
        redirect('/movies/' . $movieId);
    }

    public function update(): void
    {
        Auth::requireLogin();
        $reviewId = (int) $this->param('id');
        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $rating = (float) ($_POST['rating'] ?? 0);

        if ($title === '' || $body === '' || !Validator::rating($rating) || fmod($rating * 2, 1.0) !== 0.0) {
            flash('error', 'Please provide valid review data.');
            redirect('/reviews/' . $reviewId . '/edit');
        }

        $reviewModel = new Review();
        $review = $reviewModel->findById($reviewId);
        if (!$review) {
            http_response_code(404);
            echo 'Review not found';
            return;
        }

        $currentUser = Auth::user() ?? [];
        if ((int) ($review['user_id'] ?? 0) !== (int) ($currentUser['id'] ?? 0) && (($currentUser['role'] ?? '') !== 'admin')) {
            http_response_code(403);
            echo '403 Forbidden';
            return;
        }

        $imageUrl = null;
        if (!empty($_FILES['image']['name'] ?? '')) {
            $upload = $_FILES['image'];
            if (($upload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                flash('error', 'Image upload failed.');
                redirect('/reviews/' . $reviewId . '/edit');
            }

            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $mime = mime_content_type($upload['tmp_name']);
            if (!in_array($mime, $allowed, true)) {
                flash('error', 'Unsupported image type.');
                redirect('/reviews/' . $reviewId . '/edit');
            }

            $ext = pathinfo($upload['name'], PATHINFO_EXTENSION);
            $ext = $ext ? strtolower($ext) : 'jpg';
            $safeName = 'review_' . $review['movie_id'] . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $targetDir = __DIR__ . '/../../public/uploads/reviews';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0775, true);
            }
            $targetPath = $targetDir . '/' . $safeName;
            if (!move_uploaded_file($upload['tmp_name'], $targetPath)) {
                flash('error', 'Failed to save image.');
                redirect('/reviews/' . $reviewId . '/edit');
            }

            $imageUrl = '/uploads/reviews/' . $safeName;
        }

        $reviewModel->update($reviewId, $title, $body, $rating, $imageUrl);
        flash('success', 'Review updated.');
        redirect('/movies/' . (int) $review['movie_id']);
    }

    public function destroy(): void
    {
        Auth::requireLogin();
        $reviewId = (int) $this->param('id');

        $reviewModel = new Review();
        $review = $reviewModel->findById($reviewId);
        if (!$review) {
            http_response_code(404);
            echo 'Review not found';
            return;
        }

        $currentUser = Auth::user() ?? [];
        if ((int) ($review['user_id'] ?? 0) !== (int) ($currentUser['id'] ?? 0) && (($currentUser['role'] ?? '') !== 'admin')) {
            http_response_code(403);
            echo '403 Forbidden';
            return;
        }

        $reviewModel->delete($reviewId);
        flash('success', 'Review deleted.');
        redirect('/movies/' . (int) $review['movie_id']);
    }
}
