<main class="main-content">

    <?php
    $userProfile = $data['userProfile'] ?? [];
    $displayName = '';
    if (!empty($userProfile['fname']) || !empty($userProfile['lname'])) {
        $displayName = trim(($userProfile['fname'] ?? '') . ' ' . ($userProfile['lname'] ?? ''));
    } else {
        $displayName = $userProfile['username'] ?? 'User';
    }
    ?>

    <div class="profile-layout">
        <!-- Sidebar -->
        <!-- Sidebar -->
        <?php require_once APPROOT . '/views/partials/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="profile-main">
            <!-- Top Header -->
            <header class="dashboard-top-bar">
                <h1 class="dashboard-page-title"><?= $data['page_title'] ?? 'Vote' ?></h1>

                <div class="dashboard-top-actions">
                    <!-- Search Bar -->
                    <div class="nav-search-form" style="margin-right: 15px; margin-left: 0;">
                        <input type="text" id="contestantSearch" class="nav-search-input"
                            placeholder="Search contestant..." style="width: 200px;">
                        <button class="nav-search-button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                    </div>


                    <div class="user-id-badge">
                        <span class="user-id-number">#<?= htmlspecialchars($userProfile['pernum'] ?? '') ?></span>
                        <span class="user-id-label">VERIFIED USER</span>
                    </div>
                    <?php if (isset($data['activeSeason']) && $data['activeSeason']): ?>
                        <div class="user-id-badge"
                            style="background: linear-gradient(135deg, var(--primary), var(--secondary)); border-color: var(--primary); padding: 10px 18px;">
                            <span class="user-id-number"
                                style="font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; display: block;">
                                <?= htmlspecialchars($data['activeSeason']['title']) ?>
                            </span>
                            <span class="user-id-label">VOTING SEASON</span>
                        </div>
                    <?php endif; ?>
                    <!-- Avatar Removed -->
                </div>
            </header>

            <div class="dashboard-content" style="padding: 40px;">
                <!-- Voting Section -->
                <section class="voting-section" style="padding-top: 0; min-height: auto;">
                    <div class="container" style="max-width: 100%; padding: 0;">
                        <?php if (!empty($data['voting_closed'])): ?>
                            <div class="no-contestants">
                                <div class="no-contestants-card">
                                    <h2>Voting Is Currently Closed</h2>
                                    <p>Voting for this season is not open yet. Please stay tuned to our social media channels for updates on when voting begins!</p>
                                    <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php" class="btn-submit">Back to Home</a>
                                </div>
                            </div>
                        <?php elseif (empty($data['contestants'])): ?>
                            <div class="no-contestants">
                                <div class="no-contestants-card">
                                    <h2>No Contestants Available</h2>
                                    <p>There are no approved contestants available for voting at this time.</p>
                                    <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php" class="btn-submit">Back to Home</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="contestants-grid">
                                <?php foreach ($data['contestants'] as $index => $contestant):
                                    // Note: Using legacy function calls inside view is not ideal but pragmatic for this step
                                    $votesToday = function_exists('getVotesToday') ? getVotesToday($contestant['id'], $data['userID'], $data['userIP']) : 0;
                                    $canVote = $votesToday < 1;
                                    $votesRemaining = 1 - $votesToday;
                                    $voteCount = (int) $contestant['vote_count'];

                                    // Determine video source
                                    $videoUrl = '';
                                    $videoType = '';
                                    if (!empty($contestant['video_file'])) {
                                        $videoUrl = $contestant['video_file']; // Assumes relative path stored or needs URLROOT prefix?
                                        // Usually stored as uploads/videos/filename.ext. Check if it needs URLROOT.
                                        // If stored path is 'uploads/videos/...', we probably need URLROOT . '/' . path
                                        if (strpos($videoUrl, 'http') === 0) {
                                            // Absolute URL (e.g. S3)
                                        } else {
                                            $videoUrl = defined('URLROOT') ? URLROOT . '/' . $videoUrl : $videoUrl;
                                        }
                                        $videoType = 'file';
                                    } elseif (!empty($contestant['vlink'])) {
                                        $videoUrl = $contestant['vlink'];
                                        $videoType = 'link';
                                    }
                                    $profilePhotoUrl = !empty($contestant['profile_photo'])
                                        ? (strpos($contestant['profile_photo'], 'http') === 0 ? $contestant['profile_photo'] : (defined('URLROOT') ? URLROOT . '/' : '') . $contestant['profile_photo'])
                                        : null;
                                    $videoUrlForModal = $videoUrl ?: ($contestant['vlink'] ?? '');
                                    if ($videoUrlForModal && strpos($videoUrlForModal, 'http') !== 0) {
                                        $videoUrlForModal = (defined('URLROOT') ? URLROOT . '/' : '') . $videoUrlForModal;
                                    }
                                    $contestantData = [
                                        'id' => $contestant['id'],
                                        'aname' => $contestant['aname'] ?? '',
                                        'title' => $contestant['title'] ?? '',
                                        'description' => $contestant['description'] ?? '',
                                        'country' => $contestant['country_name'] ?? $contestant['country'] ?? '',
                                        'province' => $contestant['province'] ?? '',
                                        'category_name' => $contestant['category_name'] ?? '',
                                        'date' => $contestant['date'] ?? '',
                                        'vote_count' => $voteCount,
                                        'profile_photo' => $profilePhotoUrl ?? '',
                                        'video_url' => $videoUrlForModal,
                                        'vlink' => $contestant['vlink'] ?? '',
                                        'video_file' => $contestant['video_file'] ?? ''
                                    ];
                                    ?>
                                    <div class="contestant-card" data-id="<?= $contestant['id'] ?>"
                                        data-contestant="<?= htmlspecialchars(json_encode($contestantData), ENT_QUOTES, 'UTF-8') ?>"
                                        style="background: rgba(30, 30, 40, 0.8); border-radius: 16px; padding: 30px; border: 1px solid rgba(255,255,255,0.1);">

                                        <div class="contestant-rank" style="position: absolute; top: 15px; right: 15px;">
                                            <span class="rank-number"
                                                style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 8px 14px; border-radius: 50%; font-weight: 700; font-size: 14px;">#<?= $index + 1 ?></span>
                                        </div>

                                        <?php if ($profilePhotoUrl): ?>
                                        <div class="contestant-photo" style="margin-bottom: 16px; text-align: center;">
                                            <img src="<?= htmlspecialchars($profilePhotoUrl) ?>" alt="<?= htmlspecialchars($contestant['aname']) ?>"
                                                style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(205, 33, 125, 0.5);">
                                        </div>
                                        <?php endif; ?>

                                        <div class="contestant-info" style="margin-bottom: 20px;">
                                            <h3 class="contestant-name"
                                                style="font-size: 22px; font-weight: 700; color: #fff; margin-bottom: 8px;">
                                                <?= htmlspecialchars($contestant['aname']) ?>
                                            </h3>
                                            <p class="contestant-country" style="color: #888; font-size: 14px;">
                                                <?= htmlspecialchars($contestant['country_name'] ?? $contestant['country']) ?>
                                            </p>
                                        </div>

                                        <div class="contestant-votes"
                                            style="background: rgba(0,0,0,0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; text-align: center;">
                                            <div class="vote-count-display">
                                                <span class="vote-number" id="votes-<?= $contestant['id'] ?>"
                                                    style="font-size: 36px; font-weight: 700; color: var(--primary); display: block;"><?= number_format($voteCount) ?></span>
                                                <span class="vote-label"
                                                    style="color: #888; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;"><?= $voteCount == 1 ? 'Vote' : 'Votes' ?></span>
                                            </div>
                                        </div>

                                        <?php if ($videoUrl): ?>
                                            <div class="contestant-video" style="margin-bottom: 15px;">
                                                <button
                                                    onclick="openVideoModal('<?= htmlspecialchars($videoUrl) ?>', '<?= $videoType ?>')"
                                                    class="btn-video"
                                                    style="display: block; width: 100%; padding: 14px; background: transparent; border: 2px solid rgba(255,255,255,0.2); border-radius: 12px; color: #fff; font-weight: 600; cursor: pointer; text-align: center; font-size: 15px; transition: all 0.3s ease;">
                                                    Watch Video
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <div style="margin-bottom: 15px;">
                                            <button type="button" onclick="showContestantDetails(this)"
                                                style="display: block; width: 100%; padding: 12px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-radius: 10px; color: rgba(255,255,255,0.9); font-weight: 600; cursor: pointer; text-align: center; font-size: 14px; transition: all 0.2s ease;">
                                                View Full Details
                                            </button>
                                        </div>

                                        <div class="contestant-action">
                                            <?php if ($canVote): ?>
                                                <button class="btn-vote"
                                                    onclick="castVote(<?= $contestant['id'] ?>, '<?= htmlspecialchars($contestant['aname'], ENT_QUOTES) ?>')"
                                                    style="width: 100%; padding: 16px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border: none; border-radius: 12px; color: white; font-weight: 700; font-size: 16px; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; transition: all 0.3s ease;">
                                                    Vote Now
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-vote btn-vote-disabled" disabled
                                                    style="width: 100%; padding: 16px; background: linear-gradient(135deg, var(--primary), var(--secondary)); opacity: 0.5; border: none; border-radius: 12px; color: white; font-weight: 700; font-size: 16px; cursor: not-allowed; text-transform: uppercase; letter-spacing: 1px;">
                                                    Voted Already
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>

