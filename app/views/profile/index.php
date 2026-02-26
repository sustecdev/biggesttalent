<?php
function nominationVideoEmbed($videoUrl, $videoFile = '') {
    if (empty($videoUrl)) return '';
    $url = (string) $videoUrl;
    $isDirect = preg_match('/\.(mp4|webm|ogg|mov)(\?|$)/i', $url) || ($videoFile && preg_match('/\.(mp4|webm|ogg|mov)(\?|$)/i', (string) $videoFile));
    if ($isDirect) {
        $esc = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        return '<video controls class="w-full rounded-xl bg-black aspect-video" playsinline preload="metadata" onclick="event.stopPropagation()"><source src="' . $esc . '" type="video/mp4">Not supported</video>';
    }
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
        $esc = htmlspecialchars('https://www.youtube.com/embed/' . $m[1] . '?rel=0', ENT_QUOTES, 'UTF-8');
        return '<div class="aspect-video rounded-xl overflow-hidden bg-black"><iframe src="' . $esc . '" class="w-full h-full" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe></div>';
    }
    if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $url, $m)) {
        $esc = htmlspecialchars('https://player.vimeo.com/video/' . $m[1], ENT_QUOTES, 'UTF-8');
        return '<div class="aspect-video rounded-xl overflow-hidden bg-black"><iframe src="' . $esc . '" class="w-full h-full" allowfullscreen></iframe></div>';
    }
    $esc = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    return '<div class="aspect-video rounded-xl overflow-hidden bg-black"><iframe src="' . $esc . '" class="w-full h-full" allowfullscreen></iframe></div>';
}
?>
<main class="main-content">
    <?php
    // Prepare Data
    $userProfile = $data['userProfile'];
    $balanceData = $data['balanceData'];
    $nominations = $data['userNominations'];
    $totalNominations = count($nominations);

    // Calculate Votes
    $totalVotes = 0;
    foreach ($nominations as $nom) {
        $totalVotes += ($nom['vote_count'] ?? 0);
    }

    // Display Name
    $displayName = '';
    if (!empty($userProfile['fname']) || !empty($userProfile['lname'])) {
        $displayName = trim(($userProfile['fname'] ?? '') . ' ' . ($userProfile['lname'] ?? ''));
    } else {
        $displayName = $userProfile['username'] ?? 'User';
    }
    ?>

    <div class="profile-layout">
        <!-- 1. Sidebar -->
        <!-- 1. Sidebar -->
        <!-- 1. Sidebar -->
        <!-- Sidebar -->
        <?php require_once APPROOT . '/views/partials/user_sidebar.php'; ?>

        <!-- 2. Main Content -->
        <div class="profile-main">
            <!-- Top Header -->
            <header class="dashboard-top-bar">
                <h1 class="dashboard-page-title">My Dashboard</h1>

                <div class="dashboard-top-actions">

                    <?php if (function_exists('isAdmin') && isAdmin()): ?>
                        <div class="user-id-badge"
                            style="background: linear-gradient(135deg, #dc2626, #991b1b); border: none;">
                            <span class="user-id-number" style="color: white;">👑 <?= ($_SESSION['role'] ?? '') === 'super_admin' ? 'SUPER ADMIN' : 'ADMIN' ?></span>
                            <span class="user-id-label" style="color: rgba(255,255,255,0.9);"><?= ($_SESSION['role'] ?? '') === 'super_admin' ? 'SUPER ADMINISTRATOR' : 'ADMINISTRATOR' ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="user-id-badge">
                        <span
                            class="user-id-number">#<?= htmlspecialchars($userProfile['pernum'] ?? $_SESSION['pernum']) ?></span>
                    </div>
                    <!-- Avatar Removed -->
                </div>
            </header>

            <div class="dashboard-content">

                <!-- DASHBOARD TAB -->
                <div id="dashboardTab" class="tab-content active">
                    <!-- Welcome Section -->
                    <div class="welcome-section">
                        <div>
                            <h2 class="welcome-title">Welcome Back!</h2>
                            <p class="welcome-subtitle">Here's what's happening with your nominations.</p>
                        </div>
                        <a href="index.php?url=nominate" class="btn-add-new">
                            <span>+</span> Add New Nomination
                        </a>
                    </div>

                    <!-- Stats Cards (Reference Layout) -->
                    <div class="stats-grid">
                        <!-- Card 1: Total Nominations -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <span class="stat-label-ref">TOTAL NOMINATIONS</span>
                            </div>
                            <div>
                                <div class="stat-value-ref" style="color: #FFFFFF !important;">
                                    <?= number_format($totalNominations) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Votes (Pending Orders equivalent) -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <span class="stat-label-ref">VOTES RECEIVED</span>
                            </div>
                            <div>
                                <div class="stat-value-ref" style="color: #FFFFFF !important;">
                                    <?= number_format($totalVotes) ?>
                                </div>
                                <div class="stat-footer" style="margin-top: 8px;">
                                    <span class="stat-badge">Active Season</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: DBV Balance (Rating equivalent) -->
                        <div class="stat-card">
                            <div class="stat-header">
                                <span class="stat-label-ref">DBV BALANCE</span>
                            </div>
                            <div>
                                <div class="stat-value-ref" style="color: #FFFFFF !important;">
                                    <?= $balanceData && isset($balanceData['balance']) ? number_format((float) $balanceData['balance'], 2) : '0.00' ?>
                                </div>
                                <div style="margin-top: 5px; color: #f59e0b; font-size: 14px;">★</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- NOMINATIONS TAB -->
                <link rel="stylesheet" href="<?= URLROOT ?>/css/user-dashboard.css?v=<?= time() ?>">

                <div id="nominationsTab" class="tab-content" style="display: none;">
                    <div class="mb-8">
                        <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">My Nominations</h2>
                        <p class="text-gray-400">Track the status and performance of your submissions</p>
                    </div>

                    <div class="nominations-list">
                        <?php if (empty($data['userNominations'])): ?>
                            <div class="no-nominations flex flex-col items-center justify-center py-16 px-6 rounded-2xl border-2 border-dashed border-white/10 bg-white/[0.03]">
                                <div class="text-6xl mb-6 opacity-50">🎵</div>
                                <h3 class="text-xl font-bold text-white mb-3">No Nominations Yet</h3>
                                <p class="text-gray-400 text-center max-w-sm mb-8">You haven't submitted any nominations yet to Biggest Talent Africa. Showcase amazing talent to the world!</p>
                                <a href="<?= defined('URLROOT') ? URLROOT . '/index.php?url=nominate' : 'index.php?url=nominate' ?>" class="inline-flex items-center gap-2 px-8 py-4 rounded-full font-bold bg-gradient-to-r from-[#cd217d] to-[#9a288d] text-white hover:opacity-90 hover:-translate-y-0.5 transition-all shadow-lg shadow-[#cd217d]/20">
                                    <span>+</span> Submit a Nomination
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="nominations-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php foreach ($data['userNominations'] as $nom):
                                    $profilePhotoUrl = !empty($nom['profile_photo'])
                                        ? (strpos($nom['profile_photo'], 'http') === 0 ? $nom['profile_photo'] : (defined('URLROOT') ? URLROOT . '/' : '') . $nom['profile_photo'])
                                        : null;
                                    $videoUrl = '';
                                    if (!empty($nom['video_file'])) {
                                        $videoUrl = strpos($nom['video_file'], 'http') === 0 ? $nom['video_file'] : (defined('URLROOT') ? URLROOT . '/' : '') . $nom['video_file'];
                                    } elseif (!empty($nom['vlink'])) {
                                        $videoUrl = $nom['vlink'];
                                    }
                                    $status = strtolower($nom['status'] ?? 'pending');
                                    $statusClasses = [
                                        'approved' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                        'pending' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                        'rejected' => 'bg-red-500/20 text-red-400 border-red-500/30'
                                    ];
                                    $statusClass = $statusClasses[$status] ?? $statusClasses['pending'];
                                ?>
                                    <div class="nomination-card nomination-card-clickable group relative bg-[#16161d]/80 backdrop-blur-sm border border-white/10 rounded-2xl p-6 hover:border-[#cd217d]/40 hover:-translate-y-1 hover:shadow-xl hover:shadow-[#cd217d]/10 transition-all duration-300 cursor-pointer overflow-hidden"
                                        role="button" tabindex="0"
                                        data-nom='<?= htmlspecialchars(json_encode([
                                            'aname' => $nom['aname'] ?? '',
                                            'title' => $nom['title'] ?? '',
                                            'description' => $nom['description'] ?? '',
                                            'country' => $nom['country'] ?? '',
                                            'province' => $nom['province'] ?? '',
                                            'gender' => $nom['gender'] ?? '',
                                            'age' => $nom['age'] ?? '',
                                            'nominee_email' => $nom['nominee_email'] ?? '',
                                            'nominee_phone' => $nom['nominee_phone'] ?? '',
                                            'category_name' => $nom['category_name'] ?? '',
                                            'date' => $nom['date'] ?? '',
                                            'status' => $nom['status'] ?? '',
                                            'vote_count' => $nom['vote_count'] ?? 0,
                                            'profile_photo' => $profilePhotoUrl,
                                            'video_url' => $videoUrl,
                                            'vlink' => $nom['vlink'] ?? '',
                                            'video_file' => $nom['video_file'] ?? ''
                                        ]), ENT_QUOTES, 'UTF-8') ?>'
                                        onclick="showNominationDetails(this)" onkeydown="if(event.key==='Enter')showNominationDetails(this)">
                                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#cd217d] to-[#9a288d] opacity-0 group-hover:opacity-100 transition-opacity"></div>

                                        <?php if ($profilePhotoUrl): ?>
                                        <div class="flex justify-center mb-5">
                                            <img src="<?= htmlspecialchars($profilePhotoUrl) ?>" alt="<?= htmlspecialchars($nom['aname']) ?>" class="w-20 h-20 rounded-full object-cover border-2 border-[#cd217d]/50">
                                        </div>
                                        <?php endif; ?>

                                        <div class="flex justify-between items-start gap-3 mb-5">
                                            <h3 class="nomination-title flex-1 min-w-0 text-lg font-bold text-white line-clamp-2"><?= htmlspecialchars($nom['title'] ?? $nom['aname']) ?></h3>
                                            <span class="nomination-status badge-<?= $status ?> flex-shrink-0 px-3 py-1 text-xs font-semibold uppercase rounded-full border <?= $statusClass ?>"><?= ucfirst($nom['status'] ?? 'pending') ?></span>
                                        </div>

                                        <div class="space-y-3 mb-5">
                                            <div class="flex items-center gap-3 text-gray-400 text-sm">
                                                <svg class="w-5 h-5 text-[#cd217d] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                <span><span class="text-gray-500">Artist:</span> <?= htmlspecialchars($nom['aname']) ?></span>
                                            </div>
                                            <div class="flex items-center gap-3 text-gray-400 text-sm">
                                                <svg class="w-5 h-5 text-[#cd217d] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span><span class="text-gray-500">From:</span> <?= htmlspecialchars($nom['country']) ?></span>
                                            </div>
                                            <div class="flex items-center gap-3 text-gray-400 text-sm">
                                                <svg class="w-5 h-5 text-[#cd217d] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <span><span class="text-gray-500">Date:</span> <?= date('M d, Y', strtotime($nom['date'])) ?></span>
                                            </div>
                                        </div>

                                        <?php if ($videoUrl): ?>
                                        <div class="mb-4" onclick="event.stopPropagation()">
                                            <?= nominationVideoEmbed($videoUrl, $nom['video_file'] ?? '') ?>
                                        </div>
                                        <?php endif; ?>

                                        <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                                            <svg class="w-5 h-5 text-[#cd217d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                            <span class="vote-number font-bold text-white"><?= number_format($nom['vote_count'] ?? 0) ?></span>
                                            <span class="vote-label text-gray-500 text-sm">votes</span>
                                        </div>

                                        <p class="mt-3 text-center text-gray-500 text-xs">Click for full details</p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Nomination Details Modal -->
                <div id="nominationDetailModal" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4 md:p-6 bg-black/80 backdrop-blur-sm" style="display: none;">
                    <div class="relative w-full max-w-2xl max-h-[90vh] overflow-hidden rounded-2xl border border-white/10 bg-[#1a1a24] shadow-2xl shadow-black/50">
                        <button onclick="closeNominationDetails()" class="absolute top-4 right-4 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition text-xl leading-none">&times;</button>
                        <div id="nominationDetailContent" class="overflow-y-auto max-h-[90vh] p-6 md:p-8"></div>
                    </div>
                </div>

                <!-- EDIT TAB -->
                <div id="editTab" class="tab-content">
                    <div class="section-header" style="margin-bottom: 24px;">
                        <h2 class="welcome-title">Profile Settings</h2>
                    </div>

                    <?php if (!empty($data['updateSuccess'])): ?>
                        <div class="alert alert-success"
                            style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                            <?= $data['updateSuccess'] ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($data['updateError'])): ?>
                        <div class="alert alert-error"
                            style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                            <?= $data['updateError'] ?>
                        </div>
                    <?php endif; ?>
                    <div class="profile-form-card">
                        <form method="POST" enctype="multipart/form-data" class="profile-form">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="form-group">
                                <label for="profile_pic" class="form-label">Profile Picture</label>
                                <input type="file" class="form-input" id="profile_pic" name="profile_pic"
                                    accept="image/jpeg,image/png,image/gif,image/webp">
                            </div>

                            <div class="form-group">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-input" id="bio" name="bio" rows="5"
                                    maxlength="500"><?= htmlspecialchars($userProfile['bio'] ?? '') ?></textarea>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn-submit">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<script>
    function switchTab(tabId, btnElement) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
            tab.style.display = 'none';
        });

        document.querySelectorAll('.sidebar-item').forEach(btn => btn.classList.remove('active'));

        const targetTab = document.getElementById(tabId + 'Tab');
        if (targetTab) {
            targetTab.classList.add('active');
            targetTab.style.display = 'block';
        }

        // Update functionality to accept element or find it by text/attribute if needed, 
        // but for hash-based loading we might just set the tab to active.
        if (btnElement) {
            btnElement.classList.add('active');
        } else {
            // Try to find the sidebar item corresponding to this tab
            // This is a naive check; ideally sidebar items should have data-target attributes
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const hash = window.location.hash.substring(1); // Remove the '#'
        if (hash) {
            if (hash === 'nominations') {
                switchTab('nominations', null);
                // Manually activate sidebar item
                document.querySelectorAll('.sidebar-item').forEach(item => {
                    if (item.innerText.trim() === 'My Nominations') {
                        item.classList.add('active');
                    }
                });
            } else if (hash === 'settings') {
                switchTab('edit', null);
                document.querySelectorAll('.sidebar-item').forEach(item => {
                    if (item.innerText.trim() === 'Profile Settings') {
                        item.classList.add('active');
                    }
                });
            }
        }
    });

    function showNominationDetails(cardEl) {
        const data = JSON.parse(cardEl.getAttribute('data-nom') || '{}');
        const modal = document.getElementById('nominationDetailModal');
        const content = document.getElementById('nominationDetailContent');
        const status = (data.status || 'pending').toLowerCase();
        const statusClasses = { approved: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30', pending: 'bg-amber-500/20 text-amber-400 border-amber-500/30', rejected: 'bg-red-500/20 text-red-400 border-red-500/30' };
        const statusClass = statusClasses[status] || statusClasses.pending;
        const statusText = (data.status || 'pending').charAt(0).toUpperCase() + (data.status || 'pending').slice(1);

        let html = '<div class="space-y-6">';
        if (data.profile_photo) {
            html += '<div class="flex justify-center"><img src="' + escapeHtml(data.profile_photo) + '" alt="" class="w-28 h-28 rounded-full object-cover border-2 border-[#cd217d]/50"></div>';
        }
        html += '<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">';
        html += '<h2 class="text-2xl font-bold text-white">' + escapeHtml(data.title || data.aname) + '</h2>';
        html += '<span class="px-4 py-2 text-xs font-semibold uppercase rounded-full border flex-shrink-0 w-fit ' + statusClass + '">' + escapeHtml(statusText) + '</span>';
        html += '</div>';

        html += '<div class="grid gap-4 sm:grid-cols-2">';
        html += '<div class="flex items-center gap-3 p-4 rounded-xl bg-white/5 border border-white/10"><svg class="w-6 h-6 text-[#cd217d] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg><div><p class="text-xs text-gray-500">Artist</p><p class="text-white font-medium">' + escapeHtml(data.aname) + '</p></div></div>';
        if (data.category_name) html += '<div class="flex items-center gap-3 p-4 rounded-xl bg-white/5 border border-white/10"><svg class="w-6 h-6 text-[#cd217d] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg><div><p class="text-xs text-gray-500">Category</p><p class="text-white font-medium">' + escapeHtml(data.category_name) + '</p></div></div>';
        html += '<div class="flex items-center gap-3 p-4 rounded-xl bg-white/5 border border-white/10"><svg class="w-6 h-6 text-[#cd217d] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><div><p class="text-xs text-gray-500">Location</p><p class="text-white font-medium">' + escapeHtml(data.country) + (data.province ? ', ' + escapeHtml(data.province) : '') + '</p></div></div>';
        html += '<div class="flex items-center gap-3 p-4 rounded-xl bg-white/5 border border-white/10"><svg class="w-6 h-6 text-[#cd217d] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg><div><p class="text-xs text-gray-500">Votes</p><p class="text-white font-bold">' + (data.vote_count || 0).toLocaleString() + '</p></div></div>';
        html += '</div>';

        html += '<div class="space-y-3 text-sm text-gray-400">';
        if (data.gender) html += '<p><span class="text-gray-500">Gender:</span> ' + escapeHtml(data.gender) + '</p>';
        if (data.age) html += '<p><span class="text-gray-500">Age:</span> ' + escapeHtml(String(data.age)) + '</p>';
        if (data.nominee_email) html += '<p><span class="text-gray-500">Email:</span> <a href="mailto:' + escapeHtml(data.nominee_email) + '" class="text-[#cd217d] hover:underline">' + escapeHtml(data.nominee_email) + '</a></p>';
        if (data.nominee_phone) html += '<p><span class="text-gray-500">Phone:</span> <a href="tel:' + escapeHtml(data.nominee_phone) + '" class="text-[#cd217d] hover:underline">' + escapeHtml(data.nominee_phone) + '</a></p>';
        html += '<p><span class="text-gray-500">Submitted:</span> ' + (data.date ? new Date(data.date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '-') + '</p>';
        html += '</div>';

        if (data.description) html += '<div class="pt-2 border-t border-white/10"><p class="text-xs text-gray-500 mb-2">Description</p><p class="text-gray-300 text-sm whitespace-pre-wrap leading-relaxed">' + escapeHtml(data.description) + '</p></div>';

        if (data.video_url) {
            html += '<div class="pt-2 border-t border-white/10"><p class="text-xs text-gray-500 mb-3">Performance Video</p>' + getVideoEmbedHtml(data.video_url, data.video_file) + '</div>';
        }
        html += '</div>';

        content.innerHTML = html;
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeNominationDetails() {
        const modal = document.getElementById('nominationDetailModal');
        modal.querySelectorAll('video').forEach(v => { v.pause(); });
        modal.querySelectorAll('iframe').forEach(f => { f.src = 'about:blank'; });
        modal.style.display = 'none';
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function escapeHtml(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function getVideoEmbedHtml(videoUrl, videoFile) {
        if (!videoUrl) return '';
        const url = String(videoUrl);
        const isDirectVideo = /\.(mp4|webm|ogg|mov)(\?|$)/i.test(url) || (videoFile && /\.(mp4|webm|ogg|mov)(\?|$)/i.test(String(videoFile)));
        if (isDirectVideo) {
            return '<video controls class="w-full rounded-xl bg-black aspect-video" playsinline preload="metadata"><source src="' + escapeHtml(url) + '" type="video/mp4">Your browser does not support the video tag.</video>';
        }
        let embedSrc = '';
        const ytMatch = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
        const vimeoMatch = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
        if (ytMatch) {
            embedSrc = 'https://www.youtube.com/embed/' + ytMatch[1] + '?rel=0';
        } else if (vimeoMatch) {
            embedSrc = 'https://player.vimeo.com/video/' + vimeoMatch[1];
        }
        if (embedSrc) {
            return '<div class="aspect-video rounded-xl overflow-hidden bg-black"><iframe src="' + escapeHtml(embedSrc) + '" class="w-full h-full" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe></div>';
        }
        return '<div class="aspect-video rounded-xl overflow-hidden bg-black"><iframe src="' + escapeHtml(url) + '" class="w-full h-full" allowfullscreen></iframe></div>';
    }

    document.getElementById('nominationDetailModal').addEventListener('click', function(e) {
        if (e.target === this) closeNominationDetails();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeNominationDetails();
    });
</script>