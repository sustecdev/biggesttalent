<div class="admin-page-header">
    <h2>Manage Judges</h2>
    <div class="page-actions">
        <button class="btn-admin btn-primary" data-toggle="modal" data-target="#addJudgeModal">Add New Judge</button>
    </div>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Title/Role</th>
                <th>Bio</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['judges'])): ?>
                <tr>
                    <td colspan="7" class="text-center">No judges found. <a href="#" data-toggle="modal"
                            data-target="#addJudgeModal">Add your first judge</a></td>
                </tr>
            <?php else: ?>
                <?php foreach ($data['judges'] as $judge): ?>
                    <tr>
                        <td>
                            <?= $judge['id'] ?>
                        </td>
                        <td>
                            <?php if (!empty($judge['image'])): ?>
                                <img src="<?= URLROOT ?>/images/judges/<?= htmlspecialchars($judge['image']) ?>"
                                    alt="<?= htmlspecialchars($judge['name']) ?>" class="judge-thumb"
                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <div class="judge-thumb-placeholder"
                                    style="width: 50px; height: 50px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                                    No Image</div>
                            <?php endif; ?>
                        </td>
                        <td><strong>
                                <?= htmlspecialchars($judge['name']) ?>
                            </strong></td>
                        <td>
                            <?= htmlspecialchars($judge['title']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(substr($judge['bio'], 0, 100)) ?>...
                        </td>
                        <td>
                            <span class="badge <?= ($judge['active'] ?? 1) ? 'badge-success' : 'badge-secondary' ?>">
                                <?= ($judge['active'] ?? 1) ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <!-- Link to legacy edit page for now -->
                                <a href="<?= URLROOT ?>/admin/judges/edit/<?= $judge['id'] ?>"
                                    class="btn-admin btn-sm btn-secondary">Edit</a>
                                <button class="btn-admin btn-sm btn-danger btn-delete" data-id="<?= $judge['id'] ?>"
                                    data-type="judge">Delete</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Judge Modal -->
<div class="modal fade" id="addJudgeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Judge</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addJudgeForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Title/Role *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea class="form-control" name="bio" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Profile Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="active" value="1" checked> Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-admin btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-admin btn-primary">Add Judge</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#addJudgeForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'add_judge');

        $.ajax({
            url: '<?= URLROOT ?>/admin/judges/save',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                // Check if response is JSON string
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) { }
                }

                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Unknown error'));
                }
            },
            error: function (xhr, status, error) {
                alert('Request failed: ' + error);
            }
        });
    });

    $('.btn-delete').on('click', function () {
        if (!confirm('Are you sure you want to delete this judge?')) return;

        const id = $(this).data('id');
        const type = $(this).data('type'); // judge

        $.ajax({
            url: '<?= URLROOT ?>/admin/judges/delete',
            type: 'POST',
            data: {
                id: id
            },
            success: function (response) {
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) { }
                }
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Failed to delete'));
                }
            }
        });
    });
</script>