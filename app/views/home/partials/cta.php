<!-- Final CTA Section -->
<section class="py-20 md:py-32 relative overflow-hidden bg-[#0a0a0a]">
    <!-- Gradient Background Glow -->
    <div class="absolute inset-0 bg-gradient-to-t from-[#cd217d]/10 to-transparent pointer-events-none"></div>
    <div
        class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-[#aa843f] opacity-10 blur-[120px] rounded-full pointer-events-none">
    </div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
        <h2 class="text-4xl sm:text-7xl font-black text-white mb-8 tracking-tighter">
            READY TO <br>
            <span class="text-gradient-gold">BECOME A STAR?</span>
        </h2>

        <p class="text-xl text-gray-400 mb-12 max-w-2xl mx-auto font-light leading-relaxed">
            Join thousands of talented artists from across Africa competing for glory and a life-changing prize package.
        </p>

        <?php
        // Feature Flags Check
        $activeSeasonCta = function_exists('getActiveSeasonSimple') ? getActiveSeasonSimple() : null;
        $nomOpenCta = $activeSeasonCta ? ($activeSeasonCta['is_nominations_open'] ?? 1) : 1;
        ?>
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
            <?php if ($nomOpenCta): ?>
                <a href="index.php?url=nominate"
                    class="w-full sm:w-auto px-12 py-5 bg-[#cd217d] text-white rounded-full font-bold text-lg tracking-wide hover:bg-[#a51a64] transition-all transform hover:scale-105 shadow-2xl hover:shadow-[#cd217d]/50 nominate-trigger">Nominate
                    Yourself</a>
            <?php else: ?>
                <a href="javascript:void(0)" onclick="showFeatureClosedModal('Nominations')"
                    class="w-full sm:w-auto px-12 py-5 bg-[#cd217d] text-white rounded-full font-bold text-lg tracking-wide hover:bg-[#a51a64] transition-all transform hover:scale-105 shadow-2xl hover:shadow-[#cd217d]/50 nominate-trigger service-locked">Nominate
                    Yourself</a>
            <?php endif; ?>
            <a href="#phases"
                class="w-full sm:w-auto px-12 py-5 bg-white/5 border border-white/10 text-white rounded-full font-bold text-lg hover:bg-white/10 transition-all backdrop-blur-md">Learn
                More</a>
        </div>
    </div>
</section>