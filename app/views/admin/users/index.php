<div class="admin-page-header">
    <h2>Users & Roles</h2>
    <?php if (!empty($data['is_super_admin'])): ?>
        <p class="text-muted" style="margin-top: 4px; font-size: 13px;">You are Super Admin. Only you can add or remove admins.</p>
    <?php endif; ?>
    <div class="page-actions">
        <input type="text" class="form-control" id="searchUsers" placeholder="Search users..."
            style="display: inline-block; width: 300px;">
    </div>
</div>

<div class="admin-table-container">
    <table class="admin-table" id="usersTable">
        <thead>
            <tr>
                <th>UID</th>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Country</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['users'])): ?>
                <tr>
                    <td colspan="7" class="text-center">No users found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data['users'] as $user): ?>
                    <tr>
                        <td>
                            <?= $user['uid'] ?>
                        </td>
                        <td><strong>
                                <?= htmlspecialchars($user['username']) ?>
                            </strong></td>
                        <td>
                            <?= htmlspecialchars(($user['fname'] ?? '') . ' ' . ($user['lname'] ?? '')) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($user['email']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($user['country'] ?? 'N/A') ?>
                        </td>
                        <td>
                            <?php
                            $userRole = $user['role'] ?? 'user';
                            $isSuperAdmin = !empty($data['is_super_admin']);
                            $canEditAdmins = $isSuperAdmin; // Only super_admin can change admin roles
                            $isAdminUser = in_array($userRole, ['admin', 'super_admin'], true);
                            $disabled = ($isAdminUser && !$canEditAdmins) ? 'disabled' : '';
                            ?>
                            <select class="form-control role-update" data-uid="<?= $user['uid'] ?>" style="width: 140px;" <?= $disabled ?>>
                                <option value="user" <?= $userRole == 'user' ? 'selected' : '' ?>>User</option>
                                <?php if ($isSuperAdmin): ?>
                                    <option value="admin" <?= $userRole == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="super_admin" <?= $userRole == 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                                <?php endif; ?>
                                <option value="banned" <?= $userRole == 'banned' ? 'selected' : '' ?>>Banned</option>
                            </select>
                            <?php if ($isAdminUser && !$canEditAdmins): ?>
                                <small class="text-muted" style="display:block;margin-top:4px;">Only Super Admin can change</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <?php if (!$isAdminUser || $canEditAdmins): ?>
                                    <button class="btn-admin btn-sm btn-danger btn-delete" data-id="<?= $user['uid'] ?>"
                                        data-type="user">Delete</button>
                                    <button class="btn-admin btn-sm btn-warning" onclick="banUser(<?= $user['uid'] ?>)">Ban</button>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size:12px;">—</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    $('.role-update').on('change', function () {
        const uid = $(this).data('uid');
        const role = $(this).val();

        $.ajax({
            url: '<?= URLROOT ?>/admin/users/updateRole',
            type: 'POST',
            data: {
                uid: uid,
                role: role
            },
            success: function (response) {
                try {
                    const res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.success) {
                        alert('User role updated successfully.');
                    } else {
                        alert('Error: ' + res.message);
                        location.reload();
                    }
                } catch (e) {
                    console.error(e);
                    alert('An error occurred while processing the response.');
                }
            },
            error: function (xhr, status, error) {
                alert('Request failed: ' + error);
            }
        });
    });

    function banUser(uid) {
        if (!confirm('Ban this user? They will not be able to login.')) {
            return;
        }

        $.ajax({
            url: '<?= URLROOT ?>/admin/users/ban',
            type: 'POST',
            data: {
                uid: uid
            },
            success: function (response) {
                try {
                    const res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.success) {
                        alert('User banned successfully.');
                        location.reload();
                    } else {
                        alert('Error: ' + res.message);
                    }
                } catch (e) {
                    console.error(e);
                    alert('An error occurred while processing the response.');
                }
            },
            error: function (xhr, status, error) {
                alert('Request failed: ' + error);
            }
        });
    }

    // Delete functionality using existing delete logic in admin-ajax or custom controller 
    $('.btn-delete').on('click', function () {
        const id = $(this).data('id');
        const type = $(this).data('type'); // 'user'

        if (!confirm('Are you sure you want to delete this user? This cannot be undone.')) {
            return;
        }

        $.ajax({
            url: '<?= URLROOT ?>/admin/users/delete',
            type: 'POST',
            data: {
                id: id,
                type: type
            },
            success: function (response) {
                try {
                    const res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.success) {
                        alert('User deleted successfully.');
                        location.reload();
                    } else {
                        alert('Error: ' + res.message);
                    }
                } catch (e) {
                    console.error(e);
                    alert('An error occurred while processing the response.');
                }
            },
            error: function (xhr, status, error) {
                alert('Request failed: ' + error);
            }
        });
    });

    // Search functionality
    $('#searchUsers').on('keyup', function () {
        const value = $(this).val().toLowerCase();
        $('#usersTable tbody tr').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
</script>