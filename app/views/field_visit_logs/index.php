<?php include "app/views/layouts/header.php"; ?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
    <div>
        <p class="text-muted mb-1">EVSU-ORDE&amp;S-F-076</p>
        <p class="text-muted mb-0">Create work plans and monitoring logs for project field visits.</p>
    </div>
    <?php if($canCreate): ?>
        <a class="btn btn-primary" href="index.php?page=create_field_visit_log"><i class="bi bi-plus-lg"></i> New Field Visit Log</a>
    <?php endif; ?>
</div>

<?php if(isset($_GET['success'])): ?><div class="alert alert-success">Field visit log <?= htmlspecialchars($_GET['success']) ?> action completed.</div><?php endif; ?>
<?php if(isset($_GET['error'])): ?><div class="alert alert-danger">Unable to complete that action.</div><?php endif; ?>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light"><tr><th>Period</th><th>Program / Project</th><th>Visits</th><th>Status</th><th>Created By</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach($logs as $r): ?>
                <?php $editable=in_array($r['submission_status'] ?? 'Draft',['Draft','Recalled','For Revision','Not Approved'],true); $canManageThis=$permissions[$r['id']] ?? false; ?>
                <tr>
                    <td><?= intval($r['quarter']) ?><?= ['','st','nd','rd','th'][intval($r['quarter'])] ?? 'th' ?> Quarter <?= intval($r['report_year']) ?></td>
                    <td><strong><?= htmlspecialchars($r['project_title'] ?? '') ?></strong><br><small class="text-muted"><?= htmlspecialchars($r['program_title'] ?? '') ?></small></td>
                    <td><?= intval($r['item_count'] ?? 0) ?></td>
                    <td><span class="badge bg-<?= ($r['submission_status'] ?? '')==='Approved'?'success':($editable?'secondary':'primary') ?>"><?= htmlspecialchars($r['submission_status'] ?? 'Draft') ?></span></td>
                    <td><?= htmlspecialchars($r['created_by_name'] ?? '') ?></td>
                    <td class="text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="index.php?page=view_field_visit_log&id=<?= intval($r['id']) ?>">View / Print</a>
                        <?php if($canManageThis && $editable): ?><a class="btn btn-sm btn-outline-success" href="index.php?page=edit_field_visit_log&id=<?= intval($r['id']) ?>">Edit</a><?php endif; ?>
                        <?php if($canManageThis && $editable): ?><form class="d-inline" method="post" action="index.php?page=delete_field_visit_log" onsubmit="return confirm('Delete this draft field visit log?')"><input type="hidden" name="id" value="<?= intval($r['id']) ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form><?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($logs)): ?><tr><td colspan="6" class="text-muted">No field visit logs yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?= renderPagination($pagination ?? [],'field visit logs') ?>
</div>

<?php include "app/views/layouts/footer.php"; ?>
