<?php include 'app/views/layouts/header.php'; ?>

<style>
.report-print-header {
    margin-bottom: 16px;
}

.report-print-title {
    margin: 0 0 4px;
    font-size: 28px;
    font-weight: 700;
    line-height: 1.15;
    text-align: center;
}

.report-print-meta {
    margin: 0;
    color: #6b7280;
    text-align: right;
}

@media print {
    @page {
        size: landscape;
        margin: 10mm;
    }

    body {
        background: #ffffff !important;
    }

    .prescriptive-report {
        width: 100% !important;
        max-width: none !important;
        padding: 0 !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    .prescriptive-report .table-responsive {
        width: 100% !important;
        overflow: visible !important;
    }

    .prescriptive-print-table {
        width: 100% !important;
        table-layout: fixed;
        border-collapse: collapse;
        font-size: 8.5px;
        line-height: 1.25;
    }

    .report-print-header {
        margin-bottom: 8px;
    }

    .report-print-title {
        font-size: 24px;
        text-transform: uppercase;
    }

    .report-print-meta {
        font-size: 10px;
    }

    .prescriptive-print-table thead th {
        font-size: 9.5px;
        font-weight: 700;
    }

    .prescriptive-print-table th,
    .prescriptive-print-table td {
        padding: 3px 4px !important;
        white-space: normal !important;
        overflow-wrap: anywhere;
        word-break: break-word;
        vertical-align: top !important;
    }

    .prescriptive-print-table th:nth-child(1) { width: 17%; }
    .prescriptive-print-table th:nth-child(2) { width: 13%; }
    .prescriptive-print-table th:nth-child(3) { width: 15%; }
    .prescriptive-print-table th:nth-child(4) { width: 22%; }
    .prescriptive-print-table th:nth-child(5) { width: 8%; }
    .prescriptive-print-table th:nth-child(6) { width: 10%; }
    .prescriptive-print-table th:nth-child(7) { width: 15%; }
}
</style>

<div class="card p-4 prescriptive-report">
    <div class="d-flex justify-content-end no-print mb-3">
        <button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button>
    </div>

    <div class="report-print-header">
        <h2 class="report-print-title">Prescriptive Report - <?= htmlspecialchars(function_exists('systemName') ? systemName() : 'GESESP-DA') ?></h2>
        <p class="report-print-meta">Generated on <?= date('F d, Y') ?></p>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle prescriptive-print-table">
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
