<div class="admin-page-header">
    <div>
        <h2>Voting Management</h2>
        <p class="page-subtitle">Monitor votes, detect suspicious activity, and manage voting data</p>
    </div>
    <div class="page-actions">
        <button class="btn-admin btn-warning" onclick="resetVotes()">Reset All Votes</button>
        <a href="<?= URLROOT ?>/admin/voting/export" class="btn-admin btn-primary">Export to CSV</a>
    </div>
</div>

<div class="stats-grid" style="margin-bottom: 40px;">
    <div class="stat-card stat-card-highlight">
        <div class="stat-content">
            <h3 class="stat-value">
                <?= number_format($data['votingStats']['total_votes']) ?>
            </h3>
            <p class="stat-label">Total Votes Cast</p>
            <p class="stat-description">All votes recorded in the system</p>
        </div>
    </div>

    <div class="stat-card stat-card-warning">
        <div class="stat-content">
            <h3 class="stat-value">
                <?= count($data['suspicious']) ?>
            </h3>
            <p class="stat-label">Suspicious IPs</p>
            <p class="stat-description">IPs with more than 5 votes detected</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-content">
            <h3 class="stat-value">
                <?= count($data['votingStats']['top_contestants'] ?? []) ?>
            </h3>
            <p class="stat-label">Active Contestants</p>
            <p class="stat-description">Contestants with votes received</p>
        </div>
    </div>
</div>

