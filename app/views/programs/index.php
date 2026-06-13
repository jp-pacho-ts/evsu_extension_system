<?php include 'app/views/layouts/header.php'; ?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
    <p class="text-muted mb-0">Umbrella program information from the Extension Office.</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProgramModal">Add Program</button>
</div>

<?php if($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Program</th>
                    <th>Leader</th>
                    <th>Assistant</th>
                    <th>Cost</th>
                    <th>Duration</th>
                    <th>S.O.</th>
                    <th class="text-end actions-column">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($programs as $p): ?>
                <?php $programId = intval($p['id']); ?>
                <tr>
                    <td><?= htmlspecialchars($p['program_title'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['leader'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['assistant_leader'] ?? '') ?></td>
                    <td>&#8369;<?= number_format((float)($p['project_cost'] ?? 0), 2) ?></td>
                    <td><?= htmlspecialchars($p['start_date'] ?? '') ?> to <?= htmlspecialchars($p['end_date'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['special_order_no'] ?? '') ?></td>
                    <td class="text-end table-actions actions-column">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProgramModal<?= $programId ?>">Edit</button>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteProgramModal<?= $programId ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($programs)): ?>
                <tr><td colspan="7" class="text-center text-muted">No programs recorded yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?= renderPagination($pagination ?? [], 'programs') ?>
</div>

<div class="modal fade" id="addProgramModal" tabindex="-1" aria-labelledby="addProgramModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="form_action" value="create">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProgramModalLabel">Add Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php $programForm = []; include 'app/views/programs/_form.php'; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Save Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach($programs as $p): ?>
    <?php $programId = intval($p['id']); ?>
    <div class="modal fade" id="editProgramModal<?= $programId ?>" tabindex="-1" aria-labelledby="editProgramModalLabel<?= $programId ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="form_action" value="update">
                    <input type="hidden" name="program_id" value="<?= $programId ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProgramModalLabel<?= $programId ?>">Edit Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php $programForm = $p; include 'app/views/programs/_form.php'; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary">Update Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteProgramModal<?= $programId ?>" tabindex="-1" aria-labelledby="deleteProgramModalLabel<?= $programId ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="form_action" value="delete">
                    <input type="hidden" name="program_id" value="<?= $programId ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteProgramModalLabel<?= $programId ?>">Delete Program</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-1">Delete this program?</p>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($p['program_title'] ?? '') ?></p>
                        <p class="text-muted small mt-2 mb-0">Programs with related projects cannot be deleted.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger">Delete Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include 'app/views/layouts/footer.php'; ?>
