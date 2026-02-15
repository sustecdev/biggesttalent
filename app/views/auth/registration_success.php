<?php
$title = 'Registration Complete - Biggest Talent';
?>

<main class="main-content">
    <!-- Success Section -->
    <section class="safezone-section">
        <div class="container">
            <div class="safezone-container">
                <!-- Success Card -->
                <div class="safezone-card">
                    <div class="safezone-header">
                        <div class="safezone-logo-wrapper">
                            <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-check" style="font-size: 40px; color: white;"></i>
                            </div>
                        </div>
                        <h1 class="safezone-title">Registration Complete!</h1>
                        <p class="safezone-description">
                            Your SafeZone account has been created successfully.
                        </p>
                    </div>

                    <div class="safezone-actions">
                        <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #3b82f6; font-weight: 600;">
                                <i class="fas fa-envelope" style="margin-right: 8px;"></i> Check your email!
                            </p>
                            <p style="margin: 0; font-size: 13px; color: #2563eb; line-height: 1.6;">
                                We have sent your Pernum, UID, and Password to your registered email address. You will need these details to log in.
                            </p>
                        </div>

                        <a href="<?= URLROOT ?>/auth/login" class="btn-safezone btn-safezone-primary" style="text-decoration: none; display: block; text-align: center;">
                            GO TO LOGIN
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
