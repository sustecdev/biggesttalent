<div class="admin-page-header">
    <h2>Edit Judge</h2>
    <div class="page-actions">
        <a href="<?= URLROOT ?>/admin/judges" class="btn-admin btn-secondary">Back to Judges</a>
    </div>
</div>

<div class="admin-content-card">
    <form id="editJudgeForm" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $data['judge']['id'] ?>">
        
        <div class="form-group">
            <label>Name *</label>
            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($data['judge']['name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Title/Role *</label>
            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($data['judge']['title']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Bio</label>
            <textarea class="form-control" name="bio" rows="6"><?= htmlspecialchars($data['judge']['bio']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Profile Image</label>
            <?php if (!empty($data['judge']['image'])): ?>
                <div class="current-image mb-2">
                    <img src="<?= URLROOT ?>/images/judges/<?= htmlspecialchars($data['judge']['image']) ?>" alt="Current Image" style="height: 100px; border-radius: 8px;">
                    <p class="text-sm text-muted">Current Image</p>
                </div>
            <?php endif; ?>
            <input type="file" class="form-control" name="image" accept="image/*">
            <small class="text-muted">Leave empty to keep current image</small>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="active" value="1" <?= ($data['judge']['active']) ? 'checked' : '' ?>> Active
            </label>
        </div>
        
        <div class="form-actions mt-4">
            <button type="submit" class="btn-admin btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<script>
    $('#editJudgeForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?= URLROOT ?>/admin/judges/save',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (typeof response === 'string') {
                    try { response = JSON.parse(response); } catch(e) {}
                }
                
                if (response.success) {
                    alert('Judge updated successfully');
                    window.location.href = '<?= URLROOT ?>/admin/judges';
                } else {
                    alert('Error: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                alert('Request failed: ' + error);
            }
        });
    });
</script>
