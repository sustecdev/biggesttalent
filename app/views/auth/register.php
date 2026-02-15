<?php
$title = 'Register - Get SafeZonePass';
?>

<div class="min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8 relative overflow-hidden login-video-container">
    <!-- Background (Biggest Talent doesn't ship loginVideo.webm, so use an existing image) -->
    <div class="absolute inset-0 z-0 bg-cover bg-center"
        style="background-image: url('<?= URLROOT ?>/images/bg2.png');">
    </div>

    <!-- Overlay for better readability -->
    <div class="absolute inset-0 bg-gradient-to-br from-pink-900/60 to-purple-900/60 z-0"></div>

    <!-- Back Arrow Button -->
    <a href="<?= URLROOT ?>/auth/login"
        class="absolute top-4 left-4 z-20 bg-white/90 hover:bg-white text-gray-800 rounded-full p-3 shadow-lg transition-all hover:scale-110"
        title="Back to Login">
        <i class="fas fa-arrow-left text-xl"></i>
    </a>

    <div class="max-w-md w-full space-y-8 relative z-10 -mt-8">
        <div class="text-center">
            <h2 class="text-4xl font-extrabold text-white drop-shadow-lg">Join Biggest Talent</h2>
            <p class="mt-2 text-sm text-white/90 drop-shadow">Get your free SafeZonePass</p>
        </div>

        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Create Your Account</h3>
                <p class="text-xs text-gray-500">Step 1 of 2: Basic Information</p>
            </div>

            <?php if (isset($error) && $error): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">
                        <?= htmlspecialchars($error) ?>
                    </span>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= URLROOT ?>/auth/process_register" id="registerForm">
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-pink-500 focus:border-pink-500 focus:z-10 sm:text-sm"
                            placeholder="your@email.com">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input type="button" onclick="generatePassword()" value="Generate Secure Password"
                            class="mb-2 text-xs text-pink-600 hover:text-pink-800 cursor-pointer text-right w-full block">
                        <input type="password" id="password" name="password" required minlength="6"
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-pink-500 focus:border-pink-500 focus:z-10 sm:text-sm"
                            placeholder="Min 6 characters">
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-pink-500 focus:border-pink-500 focus:z-10 sm:text-sm"
                            placeholder="Data matches above">
                    </div>

                    <div>
                        <label for="invited_by" class="block text-sm font-medium text-gray-700 mb-2">
                            Invited By (Pernum)
                        </label>
                        <input type="text" id="invited_by" name="invited_by"
                            value="<?= isset($_COOKIE['ref_pernum']) ? htmlspecialchars($_COOKIE['ref_pernum']) : '' ?>"
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-pink-500 focus:border-pink-500 focus:z-10 sm:text-sm"
                            placeholder="Inviter Pernum (Optional)">
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        NEXT: SET MASTER PIN
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>

                    <div class="text-center mt-4">
                        <a href="<?= URLROOT ?>/auth/login"
                            class="text-sm font-medium text-pink-600 hover:text-pink-500">
                            Already have an account? Login
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .login-video-container {
        min-height: 100vh;
    }
</style>

<script>
    function generatePassword() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let pass = "";
        for (let i = 0; i < 12; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        const passwordField = document.getElementById("password");
        passwordField.type = "text";
        passwordField.value = pass;
        setTimeout(() => { passwordField.type = "password"; }, 3000); // Hide after 3s
    }

    // Form validation
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }
    });
</script>
