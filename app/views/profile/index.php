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

                    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'super_admin'], true)): ?>
                        <div class="user-id-badge"
                            style="background: linear-gradient(135deg, #dc2626, #991b1b); border: none;">
                            <span class="user-id-number" style="color: white;">👑 <?= ($_SESSION['role'] ?? '') === 'super_admin' ? 'SUPER ADMIN' : 'ADMIN' ?></span>
                            <span class="user-id-label" style="color: rgba(255,255,255,0.9);"><?= ($_SESSION['role'] ?? '') === 'super_admin' ? 'SUPER ADMINISTRATOR' : 'ADMINISTRATOR' ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="user-id-badge">
                        <span
                            class="user-id-number">#<?= htmlspecialchars($userProfile['pernum'] ?? $_SESSION['pernum']) ?></span>
                        <span class="user-id-label">VERIFIED USER</span>
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
                    <div class="section-header" style="margin-bottom: 32px;">
                        <h2 class="welcome-title"
                            style="font-family: 'Outfit', sans-serif; font-size: 2rem; font-weight: 700; margin-bottom: 8px;">
                            My Nominations</h2>
                        <p style="color: rgba(255,255,255,0.6); margin: 0;">Track the status and performance of your
                            submissions</p>
                    </div>

                    <div class="nominations-list">
                        <?php if (empty($data['userNominations'])): ?>
                            <div class="no-nominations">
                                <div style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;">🎵</div>
                                <h3>No Nominations Yet</h3>
                                <p>You haven't submitted any nominations yet to Biggest Talent Africa.</p>
                                <a href="index.php?url=nominate" class="btn-submit"
                                    style="display: inline-block; margin-top: 20px; text-decoration: none;">
                                    Submit Request
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="nominations-grid">
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
                                ?>
                                    <div class="nomination-card nomination-card-clickable" role="button" tabindex="0"
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
                                        onclick="showNominationDetails(this)" onkeydown="if(event.key==='Enter')showNominationDetails(this)"
                                        style="cursor: pointer;">
                                        <?php if ($profilePhotoUrl): ?>
                                        <div class="nomination-photo" style="text-align: center; margin-bottom: 12px;">
                                            <img src="<?= htmlspecialchars($profilePhotoUrl) ?>" alt="<?= htmlspecialchars($nom['aname']) ?>"
                                                style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(205, 33, 125, 0.5);">
                                        </div>
                                        <?php endif; ?>
                                        <div class="nomination-header">
                                            <h3 class="nomination-title"><?= htmlspecialchars($nom['title'] ?? $nom['aname']) ?>
                                            </h3>
                                            <span
                                                class="nomination-status badge-<?= strtolower($nom['status'] ?? 'pending') ?>">
                                                <?= ucfirst($nom['status'] ?? 'pending') ?>
                                            </span>
                                        </div>

                                        <div class="nomination-info">
                                            <div class="info-row">
                                                <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                                <span><span class="info-label">Artist:</span>
                                                    <?= htmlspecialchars($nom['aname']) ?></span>
                                            </div>

                                            <div class="info-row">
                                                <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                                <span><span class="info-label">From:</span>
                                                    <?= htmlspecialchars($nom['country']) ?></span>
                                            </div>

                                            <div class="info-row">
                                                <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                <span><span class="info-label">Date:</span>
                                                    <?= date('M d, Y', strtotime($nom['date'])) ?></span>
                                            </div>
                                        </div>

                                        <div class="vote-count-badge">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#cd217d"
                                                stroke-width="2">
                                                <path d="M4.5 12.5l5 5 10-10" />
                                            </svg>
                                            <span class="vote-number"><?= number_format($nom['vote_count'] ?? 0) ?></span>
                                            <span class="vote-label">Total Votes</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Nomination Details Modal -->
                <div id="nominationDetailModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 10000; align-items: center; justify-content: center; padding: 20px;">
                    <div style="background: #1a1a24; border-radius: 16px; max-width: 560px; width: 100%; max-height: 90vh; overflow-y: auto; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 25px 50px rgba(0,0,0,0.5);">
                        <div style="padding: 24px; position: relative;">
                            <button onclick="closeNominationDetails()" style="position: absolute; top: 16px; right: 16px; background: rgba(255,255,255,0.1); border: none; color: #fff; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; font-size: 20px; line-height: 1;">&times;</button>
                            <div id="nominationDetailContent"></div>
                        </div>
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
        const urlRoot = '<?= defined("URLROOT") ? URLROOT : "" ?>';

        let html = '';
        if (data.profile_photo) {
            html += '<div style="text-align: center; margin-bottom: 20px;"><img src="' + escapeHtml(data.profile_photo) + '" alt="" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(205, 33, 125, 0.5);"></div>';
        }
        html += '<h2 style="font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 8px;">' + escapeHtml(data.title || data.aname) + '</h2>';
        html += '<span class="nomination-status badge-' + (data.status || 'pending').toLowerCase() + '" style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 16px;">' + escapeHtml((data.status || 'pending').charAt(0).toUpperCase() + (data.status || 'pending').slice(1)) + '</span>';
        html += '<div style="color: rgba(255,255,255,0.85); font-size: 14px; line-height: 1.7;">';
        html += '<p style="margin: 8px 0;"><strong>Artist:</strong> ' + escapeHtml(data.aname) + '</p>';
        if (data.gender) html += '<p style="margin: 8px 0;"><strong>Gender:</strong> ' + escapeHtml(data.gender) + '</p>';
        if (data.age) html += '<p style="margin: 8px 0;"><strong>Age:</strong> ' + escapeHtml(String(data.age)) + '</p>';
        if (data.nominee_email) html += '<p style="margin: 8px 0;"><strong>Email:</strong> <a href="mailto:' + escapeHtml(data.nominee_email) + '">' + escapeHtml(data.nominee_email) + '</a></p>';
        if (data.nominee_phone) html += '<p style="margin: 8px 0;"><strong>Phone:</strong> <a href="tel:' + escapeHtml(data.nominee_phone) + '">' + escapeHtml(data.nominee_phone) + '</a></p>';
        html += '<p style="margin: 8px 0;"><strong>Performance:</strong> ' + escapeHtml(data.title || '-') + '</p>';
        if (data.category_name) html += '<p style="margin: 8px 0;"><strong>Category:</strong> ' + escapeHtml(data.category_name) + '</p>';
        html += '<p style="margin: 8px 0;"><strong>Location:</strong> ' + escapeHtml(data.country) + (data.province ? ', ' + escapeHtml(data.province) : '') + '</p>';
        html += '<p style="margin: 8px 0;"><strong>Submitted:</strong> ' + (data.date ? new Date(data.date).toLocaleDateString() : '-') + '</p>';
        html += '<p style="margin: 8px 0;"><strong>Total Votes:</strong> ' + (data.vote_count || 0).toLocaleString() + '</p>';
        if (data.description) html += '<p style="margin: 12px 0 8px;"><strong>Description:</strong></p><p style="margin: 0 0 16px; white-space: pre-wrap;">' + escapeHtml(data.description) + '</p>';
        if (data.video_url) {
            html += '<a href="' + escapeHtml(data.video_url) + '" target="_blank" rel="noopener" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #cd217d, #a51a64); color: white; border-radius: 10px; font-weight: 600; text-decoration: none; margin-top: 12px;">Watch Video</a>';
        }
        html += '</div>';

        content.innerHTML = html;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeNominationDetails() {
        document.getElementById('nominationDetailModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    function escapeHtml(str) {
        if (!str) return '';
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    document.getElementById('nominationDetailModal').addEventListener('click', function(e) {
        if (e.target === this) closeNominationDetails();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeNominationDetails();
    });
</script>