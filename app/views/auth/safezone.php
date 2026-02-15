<main class="flex-grow flex justify-center p-4 sm:p-6 lg:p-8 py-12 relative overflow-hidden">
    <!-- Background Decor -->
    <div
        class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-[#cd217d] rounded-full mix-blend-multiply filter blur-[128px] opacity-20 animate-blob">
    </div>
    <div
        class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-[#9a288d] rounded-full mix-blend-multiply filter blur-[128px] opacity-20 animate-blob animation-delay-2000">
    </div>
    <div
        class="absolute bottom-[-10%] left-[20%] w-[40%] h-[40%] bg-[#aa843f] rounded-full mix-blend-multiply filter blur-[128px] opacity-20 animate-blob animation-delay-4000">
    </div>

    <div class="w-full max-w-md relative z-10">
        <!-- Auth Card -->
        <!-- Auth Card -->
        <div
            class="bg-[rgba(0,0,0,0.3)] backdrop-blur-md border border-white/10 rounded-2xl shadow-2xl overflow-hidden p-8 sm:p-10 relative">

            <!-- Dynamic Header (Login/Register) -->
            <div class="mb-8 relative z-10">
                <h2 id="form-title" class="text-3xl font-bold text-white mb-2 tracking-tight">Sign In</h2>
                <p id="form-subtitle" class="text-pink-100 text-sm">Welcome back! Please enter your details.</p>
            </div>

            <!-- Login Tab Content -->
            <div id="login-tab-content" class="relative z-10">
                <form id="safezone-login-form" class="space-y-5">
                    <div>
                        <label for="pernum" class="block text-sm font-medium text-pink-100 mb-1">Account Number
                            (Pernum)</label>
                        <input type="text" id="pernum" name="pernum" required
                            class="w-full bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-200 focus:bg-white/20"
                            placeholder="Enter your account number">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-pink-100 mb-1">Password</label>
                        <input type="password" id="password" name="password" required
                            class="w-full bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-200 focus:bg-white/20"
                            placeholder="Enter your password">
                    </div>

                    <input type="hidden" name="key" value="Dmjfk78Ckjksj23KlmdMMszcX">

                    <button type="submit" id="login-btn"
                        class="w-full bg-[#cd217d] text-white font-bold py-4 px-4 rounded-xl shadow-lg hover:bg-[#a51a64] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2 group mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Sign In
                    </button>

                    <div class="text-center mt-8">
                        <p class="text-sm text-pink-100">
                            Don't have an account?
                            <button type="button" id="go-to-signup"
                                class="text-white font-bold hover:text-pink-200 transition-colors hover:underline">Sign
                                Up</button>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Signup Tab Content -->
            <div id="signup-tab-content" style="display: none;" class="relative z-10">
                <form id="signup-form" class="space-y-4">
                    <div>
                        <label for="invitation_code" class="block text-sm font-medium text-pink-100 mb-1">Invitation
                            Code <span class="text-pink-200 font-normal">(Optional)</span></label>
                        <input type="text" id="invitation_code" name="invitation_code"
                            class="w-full bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-200 focus:bg-white/20"
                            placeholder="Enter code if you have one">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-pink-100 mb-1">Email Address</label>
                        <div class="flex gap-2">
                            <input type="email" id="email" name="email" required
                                class="flex-1 bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-200 focus:bg-white/20"
                                placeholder="name@example.com">
                            <button type="button" id="send-otp-btn"
                                class="px-4 py-3.5 bg-white/10 hover:bg-white/20 border border-pink-300/30 rounded-xl text-pink-100 text-sm font-medium whitespace-nowrap">
                                Send Code
                            </button>
                        </div>
                        <div id="otp-section" class="mt-3" style="display: none;">
                            <label for="otp" class="block text-sm font-medium text-pink-100 mb-1">Verification Code</label>
                            <div class="flex gap-2">
                                <input type="text" id="otp" name="otp" maxlength="6" inputmode="numeric"
                                    class="flex-1 bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 text-center text-xl font-mono tracking-widest"
                                    placeholder="000000">
                                <button type="button" id="verify-otp-btn"
                                    class="px-4 py-3.5 bg-[#cd217d] hover:bg-[#a51a64] rounded-xl text-white text-sm font-medium whitespace-nowrap">
                                    Verify
                                </button>
                            </div>
                            <p id="otp-status" class="text-xs mt-1 text-pink-200"></p>
                        </div>
                        <p id="email-verified-badge" class="text-xs mt-2 text-emerald-400" style="display: none;">✓ Email verified</p>
                    </div>

                    <div>
                        <label for="signup_password"
                            class="block text-sm font-medium text-pink-100 mb-1">Password</label>
                        <input type="password" id="signup_password" name="password" required
                            class="w-full bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-200 focus:bg-white/20"
                            placeholder="Create a strong password">
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-pink-100 mb-1">Confirm
                            Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                            class="w-full bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-200 focus:bg-white/20"
                            placeholder="Confirm your password">
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox" id="age_confirm" name="age_confirm" required
                                class="mt-1 w-4 h-4 rounded border-pink-300 text-pink-600 focus:ring-white bg-white/20">
                            <span class="text-sm text-pink-100 group-hover:text-white transition-colors">I confirm that
                                I am 18 years of age or older</span>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox" id="terms_agree" name="terms_agree" required
                                class="mt-1 w-4 h-4 rounded border-pink-300 text-pink-600 focus:ring-white bg-white/20">
                            <span class="text-sm text-pink-100 group-hover:text-white transition-colors">
                                I agree to the <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php?url=terms" target="_blank" class="text-white font-bold hover:underline">Terms and Conditions</a> and <a href="#" class="text-white font-bold hover:underline">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <div id="signup-error"
                        class="hidden p-3 rounded-lg bg-red-900/40 border border-red-500/50 text-white text-sm text-center">
                    </div>

                    <button type="submit" id="signup-btn"
                        class="w-full bg-[#cd217d] text-white font-bold py-4 px-4 rounded-xl shadow-lg hover:bg-[#a51a64] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2 group mt-4">
                        Create Account
                    </button>

                    <div class="text-center mt-8">
                        <p class="text-sm text-pink-100">
                            Already have an account?
                            <button type="button" id="go-to-login"
                                class="text-white font-bold hover:text-pink-200 transition-colors hover:underline">Sign
                                In</button>
                        </p>
                    </div>
                </form>
            </div>

            <!-- PIN Form (Initially Hidden) -->
            <form id="safezone-pin-form" style="display: none;" class="space-y-6 relative z-10">
                <div class="bg-white/10 border border-white/20 rounded-xl p-6 text-center">
                    <p class="text-pink-100 text-sm mb-3 font-medium">Login successful! Please verify your identity.</p>
                    <p class="text-lg font-bold text-white mb-2" id="pin-positions">
                        Enter positions: <span id="positions-display"
                            class="text-white text-xl bg-white/20 px-2 py-1 rounded"></span>
                    </p>
                    <p class="text-xs text-pink-200">Example: If PIN is 123456 and positions are 2,4,6, enter 246</p>
                </div>

                <div class="space-y-3">
                    <label for="pin" class="block text-sm font-medium text-pink-100 text-center">Enter 3 Digits</label>
                    <input type="password" id="pin" name="pin" required maxlength="3" pattern="[0-9]{3}"
                        inputmode="numeric" autocomplete="off"
                        class="w-full bg-white/10 border border-white/20 rounded-xl py-4 text-center text-3xl font-mono tracking-[1em] text-white focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-300 placeholder-white/20"
                        placeholder="•••">
                </div>

                <input type="hidden" id="pin-uid" name="uid" value="">
                <input type="hidden" id="pin-key" name="key" value="">
                <input type="hidden" id="pin-pernum" name="pernum" value="">

                <div id="safezone-error"
                    class="hidden p-3 rounded-lg bg-red-900/40 border border-red-500/50 text-white text-sm text-center">
                </div>

                <div class="space-y-3">
                    <button type="submit" id="pin-btn"
                        class="w-full bg-[#cd217d] text-white font-bold py-4 px-4 rounded-xl shadow-lg hover:bg-[#a51a64] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300">
                        Verify PIN
                    </button>
                    <button type="button" id="back-to-login"
                        class="w-full bg-white/10 text-pink-100 font-semibold py-4 px-4 rounded-xl hover:bg-white/20 transition-all duration-300">
                        Back to Login
                    </button>
                </div>
            </form>

        </div>

        <!-- Footer Info -->
        <div class="px-8 pt-6 pb-2 text-center">
            <p class="text-xs text-gray-500">
                Protected by SafeZone &copy; <?= date('Y') ?>
            </p>
        </div>
    </div>
</main>

<script>
    (function () {
        const loginForm = document.getElementById('safezone-login-form');
        const pinForm = document.getElementById('safezone-pin-form');
        const signupForm = document.getElementById('signup-form');
        const errorDiv = document.getElementById('safezone-error');
        const signupErrorDiv = document.getElementById('signup-error');
        const backToLoginBtn = document.getElementById('back-to-login');
        const goToSignupBtn = document.getElementById('go-to-signup');
        const goToLoginBtn = document.getElementById('go-to-login');
        const rootUrl = '<?= URLROOT ?>';

        const loginTabContent = document.getElementById('login-tab-content');
        const signupTabContent = document.getElementById('signup-tab-content');
        const formTitle = document.getElementById('form-title');
        const formSubtitle = document.getElementById('form-subtitle');

        // Toggle functionality
        function showLoginSection() {
            loginTabContent.style.display = 'block';
            signupTabContent.style.display = 'none';
            pinForm.style.display = 'none';
            formTitle.textContent = 'Sign In';
            formTitle.style.display = 'block';
            formSubtitle.textContent = 'Welcome back! Please enter your details.';
            formSubtitle.style.display = 'block';
        }

        function showSignupSection() {
            loginTabContent.style.display = 'none';
            signupTabContent.style.display = 'block';
            pinForm.style.display = 'none';
            formTitle.textContent = 'Create Account';
            formTitle.style.display = 'block';
            formSubtitle.textContent = 'Start your journey with us today.';
            formSubtitle.style.display = 'block';
        }

        if (goToSignupBtn) {
            goToSignupBtn.addEventListener('click', showSignupSection);
        }

        if (goToLoginBtn) {
            goToLoginBtn.addEventListener('click', showLoginSection);
        }

        let pinPositions = [];
        let currentUid = '';
        let currentKey = '';
        let currentPernum = '';

        // Generate 3 random positions from 1-6 for PIN verification
        function generateRandomPositions() {
            const positions = [];
            while (positions.length < 3) {
                const pos = Math.floor(Math.random() * 6) + 1;
                if (!positions.includes(pos)) {
                    positions.push(pos);
                }
            }
            return positions.sort((a, b) => a - b);
        }

        // Show/hide forms
        function showLogin() {
            showLoginSection();
            if (errorDiv) errorDiv.style.display = 'none';
        }

        function showPin() {
            loginTabContent.style.display = 'none';
            signupTabContent.style.display = 'none';
            pinForm.style.display = 'block';
            formTitle.style.display = 'none';
            formSubtitle.style.display = 'none';
            if (errorDiv) errorDiv.style.display = 'none';
        }

        // Login form submission
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const loginBtn = document.getElementById('login-btn');
            const originalBtnContent = loginBtn.innerHTML;
            loginBtn.disabled = true;
            loginBtn.innerHTML = 'Authenticating...';
            if (errorDiv) errorDiv.style.display = 'none';

            const formData = new FormData(loginForm);
            const data = {
                pernum: formData.get('pernum'),
                password: formData.get('password'),
                key: formData.get('key')
            };

            try {
                const response = await fetch(rootUrl + '/safezone/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success && result.uid) {
                    // Generate random positions for PIN verification
                    pinPositions = generateRandomPositions();
                    currentUid = result.uid.toString();
                    currentKey = pinPositions.join('');

                    // Store pernum for later use
                    if (result.pernum) {
                        currentPernum = result.pernum;
                    } else if (result.username) {
                        currentPernum = result.username;
                    } else {
                        currentPernum = formData.get('pernum');
                    }

                    // Update PIN form
                    document.getElementById('positions-display').textContent = pinPositions.join(', ');
                    document.getElementById('pin').value = '';
                    document.getElementById('pin-uid').value = currentUid;
                    document.getElementById('pin-key').value = currentKey;
                    document.getElementById('pin-pernum').value = currentPernum;

                    showPin();
                } else {
                    if (errorDiv) {
                        errorDiv.textContent = result.message || 'Login failed';
                        errorDiv.classList.remove('hidden');
                        errorDiv.style.display = 'block';
                    } else {
                        alert(result.message || 'Login failed');
                    }
                }

                loginBtn.disabled = false;
                loginBtn.innerHTML = originalBtnContent;
            } catch (error) {
                console.error(error);
                if (errorDiv) {
                    errorDiv.textContent = 'Network error. Please try again.';
                    errorDiv.classList.remove('hidden');
                    errorDiv.style.display = 'block';
                }
                loginBtn.disabled = false;
                loginBtn.innerHTML = originalBtnContent;
            }
        });

        // PIN form submission
        pinForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const pinBtn = document.getElementById('pin-btn');
            const originalBtnContent = pinBtn.innerHTML;
            pinBtn.disabled = true;
            pinBtn.textContent = 'Verifying...';
            if (errorDiv) errorDiv.style.display = 'none';

            const pin = document.getElementById('pin').value;

            if (pin.length !== 3) {
                errorDiv.textContent = 'PIN must be exactly 3 digits';
                errorDiv.classList.remove('hidden');
                errorDiv.style.display = 'block';
                pinBtn.disabled = false;
                pinBtn.innerHTML = originalBtnContent;
                return;
            }

            const data = {
                uid: currentUid,
                key: currentKey,
                pin: pin,
                pernum: currentPernum
            };

            try {
                const response = await fetch(rootUrl + '/safezone/verifyPin', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    // PIN verified successfully - redirect
                    const redirect = result.redirect || rootUrl + '/index.php';
                    window.location.href = redirect;
                } else {
                    errorDiv.textContent = result.message || 'PIN verification failed';
                    errorDiv.classList.remove('hidden');
                    errorDiv.style.display = 'block';
                    pinBtn.disabled = false;
                    pinBtn.innerHTML = originalBtnContent;
                }
            } catch (error) {
                errorDiv.textContent = 'Network error. Please try again.';
                errorDiv.classList.remove('hidden');
                errorDiv.style.display = 'block';
                pinBtn.disabled = false;
                pinBtn.innerHTML = originalBtnContent;
            }
        });

        // Back button
        backToLoginBtn.addEventListener('click', function () {
            showLogin();
            document.getElementById('pin').value = '';
        });

        // Limit PIN input to 3 digits
        document.getElementById('pin').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 3);
        });

        // OTP verification for signup
        let signupEmailVerified = false;
        const sendOtpBtn = document.getElementById('send-otp-btn');
        const otpSection = document.getElementById('otp-section');
        const otpInput = document.getElementById('otp');
        const verifyOtpBtn = document.getElementById('verify-otp-btn');
        const otpStatus = document.getElementById('otp-status');
        const emailVerifiedBadge = document.getElementById('email-verified-badge');
        const signupEmailInput = document.getElementById('email');

        if (sendOtpBtn) {
            sendOtpBtn.addEventListener('click', async function () {
                const email = signupEmailInput.value.trim();
                if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    otpStatus.textContent = 'Enter a valid email first.';
                    otpSection.style.display = 'block';
                    return;
                }
                sendOtpBtn.disabled = true;
                sendOtpBtn.textContent = 'Sending...';
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
                    } else {
                        otpStatus.textContent = data.message || 'Failed to send.';
                    }
                } catch (e) {
                    otpStatus.textContent = 'Network error.';
                    console.error('Send OTP error:', e);
                }
                sendOtpBtn.disabled = false;
                sendOtpBtn.textContent = 'Send Code';
            });
        }
        if (verifyOtpBtn) {
            verifyOtpBtn.addEventListener('click', async function () {
                const email = signupEmailInput.value.trim();
                const otp = otpInput.value.trim();
                if (!email || !otp || otp.length !== 6) {
                    otpStatus.textContent = 'Enter the 6-digit code.';
                    return;
                }
                verifyOtpBtn.disabled = true;
                verifyOtpBtn.textContent = 'Verifying...';
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
                        signupEmailVerified = true;
                        emailVerifiedBadge.style.display = 'block';
                        signupEmailInput.disabled = true;
                        sendOtpBtn.disabled = true;
                        otpSection.style.display = 'none';
                        otpStatus.textContent = '';
                    } else {
                        otpStatus.textContent = data.message || 'Invalid code.';
                    }
                } catch (e) {
                    otpStatus.textContent = 'Network error.';
                }
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.textContent = 'Verify';
            });
        }
        if (otpInput) {
            otpInput.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 6);
            });
        }

        // Signup form submission
        signupForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (!signupEmailVerified) {
                signupErrorDiv.textContent = 'Please verify your email first. Click Send Code and enter the code from your email.';
                signupErrorDiv.classList.remove('hidden');
                signupErrorDiv.style.display = 'block';
                return;
            }

            // Reset error
            signupErrorDiv.style.display = 'none';
            signupErrorDiv.classList.add('hidden');
            signupErrorDiv.textContent = '';

            const signupBtn = document.getElementById('signup-btn');
            const originalBtnContent = signupBtn.innerHTML;
            signupBtn.disabled = true;
            signupBtn.innerHTML = 'Creating Account...';

            // Get form values
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('signup_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const invitationCode = document.getElementById('invitation_code').value.trim();
            const ageConfirm = document.getElementById('age_confirm').checked;
            const termsAgree = document.getElementById('terms_agree').checked;

            // Validation
            if (password !== confirmPassword) {
                signupErrorDiv.textContent = 'Passwords do not match';
                signupErrorDiv.classList.remove('hidden');
                signupErrorDiv.style.display = 'block';
                signupBtn.disabled = false;
                signupBtn.innerHTML = originalBtnContent;
                return;
            }

            if (!ageConfirm) {
                signupErrorDiv.textContent = 'You must confirm that you are 18 years or older';
                signupErrorDiv.classList.remove('hidden');
                signupErrorDiv.style.display = 'block';
                signupBtn.disabled = false;
                signupBtn.innerHTML = originalBtnContent;
                return;
            }

            if (!termsAgree) {
                signupErrorDiv.textContent = 'You must agree to the Terms of Service and Privacy Policy';
                signupErrorDiv.classList.remove('hidden');
                signupErrorDiv.style.display = 'block';
                signupBtn.disabled = false;
                signupBtn.innerHTML = originalBtnContent;
                return;
            }

            try {
                // Prepare signup data
                const signupData = {
                    email: email,
                    password: password
                };

                // Add invitation code if provided
                if (invitationCode) {
                    signupData.invited_by = invitationCode;
                }

                // Call signup API
                const response = await fetch(rootUrl + '/safezone/signup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(signupData)
                });

                const result = await response.json();

                if (result.success && result.pernum) {
                    // Redirect to PIN setup page
                    window.location.href = rootUrl + '/safezone/setPinForm?pernum=' + encodeURIComponent(result.pernum);
                } else if (result.success && !result.pernum) {
                    signupErrorDiv.textContent = 'Account created but missing registration number. Please try again or contact support.';
                    signupErrorDiv.classList.remove('hidden');
                    signupErrorDiv.style.display = 'block';
                    signupBtn.disabled = false;
                    signupBtn.innerHTML = originalBtnContent;
                } else {
                    signupErrorDiv.textContent = result.message || 'Registration failed. Please try again.';
                    signupErrorDiv.classList.remove('hidden');
                    signupErrorDiv.style.display = 'block';
                    signupBtn.disabled = false;
                    signupBtn.innerHTML = originalBtnContent;
                }
            } catch (error) {
                console.error('Signup error:', error);
                signupErrorDiv.textContent = 'An error occurred. Please try again.';
                signupErrorDiv.classList.remove('hidden');
                signupErrorDiv.style.display = 'block';
                signupBtn.disabled = false;
                signupBtn.innerHTML = originalBtnContent;
            }
        });
    })();
</script>