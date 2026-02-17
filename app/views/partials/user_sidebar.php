<?php
// Sidebar Partial for User Dashboard
// Requires $data['active_tab'] to be set to highlight current page, or logic based on URL
$currentUrl = $_SERVER['REQUEST_URI'];
$isDashboard = strpos($currentUrl, 'profile') !== false;
$isNominate = strpos($currentUrl, 'nominate') !== false || strpos($currentUrl, 'nomination') !== false;
$isVote = strpos($currentUrl, 'vote') !== false;
$isSettings = strpos($currentUrl, 'settings') !== false;
?>
<aside class="profile-sidebar">
    <?php if (!$isVote && !$isNominate && !$isDashboard): ?>
        <div class="sidebar-logo-area" style="text-align: center; padding: 20px 0;">
            <img src="<?= defined('URLROOT') ? URLROOT : '' ?>/images/Official logo.png" alt="Biggest Talent Africa"
                style="max-width: 80%; height: auto; max-height: 80px;">
        </div>
    <?php endif; ?>

    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <a href="<?= defined('URLROOT') ? URLROOT . '/index.php?url=profile' : 'index.php?url=profile' ?>"
            class="sidebar-item <?= $isDashboard && !$isNominate ? 'active' : '' ?>" style="text-decoration: none;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- My Nominations -->
        <?php if ($isDashboard && !$isNominate): ?>
            <li class="sidebar-item" onclick="switchTab('nominations', this)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>My Nominations</span>
            </li>
        <?php else: ?>
            <a href="<?= defined('URLROOT') ? URLROOT . '/index.php?url=profile#nominations' : 'index.php?url=profile#nominations' ?>"
                class="sidebar-item" style="text-decoration: none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>My Nominations</span>
            </a>
        <?php endif; ?>

        <!-- Add Nomination -->
        <a href="<?= defined('URLROOT') ? URLROOT . '/index.php?url=nominate' : 'index.php?url=nominate' ?>"
            class="sidebar-item <?= $isNominate ? 'active' : '' ?>" style="text-decoration: none;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            <span>Add Nomination</span>
        </a>

        <!-- Vote -->
        <a href="<?= defined('URLROOT') ? URLROOT . '/index.php?url=vote' : 'index.php?url=vote' ?>"
            class="sidebar-item <?= $isVote ? 'active' : '' ?>" style="text-decoration: none;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;">
                <path
                    d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3">
                </path>
            </svg>
            <span>Vote</span>
        </a>

        <?php if (function_exists('isAdmin') && isAdmin()): ?>
            <!-- Admin Panel -->
            <a href="<?= defined('URLROOT') ? URLROOT . '/index.php?url=admin/nominations' : 'index.php?url=admin/nominations' ?>"
                class="sidebar-item" style="text-decoration: none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
                <span>Admin Panel</span>
            </a>
        <?php endif; ?>

        <li class="sidebar-category">ACCOUNT</li>

        <!-- Profile Settings -->
        <?php if ($isDashboard && !$isNominate): ?>
            <li class="sidebar-item" onclick="switchTab('edit', this)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Profile Settings</span>
            </li>
        <?php else: ?>
            <a href="<?= defined('URLROOT') ? URLROOT . '/index.php?url=profile#settings' : 'index.php?url=profile#settings' ?>"
                class="sidebar-item" style="text-decoration: none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Profile Settings</span>
            </a>
        <?php endif; ?>

        <!-- View Website -->
        <a href="<?= defined('URLROOT') ? URLROOT : 'index.php' ?>" target="_blank" class="sidebar-item"
            style="text-decoration: none;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="2" y1="12" x2="22" y2="12"></line>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                </path>
            </svg>
            <span>View Website</span>
        </a>

        <!-- Logout -->
        <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/auth/logout" class="sidebar-item sidebar-item-logout"
            style="text-decoration: none;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px;">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            <span>Logout</span>
        </a>
    </ul>
</aside>

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav">
    <a href="<?= defined('URLROOT') ? URLROOT . '/index.php?url=profile' : 'index.php?url=profile' ?>"
        class="mobile-nav-item <?= $isDashboard && !$isNominate ? 'active' : '' ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
        </svg>
        <span>Home</span>
    </a>

    <?php if ($isDashboard && !$isNominate): ?>
        <button class="mobile-nav-item" onclick="switchTab('nominations', this)">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
            </svg>
            <span>My Noms</span>
        </button>
    <?php else: ?>
        <a href="<?= defined('URLROOT') ? URLROOT . '/index.php?url=profile#nominations' : 'index.php?url=profile#nominations' ?>"
            class="mobile-nav-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
            </svg>
            <span>My Noms</span>
        </a>
    <?php endif; ?>

    <a href="<?= defined('URLROOT') ? URLROOT . '/index.php?url=nominate' : 'index.php?url=nominate' ?>"
        class="mobile-nav-item mobile-nav-add <?= $isNominate ? 'active' : '' ?>">
        <div class="add-icon-circle">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
        </div>
    </a>

    <a href="<?= defined('URLROOT') ? URLROOT . '/index.php?url=vote' : 'index.php?url=vote' ?>"
        class="mobile-nav-item <?= $isVote ? 'active' : '' ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path
                d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3">
            </path>
        </svg>
        <span>Vote</span>
    </a>

    <?php if (function_exists('isAdmin') && isAdmin()): ?>
    <a href="<?= defined('URLROOT') ? URLROOT . '/admin/nominations' : 'index.php?url=admin/nominations' ?>"
        class="mobile-nav-item">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
            <polyline points="2 17 12 22 22 17"></polyline>
        </svg>
        <span>Admin</span>
    </a>
    <?php endif; ?>

    <?php if ($isDashboard && !$isNominate): ?>
        <button class="mobile-nav-item" onclick="switchTab('edit', this)">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Profile</span>
        </button>
    <?php else: ?>
        <a href="<?= defined('URLROOT') ? URLROOT . '/index.php?url=profile#settings' : 'index.php?url=profile#settings' ?>"
            class="mobile-nav-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Profile</span>
        </a>
    <?php endif; ?>
</div>