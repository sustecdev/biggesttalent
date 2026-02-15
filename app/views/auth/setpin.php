<main class="flex-grow flex justify-center p-4 sm:p-6 lg:p-8 py-12 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-[#cd217d] rounded-full mix-blend-multiply filter blur-[128px] opacity-20 animate-blob"></div>
    <div class="absolute top-[-10%] right-[-10%] w-[40%] h-[40%] bg-[#9a288d] rounded-full mix-blend-multiply filter blur-[128px] opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-[-10%] left-[20%] w-[40%] h-[40%] bg-[#aa843f] rounded-full mix-blend-multiply filter blur-[128px] opacity-20 animate-blob animation-delay-4000"></div>

    <div class="w-full max-w-md relative z-10">
        <div class="bg-[rgba(0,0,0,0.3)] backdrop-blur-md border border-white/10 rounded-2xl shadow-2xl overflow-hidden p-8 sm:p-10 relative">
            <!-- Success State (Hidden by default) -->
            <div id="success-state" class="hidden">
                <div class="mb-8 text-center">
                    <div class="mb-6">
                        <svg class="w-20 h-20 mx-auto text-[#cd217d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-[#cd217d] mb-2">Registration Complete!</h2>
                    <p class="text-pink-100 text-sm">Your Master PIN has been set successfully. You can now log in to your account.</p>
                </div>
                <a href="<?= URLROOT ?>/safezone"
                    class="w-full bg-[#cd217d] hover:bg-[#a51a64] text-white font-bold py-4 px-4 rounded-xl shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2 mt-4 no-underline">
                    Go to Login
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M7.5 15L12.5 10L7.5 5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>

            <!-- Form State -->
            <div id="form-state">
                <div class="mb-8 relative z-10">
                    <h2 class="text-3xl font-bold text-white mb-2 tracking-tight">Set Your Master PIN</h2>
                    <p class="text-pink-100 text-sm">Step 2 of 2: Create a 6-digit PIN for secure access</p>
                </div>

                <div class="bg-white/10 border border-white/20 rounded-xl p-4 mb-6 text-center">
                    <p class="text-pink-100 text-sm mb-1 font-medium">Your Account Number (Pernum)</p>
                    <p class="text-xl font-bold text-white font-mono"><?= htmlspecialchars($data['pernum'] ?? '') ?></p>
                </div>

                <form id="setpin-form" class="space-y-4 relative z-10">
                    <input type="hidden" id="pernum" name="pernum" value="<?= htmlspecialchars($data['pernum'] ?? '') ?>">

                    <div>
                        <label for="pin" class="block text-sm font-medium text-pink-100 mb-1">Master PIN (6 digits)</label>
                        <input type="password" id="pin" name="pin" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                            class="w-full bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-200 focus:bg-white/20 text-center text-xl font-mono tracking-[0.5em]"
                            placeholder="••••••">
                        <p class="text-xs text-pink-200 mt-1">Your Master PIN must be exactly 6 digits</p>
                    </div>

                    <div>
                        <label for="confirm_pin" class="block text-sm font-medium text-pink-100 mb-1">Confirm Master PIN</label>
                        <input type="password" id="confirm_pin" name="confirm_pin" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                            class="w-full bg-white/10 border border-pink-300/30 rounded-xl py-3.5 px-4 text-white placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent transition-all duration-200 focus:bg-white/20 text-center text-xl font-mono tracking-[0.5em]"
                            placeholder="••••••">
                    </div>

                    <div id="setpin-error" class="hidden p-3 rounded-lg bg-red-900/40 border border-red-500/50 text-white text-sm text-center"></div>

                    <button type="submit" id="setpin-btn"
                        class="w-full bg-[#cd217d] text-white font-bold py-4 px-4 rounded-xl shadow-lg hover:bg-[#a51a64] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2 group mt-4">
                        Complete Registration
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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

    const setpinForm = document.getElementById('setpin-form');
    const errorDiv = document.getElementById('setpin-error');
    const setpinBtn = document.getElementById('setpin-btn');
    const pinInput = document.getElementById('pin');
    const confirmPinInput = document.getElementById('confirm_pin');
    const formState = document.getElementById('form-state');
    const successState = document.getElementById('success-state');
    const rootUrl = '<?= URLROOT ?>';
    const originalBtnHtml = setpinBtn.innerHTML;

    [pinInput, confirmPinInput].forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    setpinForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
        setpinBtn.disabled = true;
        setpinBtn.textContent = 'Setting PIN...';

        const pernum = document.getElementById('pernum').value.trim();
        const pin = pinInput.value.trim();
        const confirmPin = confirmPinInput.value.trim();

        if (pin.length !== 6) {
            errorDiv.textContent = 'Master PIN must be exactly 6 digits';
            errorDiv.classList.remove('hidden');
            setpinBtn.disabled = false;
            setpinBtn.innerHTML = originalBtnHtml;
            return;
        }
        if (pin !== confirmPin) {
            errorDiv.textContent = 'PINs do not match';
            errorDiv.classList.remove('hidden');
            setpinBtn.disabled = false;
            setpinBtn.innerHTML = originalBtnHtml;
            return;
        }

        try {
            const response = await fetch(rootUrl + '/safezone/setPin', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ pernum, pin })
            });
            const result = await response.json();

            if (result.success) {
                formState.classList.add('hidden');
                successState.classList.remove('hidden');
            } else {
                errorDiv.textContent = result.message || 'PIN setup failed. Please try again.';
                errorDiv.classList.remove('hidden');
                setpinBtn.disabled = false;
                setpinBtn.innerHTML = originalBtnHtml;
            }
        } catch (error) {
            errorDiv.textContent = 'An error occurred. Please try again.';
            errorDiv.classList.remove('hidden');
            setpinBtn.disabled = false;
            setpinBtn.innerHTML = originalBtnHtml;
        }
    });
});
</script>
