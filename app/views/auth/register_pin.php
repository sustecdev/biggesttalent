<?php
$title = 'Set Master PIN - SafeZone';
?>

<main class="main-content">
    <!-- PIN Setup Section -->
    <section class="safezone-section">
        <div class="container">
            <div class="safezone-container">
                <!-- PIN Setup Card -->
                <div class="safezone-card">
                    <div class="safezone-header">
                        <div class="safezone-logo-wrapper">
                            <i class="fas fa-lock" style="font-size: 48px; color: #667eea; margin-bottom: 20px;"></i>
                        </div>
                        <h1 class="safezone-title">Secure Your Data</h1>
                        <p class="safezone-subtitle">
                            Step 2 of 2: Set Master PIN
                        </p>
                        <p class="safezone-description">
                            Your 6-digit Master PIN is required for secure transactions and login verification.
                        </p>
                        <?php if (isset($_SESSION['register_pernum'])): ?>
                            <div style="background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 8px; padding: 12px; margin-top: 15px;">
                                <p style="margin: 0; font-size: 14px; color: #667eea;">
                                    Your Pernum: <strong><?= htmlspecialchars($_SESSION['register_pernum']) ?></strong>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" style="margin: 20px; padding: 15px; background: #fee; border: 1px solid #fcc; border-radius: 8px; color: #c33;">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="safezone-actions">
                        <form method="POST" action="<?= URLROOT ?>/auth/process_pin" id="pinForm" class="safezone-form">
                            <div class="form-group">
                                <label for="pin" class="form-label">6-Digit Master PIN</label>
                                <input type="password" id="pin" name="pin" required maxlength="6" minlength="6"
                                    pattern="[0-9]{6}"
                                    class="form-input"
                                    style="text-align: center; font-size: 24px; letter-spacing: 8px; font-family: monospace;"
                                    placeholder="******">
                                <p style="margin-top: 8px; text-align: center; font-size: 12px; color: #999;">
                                    Must be exactly 6 digits
                                </p>
                            </div>

                            <button type="submit" class="btn-safezone btn-safezone-primary">
                                COMPLETE REGISTRATION
                                <i class="fas fa-check-circle" style="margin-left: 8px;"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    // Form validation
    document.getElementById('pinForm').addEventListener('submit', function(e) {
        const pin = document.getElementById('pin').value;
        
        if (pin.length !== 6 || !/^\d{6}$/.test(pin)) {
            e.preventDefault();
            alert('PIN must be exactly 6 digits');
            return false;
        }
    });

    // Auto-focus PIN input
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('pin').focus();
    });
</script>
