<?php
/**
 * Email Service
 * Handles email notifications
 * Note: This is a simplified version. For production, consider using PHPMailer or similar library.
 */

class EmailService
{
    /**
     * Send email using PHP's mail() function
     * For production, replace with SMTP library like PHPMailer
     */
    public function send($to, $toName, $subject, $body, $altBody = null)
    {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Biggest Talent <noreply@biggesttalent.world>" . "\r\n";
        
        $altBody = $altBody ?? strip_tags($body);
        
        // Try to send email
        $result = mail($to, $subject, $body, $headers);
        
        if (!$result) {
            error_log("Email send failed to: $to");
            return false;
        }
        
        return true;
    }

    /**
     * Send SafeZone credentials to user
     */
    public function sendSafeZoneCredentials($email, $pernum, $uid, $password)
    {
        $subject = "Your SafeZone Pass Credentials - Biggest Talent";
        $baseUrl = defined('BASE_URL') ? BASE_URL : (defined('URLROOT') ? URLROOT . '/' : '');

        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                .cred-box { background: white; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 5px solid #667eea; }
                .cred-item { margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
                .cred-label { font-weight: bold; color: #666; font-size: 0.9em; text-transform: uppercase; }
                .cred-value { font-family: monospace; font-size: 1.2em; color: #333; }
                .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin-top: 20px; font-size: 0.9em; border: 1px solid #ffeeba; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Welcome to SafeZone!</h1>
                </div>
                <div class='content'>
                    <h2>Registration Successful</h2>
                    <p>Your SafeZone Pass has been created successfully. Here are your login credentials:</p>
                    
                    <div class='cred-box'>
                        <div class='cred-item'>
                            <div class='cred-label'>Pernum (Personal Number)</div>
                            <div class='cred-value'>{$pernum}</div>
                        </div>
                        <div class='cred-item'>
                            <div class='cred-label'>UID (User ID)</div>
                            <div class='cred-value'>{$uid}</div>
                        </div>
                        <div class='cred-item'>
                            <div class='cred-label'>Password</div>
                            <div class='cred-value'>{$password}</div>
                        </div>
                    </div>
                    
                    <div class='warning'>
                        <strong>⚠️ IMPORTANT:</strong> Please keep your <strong>Master PIN</strong> safe. It is required for transactions and login verification. We do not store your PIN and cannot recover it for you.
                    </div>
                    
                    <p style='text-align: center;'>
                        <a href='{$baseUrl}auth/login' class='button'>Login to Biggest Talent</a>
                    </p>
                    <p>For your security, we recommend changing your password after your first login.</p>
                </div>
                <div class='footer'>
                    <p>This is an automated email from Biggest Talent. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->send($email, 'SafeZone User', $subject, $body);
    }
}
