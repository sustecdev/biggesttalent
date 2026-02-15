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
        <?php require_once APPROOT . '/views/partials/user_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="profile-main">
            <!-- Top Header -->
            <header class="dashboard-top-bar">
                <h1 class="dashboard-page-title"><?= $data['page_title'] ?? 'Make a Nomination' ?></h1>

                <div class="dashboard-top-actions">
                    <a href="<?= defined('URLROOT') ? URLROOT . '/index.php?url=profile' : 'index.php?url=profile' ?>"
                        class="btn-safezone btn-safezone-secondary"
                        style="font-size: 13px; padding: 8px 16px; margin-right: 10px;">Back to Dashboard</a>

                    <div class="user-id-badge">
                        <span class="user-id-number">#<?= htmlspecialchars($userProfile['pernum'] ?? '') ?></span>
                        <span class="user-id-label">VERIFIED USER</span>
                    </div>
                    <!-- Avatar Removed -->
                </div>
            </header>

            <div class="dashboard-content" style="padding: 40px;">
                <!-- Nomination Form Section -->
                <section class="nomination-form-section" style="padding: 0;">
                    <div class="container" style="max-width: 100%; padding: 0;">
                        <?php if (!empty($data['nominations_closed'])): ?>
                            <div class="no-contestants" style="max-width: 600px; margin: 0 auto;">
                                <div class="no-contestants-card">
                                    <h2>Nominations Are Currently Closed</h2>
                                    <?php if (!empty($data['voting_open'])): ?>
                                        <p>Nominations for this season have closed. Voting has started! Cast your vote for your favorite talent and help them advance.</p>
                                        <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php?url=vote" class="btn-submit">Go to Voting</a>
                                    <?php else: ?>
                                        <p>Nominations for this season are not open yet. Please stay tuned to our social media channels for updates on when you can submit your talent!</p>
                                        <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php" class="btn-submit">Back to Home</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php elseif (!empty($data['already_nominated'])): ?>
                            <div class="no-contestants" style="max-width: 600px; margin: 0 auto;">
                                <div class="no-contestants-card">
                                    <h2>You've Already Nominated</h2>
                                    <p>You can only nominate one person per account. You have already submitted your nomination.</p>
                                    <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php?url=profile#nominations" class="btn-submit">View My Nominations</a>
                                    <a href="<?= defined('URLROOT') ? URLROOT : '' ?>/index.php?url=vote" class="btn-submit" style="margin-left: 10px;">Go to Voting</a>
                                </div>
                            </div>
                        <?php else: ?>
                        <div class="nomination-form-wrapper" style="box-shadow: none; padding: 0;">
                            <div class="nomination-form-card">
                                <div class="form-header"
                                    style="margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid var(--border);">
                                    <h2 class="form-title"
                                        style="font-size: 24px; font-weight: 800; color: var(--text-main); margin: 0 0 8px 0;">
                                        Nomination Details</h2>
                                    <p class="form-description"
                                        style="font-size: 14px; color: var(--text-muted); margin: 0;">Showcase amazing
                                        talent to the world</p>
                                </div>

                                <form id="nominationForm" class="nomination-form" enctype="multipart/form-data">
                                    <!-- Performance Info Section -->
                                    <div style="margin-bottom: 32px;">
                                        <h3
                                            style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 20px;">
                                            Performance Information</h3>

                                        <!-- Active Season Display -->
                                        <?php
                                        $activeSeason = $data['activeSeason'] ?? null;
                                        $hasSeason = $activeSeason && !empty($activeSeason['id']);
                                        ?>
                                        <?php if ($hasSeason): ?>
                                            <div class="form-group">
                                                <label for="season_display" class="form-label">
                                                    Competition Season
                                                </label>
                                                <input type="text" class="form-input" id="season_display"
                                                    value="<?= htmlspecialchars($activeSeason['title'] ?? '') ?> (<?= date('Y', strtotime($activeSeason['start_date'] ?? 'now')) ?>)"
                                                    readonly
                                                    style="background-color: #f0f9ff; border-color: #0ea5e9; color: #0c4a6e; font-weight: 600; cursor: not-allowed;">
                                                <input type="hidden" name="season_id"
                                                    value="<?= (int) $activeSeason['id'] ?>">
                                            </div>
                                        <?php else: ?>
                                            <?php
                                            // Try getActiveSeasonSimple as fallback (e.g. from header/helper)
                                            $fallbackSeason = function_exists('getActiveSeasonSimple') ? getActiveSeasonSimple() : null;
                                            if ($fallbackSeason && !empty($fallbackSeason['id'])): ?>
                                            <div class="form-group">
                                                <label for="season_display" class="form-label">
                                                    Competition Season
                                                </label>
                                                <input type="text" class="form-input" id="season_display"
                                                    value="<?= htmlspecialchars($fallbackSeason['title'] ?? '') ?> (<?= date('Y', strtotime($fallbackSeason['start_date'] ?? 'now')) ?>)"
                                                    readonly
                                                    style="background-color: #f0f9ff; border-color: #0ea5e9; color: #0c4a6e; font-weight: 600; cursor: not-allowed;">
                                                <input type="hidden" name="season_id"
                                                    value="<?= (int) $fallbackSeason['id'] ?>">
                                            </div>
                                            <?php else: ?>
                                            <div class="form-group">
                                                <div
                                                    style="background: #fef2f2; border: 1px solid #fca5a5; padding: 12px; border-radius: 6px; color: #991b1b;">
                                                    <strong>⚠️ No Active Season</strong><br>
                                                    <small>Please contact the administrator to activate a competition
                                                        season.</small>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <div class="form-group">
                                            <label for="title" class="form-label">
                                                Performance Title <span class="required">*</span>
                                            </label>
                                            <select class="form-input form-select" id="title" name="title" required>
                                                <option value="">-- Select a performance title --</option>
                                                <option value="Solo Vocal Performance">Solo Vocal Performance</option>
                                                <option value="Group Vocal Performance">Group Vocal Performance</option>
                                                <option value="Acoustic Guitar Performance">Acoustic Guitar Performance
                                                </option>
                                                <option value="Electric Guitar Performance">Electric Guitar Performance
                                                </option>
                                                <option value="Piano Performance">Piano Performance</option>
                                                <option value="Drum Performance">Drum Performance</option>
                                                <option value="Bass Performance">Bass Performance</option>
                                                <option value="DJ Performance">DJ Performance</option>
                                                <option value="Solo Dance Performance">Solo Dance Performance</option>
                                                <option value="Group Dance Performance">Group Dance Performance</option>
                                                <option value="Hip Hop Dance">Hip Hop Dance</option>
                                                <option value="Contemporary Dance">Contemporary Dance</option>
                                                <option value="Traditional Dance">Traditional Dance</option>
                                                <option value="Ballet Performance">Ballet Performance</option>
                                                <option value="Stand-up Comedy">Stand-up Comedy</option>
                                                <option value="Comedy Skit">Comedy Skit</option>
                                                <option value="Magic Show">Magic Show</option>
                                                <option value="Illusion Performance">Illusion Performance</option>
                                                <option value="Acting Monologue">Acting Monologue</option>
                                                <option value="Dramatic Scene">Dramatic Scene</option>
                                                <option value="Poetry Recitation">Poetry Recitation</option>
                                                <option value="Spoken Word Performance">Spoken Word Performance</option>
                                                <option value="Beatboxing Performance">Beatboxing Performance</option>
                                                <option value="Acrobatic Performance">Acrobatic Performance</option>
                                                <option value="Instrumental Solo">Instrumental Solo</option>
                                                <option value="Band Performance">Band Performance</option>
                                                <option value="Choir Performance">Choir Performance</option>
                                                <option value="Rap Performance">Rap Performance</option>
                                                <option value="Other Performance">Other Performance</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="category" class="form-label">
                                                Category <span class="required">*</span>
                                            </label>
                                            <select class="form-input form-select" id="category" name="category"
                                                required>
                                                <option value="">-- Select a category --</option>
                                                <?php if (!empty($data['categories'])): ?>
                                                    <?php foreach ($data['categories'] as $cat): ?>
                                                        <option value="<?= $cat['id'] ?>">
                                                            <?= htmlspecialchars($cat['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="1">Singing</option>
                                                    <option value="2">Dancing</option>
                                                    <option value="3">Music</option>
                                                    <option value="4">Comedy</option>
                                                    <option value="5">Magic</option>
                                                    <option value="6">Acting</option>
                                                    <option value="7">Other</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="description" class="form-label">
                                                Description
                                            </label>
                                            <textarea class="form-input" id="description" name="description" rows="4"
                                                placeholder="Describe the talent or performance (optional)"
                                                maxlength="1000"></textarea>
                                            <p class="form-hint"
                                                style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                                                Maximum 1000 characters</p>
                                        </div>
                                    </div>

                                    <!-- Location Section -->
                                    <div style="margin-bottom: 32px;">
                                        <h3
                                            style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 20px;">
                                            Location</h3>

                                        <div class="form-group">
                                            <label for="country" class="form-label">
                                                Country <span class="required">*</span>
                                            </label>
                                            <select class="form-input form-select" id="country" name="country" required
                                                aria-label="Select Country" style="width: 100%;">
                                                <option value="">-- Select a country --</option>
                                                <?php if (!empty($data['countries'])): ?>
                                                    <?php foreach ($data['countries'] as $country): ?>
                                                        <option value="<?= $country['id'] ?>"
                                                            data-iso="<?= $country['iso_code'] ?>"
                                                            data-name="<?= htmlspecialchars($country['name']) ?>">
                                                            <?= htmlspecialchars($country['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="form-group" id="province-group" style="display:none;">
                                            <label for="province" class="form-label">
                                                State/Province <span class="required"
                                                    style="color: var(--primary);">*</span>
                                            </label>
                                            <select class="form-input form-select" id="province" name="province"
                                                aria-label="Select State/Province" style="width: 100%;">
                                                <option value="">-- Select state/province --</option>
                                            </select>
                                            <p class="form-hint" id="province-loading"
                                                style="display:none; margin-top: 6px;">
                                                Loading states/provinces...
                                            </p>
                                        </div>

                                        <div class="form-group">
                                            <label for="aname" class="form-label">
                                                Contestant Name <span class="required"
                                                    style="color: var(--primary);">*</span>
                                            </label>
                                            <input type="text" class="form-input" id="aname" name="aname"
                                                placeholder="Enter the participant's full name" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="gender" class="form-label">
                                                Gender <span class="required"
                                                    style="color: var(--primary);">*</span>
                                            </label>
                                            <select class="form-input form-select" id="gender" name="gender" required>
                                                <option value="">-- Select gender --</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                <option value="Non-binary">Non-binary</option>
                                                <option value="Other">Other</option>
                                                <option value="Prefer not to say">Prefer not to say</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="age" class="form-label">
                                                Age <span class="required"
                                                    style="color: var(--primary);">*</span>
                                            </label>
                                            <input type="number" class="form-input" id="age" name="age"
                                                placeholder="Enter age (years)" min="1" max="120" required>
                                            <p class="form-hint"
                                                style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                                                Age of the nominated contestant</p>
                                        </div>

                                        <div class="form-group">
                                            <label for="age" class="form-label">
                                                Age <span class="required"
                                                    style="color: var(--primary);">*</span>
                                            </label>
                                            <input type="number" class="form-input" id="age" name="age"
                                                placeholder="Enter age (years)" min="1" max="120" required>
                                            <p class="form-hint"
                                                style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                                                Age of the nominated contestant</p>
                                        </div>

                                        <div class="form-group">
                                            <label for="nominee_email" class="form-label">
                                                Nominee's Email
                                            </label>
                                            <input type="email" class="form-input" id="nominee_email" name="nominee_email"
                                                placeholder="contestant@example.com">
                                            <p class="form-hint"
                                                style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                                                Contact email of the person being nominated (optional)</p>
                                        </div>

                                        <div class="form-group">
                                            <label for="nominee_phone" class="form-label">
                                                Nominee's Phone Number
                                            </label>
                                            <input type="tel" class="form-input" id="nominee_phone" name="nominee_phone"
                                                placeholder="+260 97 123 4567">
                                            <p class="form-hint"
                                                style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                                                Contact phone of the person being nominated (optional)</p>
                                        </div>

                                        <div class="form-group">
                                            <label for="profile_photo" class="form-label">
                                                Profile Picture
                                            </label>
                                            <input type="file" class="form-input" id="profile_photo" name="profile_photo"
                                                accept="image/jpeg,image/png,image/webp,image/gif">
                                            <p class="form-hint"
                                                style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                                                Optional. Shows on your voting card. Max 2MB. JPG, PNG, WebP or GIF.</p>
                                            <div id="profile-preview" style="display:none; margin-top: 12px;">
                                                <img id="profile-preview-img" src="" alt="Preview" style="max-width: 120px; max-height: 120px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border);">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Video Section -->

                                    <div>
                                        <h3
                                            style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 20px;">
                                            Video Submission</h3>

                                        <div class="form-group">
                                            <label class="form-label">
                                                Upload Method <span class="required"
                                                    style="color: var(--primary);">*</span>
                                            </label>
                                            <div class="video-upload-options">
                                                <div class="upload-option" style="flex: 1; position: relative;">
                                                    <input type="radio" id="upload_type_link" name="upload_type"
                                                        value="link" checked
                                                        style="position: absolute; opacity: 0; width: 0; height: 0;">
                                                    <label for="upload_type_link"
                                                        style="display: flex; align-items: center; justify-content: center; padding: 12px 16px; background: var(--bg); border: 2px solid var(--primary); border-radius: 8px; cursor: pointer; transition: all 0.2s; font-size: 14px; font-weight: 500;">
                                                        Video Link
                                                    </label>
                                                </div>
                                                <div class="upload-option" style="flex: 1; position: relative;">
                                                    <input type="radio" id="upload_type_file" name="upload_type"
                                                        value="file"
                                                        style="position: absolute; opacity: 0; width: 0; height: 0;">
                                                    <label for="upload_type_file"
                                                        style="display: flex; align-items: center; justify-content: center; padding: 12px 16px; background: var(--bg); border: 2px solid var(--border); border-radius: 8px; cursor: pointer; transition: all 0.2s; font-size: 14px; font-weight: 500;">
                                                        Upload File
                                                    </label>
                                                </div>
                                            </div>

                                            <div id="video-link-section" class="video-input-section">
                                                <input type="url" class="form-input" id="vlink" name="vlink"
                                                    placeholder="https://youtube.com/watch?v=..." <p class="form-hint"
                                                    style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                                                Share a link to a video showcasing the talent (YouTube, Vimeo, etc.)
                                                </p>
                                            </div>

                                            <div id="video-file-section" class="video-input-section"
                                                style="display:none;">
                                                <input type="file" class="form-input" id="video_file" name="video_file"
                                                    accept="video/mp4,video/quicktime">
                                                <p class="form-hint"
                                                    style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">
                                                    Accepted formats: MP4, H.264. Maximum file size: 100MB. Duration:
                                                    30-120 seconds.</p>
                                                <div id="video-preview"
                                                    style="display:none; margin-top: 16px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border);">
                                                    <video id="preview-player" controls
                                                        style="max-width: 100%; max-height: 300px; display: block;"></video>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-alerts" style="margin-bottom: 24px;">
                                        <div class="alert alert-error" id="err"
                                            style="display:none; padding: 12px 16px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; color: #ef4444; font-size: 14px;">
                                        </div>
                                        <div class="alert alert-success" id="suc"
                                            style="display:none; padding: 12px 16px; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 8px; color: #22c55e; font-size: 14px;">
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <button type="button" class="btn-submit" id="btnsubmit" onclick="adda();">
                                            Submit Nomination
                                        </button>
                                        <button type="button" class="btn-submit btn-submit-loading" id="btnsubmit2"
                                            style="display:none;" disabled>
                                            <span
                                                style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                                <span class="btn-spinner"
                                                    style="width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite;"></span>
                                                Submitting...
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom Select2 styling to match form theme */
    .select2-container--default .select2-selection--single {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border);
        border-radius: 8px;
        height: 44px;
        padding: 8px 16px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text-main);
        line-height: 28px;
        padding: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px;
    }

    .select2-dropdown {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 8px;
    }

    .select2-container--default .select2-results__option {
        color: var(--text-main);
        padding: 8px 12px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: rgba(205, 33, 125, 0.2);
        color: var(--text-main);
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--border);
        color: var(--text-main);
        border-radius: 6px;
        padding: 8px;
    }

    .select2-container--default .select2-selection--single:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(205, 33, 125, 0.1);
    }
