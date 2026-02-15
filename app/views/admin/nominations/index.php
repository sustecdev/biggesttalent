<div class="admin-page-header">
    <h2>All Nominations</h2>
    <div class="page-actions">
        <div class="status-filter">
            <a href="<?= URLROOT ?>/admin/nominations"
                class="btn-admin <?= $data['status'] == '' ? 'btn-primary' : 'btn-secondary' ?>">All</a>
            <a href="<?= URLROOT ?>/admin/nominations?status=pending"
                class="btn-admin <?= $data['status'] == 'pending' ? 'btn-primary' : 'btn-secondary' ?>">Pending</a>
            <a href="<?= URLROOT ?>/admin/nominations?status=approved"
                class="btn-admin <?= $data['status'] == 'approved' ? 'btn-primary' : 'btn-secondary' ?>">Approved</a>
            <a href="<?= URLROOT ?>/admin/nominations?status=rejected"
                class="btn-admin <?= $data['status'] == 'rejected' ? 'btn-primary' : 'btn-secondary' ?>">Rejected</a>
        </div>
    </div>
</div>

<!-- Nominations by Country -->
<div class="admin-section" style="margin-bottom: 32px;">
    <div class="section-header">
        <h3 class="section-title">Nominations by Country</h3>
        <p class="section-description">Breakdown of nominations by contestant country</p>
    </div>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalNoms = array_sum(array_column($data['nominationsByCountry'] ?? [], 'nomination_count'));
                if (empty($data['nominationsByCountry'])): ?>
                    <tr>
                        <td colspan="3" class="text-center" style="padding: 24px; color: var(--text-muted);">No nominations with country data yet.</td>
                    </tr>
                <?php else:
                    foreach ($data['nominationsByCountry'] as $row):
                        $pct = $totalNoms > 0 ? round(($row['nomination_count'] / $totalNoms) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['country']) ?></strong></td>
                            <td><?= number_format($row['nomination_count']) ?></td>
                            <td>
                                <div class="percentage-bar" style="max-width: 200px;">
                                    <div class="percentage-fill" style="width: <?= $pct ?>%"></div>
                                    <span class="percentage-text"><?= $pct ?>%</span>
                                </div>
                            </td>
                        </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Artist Name</th>
                <th>Country</th>
                <th>Nominated By</th>
                <th>Video Link</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['nominations'])): ?>
                <tr>
                    <td colspan="8" class="text-center">No nominations found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data['nominations'] as $nom):
                    $nomJson = htmlspecialchars(json_encode($nom), ENT_QUOTES, 'UTF-8');
                    $profilePhotoUrl = !empty($nom['profile_photo']) ? (defined('URLROOT') ? URLROOT : '') . '/' . ltrim($nom['profile_photo'], '/') : '';
                    $videoUrl = !empty($nom['vlink']) ? $nom['vlink'] : (!empty($nom['video_file']) ? (defined('URLROOT') ? URLROOT : '') . '/' . ltrim($nom['video_file'], '/') : '');
                    ?>
                    <tr data-nom="<?= $nomJson ?>" data-profile-photo="<?= htmlspecialchars($profilePhotoUrl) ?>" data-video-url="<?= htmlspecialchars($videoUrl) ?>">
                        <td>
                            <?= $nom['id'] ?>
                        </td>
                        <td><strong>
                                <?= htmlspecialchars($nom['aname']) ?>
                            </strong></td>
                        <td>
                            <?= htmlspecialchars($nom['country']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($nom['fname'] . ' ' . $nom['lname']) ?><br>
                            <small class="text-muted">
                                ID: <?= htmlspecialchars($nom['uid']) ?>
                            </small>
                        </td>
                        <td>
                            <?php if ($nom['vlink']): ?>
                                <a href="<?= htmlspecialchars($nom['vlink']) ?>" target="_blank" class="btn-link">View Video</a>
                            <?php else: ?>
                                <span class="text-muted">No link</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= date('M d, Y', strtotime($nom['date'])) ?>
                        </td>
                        <td>
                            <?php if (($nom['status'] ?? 'pending') === 'pending'): ?>
                                <select class="status-update form-control" data-id="<?= $nom['id'] ?>" data-type="nomination">
                                    <option value="pending" selected>Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            <?php else: ?>
                                <span
                                    class="badge badge-<?= ($nom['status'] ?? 'pending') === 'approved' ? 'success' : 'danger' ?>">
                                    <?= htmlspecialchars(ucfirst($nom['status'] ?? 'pending')) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn-admin btn-sm btn-secondary btn-view-details">
                                View Details
                            </button>
                            <?php if (($nom['status'] ?? 'pending') === 'pending'): ?>
                                <div class="action-buttons" style="margin-top: 6px;">
                                    <button class="btn-admin btn-sm btn-success"
                                        onclick="updateStatus(<?= $nom['id'] ?>, 'approved')">Approve</button>
                                    <button class="btn-admin btn-sm btn-danger"
                                        onclick="updateStatus(<?= $nom['id'] ?>, 'rejected')">Reject</button>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.percentage-bar { position: relative; width: 100%; height: 24px; background: var(--bg); border-radius: 12px; overflow: hidden; border: 1px solid var(--border); }
.percentage-fill { position: absolute; top: 0; left: 0; height: 100%; background: linear-gradient(90deg, var(--primary) 0%, var(--primary-hover) 100%); transition: width 0.5s ease; border-radius: 12px; }
.percentage-text { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 11px; font-weight: 600; color: var(--text-main); z-index: 1; text-shadow: 0 1px 2px rgba(0,0,0,0.3); }
</style>

<!-- Nominee Details Modal -->
<div id="nomineeDetailsModal" class="modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div class="modal-content" style="max-width: 640px; max-height: 90vh; overflow-y: auto; width: 100%;">
        <div class="modal-header">
            <h3>Nominee Details</h3>
            <span class="close" onclick="closeNomineeDetailsModal()">&times;</span>
        </div>
        <div class="modal-body" id="nomineeDetailsContent" style="padding: 24px;">
            <!-- Populated by JS -->
        </div>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div id="rejectionModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Reject Nomination</h3>
            <span class="close" onclick="closeRejectionModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p>Please provide a reason for rejecting this nomination:</p>
            <form id="rejectionForm">
                <input type="hidden" id="rejectionNomId" value="">
                <div class="form-group">
                    <label for="rejectionReason">Rejection Reason *</label>
                    <textarea class="form-control" id="rejectionReason" name="rejection_reason" rows="4" required
                        maxlength="500"
                        placeholder="e.g., Video quality too low, inappropriate content, etc."></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-admin btn-secondary"
                        onclick="closeRejectionModal()">Cancel</button>
                    <button type="submit" class="btn-admin btn-danger">Reject Nomination</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var pendingRejectionId = null;

    function updateStatus(id, status) {
        if (status === 'rejected') {
            // Show rejection reason modal
            pendingRejectionId = id;
            document.getElementById('rejectionNomId').value = id;
            document.getElementById('rejectionReason').value = '';
            document.getElementById('rejectionModal').style.display = 'block';
            return;
        }

        // For approve, proceed directly
        if (!confirm('Are you sure you want to approve this nomination?')) {
            return;
        }

        submitStatusUpdate(id, status, '');
    }

    function closeRejectionModal() {
        document.getElementById('rejectionModal').style.display = 'none';
        pendingRejectionId = null;
    }

    function submitStatusUpdate(id, status, rejectionReason) {
        $.ajax({
            url: '<?= URLROOT ?>/admin/nominations/updateStatus',
            type: 'POST',
            data: {
                action: 'update_nomination_status',
                id: id,
                status: status,
                rejection_reason: rejectionReason
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Failed to update status'));
                }
            },
            error: function () {
                alert('Error: Failed to update status. Please try again.');
            }
        });
    }

    $(document).ready(function () {
        $('#rejectionForm').on('submit', function (e) {
            e.preventDefault();
            var id = document.getElementById('rejectionNomId').value;
            var reason = document.getElementById('rejectionReason').value.trim();

            if (!reason) {
                alert('Please provide a rejection reason.');
                return;
            }

            submitStatusUpdate(id, 'rejected', reason);
        });

        // Close modals when clicking outside
        window.onclick = function (event) {
            if (event.target === document.getElementById('nomineeDetailsModal')) closeNomineeDetailsModal();
            if (event.target === document.getElementById('rejectionModal')) closeRejectionModal();
        };
    });

    // Handle status dropdown change
    $(document).ready(function () {
        $('.status-update').on('change', function () {
            var id = $(this).data('id');
            var status = $(this).val();
            updateStatus(id, status);
        });

        // View Details click
        $(document).on('click', '.btn-view-details', function () {
            var $row = $(this).closest('tr');
            var nom = $row.data('nom');
            var profilePhoto = $row.attr('data-profile-photo') || '';
            var videoUrl = $row.attr('data-video-url') || '';
            if (nom) showNomDetails(nom, profilePhoto, videoUrl);
        });
    });

    function showNomDetails(nom, profilePhoto, videoUrl) {
        var html = '';
        if (profilePhoto) {
            html += '<div style="text-align:center;margin-bottom:20px;"><img src="' + escapeHtml(profilePhoto) + '" alt="" style="max-width:120px;max-height:120px;border-radius:50%;object-fit:cover;border:3px solid var(--primary);"></div>';
        }
        html += '<div class="detail-grid" style="display:grid;gap:12px;">';
        html += detailRow('Artist / Contestant Name', nom.aname);
        html += detailRow('Performance Title', nom.title);
        html += detailRow('Category', nom.category_name || (nom.category_id ? 'ID: ' + nom.category_id : '—'));
        html += detailRow('Gender', nom.gender || '—');
        html += detailRow('Age', nom.age || '—');
        html += detailRow("Nominee's Email", nom.nominee_email ? '<a href="mailto:' + escapeHtml(nom.nominee_email) + '">' + escapeHtml(nom.nominee_email) + '</a>' : '—');
        html += detailRow("Nominee's Phone", nom.nominee_phone ? '<a href="tel:' + escapeHtml(nom.nominee_phone) + '">' + escapeHtml(nom.nominee_phone) + '</a>' : '—');
        html += detailRow('Country', nom.country || '—');
        html += detailRow('Province/State', nom.province || '—');
        html += detailRow('Description', nom.description ? '<span style="white-space:pre-wrap;">' + escapeHtml(nom.description) + '</span>' : '—');
        html += detailRow('Nominated By', (nom.fname || '') + ' ' + (nom.lname || '') + (nom.username ? ' (@' + nom.username + ')' : '') + (nom.email ? ' &lt;' + escapeHtml(nom.email) + '&gt;' : ''));
        html += detailRow('User ID', nom.uid || '—');
        html += detailRow('Status', nom.status ? nom.status.charAt(0).toUpperCase() + nom.status.slice(1) : '—');
        html += detailRow('Date Submitted', nom.date ? new Date(nom.date).toLocaleDateString() : '—');
        html += detailRow('Nomination ID', nom.id || '—');
        if (nom.rejection_reason) html += detailRow('Rejection Reason', '<span style="color:#ef4444;">' + escapeHtml(nom.rejection_reason) + '</span>');
        if (videoUrl) html += detailRow('Video', '<a href="' + escapeHtml(videoUrl) + '" target="_blank" class="btn-admin btn-sm btn-primary">Watch Video</a>');
        html += '</div>';
        document.getElementById('nomineeDetailsContent').innerHTML = html;
        var m = document.getElementById('nomineeDetailsModal');
        m.style.display = 'flex';
    }

    function detailRow(label, value) {
        if (!value && value !== 0) value = '—';
        return '<div><strong style="color:var(--text-muted);font-size:12px;text-transform:uppercase;">' + escapeHtml(label) + '</strong><div style="margin-top:4px;">' + value + '</div></div>';
    }

    function escapeHtml(str) {
        if (str == null) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function closeNomineeDetailsModal() {
        document.getElementById('nomineeDetailsModal').style.display = 'none';
    }
</script>