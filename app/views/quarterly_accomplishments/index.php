<?php include "app/views/layouts/header.php"; ?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
    <div>
        <p class="text-muted mb-1">EVSU-ORDE&amp;S-F-027</p>
        <p class="text-muted mb-0">Record extension activities and accomplishments for each quarter.</p>
    </div>
    <?php if($canCreate): ?>
        <a class="btn btn-primary" href="index.php?page=create_quarterly_accomplishment"><i class="bi bi-plus-lg"></i> New Accomplishment Report</a>
    <?php endif; ?>
</div>

<?php if(isset($_GET['success'])): ?><div class="alert alert-success">Report <?= htmlspecialchars($_GET['success']) ?> action completed.</div><?php endif; ?>
<?php if(isset($_GET['error'])): ?><div class="alert alert-danger">Unable to complete that action.</div><?php endif; ?>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light"><tr><th>Period</th><th>College / Unit</th><th>Activities</th><th>Status</th><th>Created By</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach($reports as $r): ?>
                <?php
                    $editable=in_array($r['submission_status'] ?? 'Draft',['Draft','Recalled','For Revision','Not Approved'],true);
                    $canManageThis=$permissions[$r['id']] ?? false;
                ?>
                <tr>
                    <td><?= intval($r['quarter']) ?><?= ['','st','nd','rd','th'][intval($r['quarter'])] ?? 'th' ?> Quarter <?= intval($r['report_year']) ?></td>
                    <td><strong><?= htmlspecialchars($r['college'] ?? '') ?></strong><br><small class="text-muted"><?= htmlspecialchars($r['department'] ?? '') ?></small></td>
                    <td><?= intval($r['item_count'] ?? 0) ?></td>
                    <td><span class="badge bg-<?= ($r['submission_status'] ?? '')==='Approved'?'success':($editable?'secondary':'primary') ?>"><?= htmlspecialchars($r['submission_status'] ?? 'Draft') ?></span></td>
                    <td><?= htmlspecialchars($r['created_by_name'] ?? '') ?></td>
                    <td class="text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="index.php?page=view_quarterly_accomplishment&id=<?= intval($r['id']) ?>">View / Print</a>
                        <?php if($canManageThis && $editable): ?><a class="btn btn-sm btn-outline-success" href="index.php?page=edit_quarterly_accomplishment&id=<?= intval($r['id']) ?>">Edit</a><?php endif; ?>
                        <?php if($canManageThis && $editable): ?>
                            <form class="d-inline" method="post" action="index.php?page=delete_quarterly_accomplishment" onsubmit="return confirm('Delete this draft report?')"><input type="hidden" name="id" value="<?= intval($r['id']) ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($reports)): ?><tr><td colspan="6" class="text-muted">No quarterly accomplishment reports yet.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?= renderPagination($pagination ?? [],'accomplishment reports') ?>
</div>

<?php include "app/views/layouts/footer.php"; ?>
