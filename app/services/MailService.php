<?php

namespace App\Services;

/**
 * Mail Service - SMTP email using PHPMailer (or PHP mail() fallback)
 */
class MailService
{
    private $mailer;
    private $enabled = false;
    private $usePhpMailer = false;

    public function __construct()
    {
        if (class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
            $this->usePhpMailer = true;
            $this->mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
            $this->configure();
        }
    }

    private function configure(): void
    {
        $host = getenv('SMTP_HOST');
        $user = getenv('SMTP_USERNAME');
        $pass = getenv('SMTP_PASSWORD');
        $port = (int)(getenv('SMTP_PORT') ?: 587);
        $encryption = strtolower(getenv('SMTP_ENCRYPTION') ?: 'tls');
        $fromEmail = getenv('SMTP_FROM_EMAIL') ?: 'noreply@biggesttalent.africa';
        $fromName = getenv('SMTP_FROM_NAME') ?: 'Biggest Talent Africa';

        if (empty($host) || empty($user) || empty($pass)) {
            $this->enabled = false;
            return;
        }

        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = $host;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $user;
            $this->mailer->Password = $pass;
            $this->mailer->SMTPSecure = $encryption === 'ssl' ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $port;
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->setFrom($fromEmail, $fromName);
            $this->mailer->isHTML(true);

            $this->enabled = true;
        } catch (\Exception $e) {
            error_log('MailService config error: ' . $e->getMessage());
            $this->enabled = false;
        }
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Send an email
     */
    public function send(string $to, string $toName, string $subject, string $htmlBody, ?string $altBody = null): bool
    {
        if (!$this->usePhpMailer || !$this->enabled) {
            return $this->fallbackMail($to, $subject, $htmlBody);
        }

        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to, $toName ?: '');
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = $altBody ?? strip_tags($htmlBody);
            $this->mailer->send();
            return true;
        } catch (\Exception $e) {
            error_log('MailService send error: ' . $this->mailer->ErrorInfo);
            return false;
        }
    }

    private function fallbackMail(string $to, string $subject, string $body): bool
    {
        $from = getenv('SMTP_FROM_EMAIL') ?: 'noreply@biggesttalent.africa';
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: Biggest Talent <{$from}>\r\n";
        return @mail($to, $subject, $body, $headers);
    }

    /**
     * Send OTP for email verification
     */
    public function sendOtp(string $to, string $otp): bool
    {
        $subject = 'Your verification code - Biggest Talent Africa';
        $body = "
        <html><body style='font-family:Arial,sans-serif;line-height:1.6;color:#333;'>
        <div style='max-width:400px;margin:0 auto;padding:20px;'>
            <h2 style='color:#cd217d;'>Email Verification</h2>
            <p>Your verification code is:</p>
            <p style='font-size:28px;font-weight:bold;letter-spacing:6px;color:#1a1a2e;'>{$otp}</p>
            <p style='font-size:12px;color:#666;'>This code expires in 10 minutes. If you didn't request this, please ignore this email.</p>
            <p>— Biggest Talent Africa</p>
        </div></body></html>";
        return $this->send($to, '', $subject, $body);
    }

    /**
     * Send SafeZone credentials (pernum, master pin, password) after registration
     */
    public function sendCredentials(string $to, string $pernum, string $masterPin, string $password): bool
    {
        $subject = 'Your SafeZone credentials - Biggest Talent Africa';
        $baseUrl = defined('URLROOT') ? URLROOT : '';
        $body = "
        <html>
        <head><style>
            body{font-family:Arial,sans-serif;line-height:1.6;color:#333;}
            .container{max-width:600px;margin:0 auto;padding:20px;}
            .header{background:linear-gradient(135deg,#cd217d 0%,#9a288d 100%);color:white;padding:30px;text-align:center;border-radius:10px 10px 0 0;}
            .content{background:#f9f9f9;padding:30px;border-radius:0 0 10px 10px;}
            .cred-box{background:white;padding:20px;border-radius:8px;margin:20px 0;border-left:5px solid #cd217d;}
            .cred-item{margin-bottom:12px;}
            .cred-label{font-weight:bold;color:#666;font-size:0.85em;text-transform:uppercase;}
            .cred-value{font-family:monospace;font-size:1.2em;color:#333;}
            .warning{background:#fff3cd;color:#856404;padding:15px;border-radius:8px;margin-top:20px;font-size:0.9em;border:1px solid #ffeeba;}
            .footer{text-align:center;margin-top:20px;color:#666;font-size:12px;}
        </style></head>
        <body>
        <div class='container'>
            <div class='header'><h1>🔐 Welcome to SafeZone!</h1></div>
            <div class='content'>
                <h2>Registration Successful</h2>
                <p>Your SafeZone account has been created. Here are your login credentials:</p>
                <div class='cred-box'>
                    <div class='cred-item'><div class='cred-label'>Account Number (Pernum)</div><div class='cred-value'>{$pernum}</div></div>
                    <div class='cred-item'><div class='cred-label'>Master PIN (6 digits)</div><div class='cred-value'>{$masterPin}</div></div>
                    <div class='cred-item'><div class='cred-label'>Password</div><div class='cred-value'>{$password}</div></div>
                </div>
                <div class='warning'><strong>⚠️ IMPORTANT:</strong> Keep these credentials safe. You'll need your Pernum and Master PIN to log in. We do not store your Master PIN.</div>
                <p style='text-align:center;'><a href='{$baseUrl}/safezone' style='display:inline-block;padding:12px 30px;background:#cd217d;color:white;text-decoration:none;border-radius:8px;'>Login to Biggest Talent</a></p>
            </div>
            <div class='footer'><p>Automated email from Biggest Talent Africa. Do not reply.</p></div>
        </div>
        </body></html>";
        return $this->send($to, '', $subject, $body);
    }

    /**
     * Send email when a nomination is approved or rejected
     */
    public function sendNominationStatus(string $to, string $recipientName, string $status, string $nomineeName, ?string $rejectionReason = null): bool
    {
        $baseUrl = defined('URLROOT') ? URLROOT : '';
        $statusLabel = ucfirst($status);

        if ($status === 'approved') {
            $subject = "Your nomination has been approved! – Biggest Talent Africa";
            $headerColor = '#22c55e';
            $headerText = 'Nomination Approved';
            $message = "Great news! Your nomination for <strong>" . htmlspecialchars($nomineeName) . "</strong> has been approved and is now live on Biggest Talent Africa.";
            $ctaText = 'View Nomination';
            $ctaUrl = $baseUrl . '/profile';
        } else {
            $subject = "Update on your nomination – Biggest Talent Africa";
            $headerColor = '#ef4444';
            $headerText = 'Nomination Not Approved';
            $message = "We've reviewed your nomination for <strong>" . htmlspecialchars($nomineeName) . "</strong> and unfortunately we're unable to approve it at this time.";
            if (!empty($rejectionReason)) {
                $message .= "<div style='margin:16px 0;padding:12px;background:#fef2f2;border-radius:8px;border-left:4px solid #ef4444;'><strong>Reason:</strong> " . htmlspecialchars($rejectionReason) . "</div>";
            }
            $message .= "<p>You can submit a new nomination or contact us if you have questions.</p>";
            $ctaText = 'Nominate Again';
            $ctaUrl = $baseUrl . '/nominate';
        }

        $body = "
        <html>
        <head><style>
            body{font-family:Arial,sans-serif;line-height:1.6;color:#333;}
            .container{max-width:600px;margin:0 auto;padding:20px;}
            .header{color:white;padding:30px;text-align:center;border-radius:10px 10px 0 0;}
            .content{background:#f9f9f9;padding:30px;border-radius:0 0 10px 10px;}
            .footer{text-align:center;margin-top:20px;color:#666;font-size:12px;}
            .btn{display:inline-block;padding:12px 30px;color:white!important;text-decoration:none;border-radius:8px;font-weight:600;}
        </style></head>
        <body>
        <div class='container'>
            <div class='header' style='background:linear-gradient(135deg," . ($status === 'approved' ? '#22c55e 0%,#16a34a 100%' : '#ef4444 0%,#dc2626 100%') . ");'>
                <h1 style='margin:0;font-size:24px;'>" . $headerText . "</h1>
            </div>
            <div class='content'>
                <p>Hello" . ($recipientName ? ' ' . htmlspecialchars($recipientName) : '') . ",</p>
                <p>" . $message . "</p>
                <p style='text-align:center;'><a href='{$ctaUrl}' class='btn' style='background:linear-gradient(135deg,#cd217d 0%,#a51a64 100%);'>" . $ctaText . "</a></p>
            </div>
            <div class='footer'><p>— Biggest Talent Africa</p></div>
        </div>
        </body></html>";

        return $this->send($to, $recipientName ?: '', $subject, $body);
    }
}
