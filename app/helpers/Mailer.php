<?php
declare(strict_types=1);

// Simple Mailer wrapper that prefers PHPMailer (if installed via Composer)
class Mailer
{
    public static function send(string $to, string $subject, string $body, ?string $from = null, ?string $fromName = null): bool
    {
        $cfg = config('mail', []);
        $fromAddr = $from ?? ($cfg['from']['address'] ?? ('noreply@' . ($_SERVER['SERVER_NAME'] ?? 'localhost')));
        $fromName = $fromName ?? ($cfg['from']['name'] ?? 'CineDiary');

        // Use PHPMailer if available
        if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
            require_once __DIR__ . '/../../vendor/autoload.php';
        }

        if (class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $driver = $cfg['driver'] ?? 'smtp';
                if ($driver === 'smtp') {
                    $mail->isSMTP();
                    $mail->Host = $cfg['host'] ?? 'localhost';
                    $mail->SMTPAuth = !empty($cfg['username']);
                    if (!empty($cfg['username'])) $mail->Username = $cfg['username'];
                    if (!empty($cfg['password'])) $mail->Password = $cfg['password'];
                    $enc = $cfg['encryption'] ?? 'tls';
                    if ($enc === 'ssl') {
                        if (defined('PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS')) {
                            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                        } else {
                            $mail->SMTPSecure = 'ssl';
                        }
                    } else {
                        if (defined('PHPMailer\PHPMailer\PHPMailer::PHPMailer')) {
                            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                        } else {
                            $mail->SMTPSecure = 'tls';
                        }
                    }
                    $mail->Port = $cfg['port'] ?? 587;
                }

                $mail->setFrom($fromAddr, $fromName);
                $mail->addAddress($to);
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->isHTML(false);

                return (bool) $mail->send();
            } catch (\PHPMailer\PHPMailer\Exception $ex) {
                // fall through to fallback
            }
        }

        // Fallback to PHP mail()
        $headers = 'From: ' . $fromName . ' <' . $fromAddr . ">\r\n" .
            "Content-Type: text/plain; charset=utf-8\r\n";
        return (bool) @mail($to, $subject, $body, $headers);
    }
}
