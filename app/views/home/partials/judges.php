<!-- Judges Section -->
<section id="judges" class="py-4 md:py-8 bg-[#0a0a0a] relative overflow-hidden text-center">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-4xl mx-auto mb-20">
            <h2 class="text-4xl sm:text-6xl md:text-7xl font-black text-white mb-8 tracking-tighter uppercase">
                Meet the Grand Finale <span class="text-gradient-gold">Judges</span>
            </h2>
            <p class="text-xl text-gray-400 leading-relaxed font-light">
                Africa's leading experts carefully selected to evaluate talent from across the continent
            </p>
        </div>

        <?php if (!empty($data['judges'])): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($data['judges'] as $judge): ?>
                    <div class="judge-card group relative">
                        <div
                            class="relative mb-8 overflow-hidden rounded-3xl aspect-[3/4] border-0 group-hover:shadow-[0_0_40px_rgba(205,33,125,0.3)] transition-all duration-500">
                            <img src="<?php
                            $imgSrc = 'images/placeholder-user.jpg';
                            if (!empty($judge['image'])) {
                                // Check if image path already includes 'images/' or 'uploads/'
                                if (file_exists(APPROOT . '/../public/' . $judge['image'])) {
                                    $imgSrc = $judge['image'];
                                } elseif (file_exists(APPROOT . '/../public/images/' . $judge['image'])) {
                                    $imgSrc = 'images/' . $judge['image'];
                                }
                            }
                            echo URLROOT . '/public/' . $imgSrc;
                            ?>" alt="<?php echo htmlspecialchars($judge['name']); ?>"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] via-transparent to-transparent opacity-80 group-hover:opacity-60 transition-opacity">
                            </div>

                            <!-- Floating Info -->
                            <div
                                class="absolute bottom-0 left-0 w-full p-8 text-left translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                <h3 class="text-3xl font-black text-white mb-2 uppercase italic leading-none">
                                    <?php echo htmlspecialchars($judge['name']); ?>
                                </h3>
                                <p class="text-[#aa843f] font-bold text-sm tracking-widest uppercase mb-0">
                                    <?php echo htmlspecialchars($judge['role'] ?? 'Judge'); ?>
                                </p>
                            </div>
                        </div>

                        <p
                            class="text-gray-400 text-sm leading-relaxed max-w-sm mx-auto opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100 hidden">
                            <?php echo htmlspecialchars($judge['description'] ?? ''); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div
                class="glass-card rounded-3xl p-16 border border-white/5 inline-block max-w-2xl bg-gradient-to-b from-white/5 to-transparent">
                <p class="text-gray-300 text-xl md:text-2xl font-medium">Judges will be announced soon.</p>
            </div>
        <?php endif; ?>
    </div>
</section>