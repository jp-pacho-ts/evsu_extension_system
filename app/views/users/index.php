<?php include "app/views/layouts/header.php"; ?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
    <p class="text-muted mb-0">Manage accounts, roles, signatories, and routing groups.</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Create Account</button>
</div>

<?php if(isset($message) && $message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php
$search = strtolower($_GET['search'] ?? '');
$collegeFilter = $_GET['college_filter'] ?? '';
$usersFiltered = $users ?? [];

if($search !== '') {
    $usersFiltered = array_filter($usersFiltered, function($u) use ($search) {
        return str_contains(strtolower(($u['fullname'] ?? '').' '.($u['username'] ?? '').' '.($u['role'] ?? '').' '.($u['college'] ?? '').' '.($u['department'] ?? '').' '.($u['campus'] ?? '')), $search);
    });
}
if($collegeFilter !== '') {
    $usersFiltered = array_filter($usersFiltered, fn($u) => ($u['college'] ?? '') == $collegeFilter);
}

$colleges = [];
foreach($usersFiltered as $u) {
    $college = $u['college'] ?: 'Unassigned College';
    $colleges[$college][] = $u;
}

$roleOptions = ['Faculty','Department Coordinator','School Coordinator','Campus Director','VP ORIES','Super Admin','Admin'];
$nameForRole = function($list, $role, $fallback) {
    foreach($list as $item) {
        if(($item['role'] ?? '') === $role) return $item['fullname'] ?? $fallback;
    }
    return $fallback;
};
?>

<div class="card p-3 mb-4">
    <h5 class="fw-bold">Search / Filter Users</h5>
    <form method="GET" class="row g-2">
        <input type="hidden" name="page" value="users">
        <div class="col-md-4"><input name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="form-control" placeholder="Search name, username, role, department..."></div>
        <div class="col-md-4">
            <select name="college_filter" class="form-select">
                <option value="">All Colleges</option>
                <?php foreach(array_unique(array_map(fn($u)=>$u['college'] ?? '', $users ?? [])) as $c): if($c): ?>
                    <option value="<?= htmlspecialchars($c) ?>" <?= $collegeFilter==$c?'selected':'' ?>><?= htmlspecialchars($c) ?></option>
                <?php endif; endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary">Filter</button>
            <a href="index.php?page=users" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="card p-3 mb-4">
    <h5 class="fw-bold">Signatory Routing Management UI</h5>
    <p class="text-muted small">Use this as routing reference for school/campus approval flow.</p>
    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>College / School</th>
                    <th>Department</th>
                    <th>School Coordinator</th>
                    <th>Campus Director / Dean</th>
                    <th>Extension Office</th>
                    <th>VP ORIES</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($colleges as $college => $list): ?>
                <?php
                    $departments = array_unique(array_filter(array_map(fn($u)=>$u['department'] ?? '', $list)));
                    if(empty($departments)) $departments = ['General'];
                ?>
                <?php foreach($departments as $dept): ?>
                    <tr>
                        <td><?= htmlspecialchars($college) ?></td>
                        <td><?= htmlspecialchars($dept) ?></td>
                        <td><?= htmlspecialchars($nameForRole($list, 'School Coordinator', 'schoolcoord')) ?></td>
                        <td><?= htmlspecialchars($nameForRole($list, 'Campus Director', 'campusdirector')) ?></td>
                        <td>System Super Admin / Extension Office</td>
                        <td>VP ORIES</td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<h5 class="fw-bold">Grouped / Collapsible College View</h5>
<div class="accordion" id="collegeAccordion">
<?php $i=0; foreach($colleges as $college => $list): $i++; ?>
    <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="heading<?= $i ?>">
            <button class="accordion-button <?= $i>1?'collapsed':'' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#college<?= $i ?>">
                <?= htmlspecialchars($college) ?> <span class="badge bg-primary ms-2"><?= count($list) ?></span>
            </button>
        </h2>
        <div id="college<?= $i ?>" class="accordion-collapse collapse <?= $i==1?'show':'' ?>" data-bs-parent="#collegeAccordion">
            <div class="accordion-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Department</th>
                                <th>Campus</th>
                                <th>Signatory</th>
                                <th>Ext. Services</th>
                                <th>Status</th>
                                <th>Signature</th>
                                <th class="text-end actions-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($list as $u): ?>
                            <?php $userId = intval($u['id']); ?>
                            <tr>
                                <td><?= htmlspecialchars($u['fullname'] ?? '') ?></td>
                                <td><?= htmlspecialchars($u['username'] ?? '') ?></td>
                                <td><?= htmlspecialchars($u['role'] ?? '') ?></td>
                                <td><?= htmlspecialchars($u['department'] ?? '') ?></td>
                                <td><?= htmlspecialchars($u['campus'] ?? '') ?></td>
                                <td><?= htmlspecialchars($u['signatory_title'] ?? '') ?></td>
                                <td><?= !empty($u['has_extension_services']) ? 'Yes' : 'No' ?></td>
                                <td><span class="badge bg-<?= ($u['account_status'] ?? 'Active') === 'Active' ? 'success' : 'secondary' ?>"><?= htmlspecialchars($u['account_status'] ?? 'Active') ?></span></td>
                                <td><?= !empty($u['signature_image']) ? '<span class="text-success">Uploaded</span>' : '<span class="text-muted">None</span>' ?></td>
                                <td class="text-end table-actions actions-column">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $userId ?>">Edit</button>
                                    <?php if(($u['username'] ?? '') !== 'admin'): ?>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?= $userId ?>">Delete</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="create_user" value="1">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Create Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php $userForm = []; $isEdit = false; include "app/views/users/_form.php"; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach($usersFiltered as $u): ?>
    <?php $userId = intval($u['id']); ?>
    <div class="modal fade" id="editUserModal<?= $userId ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?= $userId ?>" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="update_user" value="1">
                    <input type="hidden" name="user_id" value="<?= $userId ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel<?= $userId ?>">Edit Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php $userForm = $u; $isEdit = true; include "app/views/users/_form.php"; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary">Update Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if(($u['username'] ?? '') !== 'admin'): ?>
        <div class="modal fade" id="deleteUserModal<?= $userId ?>" tabindex="-1" aria-labelledby="deleteUserModalLabel<?= $userId ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <input type="hidden" name="delete_user" value="1">
                        <input type="hidden" name="user_id" value="<?= $userId ?>">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteUserModalLabel<?= $userId ?>">Delete Account</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-1">Delete this account?</p>
                            <p class="fw-bold mb-0"><?= htmlspecialchars($u['fullname'] ?? '') ?></p>
                            <p class="text-muted small mt-2 mb-0">This removes the user account from the system.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-danger">Delete Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php include "app/views/layouts/footer.php"; ?>
