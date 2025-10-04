<?php
/**
 * Admin Footer Template
 * Footer chung cho tất cả trang admin
 */
?>
            </main>
        </div>
    </div>

    <!-- Component Manager -->
    <script src="<?= $baseUrl ?>components/component-manager.js"></script>
    
    <!-- Page Configuration -->
    <script>
        window.pageConfig = {
            sidebar: {
                brandName: 'JEWELLERY',
                activePage: '<?= $currentPage ?? 'dashboard' ?>',
                links: {
                    dashboard: 'index.php',
                    products: 'index.php?page=products',
                    orders: 'index.php?page=orders'
                },
                categories: <?= json_encode($categories ?? []) ?>,
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: '<?= $pageTitle ?? 'Dashboard' ?>',
                breadcrumb: '<?= $breadcrumb ?? 'Home > Dashboard' ?>',
                showDateRange: <?= $showDateRange ?? 'false' ?>,
                dateRange: '<?= $dateRange ?? date('d M Y') ?> - <?= date('d M Y', strtotime('+7 days')) ?>'
            }
        };
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $baseUrl ?>assets/js/main.js"></script>
</body>
</html>
