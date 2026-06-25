<?php include "app/views/layouts/header.php"; ?>

<p class="text-muted">Documents that need action from your account appear here.</p>

<div class="card p-3">
    <h5 class="fw-bold">Approval Transactions</h5>
    <p class="text-muted small">The number on the bell is based on these pending approval transactions.</p>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Document</th>
                    <th>College</th>
                    <th>Department</th>
                    <th>Period</th>
                    <th>Status</th>
                    <th style="min-width:280px;">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($reports as $r): ?>
                <?php
                    $documentType = $r['document_type'] ?? 'quarterly_report';
                    $viewPage = $documentType === 'quarterly_accomplishment' ? 'view_quarterly_accomplishment' : ($documentType === 'field_visit_log' ? 'view_field_visit_log' : 'view_quarterly_report');
                ?>
                <tr>
                    <td><?= htmlspecialchars($r['document_label'] ?? 'Quarterly Monitoring') ?></td>
                    <td><?= htmlspecialchars($r['college']) ?></td>
                    <td><?= htmlspecialchars($r['department']) ?></td>
                    <td><?= htmlspecialchars($r['period_covered']) ?></td>
                    <td><span class="badge bg-primary"><?= htmlspecialchars($r['submission_status']) ?></span></td>
                    <td>
                        <a class="btn btn-sm btn-outline-primary" href="index.php?page=<?= $viewPage ?>&id=<?= $r['id'] ?>">View</a>
                        <form method="POST" action="index.php?page=approval_action" class="mt-2">
                            <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                            <input type="hidden" name="document_type" value="<?= htmlspecialchars($documentType) ?>">
                            <textarea name="approval_remarks" class="form-control form-control-sm mb-2" placeholder="Remarks"></textarea>
                            <button class="btn btn-sm btn-success" name="approval_action" value="approve">Approved</button>
                            <button class="btn btn-sm btn-danger" name="approval_action" value="not_approve">Not Approved</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($reports)): ?>
                <tr><td colspan="6" class="text-muted">No pending approval transaction for your account.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "app/views/layouts/footer.php"; ?>
