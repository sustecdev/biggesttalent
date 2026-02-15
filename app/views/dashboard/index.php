<!-- Dashboard Header -->
<div class="mb-12">
    <h1 class="text-4xl md:text-5xl font-black text-white tracking-tighter mb-4">
        Welcome <span class="text-gradient-gold">Back</span>
    </h1>
    <p class="text-gray-400 text-lg font-light">Manage your competition presence and track your progress.</p>
</div>

<!-- User Info Display -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
    <?php if (!empty($data['pernum'])): ?>
        <div class="glass-card p-6 rounded-2xl flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-[#cd217d]/20 flex items-center justify-center text-[#cd217d]">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" fill="currentColor"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-400 uppercase tracking-wider">Pernum</p>
                <p class="text-xl font-bold text-white"><?= htmlspecialchars($data['pernum']) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($data['balanceData']): ?>
        <div class="glass-card p-6 rounded-2xl flex items-center gap-4 border border-[#aa843f]/30 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-r from-[#aa843f]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="w-12 h-12 rounded-full bg-[#aa843f]/20 flex items-center justify-center text-[#aa843f]">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 18V19C21 20.1 20.1 21 19 21H5C3.89 21 3 20.1 3 19V5C3 3.9 3.89 3 5 3H19C20.1 3 21 3.9 21 5V6H12C10.89 6 10 6.9 10 8V16C10 17.1 10.89 18 12 18H21ZM12 16H22V8H12V16ZM16 13.5C15.17 13.5 14.5 12.83 14.5 12C14.5 11.17 15.17 10.5 16 10.5C16.83 10.5 17.5 11.17 17.5 12C17.5 12.83 16.83 13.5 16 13.5Z" fill="currentColor"/>
                </svg>
            </div>
            <div class="relative z-10">
                <p class="text-sm text-[#aa843f] uppercase tracking-wider font-bold">DBV Balance</p>
                <?php if (isset($data['balanceData']['balance'])): ?>
                    <p class="text-xl font-bold text-white"><?= htmlspecialchars(number_format((float) $data['balanceData']['balance'], 2)) ?> DBV</p>
                <?php else: ?>
                    <p class="text-xl font-bold text-red-400">Unable to load</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Stats Grid -->
<div class="mb-16">
    <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
        <span class="w-1 h-8 bg-[#cd217d] rounded-full"></span>
        Overview
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Stat Cards -->
        <?php 
        $stats_list = [
            ['label' => 'Total Contestants', 'key' => 'total_contestants', 'desc' => 'Approved participants'],
            ['label' => 'Total Votes', 'key' => 'total_votes', 'desc' => 'Community engagement'],
            ['label' => 'Total Judges', 'key' => 'total_judges', 'desc' => 'Active panel members'],
            ['label' => 'Nominations', 'key' => 'total_nominations', 'desc' => 'Submitted entries'],
            ['label' => 'Seasons', 'key' => 'total_seasons', 'desc' => 'Competition history'],
            ['label' => 'Users', 'key' => 'users_registered', 'desc' => 'Registered accounts'],
            ['label' => 'Active Contests', 'key' => 'active_contests', 'desc' => 'Currently running'],
            ['label' => 'Video Uploads', 'key' => 'total_video_uploads', 'desc' => 'Content library']
        ];

        foreach($stats_list as $stat): ?>
            <div class="glass-card p-6 rounded-2xl hover:bg-white/5 transition-colors group">
                <h3 class="text-3xl font-black text-white mb-1 group-hover:text-[#cd217d] transition-colors">
                    <?= number_format($data['stats'][$stat['key']] ?? 0) ?>
                </h3>
                <p class="text-[#aa843f] font-bold text-sm uppercase tracking-wider mb-2"><?= $stat['label'] ?></p>
                <p class="text-gray-500 text-xs"><?= $stat['desc'] ?></p>
            </div>
        <?php endforeach; ?>

        <!-- Highlighted Cards (Pending) -->
        <?php if (($data['stats']['pending_applications'] ?? 0) > 0): ?>
            <div class="glass-card p-6 rounded-2xl border border-[#cd217d]/50 bg-[#cd217d]/10 hover:bg-[#cd217d]/20 transition-all cursor-pointer group" onclick="window.location.href='<?= URLROOT ?>/legacy/admin-nominations.php?status=pending'">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-3xl font-black text-white group-hover:scale-110 transition-transform origin-left">
                        <?= number_format($data['stats']['pending_applications'] ?? 0) ?>
                    </h3>
                    <div class="w-2 h-2 rounded-full bg-[#cd217d] animate-pulse"></div>
                </div>
                <p class="text-[#cd217d] font-bold text-sm uppercase tracking-wider mb-2">Pending Applications</p>
                <p class="text-white/60 text-xs mb-4">Requires immediate review</p>
                <span class="text-xs text-white font-bold flex items-center gap-1 group-hover:gap-2 transition-all">
                    Review Now <span>&rarr;</span>
                </span>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
        <span class="w-1 h-8 bg-[#aa843f] rounded-full"></span>
        Quick Actions
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $actions = [
            ['url' => 'legacy/admin-nominations.php?status=pending', 'title' => 'Review Nominations', 'desc' => 'Approve or reject applications'],
            ['url' => 'legacy/admin-contestants.php', 'title' => 'Manage Contestants', 'desc' => 'View details and edit profiles'],
            ['url' => 'legacy/admin-judges.php', 'title' => 'Manage Judges', 'desc' => 'Add or edit judge panels'],
            ['url' => 'legacy/admin-seasons.php', 'title' => 'Manage Seasons', 'desc' => 'Create new competition seasons'],
            ['url' => 'legacy/admin-voting.php', 'title' => 'Voting System', 'desc' => 'Monitor votes and analytics'],
            ['url' => 'legacy/admin-reports.php', 'title' => 'View Reports', 'desc' => 'System flags and user reports'],
            ['url' => 'legacy/admin-categories.php', 'title' => 'Categories', 'desc' => 'Manage talent categories'],
            ['url' => 'legacy/admin-contests.php', 'title' => 'Contests', 'desc' => 'Setup dates and prizes'],
            ['url' => 'legacy/videos.php', 'title' => 'Video Feed', 'desc' => 'Browse uploaded content']
        ];

        foreach($actions as $action): ?>
            <a href="<?= URLROOT ?>/<?= $action['url'] ?>" class="glass-card p-6 rounded-2xl hover:border-[#aa843f]/50 hover:bg-white/5 transition-all group flex flex-col justify-between h-full">
                <div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-[#aa843f] transition-colors"><?= $action['title'] ?></h3>
                    <p class="text-gray-400 text-sm leading-relaxed"><?= $action['desc'] ?></p>
                </div>
                <div class="mt-4 flex justify-end">
                    <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-[#aa843f] group-hover:text-black transition-colors">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="text-gray-400 group-hover:text-black transition-colors" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<style>
    /* Scoped Dashboard Styles if needed, mostly using utility classes now */
    .glass-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
</style>