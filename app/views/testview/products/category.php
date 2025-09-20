<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $category->name ?? 'Danh mục' ?> - Sản phẩm</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .breadcrumb { margin-bottom: 20px; }
        .breadcrumb a { color: #007bff; text-decoration: none; }
        .filter-bar { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .filter-bar form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .product-card { border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        .product-card img { width: 100%; height: 200px; object-fit: cover; background: #f5f5f5; }
        .pagination { margin-top: 30px; text-align: center; }
        .pagination a { padding: 8px 12px; margin: 0 5px; border: 1px solid #ddd; text-decoration: none; }
        .pagination .current { background: #007bff; color: white; }
        .category-info { background: #e9ecef; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="/Ecom_website/products">Tất cả sản phẩm</a> > 
            <strong><?= $category->name ?? 'Danh mục' ?></strong>
        </div>

        <!-- Category Info -->
        <div class="category-info">
            <h1><?= $category->name ?? 'Danh mục' ?></h1>
            <?php if (isset($category->description) && $category->description): ?>
                <p><?= $category->description ?></p>
            <?php endif; ?>
            <p><strong>Tổng số sản phẩm:</strong> <?= $totalProducts ?? 0 ?></p>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <form method="GET">
                <label>Giá từ:</label>
                <input type="number" name="min_price" placeholder="0" value="<?= $_GET['min_price'] ?? '' ?>" style="width: 100px;">
                
                <label>đến:</label>
                <input type="number" name="max_price" placeholder="999999" value="<?= $_GET['max_price'] ?? '' ?>" style="width: 100px;">
                
                <label>Sắp xếp:</label>
                <select name="sort">
                    <option value="newest" <?= ($_GET['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                    <option value="price_low" <?= ($_GET['sort'] ?? '') == 'price_low' ? 'selected' : '' ?>>Giá thấp đến cao</option>
                    <option value="price_high" <?= ($_GET['sort'] ?? '') == 'price_high' ? 'selected' : '' ?>>Giá cao đến thấp</option>
                    <option value="name" <?= ($_GET['sort'] ?? '') == 'name' ? 'selected' : '' ?>>Tên A-Z</option>
                </select>
                
                <button type="submit">Áp dụng</button>
                <a href="/Ecom_website/products/category/<?= $category->slug ?? '' ?>">Xóa bộ lọc</a>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="product-grid">
            <?php if (isset($products) && $products): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php if (isset($product->primary_image) && $product->primary_image): ?>
                            <img src="<?= $product->primary_image ?>" alt="<?= $product->name ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; color: #666;">
                                Chưa có ảnh
                            </div>
                        <?php endif; ?>
                        
                        <h3><a href="/Ecom_website/products/show/<?= $product->slug ?>"><?= $product->name ?></a></h3>
                        <p><strong>Giá: <?= number_format($product->base_price, 0, ',', '.') ?>đ</strong></p>
                        
                        <?php if (isset($product->collection_name)): ?>
                            <p><small>Collection: <?= $product->collection_name ?></small></p>
                        <?php endif; ?>
                        
                        <?php if (isset($product->short_description)): ?>
                            <p><small><?= substr($product->short_description, 0, 100) ?>...</small></p>
                        <?php endif; ?>
                        
                        <a href="/Ecom_website/products/show/<?= $product->slug ?>" style="background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; display: inline-block; margin-top: 10px;">Xem chi tiết</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                    <h3>Chưa có sản phẩm nào trong danh mục này</h3>
                    <p><a href="/Ecom_website/products">← Quay lại tất cả sản phẩm</a></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <div class="pagination">
                <?php 
                $currentQuery = $_GET;
                unset($currentQuery['page']);
                $queryString = http_build_query($currentQuery);
                $queryString = $queryString ? '&' . $queryString : '';
                ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == ($currentPage ?? 1)): ?>
                        <span class="current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $i ?><?= $queryString ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

        <!-- Back to all products -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="/Ecom_website/products">← Xem tất cả sản phẩm</a>
        </div>
    </div>
</body>
</html>