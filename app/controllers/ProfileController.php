<?php
declare(strict_types=1);

class ProfileController extends Controller
{
    public function show(): void
    {
        $userModel = new User();
        $id = $this->param('id');
        if ($id === null) {
            Auth::requireLogin();
            $userId = (int) Auth::user()['id'];
            $isOwner = true;
        } else {
            $userId = (int) $id;
            $isOwner = Auth::check() && (int) Auth::user()['id'] === $userId;
        }

        $user = $userModel->findById($userId);
        if (!$user) {
            http_response_code(404);
            echo 'User not found';
            return;
        }

        $reviews = $userModel->getReviewsByUser($userId, 6);
        $loggedMovies = $userModel->getLoggedMoviesByUser($userId, 6);
        $lists = $userModel->getListsByUser($userId, 6);
        $reviewCount = count($reviews);
        $listCount = count($lists);
        $movieCount = count($loggedMovies);
        $avgRating = 0.0;
        if ($reviewCount > 0) {
            $sum = 0.0;
            foreach ($reviews as $review) {
                $sum += (float) ($review['rating'] ?? 0);
            }
            $avgRating = $sum / $reviewCount;
        }

        $this->view('profile/show', [
            'user' => $user,
            'is_owner' => $isOwner,
            'reviews' => $reviews,
            'logged_movies' => $loggedMovies,
            'lists' => $lists,
            'review_count' => $reviewCount,
            'list_count' => $listCount,
            'movie_count' => $movieCount,
            'avg_rating' => $avgRating,
        ]);
    }

    public function edit(): void
    {
        Auth::requireLogin();
        $userModel = new User();
        $user = $userModel->findById((int) Auth::user()['id']);
        if (!$user) {
            http_response_code(404);
            echo 'User not found';
            return;
        }

        $this->view('profile/edit', ['user' => $user]);
    }

    public function update(): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::user()['id'];
        $displayName = trim($_POST['display_name'] ?? '');
        $bio = trim($_POST['bio'] ?? '');

        if ($displayName === '' || strlen($displayName) < 3 || strlen($displayName) > 50) {
            flash('error', 'Ten hien thi phai tu 3-50 ky tu.');
            redirect('/profile/edit');
        }

        if (strlen($bio) > 500) {
            flash('error', 'Bio toi da 500 ky tu.');
            redirect('/profile/edit');
        }

        $userModel = new User();
        if ($userModel->isDisplayNameTaken($displayName, $userId)) {
            flash('error', 'Ten hien thi da ton tai.');
            redirect('/profile/edit');
        }

        $currentUser = $userModel->findById($userId);
        if (!$currentUser) {
            flash('error', 'Khong tim thay tai khoan.');
            redirect('/profile/edit');
        }

        $avatarUrl = null;
        if (!empty($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                flash('error', 'Tai anh that bai.');
                redirect('/profile/edit');
            }

            $imageInfo = getimagesize($_FILES['avatar']['tmp_name']);
            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
            ];
            if ($imageInfo === false || !isset($allowed[$imageInfo['mime']])) {
                flash('error', 'Chi ho tro anh JPG, PNG, WEBP.');
                redirect('/profile/edit');
            }

            $ext = $allowed[$imageInfo['mime']];
            $filename = 'avatar-' . $userId . '-' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../../public/uploads/avatars';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $target = $uploadDir . '/' . $filename;
            if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                flash('error', 'Luu anh that bai.');
                redirect('/profile/edit');
            }

            $avatarUrl = '/public/uploads/avatars/' . $filename;
        }

        $userModel->updateProfile($userId, $displayName, $bio, $avatarUrl);
        $updatedUser = $userModel->findById($userId);
        if ($updatedUser) {
            Auth::login($updatedUser);
        }
        flash('success', 'Cap nhat ho so thanh cong.');
        redirect('/profile');
    }

    public function passwordForm(): void
    {
        Auth::requireLogin();
        $userModel = new User();
        $user = $userModel->findById((int) Auth::user()['id']);
        if (!$user) {
            http_response_code(404);
            echo 'User not found';
            return;
        }

        $this->view('profile/password', ['user' => $user]);
    }

    public function updatePassword(): void
    {
        Auth::requireLogin();
        $userId = (int) Auth::user()['id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (trim((string) $currentPassword) === '') {
            flash('error', 'Vui lòng nhập mật khẩu hiện tại.');
            redirect('/profile/password');
        }
        if (!Validator::password($newPassword)) {
            flash('error', 'Mật khẩu mới không đạt yêu cầu.');
            redirect('/profile/password');
        }
        if ($newPassword !== $confirmPassword) {
            flash('error', 'Mật khẩu mới không khớp.');
            redirect('/profile/password');
        }

        $userModel = new User();
        $user = $userModel->findById($userId);
        if (!$user) {
            flash('error', 'Không tìm thấy tài khoản.');
            redirect('/profile/password');
        }

        $storedHash = (string) ($user['password_hash'] ?? '');
        $currentValid = password_verify($currentPassword, $storedHash);
        if (!$currentValid && $currentPassword === $storedHash) {
            $currentValid = true;
            $userModel->updatePasswordHash($userId, password_hash($currentPassword, PASSWORD_BCRYPT));
        }
        if (!$currentValid && strlen($storedHash) === 32 && hash_equals($storedHash, md5($currentPassword))) {
            $currentValid = true;
            $userModel->updatePasswordHash($userId, password_hash($currentPassword, PASSWORD_BCRYPT));
        }
        if (!$currentValid && strlen($storedHash) === 40 && hash_equals($storedHash, sha1($currentPassword))) {
            $currentValid = true;
            $userModel->updatePasswordHash($userId, password_hash($currentPassword, PASSWORD_BCRYPT));
        }
        if (!$currentValid) {
            flash('error', 'Mật khẩu hiện tại không đúng.');
            redirect('/profile/password');
        }

        $userModel->updatePasswordHash($userId, password_hash($newPassword, PASSWORD_BCRYPT));
        flash('success', 'Đã đổi mật khẩu thành công.');
        redirect('/profile');
    }
}
