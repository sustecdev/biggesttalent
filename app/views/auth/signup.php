<main class="flex-grow flex justify-center p-4 sm:p-6 lg:p-8 py-12 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-[#cd217d] rounded-full mix-blend-multiply filter blur-[128px] opacity-20 animate-blob"></div>
    <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-[#9a288d] rounded-full mix-blend-multiply filter blur-[128px] opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-[-10%] left-[20%] w-[40%] h-[40%] bg-[#aa843f] rounded-full mix-blend-multiply filter blur-[128px] opacity-20 animate-blob animation-delay-4000"></div>

    <div class="w-full max-w-md relative z-10">
        <div class="bg-[rgba(0,0,0,0.3)] backdrop-blur-md border border-white/10 rounded-2xl shadow-2xl overflow-hidden p-8 sm:p-10 relative">
            <div class="mb-8 relative z-10">
                <h2 class="text-3xl font-bold text-white mb-2 tracking-tight">Create Account</h2>
                <p class="text-pink-100 text-sm">Step 1 of 2: Verify your email, then enter your details</p>
            </div>

            <form id="signup-form" class="space-y-4 relative z-10">
                <div>
                    <label for="invitation_code" class="block text-sm font-medium text-pink-100 mb-1">Invitation Code <span class="text-pink-200 font-normal">(Optional)</span></label>
                    <input type="text" id="invitation_code" name="invitation_code"
                        class="w-full bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-200 focus:bg-white/20"
                        placeholder="Enter code if you have one">
                </div>

                <!-- Email + OTP verification -->
                <div>
                    <label for="email" class="block text-sm font-medium text-pink-100 mb-1">Email Address</label>
                    <div class="flex gap-2">
                        <input type="email" id="email" name="email" required
                            class="flex-1 bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-200 focus:bg-white/20"
                            placeholder="name@example.com">
                        <button type="button" id="send-otp-btn"
                            class="px-4 py-3.5 bg-white/10 hover:bg-white/20 border border-pink-300/30 rounded-xl text-pink-100 text-sm font-medium whitespace-nowrap transition-all">
                            Send Code
                        </button>
                    </div>
                    <div id="otp-section" class="mt-3" style="display: none;">
                        <label for="otp" class="block text-sm font-medium text-pink-100 mb-1">Verification Code</label>
                        <div class="flex gap-2">
                            <input type="text" id="otp" name="otp" maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                                class="flex-1 bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent text-center text-xl font-mono tracking-widest"
                                placeholder="000000">
                            <button type="button" id="verify-otp-btn"
                                class="px-4 py-3.5 bg-[#cd217d] hover:bg-[#a51a64] border border-white/20 rounded-xl text-white text-sm font-medium whitespace-nowrap transition-all">
                                Verify
                            </button>
                        </div>
                        <p id="otp-status" class="text-xs mt-1 text-pink-200"></p>
                    </div>
                    <p id="email-verified-badge" class="text-xs mt-2 text-emerald-400 flex items-center gap-1" style="display: none;">
                        <span>✓</span> Email verified
                    </p>
                </div>

                <div id="rest-of-form">
                    <div>
                        <label for="password" class="block text-sm font-medium text-pink-100 mb-1">Password</label>
                        <input type="password" id="password" name="password" required
                            class="w-full bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-200 focus:bg-white/20"
                            placeholder="Create a strong password">
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-pink-100 mb-1">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                            class="w-full bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-200 focus:bg-white/20"
                            placeholder="Confirm your password">
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox" id="age_confirm" name="age_confirm" required
                                class="mt-1 w-4 h-4 rounded border-pink-300 text-pink-600 focus:ring-white bg-white/20">
                            <span class="text-sm text-pink-100 group-hover:text-white transition-colors">I confirm that I am 18 years of age or older</span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox" id="terms_agree" name="terms_agree" required
                                class="mt-1 w-4 h-4 rounded border-pink-300 text-pink-600 focus:ring-white bg-white/20">
                            <span class="text-sm text-pink-100 group-hover:text-white transition-colors">
                                I agree to the <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php?url=terms" target="_blank" class="text-white font-bold hover:underline">Terms and Conditions</a> and <a href="#" class="text-white font-bold hover:underline">Privacy Policy</a>
                            </span>
                        </label>
                    </div>
                </div>

                <div id="signup-error" class="hidden p-3 rounded-lg bg-red-900/40 border border-red-500/50 text-white text-sm text-center"></div>

                <button type="submit" id="signup-btn" disabled
                    class="w-full bg-[#cd217d] text-white font-bold py-4 px-4 rounded-xl shadow-lg hover:bg-[#a51a64] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2 group mt-4 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                    Next: Set Master PIN
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div class="text-center mt-8">
                    <p class="text-sm text-pink-100">
                        Already have an account?
                        <a href="<?= URLROOT ?>/safezone" class="text-white font-bold hover:text-pink-200 transition-colors hover:underline">Sign In</a>
                    </p>
                </div>
            </form>
        </div>

        <div class="px-8 pt-6 pb-2 text-center">
            <p class="text-xs text-gray-500">Protected by SafeZone &copy; <?= date('Y') ?></p>
        </div>
    </div>
</main>

<!-- Coming Soon Modal -->
<div id="coming-soon-modal" class="fixed inset-0 bg-black/70 flex justify-center items-center z-[9999]" style="display: none;">
    <div class="bg-white p-10 rounded-2xl text-center max-w-[90%] w-[450px] shadow-2xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-8 uppercase tracking-wide">2026 biggest talent africa <br> season is coming soon</h2>
        <button id="close-modal-btn" class="bg-[#cd217d] text-white font-bold py-3 px-8 rounded-xl hover:bg-[#a51a64]">Okay</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('coming-soon-modal');
    const closeBtn = document.getElementById('close-modal-btn');
    if (modal && closeBtn) {
        closeBtn.addEventListener('click', function() { modal.style.display = 'none'; });
        modal.addEventListener('click', function(e) { if (e.target === modal) modal.style.display = 'none'; });
    }

    const signupForm = document.getElementById('signup-form');
    const errorDiv = document.getElementById('signup-error');
    const signupBtn = document.getElementById('signup-btn');
    const emailInput = document.getElementById('email');
    const sendOtpBtn = document.getElementById('send-otp-btn');
    const otpSection = document.getElementById('otp-section');
    const otpInput = document.getElementById('otp');
    const verifyOtpBtn = document.getElementById('verify-otp-btn');
    const otpStatus = document.getElementById('otp-status');
    const emailVerifiedBadge = document.getElementById('email-verified-badge');
    const rootUrl = '<?= URLROOT ?>';
    const originalBtnHtml = signupBtn.innerHTML;
    let emailVerified = false;

    sendOtpBtn.addEventListener('click', async function() {
        const email = emailInput.value.trim();
        if (!email) {
            otpStatus.textContent = 'Enter your email first.';
            otpSection.style.display = 'block';
            return;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            otpStatus.textContent = 'Please enter a valid email.';
            otpSection.style.display = 'block';
            return;
        }
        sendOtpBtn.disabled = true;
        sendOtpBtn.textContent = 'Sending...';
        otpStatus.textContent = '';
        otpSection.style.display = 'block';
        const base = (rootUrl && !rootUrl.endsWith('/')) ? rootUrl + '/' : (rootUrl || '');
        const apiUrl = base + 'index.php?url=safezone/sendotp';
        try {
            const res = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email })
            });
            const data = await res.json();
            if (data.success) {
                otpSection.style.display = 'block';
                otpInput.value = '';
                otpInput.focus();
                otpStatus.textContent = 'Code sent! Check your email.';
                otpStatus.classList.remove('text-red-400');
            } else {
                otpStatus.textContent = data.message || 'Failed to send. Try again.';
                otpStatus.classList.add('text-red-400');
            }
        } catch (e) {
            otpStatus.textContent = 'Network error. Please try again.';
            console.error('Send OTP error:', e);
        }
        sendOtpBtn.disabled = false;
        sendOtpBtn.textContent = 'Send Code';
    });

    verifyOtpBtn.addEventListener('click', async function() {
        const email = emailInput.value.trim();
        const otp = otpInput.value.trim();
        if (!email || !otp || otp.length !== 6) {
            otpStatus.textContent = 'Enter the 6-digit code from your email.';
            return;
        }
        verifyOtpBtn.disabled = true;
        verifyOtpBtn.textContent = 'Verifying...';
        otpStatus.textContent = '';
        const base = (rootUrl && !rootUrl.endsWith('/')) ? rootUrl + '/' : (rootUrl || '');
        const verifyUrl = base + 'index.php?url=safezone/verifyotp';
        try {
            const res = await fetch(verifyUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, otp })
            });
            const data = await res.json();
            if (data.success) {
                emailVerified = true;
                emailVerifiedBadge.style.display = 'flex';
                emailInput.disabled = true;
                sendOtpBtn.disabled = true;
                otpSection.style.display = 'none';
                signupBtn.disabled = false;
                otpStatus.textContent = '';
            } else {
                otpStatus.textContent = data.message || 'Invalid code.';
                otpStatus.classList.add('text-red-400');
            }
        } catch (e) {
            otpStatus.textContent = 'Network error. Please try again.';
        }
        verifyOtpBtn.disabled = false;
        verifyOtpBtn.textContent = 'Verify';
    });

    otpInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });

    signupForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!emailVerified) {
            errorDiv.textContent = 'Please verify your email first.';
            errorDiv.classList.remove('hidden');
            return;
        }

        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
        signupBtn.disabled = true;
        signupBtn.innerHTML = 'Creating Account...';

        const email = emailInput.value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const invitationCode = document.getElementById('invitation_code').value.trim();
        const ageConfirm = document.getElementById('age_confirm').checked;
        const termsAgree = document.getElementById('terms_agree').checked;

        if (password !== confirmPassword) {
            errorDiv.textContent = 'Passwords do not match';
            errorDiv.classList.remove('hidden');
            signupBtn.disabled = false;
            signupBtn.innerHTML = originalBtnHtml;
            return;
        }
        if (!ageConfirm) {
            errorDiv.textContent = 'You must confirm that you are 18 years or older';
            errorDiv.classList.remove('hidden');
            signupBtn.disabled = false;
            signupBtn.innerHTML = originalBtnHtml;
            return;
        }
        if (!termsAgree) {
            errorDiv.textContent = 'You must agree to the Terms of Service and Privacy Policy';
            errorDiv.classList.remove('hidden');
            signupBtn.disabled = false;
            signupBtn.innerHTML = originalBtnHtml;
            return;
        }

        try {
            const signupData = { email, password };
            if (invitationCode) signupData.invited_by = invitationCode;

            const base = (rootUrl && !rootUrl.endsWith('/')) ? rootUrl + '/' : (rootUrl || '');
            const signupUrl = base + 'index.php?url=safezone/signup';
            const response = await fetch(signupUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(signupData)
            });
            const result = await response.json();

            if (result.success && result.pernum) {
                window.location.href = rootUrl + '/safezone/setPinForm?pernum=' + encodeURIComponent(result.pernum);
            } else {
                errorDiv.textContent = result.message || 'Registration failed. Please try again.';
                errorDiv.classList.remove('hidden');
                signupBtn.disabled = false;
                signupBtn.innerHTML = originalBtnHtml;
            }
        } catch (error) {
            errorDiv.textContent = 'An error occurred. Please try again.';
            errorDiv.classList.remove('hidden');
            signupBtn.disabled = false;
            signupBtn.innerHTML = originalBtnHtml;
        }
    });
});
</script>
