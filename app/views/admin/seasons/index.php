<div class="admin-page-header">
    <h2>Manage Seasons</h2>
    <div class="page-actions">
        <button class="btn-admin btn-primary" data-toggle="modal" data-target="#addSeasonModal">Create New
            Season</button>
    </div>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Season Number</th>
                <th>Title</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Stage</th>
                <th>Nominations</th>
                <th>Voting</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['seasons'])): ?>
                <tr>
                    <td colspan="9" class="text-center">No seasons found. <a href="#" data-toggle="modal"
                            data-target="#addSeasonModal">Create your first season</a></td>
                </tr>
            <?php else: ?>
                <?php foreach ($data['seasons'] as $season):
                    $now = time();
                    $start = strtotime($season['start_date']);
                    $end = strtotime($season['end_date']);
                    $status = 'upcoming';
                    if ($now >= $start && $now <= $end)
                        $status = 'active';
                    if ($now > $end)
                        $status = 'ended';

                    $currentStage = $season['current_stage'] ?? 'national';
                    $stageLabel = ucfirst(str_replace('_', ' ', $currentStage));
                    ?>
                    <tr>
                        <td>
                            <?= $season['id'] ?>
                        </td>
                        <td><strong>Season
                                <?= $season['season_number'] ?>
                            </strong></td>
                        <td>
                            <?= htmlspecialchars($season['title']) ?>
                        </td>
                        <td>
                            <?= date('M d, Y', strtotime($season['start_date'])) ?>
                        </td>
                        <td>
                            <?= date('M d, Y', strtotime($season['end_date'])) ?>
                        </td>
                        <td>
                            <span
                                class="badge badge-<?= $status == 'active' ? 'success' : ($status == 'ended' ? 'secondary' : 'warning') ?>">
                                <?= ucfirst($status) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-info"><?= $stageLabel ?></span>
                        </td>
                        <td>
                            <?php
                            $nomOpen = isset($season['is_nominations_open']) && $season['is_nominations_open'] == 1;
                            $voteOpen = isset($season['is_voting_open']) && $season['is_voting_open'] == 1;
                            ?>
                            <button class="btn-admin btn-sm btn-<?= $nomOpen ? 'success' : 'secondary' ?>"
                                onclick="toggleFeature(<?= $season['id'] ?>, 'nominations', <?= $nomOpen ? 0 : 1 ?>)">
                                <?= $nomOpen ? 'Open' : 'Closed' ?>
                            </button>
                        </td>
                        <td>
                            <button class="btn-admin btn-sm btn-<?= $voteOpen ? 'success' : 'secondary' ?>"
                                onclick="toggleFeature(<?= $season['id'] ?>, 'voting', <?= $voteOpen ? 0 : 1 ?>)">
                                <?= $voteOpen ? 'Open' : 'Closed' ?>
                            </button>
                        </td>
                        <td>
                            <?php
                            $isActive = isset($season['is_active']) && $season['is_active'] == 1;
                            ?>
                            <?php if ($isActive): ?>
                                <span class="badge badge-success" style="font-size: 14px; padding: 6px 12px;">
                                    ✓ Active Season
                                </span>
                            <?php else: ?>
                                <button class="btn-admin btn-sm btn-outline-primary btn-toggle-active"
                                    data-id="<?= $season['id'] ?>" title="Set as active season">
                                    Set Active
                                </button>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <!-- Helper links for Edit/Delete points to MVC -->
                                <a href="<?= URLROOT ?>/admin/seasons/edit/<?= $season['id'] ?>"
                                    class="btn-admin btn-sm btn-secondary">Edit</a>
                                <?php if (!$isActive): ?>
                                    <button class="btn-admin btn-sm btn-danger btn-delete"
                                        data-id="<?= $season['id'] ?>">Delete</button>
                                <?php else: ?>
                                    <button class="btn-admin btn-sm btn-danger" disabled
                                        title="Cannot delete active season">Delete</button>

                                    <?php if ($currentStage !== 'closed'): ?>
                                        <button class="btn-admin btn-sm btn-primary btn-progress" data-id="<?= $season['id'] ?>"
                                            data-stage="<?= $currentStage ?>"
                                            onclick="progressStage(<?= $season['id'] ?>, '<?= $currentStage ?>')">
                                            Next Stage ⏩
                                        </button>
                                    <?php endif; ?>

                                <?php endif; ?>
                                <?php if ($status == 'active'): ?>
                                    <button class="btn-admin btn-sm btn-warning" onclick="closeSeason(<?= $season['id'] ?>)">Close
                                        Season</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div>

