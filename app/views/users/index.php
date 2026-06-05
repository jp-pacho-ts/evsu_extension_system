<?php include "app/views/layouts/header.php"; ?>

<h2 class="fw-bold">👥 User Management</h2>
<p class="text-muted">Manage accounts, roles, signatories, and routing groups.</p>

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
?>

<div class="card p-3 mb-4">
    <h5 class="fw-bold">Add / Create Account</h5>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="create_user" value="1">
        <div class="row g-2">
            <div class="col-md-3"><label>Full Name</label><input name="fullname" class="form-control" required></div>
            <div class="col-md-2"><label>Username</label><input name="username" class="form-control" required></div>
            <div class="col-md-2"><label>Password</label><input name="password" type="password" class="form-control" required></div>
            <div class="col-md-2"><label>Role</label>
                <select name="role" class="form-select">
                    <option>Department Coordinator</option>
                    <option>School Coordinator</option>
                    <option>Campus Director</option>
                    <option>VP ORIES</option>
                    <option>Super Admin</option>
                    <option>Admin</option>
                </select>
            </div>
            <div class="col-md-3"><label>Email</label><input name="email" class="form-control"></div>
            <div class="col-md-3"><label>College</label><input name="college" class="form-control" placeholder="School of Engineering"></div>
            <div class="col-md-3"><label>Department</label><input name="department" class="form-control" placeholder="Information Technology"></div>
            <div class="col-md-3"><label>Campus</label><input name="campus" class="form-control" placeholder="Main"></div>
            <div class="col-md-3"><label>Signatory Title</label><input name="signatory_title" class="form-control" placeholder="Extension Coordinator / Dean"></div>
            <div class="col-md-3"><label>Routing Group</label><input name="routing_group" class="form-control" placeholder="SOE / Main Campus"></div>
            <div class="col-md-3"><label>Status</label>
                <select name="account_status" class="form-select">
                    <option>Active</option>
                    <option>Inactive</option>
                </select>
            </div>
            <div class="col-md-3"><label>Signature Image</label><input type="file" name="signature_image" class="form-control" accept="image/png,image/jpeg"></div>
            <div class="col-12"><button class="btn btn-primary">Create Account</button></div>
        </div>
    </form>
</div>

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
        <table class="table table-bordered align-middle">
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
                        <td><?= htmlspecialchars(($list[array_search('School Coordinator', array_column($list,'role'))]['fullname'] ?? 'schoolcoord')) ?></td>
                        <td><?= htmlspecialchars(($list[array_search('Campus Director', array_column($list,'role'))]['fullname'] ?? 'campusdirector')) ?></td>
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
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th><th>Username</th><th>Role</th><th>Department</th><th>Campus</th><th>Status</th><th>Signature</th><th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($list as $u): ?>
                            <tr>
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="update_user" value="1">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <td><input name="fullname" class="form-control form-control-sm" value="<?= htmlspecialchars($u['fullname'] ?? '') ?>"></td>
                                    <td><input name="username" class="form-control form-control-sm" value="<?= htmlspecialchars($u['username'] ?? '') ?>"></td>
                                    <td><input name="role" class="form-control form-control-sm" value="<?= htmlspecialchars($u['role'] ?? '') ?>"></td>
                                    <td><input name="department" class="form-control form-control-sm" value="<?= htmlspecialchars($u['department'] ?? '') ?>"></td>
                                    <td><input name="campus" class="form-control form-control-sm" value="<?= htmlspecialchars($u['campus'] ?? '') ?>"></td>
                                    <td>
                                        <select name="account_status" class="form-select form-select-sm">
                                            <option <?= ($u['account_status']??'Active')=='Active'?'selected':'' ?>>Active</option>
                                            <option <?= ($u['account_status']??'')=='Inactive'?'selected':'' ?>>Inactive</option>
                                        </select>
                                        <input name="college" type="hidden" value="<?= htmlspecialchars($u['college'] ?? '') ?>">
                                        <input name="email" type="hidden" value="<?= htmlspecialchars($u['email'] ?? '') ?>">
                                        <input name="signatory_title" type="hidden" value="<?= htmlspecialchars($u['signatory_title'] ?? '') ?>">
                                        <input name="password" type="hidden" value="">
                                    </td>
                                    <td>
                                        <input type="file" name="signature_image" class="form-control form-control-sm" accept="image/png,image/jpeg">
                                        <?php if(!empty($u['signature_image'])): ?><small class="text-success">Uploaded</small><?php endif; ?>
                                    </td>
                                    <td><button class="btn btn-sm btn-success">Update</button></td>
                                </form>
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

<?php include "app/views/layouts/footer.php"; ?>
