<?php include "app/views/layouts/header.php"; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="fw-bold">🔔 Pending Approvals</h2>
        <p class="text-muted">Documents that need action from your account appear here.</p>
    </div>
</div>

<div class="card p-3">
    <h5 class="fw-bold">✅ Approval Transactions</h5>
    <p class="text-muted small">The number on the bell is based on these pending approval transactions.</p>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>College</th>
                    <th>Department</th>
                    <th>Period</th>
                    <th>Status</th>
                    <th style="min-width:280px;">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($reports as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['college']) ?></td>
                    <td><?= htmlspecialchars($r['department']) ?></td>
                    <td><?= htmlspecialchars($r['period_covered']) ?></td>
                    <td><span class="badge bg-primary"><?= htmlspecialchars($r['submission_status']) ?></span></td>
                    <td>
                        <a class="btn btn-sm btn-outline-primary" href="index.php?page=view_quarterly_report&id=<?= $r['id'] ?>">View</a>
                        <form method="POST" action="index.php?page=approval_action" class="mt-2">
                            <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                            <textarea name="approval_remarks" class="form-control form-control-sm mb-2" placeholder="Remarks"></textarea>
                            <button class="btn btn-sm btn-success" name="approval_action" value="approve">Approve / SGD</button>
                            <button class="btn btn-sm btn-danger" name="approval_action" value="revise">For Revision</button>
                            <?php if(in_array($_SESSION['role'] ?? '', ['Super Admin','Admin']) && (($r['submission_status'] ?? '') == 'VP ORIES Approved')): ?>
                                <button class="btn btn-sm btn-dark" name="approval_action" value="archive">Archive</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($reports)): ?>
                <tr><td colspan="5" class="text-muted">No pending approval transaction for your account.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "app/views/layouts/footer.php"; ?>