<!-- Add Season Modal -->
<div class="modal fade" id="addSeasonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Season</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addSeasonForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Season Number *</label>
                        <input type="number" class="form-control" name="season_number" required>
                    </div>
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" class="form-control" name="title" required
                            placeholder="e.g., Season 5: Global Harmony">
                    </div>
                    <div class="form-group">
                        <label>Start Date *</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>End Date *</label>
                        <input type="date" class="form-control" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-admin btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-admin btn-primary">Create Season</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Progress Stage Review Modal -->
<div class="modal fade" id="progressStageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Stage Progression</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>⚠️ WARNING:</strong> You are about to advance the season. This action will eliminate
                    contestants and reset votes.
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <strong>Current Stage:</strong> <span id="reviewCurrentStage"></span>
                    </div>
                    <div>
                        <strong>Next Stage:</strong> <span id="reviewNextStage"></span>
                    </div>
                    <div>
                        <strong>Qualifying Limit:</strong> <span id="reviewLimit"></span>
                    </div>
                </div>

                <h6>Projected Qualifiers (Top Candidates)</h6>
                <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd;">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Votes</th>
                                <th>Country</th>
                            </tr>
                        </thead>
                        <tbody id="reviewQualifiersList">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="progressSeasonId">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmProgressStage()">Confirm & Proceed
                    ⏩</button>
            </div>
        </div>
    </div>
</div>

<!-- Winner Selection Modal -->
<div class="modal fade" id="winnerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🏆 Select Season Winner</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <strong>Grand Finale:</strong> Select the ultimate winner from the finalists below.
                    This will close the season and declare the winner.
                </div>

                <form id="winnerForm">
                    <input type="hidden" name="season_id" id="winnerSeasonId">
                    <div class="row" id="winnerCandidates">
                        <!-- Populated via JS -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmWinner()">Declare Winner 🏆</button>
            </div>
        </div>
    </div>
</div>

