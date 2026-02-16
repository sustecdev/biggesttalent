<main class="main-content">
    <!-- Login Section -->
    <section class="safezone-section">
        <div class="container">
            <div class="safezone-container">
                <!-- Login Card -->
                <div class="safezone-card">

                    <div class="safezone-actions">
                        <!-- Step 1: Login Form -->
                        <form id="safezone-login-form" class="safezone-form">
                            <div class="form-group">
                                <label for="pernum" class="form-label">Pernum</label>
                                <input type="text" id="pernum" name="pernum" class="form-input"
                                    placeholder="Enter your pernum" required>
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-input"
                                    placeholder="Enter your password" required>
                            </div>

                            <input type="hidden" name="key" value="Dmjfk78Ckjksj23KlmdMMszcX">

                            <button type="submit" class="btn-safezone btn-safezone-primary" id="login-btn">
                                LOG IN WITH SAFEZONE
                            </button>
                        </form>

                        <!-- Step 2: PIN Form (hidden initially) -->
                        <form id="safezone-pin-form" class="safezone-form" style="display: none;">
                            <div class="form-group"
                                style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                                <p
                                    style="color: rgba(147, 197, 253, 1); font-size: 14px; margin-bottom: 12px; line-height: 1.5;">
                                    Login successful! Please enter 3 digits from your 6-digit Master PIN.
                                </p>
                                <p style="color: var(--primary); font-weight: 700; font-size: 16px; margin-bottom: 8px;"
                                    id="pin-positions">
                                    Enter digits at positions: <span id="positions-display"
                                        style="font-size: 18px;"></span>
                                </p>
                                <p style="color: var(--text-muted); font-size: 12px; margin: 0; line-height: 1.5;">
                                    Example: If your PIN is 123456 and positions are 2, 4, 6, enter: 246
                                </p>
                            </div>

                            <div class="form-group">
                                <label for="pin" class="form-label">PIN Digits (3 digits)</label>
                                <input type="password" id="pin" name="pin" class="form-input" placeholder="•••" required
                                    maxlength="3" pattern="[0-9]{3}" inputmode="numeric" autocomplete="off"
                                    style="text-align: center; font-size: 24px; letter-spacing: 0.5em; font-family: monospace;"
                                    autofocus>
                                <p style="color: var(--text-muted); font-size: 12px; margin-top: 8px;" id="pin-hint">
                                </p>
                            </div>

                            <input type="hidden" id="pin-uid" name="uid" value="">
                            <input type="hidden" id="pin-key" name="key" value="">
                            <input type="hidden" id="pin-pernum" name="pernum" value="">

                            <button type="submit" class="btn-safezone btn-safezone-primary" id="pin-btn">
                                VERIFY PIN
                            </button>

                            <button type="button" class="btn-safezone btn-safezone-secondary" id="back-to-login"
                                style="margin-top: 10px;">
                                Back to Login
                            </button>
                        </form>

                        <div id="safezone-error" class="safezone-error" style="display: none; margin-top: 20px;"></div>
                    </div>

                    <div class="safezone-info">
                        <p class="safezone-info-text">
                            SafeZone provides secure authentication for all Biggest Talent participants.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Coming Soon Modal -->
<div id="coming-soon-modal" class="custom-modal-overlay">
    <div class="custom-modal-content">
        <h2 class="modal-message">2026 biggest talent africa <br> season is coming soon</h2>
        <button id="close-modal-btn" class="btn-safezone btn-safezone-primary" style="width: auto; padding: 10px 30px; margin: 0 auto;">Okay</button>
    </div>
</div>

