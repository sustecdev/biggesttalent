<div class="admin-page-header">
    <h2>Edit Contestant</h2>
    <div class="page-actions">
        <a href="<?= URLROOT ?>/admin/contestants" class="btn-admin btn-secondary">Back to Contestants</a>
    </div>
</div>

<div class="admin-content-card">
    <form id="editContestantForm">
        <input type="hidden" name="id" value="<?= $data['contestant']['id'] ?>">
        
        <div class="form-group">
            <label>Stage Name *</label>
            <input type="text" class="form-control" name="stage_name" value="<?= htmlspecialchars($data['contestant']['stage_name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Category ID</label>
            <input type="number" class="form-control" name="category_id" value="<?= htmlspecialchars($data['contestant']['category_id']) ?>">
        </div>
        
        <div class="form-group">
            <label>Video URL</label>
            <input type="text" class="form-control" name="video_url" value="<?= htmlspecialchars($data['contestant']['video_url']) ?>">
        </div>
        
        <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="status">
                <option value="pending" <?= $data['contestant']['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= $data['contestant']['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= $data['contestant']['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
        </div>
        
        <div class="form-actions mt-4">
            <button type="submit" class="btn-admin btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<script>
    $('#editContestantForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= URLROOT ?>/admin/contestants/save',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (typeof response === 'string') {
                    try { response = JSON.parse(response); } catch(e) {}
                }
                
                if (response.success) {
                    alert('Contestant updated successfully');
                    window.location.href = '<?= URLROOT ?>/admin/contestants';
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
