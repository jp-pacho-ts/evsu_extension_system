<?php include 'app/views/layouts/header.php'; ?>

<div class="card p-4">
    <div class="d-flex justify-content-between no-print">
        <h2 class="fw-bold">Monitoring Prescriptive Decision Report</h2>
        <button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button>
    </div>

    <p class="text-muted">Generated on <?= date('F d, Y') ?></p>

    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Program</th>
                    <th>Project Title</th>
                    <th>Additional Monitoring</th>
                    <th>Recent Update</th>
                    <th>Status</th>
                    <th>Monitoring Date</th>
                    <th>Prescriptive Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($entries as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['program_title'] ?? '') ?></td>
                        <td><?= htmlspecialchars($e['project_title'] ?? '') ?></td>
                        <td><?= htmlspecialchars($e['activity_title'] ?? '') ?></td>
                        <td><?= htmlspecialchars($e['activity_description'] ?? '') ?></td>
                        <td><?= htmlspecialchars($e['status'] ?? '') ?></td>
                        <td><?= htmlspecialchars($e['monitoring_date'] ?? '') ?></td>
                        <td><?= htmlspecialchars(decisionSupport($e['status'] ?? '')) ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if(empty($entries)): ?>
                    <tr><td colspan="7" class="text-muted text-center">No monitoring records yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>
