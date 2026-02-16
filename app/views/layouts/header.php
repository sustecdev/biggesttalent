<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= isset($data['page_title']) ? htmlspecialchars($data['page_title']) . ' – ' : '' ?>BIGGEST TALENT AFRICA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php
    // Safe URLROOT detection
    $cssPath = defined('URLROOT') ? URLROOT . '/css/style.css' : 'css/style.css';
    ?>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= $cssPath ?>?v=<?php echo time(); ?>" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
        integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#cd217d',
                        'primary-hover': '#a51a64',
                        secondary: '#9a288d',
                        accent: '#aa843f',
                        cream: '#d5c39e',
                    }
                }
            }
        }
    </script>

    <style>
        @media (max-width :1290px) {
            .bg {
                background-position: left !important;
            }
        }

        .card-header {
            padding: 0.50rem 1.25rem;
        }

        .blocksm {
            width: 220px;
        }

        .details_upper {
            margin-left: 0px;
        }

        .details_upper2 {
            margin-left: 0px;
        }

        /* Hamburger animation */
        .nav-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(7px, 7px);
        }

        .nav-toggle.active span:nth-child(2) {
            opacity: 0;
            transform: translateX(-10px);
        }

        .nav-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -7px);
        }

        /* Nav link underline effect */
        .nav-link-underline::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #cd217d, #9a288d);
            transition: width 0.3s ease;
        }

        .nav-link-underline:hover::after {
            width: 80%;
        }
    </style>
</head>

