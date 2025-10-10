<?php
/**
 * Pagination Component - Pure MVC Component
 * Hiển thị phân trang với data từ database
 */

// Biến được truyền:
// $currentPage - trang hiện tại
// $totalPages - tổng số trang
// $baseUrl - URL cơ sở cho pagination
// $ariaLabel - label cho accessibility
?>

<!-- Pagination Component -->
<nav aria-label="<?= htmlspecialchars($ariaLabel ?? 'Page navigation') ?>" class="mt-4" data-pagination-label="pagination">
    <ul class="pagination justify-content-center">
        <?php if ($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?= htmlspecialchars($baseUrl ?? '') ?>?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                    <img src="https://cdn-icons-png.flaticon.com/512/271/271228.png" alt="Previous" width="12" height="12" class="me-1" style="transform: rotate(180deg);">
                    PREV
                </a>
            </li>
        <?php endif; ?>
        
        <?php
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);
        
        // Hiển thị trang đầu nếu cần
        if ($startPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?= htmlspecialchars($baseUrl ?? '') ?>?page=1" data-page="1">1</a>
            </li>
            <?php if ($startPage > 2): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                <?php if ($i == $currentPage): ?>
                    <span class="page-link bg-dark border-dark" data-current-page="<?= $i ?>"><?= $i ?></span>
                <?php else: ?>
                    <a class="page-link" href="<?= htmlspecialchars($baseUrl ?? '') ?>?page=<?= $i ?>" data-page="<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            </li>
        <?php endfor; ?>
        
        <?php
        // Hiển thị trang cuối nếu cần
        if ($endPage < $totalPages): ?>
            <?php if ($endPage < $totalPages - 1): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link" href="<?= htmlspecialchars($baseUrl ?? '') ?>?page=<?= $totalPages ?>" data-page="<?= $totalPages ?>"><?= $totalPages ?></a>
            </li>
        <?php endif; ?>
        
        <?php if ($currentPage < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="<?= htmlspecialchars($baseUrl ?? '') ?>?page=<?= $currentPage + 1 ?>" aria-label="Next">
                    NEXT <img src="https://cdn-icons-png.flaticon.com/512/271/271228.png" alt="Next" width="12" height="12" class="ms-1">
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
