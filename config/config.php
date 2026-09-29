<?php
declare(strict_types=1);

$config = [
    // Sub-folder the app is served from ('' when it runs at the domain root, e.g. '/CineDiary' on XAMPP)
    'base_path' => '',
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'cinediary',
        'user' => 'root',
        'pass' => '',
        'port' => 3306,
        'charset' => 'utf8mb4',
    ],
    'auth' => [
        'google_client_id' => null,
        'google_client_secret' => null,
        // example redirect after Google OAuth should be set in Google Console
        'google_redirect' => '/auth/google/callback',
    ],
    'mail' => [
        'driver' => 'smtp',
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => 'user@example.com',
        'password' => 'secret',
        'encryption' => 'tls', // tls or ssl
        'from' => ['address' => 'noreply@example.com', 'name' => 'CineDiary'],
    ],
];

// Real credentials live in config/config.local.php (not committed to Git).
// Copy config/config.local.example.php to config/config.local.php and edit it.
if (is_file(__DIR__ . '/config.local.php')) {
    $local = require __DIR__ . '/config.local.php';
    if (is_array($local)) {
        $config = array_replace_recursive($config, $local);
    }
}

function config(string $key, $default = null)
{
    global $config;
    $segments = explode('.', $key);
    $value = $config;
    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }
    return $value;
}

function url(string $path = '/'): string
{
    $base = rtrim((string) config('base_path', ''), '/');
    $path = '/' . ltrim($path, '/');
    return $base . $path;
}

function redirect(string $path): void
{
    if (preg_match('#^https?://#i', $path)) {
        header('Location: ' . $path);
        exit;
    }
    if ($path === '') {
        $path = '/';
    }
    header('Location: ' . url($path));
    exit;
}

function e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function flash(string $key, ?string $message = null): ?string
{
    if (!isset($_SESSION)) {
        return null;
    }
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function avatar_url(?string $avatar): string
{
    $avatar = trim((string) $avatar);
    if ($avatar === '') {
        return '';
    }

    if (preg_match('#^https?://#i', $avatar) || str_starts_with($avatar, '//')) {
        return $avatar;
    }

    $avatar = str_replace('\\', '/', $avatar);

    if (str_starts_with($avatar, '/public/')) {
        return url($avatar);
    }

    if (str_starts_with($avatar, '/uploads/')) {
        return url('/public' . $avatar);
    }

    if ($avatar[0] !== '/') {
        $avatar = '/public/uploads/avatars/' . ltrim($avatar, '/');
    } elseif (!str_starts_with($avatar, '/public/')) {
        $avatar = '/public' . $avatar;
    }

    return url($avatar);
}

function avatar_initial(?string $name): string
{
    $name = trim((string) $name);
    if ($name === '') {
        return '?';
    }

    $first = mb_substr($name, 0, 1, 'UTF-8');
    if ($first === '') {
        return '?';
    }

    return mb_strtoupper($first, 'UTF-8');
}

function render_avatar(?string $name, ?string $avatar = null, array $options = []): string
{
    $size = max(24, (int) ($options['size'] ?? 48));
    $label = (string) ($options['alt'] ?? ($name !== null && trim($name) !== '' ? trim($name) : 'Avatar'));
    $class = trim('user-avatar ' . (string) ($options['class'] ?? ''));
    $src = avatar_url($avatar);
    $initial = avatar_initial($name);

    $style = '--avatar-size: ' . $size . 'px;';

    if ($src !== '') {
        return '<img class="' . e($class) . '" src="' . e($src) . '" alt="' . e($label) . '" loading="lazy" style="' . e($style) . '">';
    }

    return '<span class="' . e($class . ' user-avatar--fallback') . '" aria-label="' . e($label) . '" style="' . e($style) . '">' . e($initial) . '</span>';
}
