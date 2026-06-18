<?php include "app/views/layouts/header.php"; ?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
    <p class="text-muted mb-0">Manage Sustainable Development Goal options used by extension projects.</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSdgModal">Add SDG</button>
</div>

<?php if(isset($message) && $message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">SDG Options</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    <th>SDG</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Projects</th>
                    <th class="text-end actions-column">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($sdgs as $sdg): ?>
                <?php $sdgId = intval($sdg['id']); ?>
                <tr>
                    <td><?= !empty($sdg['sdg_number']) ? intval($sdg['sdg_number']) : '-' ?></td>
                    <td><?= htmlspecialchars($sdg['label'] ?? '') ?></td>
                    <td><?= htmlspecialchars($sdg['description'] ?? '') ?></td>
                    <td>
                        <span class="badge bg-<?= ($sdg['status'] ?? 'Active') === 'Active' ? 'success' : 'secondary' ?>">
                            <?= htmlspecialchars($sdg['status'] ?? 'Active') ?>
                        </span>
                    </td>
                    <td><?= intval($sdg['project_count'] ?? 0) ?></td>
                    <td class="text-end table-actions actions-column">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSdgModal<?= $sdgId ?>">Edit</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($sdgs)): ?>
                <tr><td colspan="6" class="text-center text-muted">No SDG options recorded yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addSdgModal" tabindex="-1" aria-labelledby="addSdgModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="form_action" value="create">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSdgModalLabel">Add SDG</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label>SDG Number</label>
                            <input type="number" min="1" name="sdg_number" class="form-control">
                        </div>
                        <div class="col-md-9">
                            <label>Title</label>
                            <input name="title" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label>Status</label>
                            <select name="status" class="form-select">
                                <option>Active</option>
                                <option>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Save SDG</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach($sdgs as $sdg): ?>
    <?php $sdgId = intval($sdg['id']); ?>
    <div class="modal fade" id="editSdgModal<?= $sdgId ?>" tabindex="-1" aria-labelledby="editSdgModalLabel<?= $sdgId ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="form_action" value="update">
                    <input type="hidden" name="sdg_id" value="<?= $sdgId ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSdgModalLabel<?= $sdgId ?>">Edit SDG</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label>SDG Number</label>
                                <input type="number" min="1" name="sdg_number" class="form-control" value="<?= htmlspecialchars($sdg['sdg_number'] ?? '') ?>">
                            </div>
                            <div class="col-md-9">
                                <label>Title</label>
                                <input name="title" class="form-control" value="<?= htmlspecialchars($sdg['title'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-12">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($sdg['description'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label>Status</label>
                                <select name="status" class="form-select">
                                    <?php foreach(['Active','Inactive'] as $status): ?>
                                        <option <?= ($sdg['status'] ?? 'Active') === $status ? 'selected' : '' ?>><?= htmlspecialchars($status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary">Update SDG</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include "app/views/layouts/footer.php"; ?>
