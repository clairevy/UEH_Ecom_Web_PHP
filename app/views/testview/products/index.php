<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .sidebar { float: left; width: 200px; margin-right: 20px; }
        .content { margin-left: 220px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .product-card { border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        .product-card img { width: 100%; height: 200px; object-fit: cover; background: #f5f5f5; }
        .pagination { margin-top: 30px; text-align: center; }
        .pagination a { padding: 8px 12px; margin: 0 5px; border: 1px solid #ddd; text-decoration: none; }
        .pagination .current { background: #007bff; color: white; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tất cả sản phẩm</h1>

        <!-- Sidebar Categories -->
        <div class="sidebar">
            <h3>Danh mục</h3>
            <ul>
                <li><a href="/Ecom_website/products">Tất cả</a></li>
                <?php if (isset($categories) && $categories): ?>
                    <?php foreach ($categories as $category): ?>
                        <li><a href="/Ecom_website/products/category/<?= $category->slug ?>"><?= $category->name ?></a></li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <h3>Bộ lọc giá</h3>
            <form method="GET">
                <input type="number" name="min_price" placeholder="Giá từ" value="<?= $_GET['min_price'] ?? '' ?>">
                <input type="number" name="max_price" placeholder="Giá đến" value="<?= $_GET['max_price'] ?? '' ?>">
                <select name="sort">
                    <option value="newest" <?= ($_GET['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                    <option value="price_low" <?= ($_GET['sort'] ?? '') == 'price_low' ? 'selected' : '' ?>>Giá thấp</option>
                    <option value="price_high" <?= ($_GET['sort'] ?? '') == 'price_high' ? 'selected' : '' ?>>Giá cao</option>
                    <option value="name" <?= ($_GET['sort'] ?? '') == 'name' ? 'selected' : '' ?>>Tên A-Z</option>
                </select>
                <button type="submit">Lọc</button>
            </form>
        </div>

        <!-- Main Content -->
        <div class="content">
            <p>Tìm thấy <?= $totalProducts ?? 0 ?> sản phẩm</p>

            <div class="product-grid">
                <?php if (isset($products) && $products): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <?php if (isset($product->primary_image) && $product->primary_image): ?>
                                <img src="/Ecom_website/<?= $product->primary_image->file_path ?>" alt="<?= $product->name ?>">
                            <?php else: ?>
                                <div style="width: 100%; height: 200px; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                                    No Image
                                </div>
                            <?php endif; ?>
                            
                            <h3><a href="/Ecom_website/products/show/<?= $product->slug ?>"><?= $product->name ?></a></h3>
                            <p>Giá: <?= number_format($product->base_price, 0, ',', '.') ?>đ</p>
                            <p>Collection: <?= $product->collection_name ?? 'N/A' ?></p>
                            <a href="/Ecom_website/products/show/<?= $product->slug ?>">Xem chi tiết</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Không có sản phẩm nào.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if (isset($totalPages) && $totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == ($currentPage ?? 1)): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?><?= isset($_GET['min_price']) ? '&min_price='.$_GET['min_price'] : '' ?><?= isset($_GET['max_price']) ? '&max_price='.$_GET['max_price'] : '' ?><?= isset($_GET['sort']) ? '&sort='.$_GET['sort'] : '' ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="clear"></div>
    </div>
</body>
</html>