<!-- Participant Details Modal -->
<div id="contestantDetailModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 10000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #1a1a24; border-radius: 16px; max-width: 560px; width: 100%; max-height: 90vh; overflow-y: auto; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 25px 50px rgba(0,0,0,0.5);">
        <div style="padding: 24px; position: relative;">
            <button type="button" onclick="closeContestantDetails()" style="position: absolute; top: 16px; right: 16px; background: rgba(255,255,255,0.1); border: none; color: #fff; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; font-size: 20px; line-height: 1;">&times;</button>
            <div id="contestantDetailContent"></div>
        </div>
    </div>
</div>

<!-- Video Modal -->
<div id="videoModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 10000; justify-content: center; align-items: center;">
    <div
        style="position: relative; width: 90%; max-width: 800px; background: #000; border-radius: 12px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.5);">
        <button onclick="closeVideoModal()"
            style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.5); border: none; color: white; width: 30px; height: 30px; border-radius: 50%; font-size: 20px; cursor: pointer; z-index: 10;">&times;</button>
        <div id="videoContainer" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
            <!-- Content injected via JS -->
        </div>
    </div>
</div>

<script>
    function openVideoModal(url, type) {
        var container = document.getElementById('videoContainer');
        var modal = document.getElementById('videoModal');

        container.innerHTML = '';

        if (type === 'file') {
            var video = document.createElement('video');
            video.src = url;
            video.controls = true;
            video.autoplay = true;
            video.style.position = 'absolute';
            video.style.top = '0';
            video.style.left = '0';
            video.style.width = '100%';
            video.style.height = '100%';
            container.appendChild(video);
        } else {
            // Assume YouTube/Embed or direct link
            // Simple check for YouTube to use embed
            var embedUrl = url;
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                var videoId = '';
                if (url.includes('youtube.com/watch?v=')) {
                    videoId = url.split('v=')[1];
                    var ampersandPosition = videoId.indexOf('&');
                    if (ampersandPosition != -1) {
                        videoId = videoId.substring(0, ampersandPosition);
                    }
                } else if (url.includes('youtu.be/')) {
                    videoId = url.split('youtu.be/')[1];
                }
                if (videoId) {
                    embedUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1';
                }
            }

            var iframe = document.createElement('iframe');
            iframe.src = embedUrl;
            iframe.frameBorder = '0';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
            iframe.allowFullscreen = true;
            iframe.style.position = 'absolute';
            iframe.style.top = '0';
            iframe.style.left = '0';
            iframe.style.width = '100%';
            iframe.style.height = '100%';
            container.appendChild(iframe);
        }

        modal.style.display = 'flex';
    }

    function closeVideoModal() {
        var modal = document.getElementById('videoModal');
        var container = document.getElementById('videoContainer');
        modal.style.display = 'none';
        container.innerHTML = ''; // Stop video
    }

    // Search Filter
    document.getElementById('contestantSearch').addEventListener('input', function (e) {
        var term = e.target.value.toLowerCase();
        var cards = document.querySelectorAll('.contestant-card');
        var hasVisible = false;

        cards.forEach(function (card) {
            var name = card.querySelector('.contestant-name').textContent.toLowerCase();
            var country = card.querySelector('.contestant-country').textContent.toLowerCase();

            if (name.includes(term) || country.includes(term)) {
                card.style.display = '';
                hasVisible = true;
            } else {
                card.style.display = 'none';
            }
        });

        // Toggle no results message if needed (optional implementation)
    });

    // Close modal on outside click
    document.getElementById('videoModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeVideoModal();
        }
    });
    document.getElementById('contestantDetailModal').addEventListener('click', function (e) {
        if (e.target === this) closeContestantDetails();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeContestantDetails();
    });

    function escapeHtml(str) {
        if (!str) return '';
        var d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function showContestantDetails(btnEl) {
        var card = btnEl.closest('.contestant-card');
        if (!card) return;
        var dataStr = card.getAttribute('data-contestant');
        if (!dataStr) return;
        var data;
        try {
            data = JSON.parse(dataStr);
        } catch (e) {
            return;
        }
        var content = document.getElementById('contestantDetailContent');
        var html = '';
        if (data.profile_photo) {
            html += '<div style="text-align: center; margin-bottom: 20px;"><img src="' + escapeHtml(data.profile_photo) + '" alt="" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(205, 33, 125, 0.5);"></div>';
        }
        html += '<h2 style="font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: 8px;">' + escapeHtml(data.title || data.aname) + '</h2>';
        html += '<div style="color: rgba(255,255,255,0.85); font-size: 14px; line-height: 1.7;">';
        html += '<p style="margin: 8px 0;"><strong>Artist:</strong> ' + escapeHtml(data.aname) + '</p>';
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
        document.getElementById('contestantDetailModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeContestantDetails() {
        document.getElementById('contestantDetailModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    function castVote(nominationId, contestantName) {
        if (!confirm('Cast your vote for ' + contestantName + '?')) {
            return;
        }

        var $card = $('.contestant-card[data-id="' + nominationId + '"]');
        var $button = $card.find('.btn-vote');
        var $voteCount = $('#votes-' + nominationId);

        $button.prop('disabled', true);
        var originalHtml = $button.html();
        $button.html('Casting Vote...');

        // AJAX to legacy handler for now
        $.ajax({
            url: 'ajax.vote.php',
            type: 'POST',
            data: {
                nomination_id: nominationId
            },
            success: function (response) {
                if (response.success) {
                    var currentVotes = parseInt($voteCount.text().replace(/,/g, '')) || 0;
                    var newVotes = currentVotes + 1;
                    $voteCount.text(newVotes.toLocaleString());

                    var votesRemaining = response.votes_remaining || 0;
                    if (votesRemaining > 0) {
                        $button.prop('disabled', false);
                        $button.html('Vote Now');
                    } else {
                        $button.removeClass('btn-vote').addClass('btn-vote-disabled');
                        $button.css('background', 'rgba(255,255,255,0.05)');
                        $button.css('opacity', '0.5');
                        $button.html('Voted Already');
                        $button.prop('disabled', true);
                    }

                    showVoteMessage('success', '✓ Your vote for ' + contestantName + ' has been cast! ' + (votesRemaining > 0 ? '(' + votesRemaining + ' votes remaining today)' : ''));

                    var $voteLabel = $card.find('.vote-label');
                    $voteLabel.text(newVotes == 1 ? 'Vote' : 'Votes');

                    updateVotingStats();
                } else {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                        return;
                    }

                    $button.prop('disabled', false);
                    $button.html(originalHtml);

                    // Show debug info if available
                    var errorMsg = response.message || 'Failed to cast vote. Please try again.';
                    if (response.debug) {
                        errorMsg += ' (Debug: ' + response.debug + ')';
                    }
                    showVoteMessage('error', errorMsg);
                }
            },
            error: function (xhr, status, error) {
                $button.prop('disabled', false);
                $button.html(originalHtml);

                // Try to parse error response
                var errorMsg = 'An error occurred. Please try again.';
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMsg = response.message;
                        if (response.debug) {
                            errorMsg += ' (Debug: ' + response.debug + ')';
                        }
                    }
                } catch (e) {
                    errorMsg += ' (Status: ' + status + ', Error: ' + error + ')';
                }
                showVoteMessage('error', errorMsg);
            }
        });
    }

    function showVoteMessage(type, message) {
        var $alert = $('<div class="vote-alert vote-alert-' + type + '">' + message + '</div>');
        $('body').append($alert);

        setTimeout(function () {
            $alert.addClass('show');
        }, 100);

        setTimeout(function () {
            $alert.removeClass('show');
            setTimeout(function () {
                $alert.remove();
            }, 300);
        }, 3000);
    }

    function updateVotingStats() {
        $.ajax({
            url: 'ajax.vote.php',
            type: 'GET',
            data: { action: 'get_stats' },
            success: function (response) {
                if (response.success) {
                    $('.stat-item .stat-number').eq(1).text(response.total_votes.toLocaleString());
                }
            }
        });
    }

    // Auto-refresh vote counts every 30 seconds
    setInterval(function () {
        $('.contestant-card').each(function () {
            var nominationId = $(this).data('id');
            var $voteCount = $('#votes-' + nominationId);

            $.ajax({
                url: 'ajax.vote.php',
                type: 'GET',
                data: { action: 'get_vote_count', nomination_id: nominationId },
                success: function (response) {
                    if (response.success) {
                        var currentVotes = parseInt($voteCount.text().replace(/,/g, '')) || 0;
                        var newVotes = response.vote_count;

                        if (newVotes != currentVotes) {
                            $voteCount.text(newVotes.toLocaleString());
                            var $voteLabel = $('.contestant-card[data-id="' + nominationId + '"]').find('.vote-label');
                            $voteLabel.text(newVotes == 1 ? 'Vote' : 'Votes');
                        }
                    }
                }
            });
        });
    }, 30000);
</script>