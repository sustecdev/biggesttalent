<div class="admin-page-header">
    <h2>All Contestants</h2>
    <div class="page-actions">
        <!-- Link to Nominations controller -->
        <a href="<?= URLROOT ?>/admin/nominations" class="btn-admin btn-primary">Review Nominations</a>
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
                <th>Votes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['contestants'])): ?>
                <tr>
                    <td colspan="8" class="text-center">No contestants found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data['contestants'] as $contestant): ?>
                    <tr>
                        <td>
                            <?= $contestant['id'] ?>
                        </td>
                        <td><strong>
                                <?= htmlspecialchars($contestant['aname']) ?>
                            </strong></td>
                        <td>
                            <?= htmlspecialchars($contestant['country']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($contestant['fname'] . ' ' . $contestant['lname']) ?><br>
                            <small class="text-muted">
                                ID: <?= htmlspecialchars($contestant['uid']) ?>
                            </small>
                        </td>
                        <td>
                            <?php if ($contestant['vlink']): ?>
                                <a href="<?= htmlspecialchars($contestant['vlink']) ?>" target="_blank" class="btn-link">View
                                    Video</a>
                            <?php else: ?>
                                <span class="text-muted">No link</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= date('M d, Y', strtotime($contestant['date'])) ?>
                        </td>
                        <td><strong>
                                <?= $contestant['vote_count'] ?>
                            </strong></td>
                        <td>
                            <div class="action-buttons">
                                <!-- Helper links can point to legacy for now or future controller methods -->
                                <!-- Helper links points to MVC -->
                                <a href="<?= URLROOT ?>/admin/contestants/edit/<?= $contestant['id'] ?>"
                                    class="btn-admin btn-sm btn-secondary">Edit</a>
                                <button class="btn-admin btn-sm btn-danger btn-delete" data-id="<?= $contestant['id'] ?>"
                                    data-type="nomination">Delete</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>