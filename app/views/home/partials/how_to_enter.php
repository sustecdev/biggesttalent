<?php
$activeSeason = function_exists('getActiveSeasonSimple') ? getActiveSeasonSimple() : null;
$nomOpen = $activeSeason ? ($activeSeason['is_nominations_open'] ?? 1) : 1;
?>
<!-- 3 Ways to Enter Section -->
<section id="how-to-enter" class="py-24 md:py-36 bg-[#0a0a0a] relative overflow-hidden">
    <!-- Ambient glow -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[600px] bg-[#cd217d]/5 blur-[120px] rounded-full"></div>
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-[#cd217d]/40 to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-[#cd217d]/20 to-transparent"></div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Header -->
        <div class="text-center mb-16 md:mb-24">
            <p class="text-[#cd217d] text-sm font-bold uppercase tracking-[0.25em] mb-4">Your path in</p>
            <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black text-white mb-6 tracking-tight uppercase">
                <span class="text-gradient-gold">3 Ways</span> to Enter
            </h2>
            <p class="text-lg md:text-xl text-gray-400 max-w-2xl mx-auto font-light leading-relaxed">
                There's a path for every talent. Choose yours and let Africa discover you.
            </p>
        </div>

        <!-- 3 Ways: Featured Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-4 mb-20 md:mb-24">
            <?php $urlRoot = defined('URLROOT') ? URLROOT : ''; ?>
            <!-- 1. Get Scouted -->
            <div class="relative">
                <div class="group h-full min-h-[280px] md:min-h-[320px] p-5 md:p-6 rounded-2xl border border-white/10 hover:border-[#cd217d]/50 transition-all duration-500 overflow-hidden flex flex-col" style="background-image: url('<?= $urlRoot ?>/images/Stage%201%20(1).png'); background-size: cover; background-position: center top; background-repeat: no-repeat;">
                    <div class="absolute inset-0 bg-black/60 rounded-2xl z-0" aria-hidden="true"></div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-[#cd217d]/20 flex items-center justify-center mb-4 group-hover:bg-[#cd217d]/40 transition-colors duration-500">
                            <span class="text-[#cd217d] font-black text-xl">1</span>
                        </div>
                        <h3 class="text-lg font-black text-white mb-3 uppercase tracking-wide">Get Scouted</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Our official talent scouts are searching across Africa for the next superstar.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 2. Get Nominated by Fans -->
            <div class="relative">
                <div class="group h-full min-h-[280px] md:min-h-[320px] p-5 md:p-6 rounded-2xl border border-white/10 hover:border-[#cd217d]/50 transition-all duration-500 overflow-hidden flex flex-col" style="background-image: url('<?= $urlRoot ?>/images/satges%202.png'); background-size: cover; background-position: center top; background-repeat: no-repeat;">
                    <div class="absolute inset-0 bg-black/60 rounded-2xl z-0" aria-hidden="true"></div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-[#cd217d]/20 flex items-center justify-center mb-4 group-hover:bg-[#cd217d]/40 transition-colors duration-500">
                            <span class="text-[#cd217d] font-black text-xl">2</span>
                        </div>
                        <h3 class="text-lg font-black text-white mb-3 uppercase tracking-wide">Get Nominated by Fans</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Your supporters can nominate you to represent your country.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 3. Nominate Yourself (Featured - has CTA) -->
            <div class="relative">
                <div class="group h-full min-h-[280px] md:min-h-[320px] relative p-5 md:p-6 rounded-2xl border-2 border-[#cd217d]/40 hover:border-[#cd217d] transition-all duration-500 overflow-hidden flex flex-col lg:-translate-y-1 lg:shadow-xl lg:shadow-[#cd217d]/10" style="background-image: url('<?= $urlRoot ?>/images/satges%203.png'); background-size: cover; background-position: center top; background-repeat: no-repeat;">
                    <div class="absolute inset-0 bg-black/60 rounded-2xl z-0" aria-hidden="true"></div>
                    <div class="relative z-10">
                        <div class="w-12 h-12 rounded-xl bg-[#cd217d]/30 flex items-center justify-center mb-4 group-hover:bg-[#cd217d] group-hover:scale-110 transition-all duration-500">
                            <span class="text-[#cd217d] group-hover:text-white font-black text-xl">3</span>
                        </div>
                        <h3 class="text-lg font-black text-white mb-3 uppercase tracking-wide">Nominate Yourself</h3>
                        <p class="text-gray-400 text-sm leading-relaxed mb-5">
                            Don't wait. Sign up, upload your performance and enter directly.
                        </p>
                        <?php if ($nomOpen): ?>
                            <a href="<?= $urlRoot ? $urlRoot . '/index.php?url=nominate' : 'index.php?url=nominate' ?>" class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-lg bg-[#cd217d] hover:bg-[#a51a64] text-white font-bold text-sm transition-all hover:scale-[1.02] nominate-trigger">
                                Enter Now
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        <?php else: ?>
                            <span class="inline-flex items-center justify-center w-full py-3 px-4 rounded-lg bg-white/5 text-gray-500 font-medium text-sm">Coming Soon</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- How to Enter: Step Flow -->
        <div class="relative">
            <div class="text-center mb-12">
                <h3 class="text-2xl md:text-3xl font-black text-white uppercase tracking-tight">How to Enter</h3>
                <p class="text-gray-500 text-sm">Three simple steps to get started</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-4">
                <!-- Step 1: Record -->
                <div class="relative flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#cd217d]/30 to-[#9a288d]/20 border border-[#cd217d]/30 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-[#cd217d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-[#cd217d]/20 text-[#cd217d] font-bold text-xs flex items-center justify-center">1</span>
                        <h4 class="font-bold text-white uppercase tracking-wider text-sm">Record</h4>
                    </div>
                    <p class="text-gray-500 text-xs leading-relaxed">Record a short performance clip</p>
                </div>

                <!-- Step 2: Submit -->
                <div class="relative flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#cd217d]/30 to-[#9a288d]/20 border border-[#cd217d]/30 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-[#cd217d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-[#cd217d]/20 text-[#cd217d] font-bold text-xs flex items-center justify-center">2</span>
                        <h4 class="font-bold text-white uppercase tracking-wider text-sm">Submit</h4>
                    </div>
                    <p class="text-gray-500 text-xs leading-relaxed">Submit online</p>
                </div>

                <!-- Step 3: Vote -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#cd217d]/30 to-[#9a288d]/20 border border-[#cd217d]/30 flex items-center justify-center mb-3">
                        <svg class="w-7 h-7 text-[#cd217d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a7.5 7.5 0 0115 0v1m-15 0a1.5 1.5 0 013 0m0 0V11m0-5.5a1.5 1.5 0 013 0m3 0v1m0-5.5v-1a7.5 7.5 0 0115 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0"/>
                        </svg>
                    </div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-[#cd217d]/20 text-[#cd217d] font-bold text-xs flex items-center justify-center">3</span>
                        <h4 class="font-bold text-white uppercase tracking-wider text-sm">Vote</h4>
                    </div>
                    <p class="text-gray-500 text-xs leading-relaxed">Let Africa vote</p>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center mt-10">
                <?php if ($nomOpen): ?>
                    <a href="index.php?url=nominate" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gradient-to-r from-[#cd217d] to-[#9a288d] text-white font-bold text-base hover:opacity-90 hover:scale-[1.02] transition-all shadow-lg shadow-[#cd217d]/20 nominate-trigger">
                        Start Your Entry
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                <?php else: ?>
                    <a href="javascript:void(0)" onclick="showFeatureClosedModal('Nominations')" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-white/10 border border-white/20 text-white font-bold text-base hover:bg-white/15 transition-all nominate-trigger service-locked">
                        Start Your Entry
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
