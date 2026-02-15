<div class="admin-page-header">
    <h2>Manage Contests</h2>
    <button class="btn-admin btn-primary" onclick="showAddContestModal()">+ Create Contest</button>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Prize</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['contests'])): ?>
                <tr>
                    <td colspan="7" class="text-center">No contests found. Create your first contest!</td>
                </tr>
            <?php else: ?>
                <?php
                $today = date('Y-m-d');
                foreach ($data['contests'] as $contest):
                    $isActive = ($contest['is_active'] && $contest['start_date'] <= $today && $contest['end_date'] >= $today);
                    $isPast = ($contest['end_date'] < $today);
                    ?>
                    <tr>
                        <td>
                            <?= $contest['id'] ?>
                        </td>
                        <td><strong>
                                <?= htmlspecialchars($contest['name']) ?>
                            </strong></td>
                        <td>
                            <?= date('M d, Y', strtotime($contest['start_date'])) ?>
                        </td>
                        <td>
                            <?= date('M d, Y', strtotime($contest['end_date'])) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($contest['prize_text'] ?? '') ?>
                        </td>
                        <td>
                            <?php if ($isActive): ?>
                                <span class="badge badge-success">Active</span>
                            <?php elseif ($isPast): ?>
                                <span class="badge badge-secondary">Ended</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Upcoming</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-admin btn-sm btn-primary"
                                    onclick="editContest(<?= htmlspecialchars(json_encode($contest), ENT_QUOTES) ?>)">Edit</button>
                                <button class="btn-admin btn-sm btn-danger"
                                    onclick="deleteContest(<?= $contest['id'] ?>, '<?= htmlspecialchars($contest['name'], ENT_QUOTES) ?>')">Delete</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Contest Modal -->
<div id="contestModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="contestModalTitle">Create Contest</h3>
            <span class="close" onclick="closeContestModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="contestForm">
                <input type="hidden" id="contestId" name="id" value="">
                <div class="form-group">
                    <label for="contestName">Contest Name *</label>
                    <input type="text" class="form-control" id="contestName" name="name" required maxlength="200">
                </div>
                <div class="form-group">
                    <label for="contestStartDate">Start Date *</label>
                    <input type="date" class="form-control" id="contestStartDate" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="contestEndDate">End Date *</label>
                    <input type="date" class="form-control" id="contestEndDate" name="end_date" required>
                </div>
                <div class="form-group">
                    <label for="contestPrize">Prize Description *</label>
                    <textarea class="form-control" id="contestPrize" name="prize_text" rows="3" required
                        maxlength="500"></textarea>
                    <small class="form-hint">Describe the prize package (e.g., "$10,000 cash, recording deal,
                        performance opportunity")</small>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="contestActive" name="is_active" value="1" checked>
                        Active (visible to users)
                    </label>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-admin btn-secondary" onclick="closeContestModal()">Cancel</button>
                    <button type="submit" class="btn-admin btn-primary">Save Contest</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showAddContestModal() {
        document.getElementById('contestModalTitle').textContent = 'Create Contest';
        document.getElementById('contestForm').reset();
        document.getElementById('contestId').value = '';
        document.getElementById('contestActive').checked = true;
        document.getElementById('contestModal').style.display = 'block';
    }

    function editContest(contest) {
        document.getElementById('contestModalTitle').textContent = 'Edit Contest';
        document.getElementById('contestId').value = contest.id;
        document.getElementById('contestName').value = contest.name;
        document.getElementById('contestStartDate').value = contest.start_date;
        document.getElementById('contestEndDate').value = contest.end_date;
        document.getElementById('contestPrize').value = contest.prize_text || '';
        document.getElementById('contestActive').checked = contest.is_active == 1;
        document.getElementById('contestModal').style.display = 'block';
    }

    function closeContestModal() {
        document.getElementById('contestModal').style.display = 'none';
    }

    function deleteContest(id, name) {
        if (!confirm('Are you sure you want to delete "' + name + '"? This action cannot be undone.')) {
            return;
        }

        $.ajax({
            url: '<?= URLROOT ?>/admin/contests/delete',
            type: 'POST',
            data: {
                id: id
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Failed to delete contest'));
                }
            },
            error: function (xhr, status, error) {
                alert('Request failed: ' + error);
            }
        });
    }

    $(document).ready(function () {
        $('#contestForm').on('submit', function (e) {
            e.preventDefault();

            var startDate = new Date($('#contestStartDate').val());
            var endDate = new Date($('#contestEndDate').val());

            if (endDate < startDate) {
                alert('End date must be after start date!');
                return;
            }

            var formData = {
                id: $('#contestId').val(),
                name: $('#contestName').val(),
                start_date: $('#contestStartDate').val(),
                end_date: $('#contestEndDate').val(),
                prize_text: $('#contestPrize').val(),
                is_active: $('#contestActive').is(':checked') ? 1 : 0
            };

            $.ajax({
                url: '<?= URLROOT ?>/admin/contests/save',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to save contest'));
                    }
                },
                error: function (xhr, status, error) {
                    alert('Request failed: ' + error);
                }
            });
        });

        // Close modal when clicking outside
        window.onclick = function (event) {
            var modal = document.getElementById('contestModal');
            if (event.target == modal) {
                closeContestModal();
            }
        }
    });
</script>

<style>
    .modal {
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        display: none;
        /* Hidden by default */
        align-items: center;
        justify-content: center;
    }

    .modal[style*="block"] {
        display: flex !important;
    }

    .modal-content {
        background-color: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        color: var(--text-main);
    }

    .close {
        color: var(--text-muted);
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        line-height: 1;
    }

    .close:hover {
        color: var(--text-main);
    }

    .modal-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-main);
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 6px;
        color: var(--text-main);
        font-size: 14px;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
    }

    .form-hint {
        display: block;
        margin-top: 5px;
        font-size: 12px;
        color: var(--text-muted);
    }

    .form-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .badge-warning {
        background: #ff9800;
        color: #000;
    }
</style>