<?php include "app/views/layouts/header.php"; ?>

<h2 class="fw-bold">Approval Queue</h2>
<p class="text-muted">Reports shown here depend on your current quarterly report approval level.</p>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>College</th>
                    <th>Department</th>
                    <th>Period</th>
                    <th>Submitted By</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th style="min-width:260px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reports as $r): ?>
                    <?php
                        $status = $r['submission_status'] ?? 'Draft';
                        $badge = (strpos($status, 'Approved') !== false || $status == 'Approved') ? 'success' : (($status == 'For Revision' || $status == 'Not Approved') ? 'danger' : 'primary');
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($r['college']) ?></td>
                        <td><?= htmlspecialchars($r['department']) ?></td>
                        <td><?= htmlspecialchars($r['period_covered']) ?></td>
                        <td><?= htmlspecialchars($r['submitted_by_name'] ?? '') ?></td>
                        <td><span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($status) ?></span></td>
                        <td><?= htmlspecialchars($r['approval_remarks'] ?? '') ?></td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary mb-1" href="index.php?page=view_quarterly_report&id=<?= $r['id'] ?>">View</a>
                            <form method="POST" action="index.php?page=approval_action" class="mt-2">
                                <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                                <textarea name="approval_remarks" class="form-control form-control-sm mb-2" placeholder="Remarks"></textarea>
                                <div class="d-flex flex-wrap gap-1">
                                    <button class="btn btn-sm btn-success" name="approval_action" value="approve">Approved</button>
                                    <button class="btn btn-sm btn-danger" name="approval_action" value="not_approve">Not Approved</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if(empty($reports)): ?>
                    <tr><td colspan="7" class="text-muted">No report pending for your current approval level.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "app/views/layouts/footer.php"; ?>
