<div class="admin-page-header">
    <h2>Reports & Flags</h2>
    <div class="page-actions">
        <div class="status-filter">
            <a href="<?= URLROOT ?>/admin/reports"
                class="btn-admin <?= $data['current_status'] == '' ? 'btn-primary' : 'btn-secondary' ?>">All</a>
            <a href="<?= URLROOT ?>/admin/reports?status=pending"
                class="btn-admin <?= $data['current_status'] == 'pending' ? 'btn-primary' : 'btn-secondary' ?>">Pending</a>
            <a href="<?= URLROOT ?>/admin/reports?status=reviewed"
                class="btn-admin <?= $data['current_status'] == 'reviewed' ? 'btn-primary' : 'btn-secondary' ?>">Reviewed</a>
            <a href="<?= URLROOT ?>/admin/reports?status=resolved"
                class="btn-admin <?= $data['current_status'] == 'resolved' ? 'btn-primary' : 'btn-secondary' ?>">Resolved</a>
        </div>
    </div>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Reported Video</th>
                <th>Reason</th>
                <th>Reporter</th>
                <th>IP Address</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['reports'])): ?>
                <tr>
                    <td colspan="8" class="text-center">No reports found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data['reports'] as $report): ?>
                    <tr>
                        <td>
                            <?= $report['id'] ?>
                        </td>
                        <td>
                            <?php if ($report['aname']): ?>
                                <strong>
                                    <?= htmlspecialchars($report['aname']) ?>
                                </strong><br>
                                <small>
                                    <?= htmlspecialchars($report['title'] ?? '') ?>
                                </small><br>
                                <small class="text-muted">
                                    <?= htmlspecialchars($report['country'] ?? '') ?>
                                </small>
                                <?php if ($report['vlink']): ?>
                                    <br><a href="<?= htmlspecialchars($report['vlink']) ?>" target="_blank" class="btn-link">View
                                        Video</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Video deleted</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong>
                                <?= htmlspecialchars($report['reason']) ?>
                            </strong>
                            <?php if ($report['details']): ?>
                                <br><small>
                                    <?= htmlspecialchars($report['details']) ?>
                                </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($report['username']): ?>
                                <?= htmlspecialchars($report['username']) ?><br>
                                <small>
                                    <?= htmlspecialchars($report['email'] ?? '') ?>
                                </small>
                            <?php else: ?>
                                <span class="text-muted">Anonymous</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($report['ip_address'] ?? '') ?>
                        </td>
                        <td>
                            <?= date('M d, Y H:i', strtotime($report['created_at'])) ?>
                        </td>
                        <td>
                            <select class="status-update form-control" data-id="<?= $report['id'] ?>" style="width: 120px;">
                                <option value="pending" <?= $report['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="reviewed" <?= $report['status'] == 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                                <option value="resolved" <?= $report['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                <option value="dismissed" <?= $report['status'] == 'dismissed' ? 'selected' : '' ?>>Dismissed
                                </option>
                            </select>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($report['nomination_id']): ?>
                                    <!-- Ideally this should point to a specific view in adminnominations if implemented -->
                                    <!-- For now pointing to the general list, user can search -->
                                    <a href="<?= URLROOT ?>/admin/nominations" class="btn-admin btn-sm btn-primary">Check Nom.</a>
                                <?php endif; ?>
                                <button class="btn-admin btn-sm btn-danger"
                                    onclick="deleteReport(<?= $report['id'] ?>)">Delete</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function deleteReport(id) {
        if (!confirm('Are you sure you want to delete this report?')) {
            return;
        }

        $.ajax({
            url: '<?= URLROOT ?>/admin/reports/delete',
            type: 'POST',
            data: {
                id: id
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Failed to delete report'));
                }
            },
            error: function (xhr, status, error) {
                alert('Request failed: ' + error);
            }
        });
    }

    $(document).ready(function () {
        $('.status-update').on('change', function () {
            var id = $(this).data('id');
            var status = $(this).val();

            $.ajax({
                url: '<?= URLROOT ?>/admin/reports/updateStatus',
                type: 'POST',
                data: {
                    id: id,
                    status: status
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Optionally reload or show success message
                        if (status === 'resolved' || status === 'dismissed') {
                            location.reload();
                        }
                    } else {
                        alert('Error: ' + (response.message || 'Failed to update status'));
                        location.reload(); // Reload to reset dropdown
                    }
                },
                error: function (xhr, status, error) {
                    alert('Request failed: ' + error);
                    location.reload();
                }
            });
        });
    });
</script>