<?php
/**
 * User Dropdown Component - Pure MVC Component
 * Hiển thị dropdown người dùng với data từ database
 */

// Biến được truyền:
// $user - object user từ database
// $showProfile - hiển thị link profile - default true
// $showSettings - hiển thị link settings - default true
// $showLogout - hiển thị link logout - default true
?>

<!-- User Dropdown Component -->
<div class="dropdown user-dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
        <img src="<?= htmlspecialchars($user->avatar ?? 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png') ?>" 
             alt="User" width="20" height="20" class="rounded-circle">
        <span data-username="<?= htmlspecialchars($user->name ?? 'ADMIN') ?>">
            <?= htmlspecialchars($user->name ?? 'ADMIN') ?>
        </span>
    </button>
    <ul class="dropdown-menu">
        <?php if ($showProfile ?? true): ?>
            <li><a class="dropdown-item" href="index.php?url=profile">
                <i class="fas fa-user me-2"></i>Hồ sơ
            </a></li>
        <?php endif; ?>
        
        <?php if ($showSettings ?? true): ?>
            <li><a class="dropdown-item" href="index.php?url=settings">
                <i class="fas fa-cog me-2"></i>Cài đặt
            </a></li>
        <?php endif; ?>
        
        <li><hr class="dropdown-divider"></li>
        
        <?php if ($showLogout ?? true): ?>
            <li><a class="dropdown-item" href="index.php?url=logout">
                <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
            </a></li>
        <?php endif; ?>
    </ul>
</div>