<style>
    .finalist-card {
        background: #141414;
        /* var(--card) fallback */
        background: var(--card-bg, #141414);
        border: 1px solid var(--border, #333);
        color: var(--text-main, #fff);
        transition: transform 0.2s;
    }

    .finalist-card:hover {
        transform: translateY(-2px);
        border-color: var(--primary, #cd217d);
    }

    .finalist-card.winner-highlight {
        border: 2px solid var(--primary, #cd217d);
        box-shadow: 0 0 15px rgba(205, 33, 125, 0.3);
    }

    .finalist-card .card-subtitle {
        color: var(--text-muted, #aaa) !important;
    }
</style>

<script>
    $(document).ready(function () {
        $('#addSeasonForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: '<?= URLROOT ?>/admin/seasons/save',
                type: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    if (typeof response === 'string') {
                        try { response = JSON.parse(response); } catch (e) {
                            console.error('JSON Parse Error', e);
                            showModalAlert('Server returned invalid JSON', 'Error');
                            return;
                        }
                    }
                    if (response.success) {
                        $('#addSeasonModal').modal('hide');
                        showModalAlert('Season Created Successfully!', 'Success');
                        $('#messageModal').on('hidden.bs.modal', function () {
                            location.reload();
                        });
                    } else {
                        showModalAlert('Error: ' + (response.message || 'Unknown error'), 'Error');
                    }
                },
                error: function (xhr, status, error) {
                    showModalAlert('Connection Error: ' + status, 'Error');
                }
            });
        });
    });

    function toggleFeature(seasonId, feature, initialStatus) {
        // Optimistic UI update or wait? Let's wait for confirmation or just do it.
        // User asked for "Closed" modal on frontend, here specific UI isn't defined but simple toggle is best.
        // Let's just do it.
        var action = initialStatus == 1 ? 'Open' : 'Close';

        $.ajax({
            url: '<?= URLROOT ?>/admin/seasons/toggleFeature',
            type: 'POST',
            data: { season_id: seasonId, feature: feature, status: initialStatus },
            success: function (response) {
                if (typeof response === 'string') {
                    try { response = JSON.parse(response); } catch (e) { }
                }
                if (response.success) {
                    // removing showModalAlert to avoid spamming user
                    location.reload();
                } else {
                    showModalAlert('Error: ' + response.message, 'Error');
                }
            },
            error: function () {
                showModalAlert('Failed to update status', 'Error');
            }
        });
    }

    function closeSeason(id) {
        showModalConfirm('Close this season? Voting will be disabled.', function () {
            $.ajax({
                url: '<?= URLROOT ?>/admin/seasons/close',
                type: 'POST',
                data: { id: id },
                success: function (response) {
                    if (typeof response === 'string') {
                        try { response = JSON.parse(response); } catch (e) { }
                    }
                    if (response.success) {
                        location.reload();
                    } else {
                        showModalAlert('Error: ' + (response.message || 'Unknown error'), 'Error');
                    }
                }
            });
        });
    }


    $('.btn-delete').on('click', function () {
        var id = $(this).data('id');
        showModalConfirm('Are you sure?', function () {
            $.ajax({
                url: '<?= URLROOT ?>/admin/seasons/delete',
                type: 'POST',
                data: { id: id },
                success: function (response) {
                    if (typeof response === 'string') {
                        try { response = JSON.parse(response); } catch (e) { }
                    }
                    if (response.success) location.reload();
                    else showModalAlert('Error: ' + (response.message || 'Unknown'), 'Error');
                }
            });
        });
    });

    // Handle toggle active season
    $('.btn-toggle-active').on('click', function () {
        var id = $(this).data('id');
        var btn = $(this);

        showModalConfirm('Set this season as active? This will deactivate all other seasons and new nominations will be linked to this season.', function () {
            btn.prop('disabled', true).text('Activating...');

            $.ajax({
                url: '<?= URLROOT ?>/admin/seasons/toggleActive',
                type: 'POST',
                data: { id: id },
                success: function (response) {
                    if (typeof response === 'string') {
                        try { response = JSON.parse(response); } catch (e) { }
                    }
                    if (response.success) {
                        showModalAlert('Season activated successfully!', 'Success');
                        $('#messageModal').on('hidden.bs.modal', function () {
                            location.reload();
                        });
                    } else {
                        showModalAlert('Error: ' + (response.message || 'Unknown error'), 'Error');
                        btn.prop('disabled', false).text('Set Active');
                    }
                },
                error: function (xhr, status, error) {
                    showModalAlert('Connection Error: ' + status + ' - ' + error, 'Error');
                    btn.prop('disabled', false).text('Set Active');
                }
            });
        });
    });

    function progressStage(id, currentStage) {
        // Load data for review
        $.ajax({
            url: '<?= URLROOT ?>/admin/seasons/getStageSimulation?id=' + id,
            type: 'GET',
            success: function (response) {
                if (typeof response === 'string') {
                    try { response = JSON.parse(response); } catch (e) { }
                }

                if (!response.success) {
                    showModalAlert('Error preparing review: ' + response.message, 'Error');
                    return;
                }

                // Check if this is the final stage transition (Round 3 -> Closed)
                if (currentStage == 'round_3') {
                    openWinnerModal(id, response.qualifiers); // Use the finalists (qualifiers for this stage)
                    return;
                }

                // Populate Modal
                $('#progressSeasonId').val(id);
                $('#reviewCurrentStage').text(response.current_stage);
                $('#reviewNextStage').text(response.next_stage);
                $('#reviewLimit').text(response.limit);

                var tbody = $('#reviewQualifiersList');
                tbody.empty();

                if (response.qualifiers.length === 0) {
                    tbody.append('<tr><td colspan="4" class="text-center">No qualifiers found.</td></tr>');
                } else {
                    response.qualifiers.forEach(function (q, index) {
                        var row = '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + (q.aname || 'Unknown') + '</td>' +
                            '<td>' + (q.vote_count || 0) + '</td>' +
                            '<td>' + (q.country_name || 'N/A') + '</td>' +
                            '</tr>';
                        tbody.append(row);
                    });
                }

                // Show Modal
                $('#progressStageModal').modal('show');
            },
            error: function () {
                showModalAlert('Failed to load stage simulation.', 'Error');
            }
        });
    }

    function confirmProgressStage() {
        var id = $('#progressSeasonId').val();
        $.ajax({
            url: '<?= URLROOT ?>/admin/seasons/progressStage',
            type: 'POST',
            data: { id: id },
            success: function (response) {
                if (typeof response === 'string') {
                    try { response = JSON.parse(response); } catch (e) {
                        console.error("Parse error", e);
                    }
                }
                if (response.success) {
                    $('#progressStageModal').modal('hide');
                    showModalAlert('Success: ' + response.message, 'Success');
                    $('#messageModal').on('hidden.bs.modal', function () {
                        location.reload();
                    });
                } else {
                    showModalAlert('Error: ' + (response.message || 'Unknown error'), 'Error');
                }
            },
            error: function (xhr, status, error) {
                showModalAlert('Connection Error: ' + status + ' - ' + error, 'Error');
            }
        });
    }

    function openWinnerModal(seasonId, finalists) {
        $('#winnerSeasonId').val(seasonId);
        var container = $('#winnerCandidates');
        container.empty();

        if (finalists.length === 0) {
            container.html('<div class="col-12 text-center">No finalists found.</div>');
        } else {
            // Sort by votes DESC
            finalists.sort((a, b) => b.vote_count - a.vote_count);

            finalists.forEach(function (f, index) {
                var isTop = index === 0;
                var html = `
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 finalist-card ${isTop ? 'winner-highlight' : ''}">
                            <div class="card-body">
                                <h5 class="card-title text-white">${f.aname}</h5>
                                <h6 class="card-subtitle mb-2">${f.country_name || 'N/A'}</h6>
                                <p class="card-text text-white-50">
                                    <strong>Votes:</strong> ${f.vote_count} <br>
                                    <span class="badge badge-${isTop ? 'success' : 'secondary'}">Rank #${index + 1}</span>
                                </p>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="winner_${f.id}" name="winner_id" value="${f.id}" class="custom-control-input" ${isTop ? 'checked' : ''}>
                                    <label class="custom-control-label text-white" for="winner_${f.id}">Select as Winner</label>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.append(html);
            });
        }

        $('#winnerModal').modal('show');
    }

    function confirmWinner() {
        var winnerId = $('input[name="winner_id"]:checked').val();
        if (!winnerId) {
            showModalAlert('Please select a winner first.', 'Warning');
            return;
        }

        showModalConfirm('Are you sure you want to declare this contestant as the WINNER and close the season?', function () {
            var seasonId = $('#winnerSeasonId').val();
            $.ajax({
                url: '<?= URLROOT ?>/admin/seasons/saveWinner',
                type: 'POST',
                data: { season_id: seasonId, winner_id: winnerId },
                success: function (response) {
                    if (typeof response === 'string') {
                        try { response = JSON.parse(response); } catch (e) { }
                    }
                    if (response.success) {
                        $('#winnerModal').modal('hide');
                        showModalAlert('🏆 ' + response.message, 'Success');
                        $('#messageModal').on('hidden.bs.modal', function () {
                            location.reload();
                        });
                    } else {
                        showModalAlert('Error: ' + (response.message || 'Unknown'), 'Error');
                    }
                },
                error: function () {
                    showModalAlert('Connection failed', 'Error');
                }
            });
        });
    }
</script>