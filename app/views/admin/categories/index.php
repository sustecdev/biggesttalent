<div class="admin-page-header">
    <h2>Manage Categories</h2>
    <button class="btn-admin btn-primary" onclick="showAddCategoryModal()">+ Add Category</button>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['categories'])): ?>
                <tr>
                    <td colspan="6" class="text-center">No categories found. Add your first category!</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data['categories'] as $cat): ?>
                    <tr>
                        <td>
                            <?= $cat['id'] ?>
                        </td>
                        <td><strong>
                                <?= htmlspecialchars($cat['name']) ?>
                            </strong></td>
                        <td>
                            <?= htmlspecialchars($cat['description'] ?? '') ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= $cat['is_active'] ? 'success' : 'danger' ?>">
                                <?= $cat['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <?= date('M d, Y', strtotime($cat['created_at'] ?? 'now')) ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-admin btn-sm btn-primary"
                                    onclick="editCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($cat['description'] ?? '', ENT_QUOTES) ?>', <?= $cat['is_active'] ?>)">Edit</button>
                                <button class="btn-admin btn-sm btn-danger"
                                    onclick="deleteCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>')">Delete</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Category Modal -->
<div id="categoryModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add Category</h3>
            <span class="close" onclick="closeCategoryModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="categoryForm">
                <input type="hidden" id="categoryId" name="id" value="">
                <div class="form-group">
                    <label for="categoryName">Category Name *</label>
                    <input type="text" class="form-control" id="categoryName" name="name" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="categoryDescription">Description</label>
                    <textarea class="form-control" id="categoryDescription" name="description" rows="3"
                        maxlength="500"></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="categoryActive" name="is_active" value="1" checked>
                        Active (visible to users)
                    </label>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-admin btn-secondary" onclick="closeCategoryModal()">Cancel</button>
                    <button type="submit" class="btn-admin btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showAddCategoryModal() {
        document.getElementById('modalTitle').textContent = 'Add Category';
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
        document.getElementById('categoryActive').checked = true;
        document.getElementById('categoryModal').style.display = 'block';
    }

    function editCategory(id, name, description, isActive) {
        document.getElementById('modalTitle').textContent = 'Edit Category';
        document.getElementById('categoryId').value = id;
        document.getElementById('categoryName').value = name;
        document.getElementById('categoryDescription').value = description || '';
        document.getElementById('categoryActive').checked = isActive == 1;
        document.getElementById('categoryModal').style.display = 'block';
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').style.display = 'none';
    }

    function deleteCategory(id, name) {
        if (!confirm('Are you sure you want to delete "' + name + '"? This action cannot be undone.')) {
            return;
        }

        $.ajax({
            url: '<?= URLROOT ?>/admin/categories/delete',
            type: 'POST',
            data: {
                id: id
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (response.message || 'Failed to delete category'));
                }
            },
            error: function (xhr, status, error) {
                alert('Request failed: ' + error);
            }
        });
    }

    $(document).ready(function () {
        $('#categoryForm').on('submit', function (e) {
            e.preventDefault();

            var formData = {
                id: $('#categoryId').val(),
                name: $('#categoryName').val(),
                description: $('#categoryDescription').val(),
                is_active: $('#categoryActive').is(':checked') ? 1 : 0
            };

            $.ajax({
                url: '<?= URLROOT ?>/admin/categories/save',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to save category'));
                    }
                },
                error: function (xhr, status, error) {
                    alert('Request failed: ' + error);
                }
            });
        });

        // Close modal when clicking outside
        window.onclick = function (event) {
            var modal = document.getElementById('categoryModal');
            if (event.target == modal) {
                closeCategoryModal();
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
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
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

    .form-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }
</style>