<body
    style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); color: #FFFFFF; min-height: 100vh; min-height: 100dvh; display: flex; flex-direction: column; -webkit-text-size-adjust: 100%;">
    <?php if (empty($data['hide_nav'])): ?>
        <nav
            class="sticky top-0 left-0 right-0 z-[1000] bg-[rgba(10,10,10,0.98)] backdrop-blur-[20px] border-b border-white/10 shadow-[0_2px_20px_rgba(0,0,0,0.3)]">
            <div
                class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 min-h-[60px] sm:min-h-[68px] flex items-center justify-between gap-3 sm:gap-4 py-2">
                <!-- Logo -->
                <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php"
                    class="flex items-center flex-shrink-0 transition-transform duration-300 hover:scale-105">
                    <img src="<?= defined('URLROOT') ? URLROOT : '' ?>/images/Official logo.png"
                        alt="Biggest Talent Africa Logo"
                        class="h-14 sm:h-20 w-auto max-h-[60px] sm:max-h-[85px] object-contain">
                </a>

                <!-- User Info Desktop (Hidden as per request) -->
                <?php /* if (function_exists('isAuthenticated') && isAuthenticated() && !empty($_SESSION['pernum'])): ?>
         <div class="hidden lg:flex flex-col ml-2 text-[11px] leading-tight">
             <span class="text-white/90">Pernum: <span
                     class="text-[#cd217d]"><?= htmlspecialchars($_SESSION['pernum']) ?></span></span>
             <?php if (isset($data['balanceData']) && isset($data['balanceData']['balance'])): ?>
                 <span class="text-gray-500 text-[10px]">DBV: <span
                         class="text-[#cd217d]"><?= htmlspecialchars(number_format((float) $data['balanceData']['balance'], 2)) ?></span></span>
             <?php endif; ?>
         </div>
     <?php endif; */ ?>

                <!-- Mobile Menu Toggle -->
                <button
                    class="nav-toggle lg:hidden flex flex-col justify-center gap-1.5 w-10 h-10 bg-transparent border-0 cursor-pointer relative z-[2000] p-2 outline-none focus:ring-2 focus:ring-white/30 focus:ring-offset-2 focus:ring-offset-[#0a0a0a]"
                    type="button" aria-label="Toggle navigation" aria-expanded="false" id="nav-toggle-btn">
                    <span class="block w-6 h-0.5 bg-white rounded-sm transition-all duration-300 origin-center"></span>
                    <span class="block w-6 h-0.5 bg-white rounded-sm transition-all duration-300 origin-center"></span>
                    <span class="block w-6 h-0.5 bg-white rounded-sm transition-all duration-300 origin-center"></span>
                </button>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center gap-2 xl:gap-3 flex-1 justify-end flex-wrap" id="nav-menu-desktop">
                    <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php"
                        class="text-white/90 hover:text-white hover:bg-[rgba(205,33,125,0.1)] px-2.5 py-2 rounded-md text-sm font-medium transition-all duration-300 relative nav-link-underline">Home</a>
                    <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php#phases"
                        class="text-white/90 hover:text-white hover:bg-[rgba(205,33,125,0.1)] px-2.5 py-2 rounded-md text-sm font-medium transition-all duration-300 relative nav-link-underline">Phases</a>
                    <?php if (!empty($data['isSeasonActive'])): ?>
                        <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php#judges"
                            class="text-white/90 hover:text-white hover:bg-[rgba(205,33,125,0.1)] px-2.5 py-2 rounded-md text-sm font-medium transition-all duration-300 whitespace-nowrap relative nav-link-underline">Judges</a>
                    <?php endif; ?>
                    <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php#prizes"
                        class="text-white/90 hover:text-white hover:bg-[rgba(205,33,125,0.1)] px-2.5 py-2 rounded-md text-sm font-medium transition-all duration-300 relative nav-link-underline">Prizes</a>
                    <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php#rules"
                        class="text-white/90 hover:text-white hover:bg-[rgba(205,33,125,0.1)] px-2.5 py-2 rounded-md text-sm font-medium transition-all duration-300 relative nav-link-underline">Rules</a>

                    <?php if (function_exists('isAuthenticated') && isAuthenticated()): ?>
                        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                            <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php?url=profile"
                                class="bg-gradient-to-r from-[#cd217d] to-[#9a288d] text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_4px_12px_rgba(205,33,125,0.5)] shadow-[0_2px_8px_rgba(205,33,125,0.3)]">Dashboard</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/safezone"
                            class="bg-gradient-to-r from-[#cd217d] to-[#9a288d] text-white px-5 py-2 rounded-lg text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_4px_12px_rgba(205,33,125,0.5)] shadow-[0_2px_8px_rgba(205,33,125,0.3)]">Sign
                            In</a>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['uid']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/dashboard"
                            class="bg-gradient-to-r from-[#cd217d] to-[#9a288d] text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_4px_12px_rgba(205,33,125,0.5)] shadow-[0_2px_8px_rgba(205,33,125,0.3)]">Dashboard</a>
                    <?php endif; ?>
                </div>

            </div>
        </nav>
    <?php endif; ?>

    <!-- Mobile Menu overlay -->
    <?php if (empty($data['hide_nav'])): ?>
        <div class="fixed inset-0 bg-[rgba(10,10,10,0.98)] backdrop-blur-[20px] flex flex-col pt-[72px] px-5 pb-8 gap-1 translate-x-full lg:hidden transition-all duration-300 ease-out overflow-y-auto z-[990] invisible opacity-0"
            id="nav-menu" aria-hidden="true">
            <?php if (function_exists('isAuthenticated') && isAuthenticated() && !empty($_SESSION['pernum'])): ?>
                <div class="flex flex-col text-[11px] leading-tight mb-2">
                    <span class="text-white/90">Pernum: <span class="text-[#cd217d]">
                            <?= htmlspecialchars($_SESSION['pernum']) ?>
                        </span></span>
                    <?php if (isset($data['balanceData']) && isset($data['balanceData']['balance'])): ?>
                        <span class="text-gray-500 text-[10px]">DBV: <span class="text-[#cd217d]">
                                <?= htmlspecialchars(number_format((float) $data['balanceData']['balance'], 2)) ?>
                            </span></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php"
                class="w-full px-4 py-3 rounded-lg text-base text-left text-white/90 hover:text-white hover:bg-[rgba(205,33,125,0.1)] transition-all duration-300">Home</a>
            <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php#phases"
                class="w-full px-4 py-3 rounded-lg text-base text-left text-white/90 hover:text-white hover:bg-[rgba(205,33,125,0.1)] transition-all duration-300">Phases</a>
            <?php if (!empty($data['isSeasonActive'])): ?>
                <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php#judges"
                    class="w-full px-4 py-3 rounded-lg text-base text-left text-white/90 hover:text-white hover:bg-[rgba(205,33,125,0.1)] transition-all duration-300">Judges</a>
            <?php endif; ?>
            <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php#prizes"
                class="w-full px-4 py-3 rounded-lg text-base text-left text-white/90 hover:text-white hover:bg-[rgba(205,33,125,0.1)] transition-all duration-300">Prizes</a>
            <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php#rules"
                class="w-full px-4 py-3 rounded-lg text-base text-left text-white/90 hover:text-white hover:bg-[rgba(205,33,125,0.1)] transition-all duration-300">Rules</a>

            <?php if (function_exists('isAuthenticated') && isAuthenticated()): ?>
                <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                    <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php?url=profile"
                        class="w-full mt-2 px-4 py-3 rounded-lg text-base text-center bg-gradient-to-r from-[#cd217d] to-[#9a288d] text-white font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_4px_12px_rgba(205,33,125,0.5)] shadow-[0_2px_8px_rgba(205,33,125,0.3)]">Dashboard</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/safezone"
                    class="w-full mt-2 px-4 py-4 rounded-lg text-base text-center bg-gradient-to-r from-[#cd217d] to-[#9a288d] text-white font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_4px_12px_rgba(205,33,125,0.5)] shadow-[0_2px_8px_rgba(205,33,125,0.3)]">Sign
                    In</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['uid']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/dashboard"
                    class="w-full mt-2 px-4 py-4 rounded-lg text-base text-center bg-gradient-to-r from-[#cd217d] to-[#9a288d] text-white font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_4px_12px_rgba(205,33,125,0.5)] shadow-[0_2px_8px_rgba(205,33,125,0.3)]">Dashboard</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>



    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function () {
            // ... (Existing mobile menu code) ...
            const navToggle = document.getElementById('nav-toggle-btn');
            const navMenu = document.getElementById('nav-menu');

            if (!navToggle || !navMenu) {
                console.error('Navigation elements not found');
            } else {
                function openMenu() {
                    navToggle.classList.add('active');
                    navToggle.setAttribute('aria-expanded', 'true');
                    navMenu.setAttribute('aria-hidden', 'false');
                    navMenu.classList.remove('translate-x-full', 'invisible', 'opacity-0');
                    navMenu.classList.add('translate-x-0', 'visible', 'opacity-100');
                    document.body.style.overflow = 'hidden';
                }

                function closeMenu() {
                    navToggle.classList.remove('active');
                    navToggle.setAttribute('aria-expanded', 'false');
                    navMenu.setAttribute('aria-hidden', 'true');
                    navMenu.classList.add('translate-x-full', 'invisible', 'opacity-0');
                    navMenu.classList.remove('translate-x-0', 'visible', 'opacity-100');
                    document.body.style.overflow = '';
                }

                // Toggle menu on button click
                navToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isOpen = navMenu.classList.contains('translate-x-0');

                    if (isOpen) {
                        closeMenu();
                    } else {
                        openMenu();
                    }
                });

                // Close menu when clicking outside
                document.addEventListener('click', function (event) {
                    const isOpen = navMenu.classList.contains('translate-x-0');
                    if (!isOpen) return;

                    const isClickInside = navMenu.contains(event.target) || navToggle.contains(event.target);
                    if (!isClickInside) {
                        closeMenu();
                    }
                });

                // Close menu when clicking on a link
                const navLinks = navMenu.querySelectorAll('a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function () {
                        closeMenu();
                    });
                });

                // Close menu on escape key
                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && navMenu.classList.contains('translate-x-0')) {
                        closeMenu();
                    }
                });
            }
        });
    </script>