<style>
.custom-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.custom-modal-content {
    background: white;
    padding: 40px;
    border-radius: 16px;
    text-align: center;
    max-width: 90%;
    width: 450px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.modal-message {
    color: #1a1a1a;
    font-size: 24px;
    margin-bottom: 30px;
    font-weight: 700;
    line-height: 1.4;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
@keyframes slideUp {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('coming-soon-modal');
    const closeBtn = document.getElementById('close-modal-btn');
    
    if (modal && closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
});
</script>
<!-- Script for login form needs to be updated to point to correct API endpoint - keeping as is for now but needs API controller -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loginForm = document.getElementById('safezone-login-form');
        const pinForm = document.getElementById('safezone-pin-form');
        const errorDiv = document.getElementById('safezone-error');
        const pinPositions = document.getElementById('positions-display');
        const pinHint = document.getElementById('pin-hint');
        const backBtn = document.getElementById('back-to-login');

        // API Endpoints (MVC)
        const LOGIN_API = '<?= URLROOT ?>/safezone/login';
        const PIN_API = '<?= URLROOT ?>/safezone/verifyPin';

        let currentUid = '';
        let currentPernum = '';
        let currentKey = ''; // The positions (e.g., "135")

        // Helper: Generate 3 unique random numbers between 1 and 6
        function generatePositions() {
            const positions = [];
            while (positions.length < 3) {
                const r = Math.floor(Math.random() * 6) + 1;
                if (positions.indexOf(r) === -1) positions.push(r);
            }
            return positions.sort((a, b) => a - b);
        }

        // Helper: Show error
        function showError(msg) {
            errorDiv.textContent = msg;
            errorDiv.style.display = 'block';
            errorDiv.className = 'safezone-error visible';
        }

        // Step 1: Login Handler
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            errorDiv.style.display = 'none';

            const btn = document.getElementById('login-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Verifying...';
            btn.disabled = true;

            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => data[key] = value);

            fetch(LOGIN_API, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(result => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;

                    if (result.success) {
                        // Success Step 1
                        currentUid = result.uid;
                        currentPernum = result.pernum || data.pernum;

                        // Generate PIN challenge
                        const pos = generatePositions();
                        currentKey = pos.join('');

                        // Update UI
                        loginForm.style.display = 'none';
                        pinForm.style.display = 'block';

                        // Show positions (1st, 3rd, 5th etc)
                        pinPositions.textContent = pos.join(', ');
                        pinHint.textContent = `Please enter the ${ordinal(pos[0])}, ${ordinal(pos[1])}, and ${ordinal(pos[2])} digits of your Master PIN.`;

                        // Update hidden fields
                        document.getElementById('pin-uid').value = currentUid;
                        document.getElementById('pin-key').value = currentKey;
                        document.getElementById('pin-pernum').value = currentPernum;

                        // Focus PIN input
                        document.getElementById('pin').focus();
                    } else {
                        showError(result.message || 'Login failed');
                    }
                })
                .catch(err => {
                    console.error(err);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    showError('Network error occurred. Please try again.');
                });
        });

        // Step 2: PIN Handler
        pinForm.addEventListener('submit', function (e) {
            e.preventDefault();
            errorDiv.style.display = 'none';

            const btn = document.getElementById('pin-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Checking...';
            btn.disabled = true;

            const pinVal = document.getElementById('pin').value;

            fetch(PIN_API, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    uid: currentUid,
                    pernum: currentPernum,
                    pin: pinVal,
                    key: currentKey
                })
            })
                .then(response => response.json())
                .then(result => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;

                    if (result.success) {
                        // Success Step 2 - Redirect
                        window.location.href = result.redirect || 'index.php';
                    } else {
                        showError(result.message || 'PIN verification failed');
                        // Reset PIN input
                        document.getElementById('pin').value = '';
                    }
                })
                .catch(err => {
                    console.error(err);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    showError('Network error occurred during varification.');
                });
        });

        // Helper: Format ordinal numbers (1st, 2nd, 3rd)
        function ordinal(n) {
            const s = ["th", "st", "nd", "rd"];
            const v = n % 100;
            return n + (s[(v - 20) % 10] || s[v] || s[0]);
        }

        // Back button
        backBtn.addEventListener('click', function () {
            pinForm.style.display = 'none';
            loginForm.style.display = 'block';
            errorDiv.style.display = 'none';
            document.getElementById('pin').value = '';
        });
    });
</script>