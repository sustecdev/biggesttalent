<div class="admin-page-header">
    <h2>Website Settings</h2>
</div>

<form id="settingsForm">
    <div class="admin-section">
        <h3 class="section-title">General Settings</h3>

        <div class="form-group">
            <label>Website Logo URL</label>
            <input type="text" class="form-control" name="logo_url"
                value="<?= htmlspecialchars($data['settings']['logo_url'] ?? '') ?>"
                placeholder="https://example.com/logo.png">
        </div>

        <div class="form-group">
            <label>Homepage Hero Title</label>
            <input type="text" class="form-control" name="hero_title"
                value="<?= htmlspecialchars($data['settings']['hero_title'] ?? 'Find The Biggestest Talent') ?>">
        </div>

        <div class="form-group">
            <label>Homepage Hero Subtitle</label>
            <textarea class="form-control" name="hero_subtitle"
                rows="3"><?= htmlspecialchars($data['settings']['hero_subtitle'] ?? 'Showcase your extraordinary talent on the world\'s biggest stage.') ?></textarea>
        </div>

        <div class="form-group">
            <label>Season Badge Text</label>
            <input type="text" class="form-control" name="season_badge"
                value="<?= htmlspecialchars($data['settings']['season_badge'] ?? 'SEASON 5 NOW OPEN') ?>">
        </div>
    </div>

    <div class="admin-section">
        <h3 class="section-title">Colors & Theme</h3>

        <div class="form-group">
            <label>Primary Color</label>
            <input type="color" class="form-control" name="primary_color"
                value="<?= htmlspecialchars($data['settings']['primary_color'] ?? '#E50914') ?>"
                style="width: 100px; height: 40px;">
        </div>

        <div class="form-group">
            <label>Primary Hover Color</label>
            <input type="color" class="form-control" name="primary_hover"
                value="<?= htmlspecialchars($data['settings']['primary_hover'] ?? '#B20710') ?>"
                style="width: 100px; height: 40px;">
        </div>

        <div class="form-group">
            <label>Accent Color</label>
            <input type="color" class="form-control" name="accent_color"
                value="<?= htmlspecialchars($data['settings']['accent_color'] ?? '#FF4B4B') ?>"
                style="width: 100px; height: 40px;">
        </div>
    </div>

    <div class="admin-section">
        <h3 class="section-title">Social Links</h3>

        <div class="form-group">
            <label>Facebook URL</label>
            <input type="url" class="form-control" name="facebook_url"
                value="<?= htmlspecialchars($data['settings']['facebook_url'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Twitter URL</label>
            <input type="url" class="form-control" name="twitter_url"
                value="<?= htmlspecialchars($data['settings']['twitter_url'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Instagram URL</label>
            <input type="url" class="form-control" name="instagram_url"
                value="<?= htmlspecialchars($data['settings']['instagram_url'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>YouTube URL</label>
            <input type="url" class="form-control" name="youtube_url"
                value="<?= htmlspecialchars($data['settings']['youtube_url'] ?? '') ?>">
        </div>
    </div>

    <div class="admin-section">
        <h3 class="section-title">Contact Information</h3>

        <div class="form-group">
            <label>Contact Email</label>
            <input type="email" class="form-control" name="contact_email"
                value="<?= htmlspecialchars($data['settings']['contact_email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Contact Phone</label>
            <input type="text" class="form-control" name="contact_phone"
                value="<?= htmlspecialchars($data['settings']['contact_phone'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Contact Address</label>
            <textarea class="form-control" name="contact_address"
                rows="3"><?= htmlspecialchars($data['settings']['contact_address'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="admin-section">
        <button type="submit" class="btn-admin btn-primary btn-lg">Save Settings</button>
    </div>
</form>

<script>
    $(document).ready(function () {
        $('#settingsForm').on('submit', function (e) {
            e.preventDefault();

            // Serialize form data
            var formData = $(this).serialize();

            $.ajax({
                url: '<?= URLROOT ?>/admin/settings/save',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Settings saved successfully!');
                    } else {
                        alert('Error: ' + (response.message || 'Failed to save settings'));
                    }
                },
                error: function (xhr, status, error) {
                    alert('Request failed: ' + error);
                }
            });
        });
    });
</script>

<style>
    .section-title {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
        color: var(--primary);
    }

    .admin-section {
        background: var(--card-bg);
        padding: 25px;
        border-radius: 8px;
        border: 1px solid var(--border);
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: 500;
        margin-bottom: 8px;
        display: block;
        color: var(--text-main);
    }

    .form-control {
        background: var(--bg);
        border: 1px solid var(--border);
        color: var(--text-main);
        padding: 10px;
        border-radius: 6px;
        width: 100%;
    }

    .form-control:focus {
        border-color: var(--primary);
        outline: none;
    }
</style>