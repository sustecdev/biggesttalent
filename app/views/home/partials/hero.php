<!-- Hero Section -->
<section class="hero-section min-h-screen flex items-center justify-center relative pt-20 pb-20 md:pt-28 md:pb-24 px-4 sm:px-6 lg:px-8 bg-[#0a0a0a] overflow-hidden">
    <!-- Animated Blob Backgrounds -->
    <div class="absolute top-[-10%] left-[-5%] w-[50%] h-[50%] min-h-[400px] bg-[#cd217d] rounded-full mix-blend-multiply filter blur-[100px] opacity-25 animate-blob pointer-events-none"></div>
    <div class="absolute top-[-5%] right-[-10%] w-[45%] h-[45%] min-h-[350px] bg-[#9a288d] rounded-full mix-blend-multiply filter blur-[100px] opacity-20 animate-blob animation-delay-2000 pointer-events-none"></div>
    <div class="absolute bottom-[-5%] left-[10%] w-[35%] h-[35%] min-h-[280px] bg-[#aa843f] rounded-full mix-blend-multiply filter blur-[90px] opacity-15 animate-blob animation-delay-4000 pointer-events-none"></div>

    <!-- Content Container -->
    <div class="max-w-5xl mx-auto w-full relative z-10">
        <!-- Glass Card -->
        <div class="hero-content-card relative rounded-3xl p-8 md:p-12 lg:p-16 text-center bg-[rgba(10,10,10,0.6)] backdrop-blur-xl border border-white/10 shadow-2xl">
            <!-- Tagline -->
            <p class="text-sm sm:text-base uppercase tracking-[0.3em] text-[#d5c39e]/90 mb-6 font-medium">Africa's Premier Talent Competition</p>

            <!-- Logo -->
            <div class="mb-8 md:mb-10 flex justify-center">
                <img src="<?= defined('URLROOT') ? URLROOT : '' ?>/images/Official logo.png" alt="Biggest Talent Africa"
                    class="w-auto h-auto max-w-[85%] sm:max-w-[500px] md:max-w-[600px] object-contain drop-shadow-2xl hover:scale-[1.03] transition-transform duration-500">
            </div>

            <!-- Subtitle -->
            <p class="text-base sm:text-xl md:text-2xl text-gray-300 max-w-2xl mx-auto mb-8 leading-relaxed font-light">
                Showcase your extraordinary talent on Africa's biggest stage. Compete for fame, fortune, and national recognition.
            </p>

            <!-- Season Badge -->
            <?php if (!empty($data['activeSeason'])): ?>
            <div class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#cd217d]/15 border border-[#cd217d]/40 text-[#cd217d] font-bold text-sm uppercase tracking-wider mb-8">
                <?= htmlspecialchars($data['activeSeason']['title'] ?? 'Active Season') ?> (<?= date('Y', strtotime($data['activeSeason']['start_date'] ?? 'now')) ?>)
            </div>
            <?php endif; ?>

            <!-- Divider -->
            <div class="w-24 h-0.5 bg-gradient-to-r from-transparent via-[#cd217d]/60 to-transparent mx-auto mb-8"></div>

            <!-- CTAs -->
            <?php
            $activeSeasonHero = function_exists('getActiveSeasonSimple') ? getActiveSeasonSimple() : null;
            $nomOpenHero = $activeSeasonHero ? ($activeSeasonHero['is_nominations_open'] ?? 1) : 1;
            $votingOpenHero = $activeSeasonHero ? ($activeSeasonHero['is_voting_open'] ?? 0) : 0;
            ?>
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-5 justify-center items-center flex-wrap">
                <?php if ($nomOpenHero): ?>
                    <a href="index.php?url=nominate" class="hero-cta-primary w-full sm:w-auto nominate-trigger">
                        Nominate Now
                    </a>
                <?php else: ?>
                    <a href="javascript:void(0)" onclick="showFeatureClosedModal('Nominations')" class="hero-cta-primary w-full sm:w-auto nominate-trigger service-locked">
                        Nominate Now
                    </a>
                <?php endif; ?>
                <?php if ($votingOpenHero): ?>
                    <a href="index.php?url=vote" class="hero-cta-secondary w-full sm:w-auto">
                        Vote Now
                    </a>
                <?php endif; ?>
                <a href="#how-to-enter" class="hero-cta-outline w-full sm:w-auto">
                    How to Enter
                </a>
            </div>
        </div>

        <!-- Hero Video -->
        <div class="relative w-full mt-10 rounded-2xl overflow-hidden shadow-2xl border border-white/10 group">
            <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-transparent to-transparent z-10 opacity-70 pointer-events-none"></div>
            <video autoplay muted loop playsinline id="heroVideo"
                class="w-full h-auto object-cover transform group-hover:scale-[1.02] transition-transform duration-1000">
                <source src="<?= defined('URLROOT') ? URLROOT : '' ?>/uploads/videos/herovid.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <button id="volumeBtn" onclick="toggleVolume()" class="absolute bottom-4 right-4 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full backdrop-blur-sm transition-all z-20">
                <svg id="muteIcon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 5L6 9H2v6h4l5 4V5z"></path>
                    <line x1="23" y1="9" x2="17" y2="15"></line>
                    <line x1="17" y1="9" x2="23" y2="15"></line>
                </svg>
                <svg id="unmuteIcon" class="hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                    <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                </svg>
            </button>
        </div>

        <!-- Scroll Indicator -->
        <a href="#how-to-enter" class="hero-scroll-indicator flex flex-col items-center gap-2 mt-8 text-gray-500 hover:text-[#cd217d] transition-colors">
            <span class="text-xs uppercase tracking-widest">Discover</span>
            <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </a>
    </div>

    <div style="display: none;"><!-- STARTING IN - hidden per request -->
        <p class="text-xl sm:text-2xl font-bold text-[#cd217d] tracking-widest mb-4 uppercase">STARTING IN</p>

        <!-- Countdown Timer -->
        <div class="mb-12 flex flex-wrap justify-center gap-4 sm:gap-6" id="countdown-timer">
            <!-- Days -->
            <div
                class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-3 sm:p-4 min-w-[80px] sm:min-w-[100px] flex flex-col items-center shadow-[0_4px_20px_rgba(0,0,0,0.2)]">
                <span class="text-3xl sm:text-4xl font-bold text-white mb-1" id="days">00</span>
                <span class="text-xs sm:text-sm text-gray-400 uppercase tracking-wider">Days</span>
            </div>
            <!-- Hours -->
            <div
                class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-3 sm:p-4 min-w-[80px] sm:min-w-[100px] flex flex-col items-center shadow-[0_4px_20px_rgba(0,0,0,0.2)]">
                <span class="text-3xl sm:text-4xl font-bold text-white mb-1" id="hours">00</span>
                <span class="text-xs sm:text-sm text-gray-400 uppercase tracking-wider">Hours</span>
            </div>
            <!-- Minutes -->
            <div
                class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-3 sm:p-4 min-w-[80px] sm:min-w-[100px] flex flex-col items-center shadow-[0_4px_20px_rgba(0,0,0,0.2)]">
                <span class="text-3xl sm:text-4xl font-bold text-white mb-1" id="minutes">00</span>
                <span class="text-xs sm:text-sm text-gray-400 uppercase tracking-wider">Minutes</span>
            </div>
            <!-- Seconds -->
            <div
                class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-3 sm:p-4 min-w-[80px] sm:min-w-[100px] flex flex-col items-center shadow-[0_4px_20px_rgba(0,0,0,0.2)]">
                <span class="text-3xl sm:text-4xl font-bold text-[#cd217d] mb-1" id="seconds">00</span>
                <span class="text-xs sm:text-sm text-gray-400 uppercase tracking-wider">Seconds</span>
            </div>
        </div>

        <script>
            (function () {
                const countDownDate = new Date("February 15, 2026 00:00:00").getTime();
                const timerElement = document.getElementById("countdown-timer");

                function updateTimer() {
                    const now = new Date().getTime();
                    const distance = countDownDate - now;

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById("days").innerText = days < 10 ? "0" + days : days;
                    document.getElementById("hours").innerText = hours < 10 ? "0" + hours : hours;
                    document.getElementById("minutes").innerText = minutes < 10 ? "0" + minutes : minutes;
                    document.getElementById("seconds").innerText = seconds < 10 ? "0" + seconds : seconds;

                    if (distance < 0) {
                        if (timerElement) timerElement.innerHTML = "<div class='text-2xl font-bold text-[#cd217d]'>SEASON STARTED!</div>";
                        return true;
                    }
                    return false;
                }

                // Run immediately to avoid 00 flash
                if (!updateTimer()) {
                    const x = setInterval(function () {
                        if (updateTimer()) {
                            clearInterval(x);
                        }
                    }, 1000);
                }
            })();
        </script>
        </div><!-- /STARTING IN -->
</section>

<script>
function toggleVolume() {
    const video = document.getElementById('heroVideo');
    const muteIcon = document.getElementById('muteIcon');
    const unmuteIcon = document.getElementById('unmuteIcon');
    if (video && muteIcon && unmuteIcon) {
        if (video.muted) {
            video.muted = false;
            muteIcon.classList.add('hidden');
            unmuteIcon.classList.remove('hidden');
        } else {
            video.muted = true;
            muteIcon.classList.remove('hidden');
            unmuteIcon.classList.add('hidden');
        }
    }
}
</script>