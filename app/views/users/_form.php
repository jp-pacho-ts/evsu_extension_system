<?php
$userForm = $userForm ?? [];
$isEdit = $isEdit ?? false;
$roleOptions = $roleOptions ?? ['Faculty','Department Coordinator','School Coordinator','Campus Director','VP ORIES','Super Admin','Admin'];
?>
<div class="row g-3">
    <div class="col-md-4">
        <label>Full Name</label>
        <input name="fullname" class="form-control" value="<?= htmlspecialchars($userForm['fullname'] ?? '') ?>" required>
    </div>
    <div class="col-md-4">
        <label>Username</label>
        <input name="username" class="form-control" value="<?= htmlspecialchars($userForm['username'] ?? '') ?>" required>
    </div>
    <div class="col-md-4">
        <label><?= $isEdit ? 'New Password' : 'Password' ?></label>
        <input name="password" type="password" class="form-control" <?= $isEdit ? '' : 'required' ?>>
        <?php if($isEdit): ?><small class="text-muted">Leave blank to keep current password.</small><?php endif; ?>
    </div>
    <div class="col-md-4">
        <label>Role</label>
        <select name="role" class="form-select">
            <?php foreach($roleOptions as $role): ?>
                <option <?= ($userForm['role'] ?? '') === $role ? 'selected' : '' ?>><?= htmlspecialchars($role) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label>Email</label>
        <input name="email" class="form-control" value="<?= htmlspecialchars($userForm['email'] ?? '') ?>">
    </div>
    <div class="col-md-4">
        <label>College</label>
        <input name="college" class="form-control" value="<?= htmlspecialchars($userForm['college'] ?? '') ?>" placeholder="School of Engineering">
    </div>
    <div class="col-md-4">
        <label>Department</label>
        <input name="department" class="form-control" value="<?= htmlspecialchars($userForm['department'] ?? '') ?>" placeholder="Information Technology">
    </div>
    <div class="col-md-4">
        <label>Campus</label>
        <input name="campus" class="form-control" value="<?= htmlspecialchars($userForm['campus'] ?? '') ?>" placeholder="Main">
    </div>
    <div class="col-md-4">
        <label>Signatory Title</label>
        <input name="signatory_title" class="form-control" value="<?= htmlspecialchars($userForm['signatory_title'] ?? '') ?>" placeholder="Extension Coordinator / Dean">
    </div>
    <div class="col-md-4">
        <label>Extension Services</label>
        <select name="has_extension_services" class="form-select">
            <option value="0" <?= empty($userForm['has_extension_services']) ? 'selected' : '' ?>>No Extension Services</option>
            <option value="1" <?= !empty($userForm['has_extension_services']) ? 'selected' : '' ?>>With Extension Services</option>
        </select>
    </div>
    <div class="col-md-4">
        <label>Routing Group</label>
        <input name="routing_group" class="form-control" value="<?= htmlspecialchars($userForm['routing_group'] ?? '') ?>" placeholder="SOE / Main Campus">
    </div>
    <div class="col-md-4">
        <label>Status</label>
        <select name="account_status" class="form-select">
            <option <?= ($userForm['account_status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
            <option <?= ($userForm['account_status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
    </div>
    <div class="col-md-4">
        <label>Signature Image</label>
        <input type="file" name="signature_image" class="form-control" accept="image/png,image/jpeg">
        <?php if($isEdit && !empty($userForm['signature_image'])): ?><small class="text-success">Uploaded</small><?php endif; ?>
    </div>
</div>
