</div> <!-- .admin-content -->
</main> <!-- .admin-main -->
</div> <!-- .admin-wrapper -->

<script>
    // Admin JavaScript
    $(document).ready(function () {
        // Handle status updates
        $('.status-update').on('change', function () {
            const id = $(this).data('id');
            const status = $(this).val();
            const type = $(this).data('type');

            $.ajax({
                url: '<?= URLROOT ?>/legacy/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'update_status',
                    type: type,
                    id: id,
                    status: status
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });

        // Handle deletions
        $('.btn-delete').on('click', function (e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
                return false;
            }
        });
    });

    // Global Modal Helpers
    function showModalAlert(message, title = 'Notification') {
        $('#messageModalTitle').text(title);
        $('#messageModalBody').text(message);
        $('#messageModal').modal('show');
    }

    // Global Confirm Modal Helper
    let confirmCallback = null;
    function showModalConfirm(message, callback) {
        $('#confirmModalBody').text(message);
        confirmCallback = callback;
        $('#confirmModal').modal('show');
    }

    $(document).ready(function () {
        $('#confirmModalBtn').on('click', function () {
            if (confirmCallback) {
                confirmCallback();
                confirmCallback = null;
            }
            $('#confirmModal').modal('hide');
        });
    });
</script>

<!-- Global Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalTitle">Notification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="messageModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Global Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                Are you sure?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmModalBtn">Confirm</button>
            </div>
        </div>
    </div>
    </script>
    </body>

    </html>