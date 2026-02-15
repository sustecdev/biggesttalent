<footer class="modern-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <?php if (empty($data['hide_footer_logo'])): ?>
                        <div class="footer-logo">
                            <img src="<?= URLROOT ?>/images/footer_logo.png" alt="Biggest Talent Africa"
                                style="height: 40px; width: auto;">
                        </div>
                    <?php endif; ?>
                    <p class="footer-tagline">Africa's premier talent competition celebrating the continent's most
                        exceptional performers.
                    </p>
                </div>

                <div class="footer-links">
                    <div class="footer-column">
                        <h4 class="footer-heading">Platform</h4>
                        <ul class="footer-list">
                            <li><a href="<?= URLROOT ?>">Home</a></li>
                            <li><a href="#phases">Phases</a></li>
                            <li><a href="#judges">Judges</a></li>
                            <li><a href="#prizes">Prizes</a></li>
                            <li><a href="#rules">Rules</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4 footer-col">
                        <h5 class="footer-title">Quick Links</h5>
                        <ul class="footer-list">
                            <li><a href="<?= URLROOT ?>/index.php?url=vote">Vote</a></li>
                            <li><a href="<?= URLROOT ?>/index.php?url=nominate">Nomination</a></li>
                            <li><a href="<?= URLROOT ?>/safezone">Sign In</a></li>
                            <li><a href="<?= URLROOT ?>/safezone">Login</a></li>
                        </ul>
                    </div>

                    <div class="footer-column">
                        <h4 class="footer-heading">Connect</h4>
                        <ul class="footer-list">
                            <li><a href="https://www.facebook.com/share/17xgKxSeBS/?mibextid=wwXIfr" target="_blank" rel="noopener noreferrer">Facebook</a></li>
                            <li><a href="https://www.tiktok.com/@biggesttalentafrica?_r=1&_t=ZS-93wj3O7I8ii" target="_blank" rel="noopener noreferrer">TikTok</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>©
                    <?= date('Y') ?> Biggest Talent Africa. All rights reserved.
                </p>
                <div class="footer-legal">
                    <a href="#">Privacy Policy</a>
                    <span>|</span>
                    <a href="<?= URLROOT ?>/index.php?url=terms">Terms and Conditions</a>
                </div>
            </div>
        </div>
    </footer>

<style>
    /* Footer Styles */
    .modern-footer {
        background: #050505;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        padding: 4rem 0 2rem;
        margin-top: auto;
        font-family: 'Outfit', sans-serif;
    }

    .footer-content {
        display: grid;
        grid-template-columns: 2fr 3fr;
        gap: 4rem;
        margin-bottom: 3rem;
    }

    .footer-brand {
        max-width: 400px;
    }

    .footer-logo {
        margin-bottom: 1.5rem;
    }

    .footer-tagline {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.95rem;
        line-height: 1.7;
        font-weight: 300;
    }

    .footer-links {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }

    .footer-column {
        min-width: 0;
    }

    .footer-heading,
    .footer-title {
        color: #FFFFFF;
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        letter-spacing: 0.02em;
    }

    .footer-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-list li {
        margin-bottom: 0.75rem;
    }

    .footer-list a {
        color: rgba(255, 255, 255, 0.5);
        text-decoration: none;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        font-weight: 400;
    }

    .footer-list a:hover {
        color: #aa843f;
        padding-left: 4px;
    }

    .footer-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        flex-wrap: wrap;
        gap: 1rem;
    }

    .footer-bottom p {
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.85rem;
        margin: 0;
    }

    .footer-legal {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .footer-legal a {
        color: rgba(255, 255, 255, 0.4);
        text-decoration: none;
        font-size: 0.85rem;
        transition: color 0.3s ease;
    }

    .footer-legal a:hover {
        color: #aa843f;
    }

    .footer-legal span {
        color: rgba(255, 255, 255, 0.2);
    }

    @media (max-width: 992px) {
        .footer-content {
            grid-template-columns: 1fr;
            gap: 3rem;
        }

        .footer-links {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .footer-links {
            grid-template-columns: 1fr;
            gap: 2.5rem;
        }

        .footer-bottom {
            flex-direction: column;
            text-align: center;
            gap: 1.5rem;
        }
    }
</style>

<script>
    // Smooth scroll for anchor links (menu toggle handled in header)
    document.addEventListener('DOMContentLoaded', function () {
        const navMenu = document.getElementById('nav-menu');

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href.length > 1) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        // Close mobile menu if open
                        if (navMenu && navMenu.classList.contains('active')) {
                            const navToggle = document.getElementById('nav-toggle-btn');
                            if (navToggle) {
                                navToggle.classList.remove('active');
                            }
                            navMenu.classList.remove('active');
                            document.body.style.overflow = '';
                        }
                    }
                }
            });
        });
    });
    });

    function showFeatureClosedModal(feature) {
        // Set content based on feature
        const title = feature + ' Closed';
        const message = 'We apologize, but <strong>' + feature + '</strong> are currently closed for this season.<br><br>Please stay tuned to our social media channels for updates on when the next phase begins!';

        document.getElementById('featureClosedTitle').textContent = title;
        document.getElementById('featureClosedBody').innerHTML = message;

        // Show modal using Bootstrap if available, or fallback?
        // The public site seems to assume Bootstrap or custom JS?
        // Header includes jQuery. Let's check if Bootstrap JS is loaded.
        // Header doesn't seem to load Bootstrap JS explicitely in the viewed lines?
        // Wait, admin header did. Public header? 
        // Let's assume standard Bootstrap modal might not work if BS JS isn't there.
        // But the user mentioned "Beautiful Modal".
        // I will use a custom simple modal implementation here to be safe and dependency-free (except jQuery which is present).

        $('#featureClosedModal').fadeIn(300);
        $('body').css('overflow', 'hidden');
    }

    function closeFeatureModal() {
        $('#featureClosedModal').fadeOut(300);
        $('body').css('overflow', '');
    }

    // Close on click outside
    $(document).on('click', '#featureClosedModal', function (e) {
        if (e.target === this) {
            closeFeatureModal();
        }
    });
</script>

<!-- Custom Feature Closed Modal -->
<div id="featureClosedModal"
    style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); align-items: center; justify-content: center;">
    <div
        style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 2rem; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.5); position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <div
            style="width: 60px; height: 60px; background: rgba(205, 33, 125, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                stroke="#cd217d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        </div>

        <h3 id="featureClosedTitle"
            style="color: #fff; font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; font-family: 'Outfit', sans-serif;">
            Feature Closed</h3>

        <p id="featureClosedBody" style="color: rgba(255,255,255,0.7); line-height: 1.6; margin-bottom: 2rem;">
            This feature is currently unavailable.
        </p>

        <button onclick="closeFeatureModal()"
            style="background: linear-gradient(90deg, #cd217d 0%, #9a288d 100%); border: none; padding: 12px 30px; border-radius: 8px; color: white; font-weight: 600; cursor: pointer; transition: transform 0.2s; width: 100%;">
            Understood
        </button>
    </div>
</div>
</body>

</html>