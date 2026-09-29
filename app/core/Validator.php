<?php
declare(strict_types=1);

class Validator
{
    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function password(string $password): bool
    {
        return (bool) preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password);
    }

    public static function minLength(string $value, int $min): bool
    {
        return strlen($value) >= $min;
    }

    public static function rating(float $value): bool
    {
        return $value >= 0.5 && $value <= 5.0;
    }
}
