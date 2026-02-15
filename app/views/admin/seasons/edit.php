<div class="admin-page-header">
    <h2>Edit Season</h2>
    <div class="page-actions">
        <a href="<?= URLROOT ?>/admin/seasons" class="btn-admin btn-secondary">Back to Seasons</a>
    </div>
</div>

<div class="admin-content-card">
    <form id="editSeasonForm">
        <input type="hidden" name="id" value="<?= $data['season']['id'] ?>">
        
        <div class="form-group">
            <label>Season Number *</label>
            <input type="number" class="form-control" name="season_number" value="<?= htmlspecialchars($data['season']['season_number']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Title *</label>
            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($data['season']['title']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Start Date *</label>
            <input type="date" class="form-control" name="start_date" value="<?= htmlspecialchars($data['season']['start_date']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>End Date *</label>
            <input type="date" class="form-control" name="end_date" value="<?= htmlspecialchars($data['season']['end_date']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($data['season']['description']) ?></textarea>
        </div>
        
        <div class="form-actions mt-4">
            <button type="submit" class="btn-admin btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<script>
    $('#editSeasonForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= URLROOT ?>/admin/seasons/save', // Assuming save handles update based on ID presence?
            // Wait, SeasonModel::save (Step 1885) uses INSERT.
            // I need to update SeasonModel::save to handle UPDATE if ID exists!
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (typeof response === 'string') {
                    try { response = JSON.parse(response); } catch(e) {}
                }
                
                if (response.success) {
                    alert('Season updated successfully');
                    window.location.href = '<?= URLROOT ?>/admin/seasons';
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
