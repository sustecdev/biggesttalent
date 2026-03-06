<?php
$urlRoot = defined('URLROOT') ? URLROOT : '';
$pastSeasons = [
    ['num' => 1, 'image' => 'images/past/IMAGE - SEASON 1.png', 'video' => 'images/past/season1.mp4', 'title' => 'Season 1'],
    ['num' => 2, 'image' => 'images/past/IMAGE - SEASON 2.png', 'video' => 'images/past/season2.mp4', 'title' => 'Season 2'],
    ['num' => 3, 'image' => 'images/past/IMAGE - SEASON 3.png', 'video' => 'images/past/season3.mp4', 'title' => 'Season 3'],
    ['num' => 4, 'image' => 'images/past/IMAGE - SEASON 4.png', 'video' => 'images/past/season4.mp4', 'title' => 'Season 4'],
];
?>
<!-- Past Seasons Section -->
<section id="past-seasons" class="py-20 md:py-32 relative overflow-hidden">
    <div class="absolute inset-0 bg-black/35 pointer-events-none" aria-hidden="true"></div>
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-[#cd217d]/50 to-transparent z-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-4xl mx-auto mb-16 md:mb-20">
            <p class="text-[#cd217d] text-sm font-bold uppercase tracking-[0.25em] mb-4">Our Journey</p>
            <h2 class="text-4xl sm:text-6xl md:text-7xl font-black text-white mb-6 tracking-tight uppercase home-section-heading">
                <span class="text-gradient-gold">Past Seasons</span>
            </h2>
            <p class="text-xl text-gray-300 leading-relaxed font-light">
                Celebrating the stars who have graced Africa's biggest talent stage
            </p>
        </div>

        <p class="text-center text-gray-400 text-sm mb-8">Click on a season to watch the winner highlights</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
            <?php foreach ($pastSeasons as $season): ?>
                <button type="button" class="past-season-video-btn group relative rounded-2xl overflow-hidden border border-white/10 hover:border-[#cd217d]/50 transition-all duration-500 hover:-translate-y-2 shadow-xl w-full text-left cursor-pointer bg-transparent p-0"
                        data-video-src="<?= $urlRoot ?>/<?= str_replace(' ', '%20', htmlspecialchars($season['video'])) ?>"
                        data-season-title="<?= htmlspecialchars($season['title']) ?>">
                    <div class="aspect-[3/4] overflow-hidden">
                        <img src="<?= $urlRoot ?>/<?= str_replace(' ', '%20', htmlspecialchars($season['image'])) ?>"
                             alt="<?= htmlspecialchars($season['title']) ?>"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent opacity-80"></div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 z-10 flex items-center justify-between">
                        <span class="inline-block px-4 py-2 rounded-full bg-[#cd217d]/20 border border-[#cd217d]/40 text-[#cd217d] font-bold text-sm uppercase tracking-wider">
                            <?= htmlspecialchars($season['title']) ?>
                        </span>
                        <span class="text-white/80 text-sm font-medium">Watch</span>
                    </div>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-[#cd217d]/20 to-transparent"></div>
</section>

<!-- Past Season Video Modal -->
<div id="pastSeasonVideoModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 bg-black/90 backdrop-blur-sm" aria-modal="true" aria-labelledby="modalTitle">
    <div class="relative w-full max-w-4xl">
        <button type="button" id="pastSeasonModalClose" class="absolute -top-12 right-0 text-white hover:text-[#cd217d] transition-colors p-2" aria-label="Close">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
        <div class="aspect-video bg-black rounded-xl overflow-hidden shadow-2xl">
            <video id="pastSeasonVideo" class="w-full h-full" controls playsinline>
                <source id="pastSeasonVideoSource" src="" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        <p id="pastSeasonModalTitle" class="text-center text-white font-bold mt-4 text-lg"></p>
    </div>
</div>

<script>
(function() {
    var modal = document.getElementById('pastSeasonVideoModal');
    var video = document.getElementById('pastSeasonVideo');
    var source = document.getElementById('pastSeasonVideoSource');
    var titleEl = document.getElementById('pastSeasonModalTitle');
    var closeBtn = document.getElementById('pastSeasonModalClose');

    function openModal(videoSrc, seasonTitle) {
        source.src = videoSrc;
        video.load();
        video.play();
        titleEl.textContent = seasonTitle || 'Season Highlights';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        video.pause();
        source.src = '';
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.past-season-video-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var src = this.getAttribute('data-video-src');
            var title = this.getAttribute('data-season-title');
            if (src) openModal(src, title);
        });
    });

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });
})();
</script>