<div class="admin-section">
    <div class="section-header">
        <h3 class="section-title">Top Voted Contestants</h3>
        <p class="section-description">Ranking of contestants by total votes received</p>
    </div>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Artist Name</th>
                    <th>Country</th>
                    <th>Season</th>
                    <th>Total Votes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['votingStats']['top_contestants'])): ?>
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 40px 20px; color: var(--text-muted);">
                            <p style="margin: 0;">No votes have been cast yet.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['votingStats']['top_contestants'] as $index => $contestant): ?>
                        <tr>
                            <td>
                                <span class="rank-badge rank-badge-<?= $index < 3 ? 'top' : 'normal' ?>">
                                    #
                                    <?= $index + 1 ?>
                                </span>
                            </td>
                            <td><strong>
                                    <?= htmlspecialchars($contestant['aname']) ?>
                                </strong></td>
                            <td>
                                <?= htmlspecialchars($contestant['country_name'] ?? $contestant['country']) ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($contestant['season_title'])): ?>
                                                <span class="badge badge-info" style="background: linear-gradient(135deg, #0ea5e9, #06b6d4); color: white; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                                    <?= htmlspecialchars($contestant['season_title']) ?>
                                                </span>
                                        <?php else: ?>
                                                <span style="color: var(--text-muted); font-style: italic;">No Season</span>
                                        <?php endif; ?>
                            </td>
                            <td>
                                <span class="vote-count-badge">
                                    <?= number_format($contestant['vote_count']) ?>
                                </span>
                            </td>
                            <td>
                                <!-- Assuming we will have a view for details, or linking to legacy for now -->
                                <a href="<?= URLROOT ?>/legacy/contestant-details.php?id=<?= (int) $contestant['id'] ?>"
                                    class="btn-admin btn-sm btn-secondary">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-section">
    <div class="section-header">
        <h3 class="section-title">Votes by Country</h3>
        <p class="section-description">Geographic distribution of votes</p>
    </div>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Total Votes</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['votingStats']['votes_by_country'])): ?>
                    <tr>
                        <td colspan="3" class="text-center" style="padding: 40px 20px; color: var(--text-muted);">
                            <p style="margin: 0;">No votes have been cast yet.</p>
                        </td>
                    </tr>
                <?php else:
                    $totalCountryVotes = array_sum(array_column($data['votingStats']['votes_by_country'], 'vote_count'));
                    foreach ($data['votingStats']['votes_by_country'] as $country):
                        $percentage = $totalCountryVotes > 0 ? round(($country['vote_count'] / $totalCountryVotes) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td><strong>
                                    <?= htmlspecialchars($country['country_name'] ?? $country['country']) ?>
                                </strong></td>
                            <td>
                                <span class="vote-count-badge">
                                    <?= number_format($country['vote_count']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="percentage-bar">
                                    <div class="percentage-fill" style="width: <?= $percentage ?>%"></div>
                                    <span class="percentage-text">
                                        <?= $percentage ?>%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-section">
    <div class="section-header">
        <h3 class="section-title">Suspicious Voting Patterns</h3>
        <p class="section-description">IP addresses with more than 5 votes detected (potential cheating or abuse)</p>
    </div>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>IP Address</th>
                    <th>Vote Count</th>
                    <th>Nominations Voted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['suspicious'])): ?>
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 40px 20px; color: var(--text-muted);">
                            <p style="margin: 0;">No suspicious activity detected. All voting patterns appear normal.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data['suspicious'] as $sus): ?>
                        <tr class="table-warning-row">
                            <td>
                                <div class="ip-address-cell">
                                    <strong>
                                        <?= htmlspecialchars($sus['ip_address']) ?>
                                    </strong>
                                    <span class="suspicious-badge">Suspicious</span>
                                </div>
                            </td>
                            <td>
                                <span class="vote-count-badge vote-count-danger">
                                    <?= number_format($sus['vote_count']) ?>
                                </span>
                            </td>
                            <td>
                                <?= count(explode(',', $sus['nominations'])) ?> nominations
                            </td>
                            <td>
                                <button class="btn-admin btn-sm btn-danger"
                                    onclick="banIP('<?= htmlspecialchars($sus['ip_address']) ?>')">Ban IP</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Voting Management Page Specific Styles */
    .page-subtitle {
        color: var(--text-muted);
        font-size: 14px;
        margin: 8px 0 0 0;
        font-weight: 400;
    }

    .section-header {
        margin-bottom: 24px;
    }

    .section-header .section-title {
        margin-bottom: 8px;
    }

    .section-header .section-description {
        margin-bottom: 0;
        font-size: 14px;
    }

    /* Rank Badge */
    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        background: var(--muted);
        color: var(--text-main);
        border: 1px solid var(--border);
    }

    .rank-badge-top {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
        color: white;
        border-color: var(--primary);
        box-shadow: 0 2px 8px rgba(229, 9, 20, 0.3);
    }

    /* Vote Count Badge */
    .vote-count-badge {
        display: inline-block;
        padding: 6px 12px;
        background: rgba(229, 9, 20, 0.15);
        color: var(--primary);
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
        border: 1px solid rgba(229, 9, 20, 0.3);
    }

    .vote-count-danger {
        background: rgba(229, 9, 20, 0.2);
        color: #E50914;
        border-color: rgba(229, 9, 20, 0.4);
    }

    /* Percentage Bar */
    .percentage-bar {
        position: relative;
        width: 100%;
        height: 24px;
        background: var(--bg);
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border);
    }

    .percentage-fill {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        background: linear-gradient(90deg, var(--primary) 0%, var(--primary-hover) 100%);
        transition: width 0.5s ease;
        border-radius: 12px;
    }

    .percentage-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 11px;
        font-weight: 600;
        color: var(--text-main);
        z-index: 1;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    /* Suspicious Row */
    .table-warning-row {
        background: rgba(255, 193, 7, 0.05) !important;
        border-left: 3px solid #ffc107;
    }

    .table-warning-row:hover {
        background: rgba(255, 193, 7, 0.1) !important;
    }

    .ip-address-cell {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .suspicious-badge {
        display: inline-block;
        padding: 4px 10px;
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid rgba(255, 193, 7, 0.4);
    }
</style>

<script>
    function resetVotes() {
        if (!confirm('WARNING: This will delete ALL votes. This action cannot be undone. Are you sure?')) {
            return;
        }

        if (!confirm('Are you absolutely certain? All voting data will be permanently deleted.')) {
            return;
        }

        $.ajax({
            url: '<?= URLROOT ?>/admin/voting/reset',
            type: 'POST',
            success: function (response) {
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) { }
                }

                if (response.success) {
                    alert('All votes have been reset.');
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Unknown error'));
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
            }
        });
    }

    function banIP(ip) {
        if (!confirm('Ban this IP address from voting? This action cannot be undone.')) {
            return;
        }

        $.ajax({
            url: '<?= URLROOT ?>/admin/voting/ban',
            type: 'POST',
            data: {
                ip: ip
            },
            success: function (response) {
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) { }
                }

                if (response.success) {
                    alert('IP address has been banned successfully.');
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Unknown error'));
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
            }
        });
    }
</script>