</style>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Initialize Select2 for country dropdown
    $(document).ready(function () {
        // Initialize Select2
        $('#country').select2({
            placeholder: '-- Search for a country --',
            allowClear: true,
            width: '100%'
        });

        $('#province').select2({
            placeholder: '-- Select state/province --',
            allowClear: true,
            width: '100%'
        });

        // Handle country selection and load states/provinces
        // Use both 'change' and 'select2:select' events for better compatibility
        $('#country').on('change select2:select', function () {
            const countryId = $(this).val();
            const provinceGroup = $('#province-group');
            const provinceSelect = $('#province');
            const provinceLoading = $('#province-loading');

            console.log('Country selected:', countryId); // Debug log

            if (countryId) {
                // Show loading indicator
                provinceLoading.show();
                provinceSelect.prop('disabled', true);

                // Fetch states/provinces for selected country
                $.ajax({
                    url: '<?= URLROOT ?>/public/ajax.get_states.php',
                    type: 'GET',
                    data: { country_id: countryId },
                    dataType: 'json',
                    success: function (response) {
                        console.log('AJAX response:', response); // Debug log
                        provinceLoading.hide();
                        provinceSelect.prop('disabled', false);

                        if (response.success && response.states.length > 0) {
                            // Clear existing options
                            provinceSelect.empty().append('<option value="">-- Select state/province --</option>');

                            // Add new options
                            response.states.forEach(function (state) {
                                provinceSelect.append(
                                    $('<option></option>').val(state.name).text(state.name)
                                );
                            });

                            // Refresh Select2 and show province dropdown
                            provinceSelect.select2('destroy').select2({
                                placeholder: '-- Select state/province --',
                                allowClear: true,
                                width: '100%'
                            });
                            provinceGroup.show();
                            provinceSelect.prop('required', true);
                        } else {
                            // No states/provinces available for this country
                            provinceSelect.empty().append('<option value="">No states/provinces available</option>');
                            provinceGroup.hide();
                            provinceSelect.prop('required', false);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', status, error, xhr.responseText); // Debug log
                        provinceLoading.hide();
                        provinceSelect.prop('disabled', false);
                        provinceGroup.hide();
                        provinceSelect.prop('required', false);
                    }
                });
            } else {
                // No country selected, hide province dropdown
                provinceGroup.hide();
                provinceSelect.prop('required', false);
                provinceSelect.val('');
            }
        });
    });

    // Handle upload type toggle with visual feedback
    $('input[name="upload_type"]').change(function () {
        // Update radio button styles
        $('input[name="upload_type"]').each(function () {
            const label = $('label[for="' + $(this).attr('id') + '"]');
            if ($(this).is(':checked')) {
                label.css({
                    'border-color': 'var(--primary)',
                    'background': 'rgba(205, 33, 125, 0.05)'
                });
            } else {
                label.css({
                    'border-color': 'var(--border)',
                    'background': 'var(--bg)'
                });
            }
        });

        if ($(this).val() === 'link') {
            $('#video-link-section').show();
            $('#video-file-section').hide();
            $('#vlink').prop('required', true);
            $('#video_file').prop('required', false);

            // Update UI
            $('label[for="upload_type_link"]').css('border-color', 'var(--primary)');
            $('label[for="upload_type_file"]').css('border-color', 'var(--border)');
        } else {
            $('#video-link-section').hide();
            $('#video-file-section').show();
            $('#vlink').prop('required', false);
            $('#video_file').prop('required', true);

            // Update UI
            $('label[for="upload_type_link"]').css('border-color', 'var(--border)');
            $('label[for="upload_type_file"]').css('border-color', 'var(--primary)');
        }
    });

    // Trigger change on load to set initial state
    $('input[name="upload_type"]:checked').trigger('change');

    // Profile photo preview
    $('#profile_photo').change(function (e) {
        var file = e.target.files[0];
        var preview = $('#profile-preview');
        var previewImg = $('#profile-preview-img');
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('Profile picture must be less than 2MB');
                $(this).val('');
                preview.hide();
                return;
            }
            var reader = new FileReader();
            reader.onload = function (e) {
                previewImg.attr('src', e.target.result);
                preview.show();
            };
            reader.readAsDataURL(file);
        } else {
            preview.hide();
            previewImg.attr('src', '');
        }
    });

    // Video file preview
    $('#video_file').change(function (e) {
        var file = e.target.files[0];
        if (file) {
            if (file.size > 100 * 1024 * 1024) {
                alert('File size must be less than 100MB');
                $(this).val('');
                return;
            }
            if (!file.type.match('video/mp4') && !file.type.match('video/quicktime')) {
                alert('Please upload an MP4 video file');
                $(this).val('');
                return;
            }
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#preview-player').attr('src', e.target.result);
                $('#video-preview').show();
            };
            reader.readAsDataURL(file);
        }
    });

    // Country change handler is now above with Select2 integration

    function adda() {
        // Basic validation
        var title = $('#title').val().trim();
        var category = $('#category').val();
        var country = $("#country").val();
        var province = $("#province").val();
        var aname = $('#aname').val().trim();

        $('#err').hide();
        $('#suc').hide();

        if (!title) { $('#err').html('Please enter a performance title.').show(); return; }
        if (!category) { $('#err').html('Please select a category.').show(); return; }
        if (!country) { $('#err').html('Please select a country.').show(); return; }
        if ($('#province-group').is(':visible') && $('#province').prop('required') && !province) {
            $('#err').html('Please select a state/province.').show(); return;
        }
        if (!aname) { $('#err').html('Please enter the artist name.').show(); return; }

        // Legacy AJAX call for now
        // In future, this should point to NominateController::submit
        var formData = new FormData($('#nominationForm')[0]);

        $('#btnsubmit').hide();
        $('#btnsubmit2').show();

        $.ajax({
            url: '<?= URLROOT ?>/ajax.nominate.php', // Use absolute URL
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data == 'success') {
                    $('#btnsubmit2').hide();
                    $('#btnsubmit').show();
                    $('#suc').html('Nomination submitted successfully! It will be reviewed by an admin and you will be notified once approved.').show();
                    $('#nominationForm')[0].reset();
                    $('#video-preview').hide();
                    $('#profile-preview').hide();
                } else {
                    $('#btnsubmit2').hide();
                    $('#btnsubmit').show();
                    $('#err').html(data).show();
                }
            },
            error: function (xhr, status, error) {
                $('#btnsubmit2').hide();
                $('#btnsubmit').show();
                $('#err').html('Error: ' + error).show();
            }
        });
    }
</script>