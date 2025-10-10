<?php
/**
 * Table Card Component - Pure MVC Component
 * Hiển thị bảng dữ liệu với data từ database
 */

// Biến được truyền:
// $title - tiêu đề bảng
// $headers - mảng headers
// $rows - mảng dữ liệu rows
// $actions - mảng actions (export, print, etc.)
?>

<!-- Table Card Component -->
<div class="table-card">
    <div class="table-header">
        <h5 class="table-title" data-table-title="<?= htmlspecialchars($title ?? 'Data Table') ?>">
            <?= htmlspecialchars($title ?? 'Data Table') ?>
        </h5>
        <div class="dropdown">
            <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                <img src="https://cdn-icons-png.flaticon.com/512/2311/2311524.png" alt="More" width="20" height="20">
            </button>
            <ul class="dropdown-menu" data-table-actions>
                <?php if (!empty($actions)): ?>
                    <?php foreach ($actions as $action): ?>
                        <li><a class="dropdown-item" href="<?= htmlspecialchars($action['url'] ?? '#') ?>">
                            <?= htmlspecialchars($action['text'] ?? 'Action') ?>
                        </a></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><a class="dropdown-item" href="#">Export CSV</a></li>
                    <li><a class="dropdown-item" href="#">Export PDF</a></li>
                    <li><a class="dropdown-item" href="#">Print</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead data-table-header>
                <?php if (!empty($headers)): ?>
                    <tr>
                        <?php foreach ($headers as $header): ?>
                            <th><?= htmlspecialchars($header) ?></th>
                        <?php endforeach; ?>
                    </tr>
                <?php endif; ?>
            </thead>
            <tbody data-table-body>
                <?php if (!empty($rows)): ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php if (is_array($row)): ?>
                                <?php foreach ($row as $cell): ?>
                                    <td><?= htmlspecialchars($cell) ?></td>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <td><?= htmlspecialchars($row) ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
