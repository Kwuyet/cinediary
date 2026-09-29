<?php
declare(strict_types=1);

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login');
    }

    public function showRegister(): void
    {
        $this->view('auth/register');
    }

    public function login(): void
    {
        $identity = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($identity === '' || $password === '') {
            flash('error', 'Please enter valid credentials.');
            redirect('/login');
        }

        $userModel = new User();
        $user = Validator::email($identity)
            ? $userModel->findByEmail($identity)
            : $userModel->findByDisplayName($identity);
        if (!$user) {
            flash('error', 'Invalid email or password.');
            redirect('/login');
        }

        $storedHash = (string) ($user['password_hash'] ?? '');
        $validPassword = password_verify($password, $storedHash);

        if (!$validPassword && $password === $storedHash) {
            $validPassword = true;
            $userModel->updatePasswordHash((int) $user['id'], password_hash($password, PASSWORD_BCRYPT));
        }

        if (!$validPassword && strlen($storedHash) === 32 && hash_equals($storedHash, md5($password))) {
            $validPassword = true;
            $userModel->updatePasswordHash((int) $user['id'], password_hash($password, PASSWORD_BCRYPT));
        }

        if (!$validPassword && strlen($storedHash) === 40 && hash_equals($storedHash, sha1($password))) {
            $validPassword = true;
            $userModel->updatePasswordHash((int) $user['id'], password_hash($password, PASSWORD_BCRYPT));
        }

        if (!$validPassword) {
            flash('error', 'Invalid email or password.');
            redirect('/login');
        }

        if (($user['status'] ?? 'active') !== 'active') {
            flash('error', 'Account is locked.');
            redirect('/login');
        }

        Auth::login($user);
        flash('success', 'Logged in successfully.');
        redirect('/');
    }

    public function register(): void
    {
        $email = trim($_POST['email'] ?? '');
        $displayName = trim($_POST['display_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (!Validator::email($email)) {
            flash('error', 'Email is not valid.');
            redirect('/register');
        }
        if (!Validator::password($password)) {
            flash('error', 'Password does not meet requirements.');
            redirect('/register');
        }
        if ($password !== $confirm) {
            flash('error', 'Passwords do not match.');
            redirect('/register');
        }
        if (!Validator::minLength($displayName, 3)) {
            flash('error', 'Display name must be at least 3 characters.');
            redirect('/register');
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            flash('error', 'Email already exists.');
            redirect('/register');
        }
        if ($userModel->findByDisplayName($displayName)) {
            flash('error', 'Display name already exists.');
            redirect('/register');
        }

        // Create OTP and store pending registration in session
        $otp = random_int(100000, 999999);
        $expires = time() + 15 * 60; // 15 minutes
        $_SESSION['pending_register'] = [
            'email' => $email,
            'display_name' => $displayName,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'otp' => (string) $otp,
            'expires' => $expires,
        ];

        // send OTP email via Mailer
        $subject = 'CineDiary - Mã xác thực đăng ký';
        $message = "Mã xác thực của bạn là: $otp\nMã này có hiệu lực trong 15 phút.";
        // ensure Mailer helper is loaded
        if (file_exists(__DIR__ . '/../helpers/Mailer.php')) {
            require_once __DIR__ . '/../helpers/Mailer.php';
        }
        $sent = false;
        if (class_exists('Mailer')) {
            $sent = Mailer::send($email, $subject, $message);
        } else {
            // fallback to mail()
            $headers = "From: noreply@" . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "\r\n" .
                "Content-Type: text/plain; charset=utf-8\r\n";
            $sent = @mail($email, $subject, $message, $headers);
        }

        // update pending_register metadata
        $_SESSION['pending_register']['last_sent'] = time();
        $_SESSION['pending_register']['resend_count'] = 1;

        if (!$sent) {
            // fallback: write to system temp file for developer/testing
            $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cinediary_otp_log.txt';
            @file_put_contents($tmp, date('c') . "\t" . $email . "\t" . $otp . "\n", FILE_APPEND);
            flash('success', 'OTP created. Mail may not be configured on this server; OTP was logged to: ' . $tmp);
        } else {
            flash('success', 'We have sent an OTP to your email. Please enter it to complete registration.');
        }
        redirect('/register/verify');
    }

    public function resendRegisterOtp(): void
    {
        $pending = &$_SESSION['pending_register'];
        if (!$pending) {
            flash('error', 'No pending registration found. Please register first.');
            redirect('/register');
        }

        $now = time();
        $last = (int) ($pending['last_sent'] ?? 0);
        $count = (int) ($pending['resend_count'] ?? 0);

        // Rate limits: min 60s between sends, max 5 attempts
        if ($last > 0 && ($now - $last) < 60) {
            $wait = 60 - ($now - $last);
            flash('error', "Vui lòng chờ $wait giây trước khi gửi lại mã.");
            redirect('/register/verify');
        }
        if ($count >= 5) {
            flash('error', 'Bạn đã vượt quá số lần gửi mã cho phép. Vui lòng thử lại sau.');
            redirect('/register');
        }

        // generate new OTP
        $otp = random_int(100000, 999999);
        $expires = $now + 15 * 60;
        $pending['otp'] = (string) $otp;
        $pending['expires'] = $expires;
        $pending['last_sent'] = $now;
        $pending['resend_count'] = $count + 1;

        $email = $pending['email'];
        $subject = 'CineDiary - Mã xác thực đăng ký (resend)';
        $message = "Mã xác thực mới của bạn là: $otp\nMã này có hiệu lực trong 15 phút.";
        // send via Mailer helper if available
        if (file_exists(__DIR__ . '/../helpers/Mailer.php')) {
            require_once __DIR__ . '/../helpers/Mailer.php';
        }
        $sent = false;
        if (class_exists('Mailer')) {
            $sent = Mailer::send($email, $subject, $message);
        } else {
            $headers = "From: noreply@" . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "\r\n" .
                "Content-Type: text/plain; charset=utf-8\r\n";
            $sent = @mail($email, $subject, $message, $headers);
        }
        if (!$sent) {
            $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cinediary_otp_log.txt';
            @file_put_contents($tmp, date('c') . "\t" . $email . "\t" . $otp . "\n", FILE_APPEND);
            flash('success', 'Mã OTP đã được tạo lại và ghi vào log (mail không được cấu hình): ' . $tmp);
        } else {
            flash('success', 'Mã OTP mới đã được gửi tới email của bạn.');
        }

        redirect('/register/verify');
    }

    public function showRegisterVerify(): void
    {
        $this->view('auth/register_verify');
    }

    // Forgot password: show email form
    public function showForgot(): void
    {
        $this->view('auth/forgot');
    }

    // Send OTP for password reset
    public function sendForgotOtp(): void
    {
        $email = trim($_POST['email'] ?? '');
        if (!Validator::email($email)) {
            flash('error', 'Email không hợp lệ.');
            redirect('/forgot');
        }
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        if (!$user) {
            // don't reveal whether email exists
            flash('success', 'Nếu email tồn tại, chúng tôi đã gửi mã xác thực.');
            redirect('/forgot/verify');
        }

        $otp = random_int(100000, 999999);
        $expires = time() + 15 * 60;
        $_SESSION['pending_reset'] = [
            'email' => $email,
            'otp' => (string) $otp,
            'expires' => $expires,
        ];

        // send OTP via Mailer
        $subject = 'CineDiary - Mã xác thực khôi phục mật khẩu';
        $message = "Mã xác thực của bạn là: $otp\nMã này có hiệu lực trong 15 phút.";
        if (file_exists(__DIR__ . '/../helpers/Mailer.php')) {
            require_once __DIR__ . '/../helpers/Mailer.php';
        }
        $sent = false;
        if (class_exists('Mailer')) {
            $sent = Mailer::send($email, $subject, $message);
        } else {
            $headers = "From: noreply@" . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "\r\n" .
                "Content-Type: text/plain; charset=utf-8\r\n";
            $sent = @mail($email, $subject, $message, $headers);
        }

        $_SESSION['pending_reset']['last_sent'] = time();
        $_SESSION['pending_reset']['resend_count'] = 1;

        if (!$sent) {
            $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cinediary_otp_log.txt';
            @file_put_contents($tmp, date('c') . "\t" . $email . "\t" . $otp . "\n", FILE_APPEND);
            flash('success', 'Mã OTP đã được tạo và ghi vào log: ' . $tmp);
        } else {
            flash('success', 'Chúng tôi đã gửi mã xác thực (OTP) tới email nếu email tồn tại.');
        }

        redirect('/forgot/verify');
    }

    public function resendForgotOtp(): void
    {
        $pending = &$_SESSION['pending_reset'];
        if (!$pending) {
            flash('error', 'Không tìm thấy yêu cầu khôi phục. Vui lòng thử lại.');
            redirect('/forgot');
        }
        $now = time();
        $last = (int) ($pending['last_sent'] ?? 0);
        $count = (int) ($pending['resend_count'] ?? 0);
        if ($last > 0 && ($now - $last) < 60) {
            $wait = 60 - ($now - $last);
            flash('error', "Vui lòng chờ $wait giây trước khi gửi lại mã.");
            redirect('/forgot/verify');
        }
        if ($count >= 5) {
            flash('error', 'Bạn đã vượt quá số lần gửi mã cho phép. Vui lòng thử lại sau.');
            redirect('/forgot');
        }

        $otp = random_int(100000, 999999);
        $pending['otp'] = (string) $otp;
        $pending['expires'] = $now + 15 * 60;
        $pending['last_sent'] = $now;
        $pending['resend_count'] = $count + 1;

        $email = $pending['email'];
        $subject = 'CineDiary - Mã xác thực khôi phục mật khẩu (resend)';
        $message = "Mã xác thực mới của bạn là: $otp\nMã này có hiệu lực trong 15 phút.";
        if (file_exists(__DIR__ . '/../helpers/Mailer.php')) {
            require_once __DIR__ . '/../helpers/Mailer.php';
        }
        $sent = false;
        if (class_exists('Mailer')) {
            $sent = Mailer::send($email, $subject, $message);
        } else {
            $headers = "From: noreply@" . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "\r\n" .
                "Content-Type: text/plain; charset=utf-8\r\n";
            $sent = @mail($email, $subject, $message, $headers);
        }
        if (!$sent) {
            $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cinediary_otp_log.txt';
            @file_put_contents($tmp, date('c') . "\t" . $email . "\t" . $otp . "\n", FILE_APPEND);
            flash('success', 'Mã OTP đã được tạo lại và ghi vào log: ' . $tmp);
        } else {
            flash('success', 'Mã OTP mới đã được gửi tới email của bạn.');
        }
        redirect('/forgot/verify');
    }

    public function showForgotVerify(): void
    {
        $this->view('auth/forgot_verify');
    }

    public function verifyForgotOtp(): void
    {
        $code = trim($_POST['otp'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';
        $pending = $_SESSION['pending_reset'] ?? null;
        if (!$pending) {
            flash('error', 'No pending password reset request.');
            redirect('/forgot');
        }
        if (time() > ($pending['expires'] ?? 0)) {
            unset($_SESSION['pending_reset']);
            flash('error', 'OTP expired. Please request a new one.');
            redirect('/forgot');
        }
        if ($code !== ($pending['otp'] ?? '')) {
            flash('error', 'Invalid OTP code.');
            redirect('/forgot/verify');
        }
        if (trim((string) $currentPassword) === '') {
            flash('error', 'Vui lòng nhập mật khẩu hiện tại.');
            redirect('/forgot/verify');
        }
        if (!Validator::password($password)) {
            flash('error', 'Password does not meet requirements.');
            redirect('/forgot/verify');
        }
        if ($password !== $confirm) {
            flash('error', 'Passwords do not match.');
            redirect('/forgot/verify');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($pending['email']);
        if (!$user) {
            unset($_SESSION['pending_reset']);
            flash('error', 'Account not found.');
            redirect('/forgot');
        }
        $storedHash = (string) ($user['password_hash'] ?? '');
        $currentValid = password_verify($currentPassword, $storedHash);
        if (!$currentValid && $currentPassword === $storedHash) {
            $currentValid = true;
            $userModel->updatePasswordHash((int) $user['id'], password_hash($currentPassword, PASSWORD_BCRYPT));
        }
        if (!$currentValid && strlen($storedHash) === 32 && hash_equals($storedHash, md5($currentPassword))) {
            $currentValid = true;
            $userModel->updatePasswordHash((int) $user['id'], password_hash($currentPassword, PASSWORD_BCRYPT));
        }
        if (!$currentValid && strlen($storedHash) === 40 && hash_equals($storedHash, sha1($currentPassword))) {
            $currentValid = true;
            $userModel->updatePasswordHash((int) $user['id'], password_hash($currentPassword, PASSWORD_BCRYPT));
        }
        if (!$currentValid) {
            flash('error', 'Mật khẩu hiện tại không đúng.');
            redirect('/forgot/verify');
        }
        $userModel->updatePasswordHash((int) $user['id'], password_hash($password, PASSWORD_BCRYPT));
        unset($_SESSION['pending_reset']);
        flash('success', 'Mật khẩu đã được đặt lại. Bạn có thể đăng nhập bằng mật khẩu mới.');
        redirect('/login');
    }

    public function verifyRegisterOtp(): void
    {
        $code = trim($_POST['otp'] ?? '');
        $pending = $_SESSION['pending_register'] ?? null;
        if (!$pending) {
            flash('error', 'No pending registration found.');
            redirect('/register');
        }
        if (time() > ($pending['expires'] ?? 0)) {
            unset($_SESSION['pending_register']);
            flash('error', 'OTP expired. Please try registering again.');
            redirect('/register');
        }
        if ($code !== ($pending['otp'] ?? '')) {
            flash('error', 'Invalid OTP code.');
            redirect('/register/verify');
        }

        $userModel = new User();
        if ($userModel->findByEmail($pending['email'])) {
            unset($_SESSION['pending_register']);
            flash('error', 'Email already exists.');
            redirect('/register');
        }
        $userId = $userModel->createWithHash($pending['email'], $pending['password_hash'], $pending['display_name']);
        unset($_SESSION['pending_register']);
        if ($userId) {
            $user = $userModel->findById($userId);
            if ($user) {
                Auth::login($user);
            }
            flash('success', 'Account created.');
            redirect('/');
        }
        flash('error', 'Failed to create account.');
        redirect('/register');
    }

    public function googleRedirect(): void
    {
        $clientId = config('auth.google_client_id');
        $redirect = config('auth.google_redirect');
        if (empty($clientId) || empty($redirect)) {
            flash('error', 'Google OAuth is not configured.');
            redirect('/login');
        }
        $params = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => url($redirect),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);
        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . $params;
        redirect($authUrl);
    }

    public function googleCallback(): void
    {
        $code = $_GET['code'] ?? null;
        if (!$code) {
            flash('error', 'Google authentication failed.');
            redirect('/login');
        }
        $clientId = config('auth.google_client_id');
        $clientSecret = config('auth.google_client_secret');
        $redirect = url(config('auth.google_redirect'));
        if (empty($clientId) || empty($clientSecret)) {
            flash('error', 'Google OAuth is not configured.');
            redirect('/login');
        }

        // Exchange code for tokens
        $tokenEndpoint = 'https://oauth2.googleapis.com/token';
        $post = http_build_query([
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirect,
            'grant_type' => 'authorization_code',
        ]);
        $opts = ['http' => ['method' => 'POST', 'header' => "Content-Type: application/x-www-form-urlencoded\r\n", 'content' => $post]];
        $resp = @file_get_contents($tokenEndpoint, false, stream_context_create($opts));
        if (!$resp) {
            flash('error', 'Failed to retrieve access token from Google.');
            redirect('/login');
        }
        $data = json_decode($resp, true);
        $accessToken = $data['access_token'] ?? null;
        if (!$accessToken) {
            flash('error', 'Google access token missing.');
            redirect('/login');
        }

        $userinfo = @file_get_contents('https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . urlencode($accessToken));
        if (!$userinfo) {
            flash('error', 'Failed to retrieve Google user info.');
            redirect('/login');
        }
        $profile = json_decode($userinfo, true);
        $email = $profile['email'] ?? null;
        $name = $profile['name'] ?? ($profile['given_name'] ?? '');
        if (!$email) {
            flash('error', 'Google account has no email.');
            redirect('/login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);
        if (!$user) {
            // create a new user with random password
            $password = bin2hex(random_bytes(8));
            $userId = $userModel->create($email, $password, $name);
            $user = $userModel->findById($userId);
        }
        if ($user) {
            Auth::login($user);
            flash('success', 'Logged in with Google.');
            redirect('/');
        }
        flash('error', 'Unable to login with Google.');
        redirect('/login');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/login');
    }
}
