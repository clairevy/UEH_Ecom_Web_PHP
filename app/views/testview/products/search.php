<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm sản phẩm</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .search-form { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 30px; }
        .search-form input[type="text"] { width: 60%; padding: 10px; border: 1px solid #ddd; border-radius: 3px; }
        .search-form button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
        .search-form button:hover { background: #0056b3; }
        .search-results { margin-bottom: 30px; }
        .filter-options { display: flex; gap: 15px; margin: 15px 0; flex-wrap: wrap; align-items: center; }
        .filter-options select, .filter-options input { padding: 5px; border: 1px solid #ddd; border-radius: 3px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .product-card { border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        .product-card img { width: 100%; height: 200px; object-fit: cover; background: #f5f5f5; }
        .pagination { margin-top: 30px; text-align: center; }
        .pagination a { padding: 8px 12px; margin: 0 5px; border: 1px solid #ddd; text-decoration: none; }
        .pagination .current { background: #007bff; color: white; }
        .no-results { text-align: center; padding: 60px 20px; color: #666; }
        .search-suggestions { background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .highlight { background-color: yellow; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tìm kiếm sản phẩm</h1>

        <!-- Search Form -->
        <div class="search-form">
            <form method="GET" action="/Ecom_website/products/search">
                <input type="text" name="q" placeholder="Nhập tên sản phẩm, mô tả..." value="<?= $_GET['q'] ?? '' ?>" required>
                <button type="submit">Tìm kiếm</button>
            </form>

            <!-- Advanced Filters -->
            <?php if (isset($_GET['q']) && $_GET['q']): ?>
                <div class="filter-options">
                    <form method="GET" action="/Ecom_website/products/search" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-top: 15px;">
                        <input type="hidden" name="q" value="<?= $_GET['q'] ?>">
                        
                        <label>Danh mục:</label>
                        <select name="category">
                            <option value="">Tất cả danh mục</option>
                            <?php if (isset($categories) && $categories): ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category->id ?>" <?= ($_GET['category'] ?? '') == $category->id ? 'selected' : '' ?>>
                                        <?= $category->name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>

                        <label>Giá từ:</label>
                        <input type="number" name="min_price" placeholder="0" value="<?= $_GET['min_price'] ?? '' ?>" style="width: 80px;">
                        
                        <label>đến:</label>
                        <input type="number" name="max_price" placeholder="999999" value="<?= $_GET['max_price'] ?? '' ?>" style="width: 80px;">
                        
                        <label>Sắp xếp:</label>
                        <select name="sort">
                            <option value="relevance" <?= ($_GET['sort'] ?? '') == 'relevance' ? 'selected' : '' ?>>Liên quan nhất</option>
                            <option value="newest" <?= ($_GET['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                            <option value="price_low" <?= ($_GET['sort'] ?? '') == 'price_low' ? 'selected' : '' ?>>Giá thấp</option>
                            <option value="price_high" <?= ($_GET['sort'] ?? '') == 'price_high' ? 'selected' : '' ?>>Giá cao</option>
                            <option value="name" <?= ($_GET['sort'] ?? '') == 'name' ? 'selected' : '' ?>>Tên A-Z</option>
                        </select>
                        
                        <button type="submit">Lọc</button>
                        <a href="/Ecom_website/products/search?q=<?= urlencode($_GET['q']) ?>">Xóa bộ lọc</a>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- Search Results -->
        <?php if (isset($_GET['q']) && $_GET['q']): ?>
            <div class="search-results">
                <h2>Kết quả tìm kiếm cho: "<?= htmlspecialchars($_GET['q']) ?>"</h2>
                <p>Tìm thấy <strong><?= $totalProducts ?? 0 ?></strong> sản phẩm</p>
            </div>

            <!-- Products Grid -->
            <?php if (isset($products) && $products): ?>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <?php if (isset($product->primary_image) && $product->primary_image): ?>
                                <img src="<?= $product->primary_image ?>" alt="<?= $product->name ?>">
                            <?php else: ?>
                                <div style="width: 100%; height: 200px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; color: #666;">
                                    Chưa có ảnh
                                </div>
                            <?php endif; ?>
                            
                            <h3>
                                <a href="/Ecom_website/products/show/<?= $product->slug ?>">
                                    <?= highlightSearchTerm($product->name, $_GET['q']) ?>
                                </a>
                            </h3>
                            
                            <p><strong>Giá: <?= number_format($product->base_price, 0, ',', '.') ?>đ</strong></p>
                            
                            <?php if (isset($product->category_name)): ?>
                                <p><small>Danh mục: <a href="/Ecom_website/products/category/<?= $product->category_slug ?>"><?= $product->category_name ?></a></small></p>
                            <?php endif; ?>
                            
                            <?php if (isset($product->collection_name)): ?>
                                <p><small>Collection: <?= $product->collection_name ?></small></p>
                            <?php endif; ?>
                            
                            <?php if (isset($product->short_description)): ?>
                                <p><small><?= highlightSearchTerm(substr($product->short_description, 0, 100), $_GET['q']) ?>...</small></p>
                            <?php endif; ?>
                            
                            <a href="/Ecom_website/products/show/<?= $product->slug ?>" style="background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px; display: inline-block; margin-top: 10px;">Xem chi tiết</a>
                        </div>
                    <?php endforeach; ?>
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

            <?php else: ?>
                <div class="no-results">
                    <h3>Không tìm thấy sản phẩm nào phù hợp</h3>
                    <p>Hãy thử:</p>
                    <ul style="list-style: none; padding: 0;">
                        <li>• Kiểm tra lại từ khóa tìm kiếm</li>
                        <li>• Sử dụng từ khóa ngắn gọn hơn</li>
                        <li>• Thử tìm kiếm sản phẩm tương tự</li>
                    </ul>
                    <a href="/Ecom_website/products">Xem tất cả sản phẩm</a>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Search Suggestions when no query -->
            <div class="search-suggestions">
                <h3>Gợi ý tìm kiếm</h3>
                <p>Bạn có thể tìm kiếm theo:</p>
                <ul>
                    <li><strong>Tên sản phẩm:</strong> áo thun, quần jean, giày sneaker...</li>
                    <li><strong>Thương hiệu:</strong> Nike, Adidas, Zara...</li>
                    <li><strong>Danh mục:</strong> thời trang nam, phụ kiện, giày dép...</li>
                    <li><strong>Màu sắc:</strong> đen, trắng, xanh...</li>
                </ul>
            </div>

            <!-- Popular Categories -->
            <?php if (isset($categories) && $categories): ?>
                <div>
                    <h3>Danh mục phổ biến</h3>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <?php foreach (array_slice($categories, 0, 6) as $category): ?>
                            <a href="/Ecom_website/products/category/<?= $category->slug ?>" 
                               style="background: #e9ecef; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block;">
                                <?= $category->name ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Back Button -->
        <div style="margin-top: 40px; text-align: center;">
            <a href="/Ecom_website/products">← Xem tất cả sản phẩm</a>
        </div>
    </div>

    <?php
    // Helper function to highlight search terms
    function highlightSearchTerm($text, $term) {
        if (empty($term)) return $text;
        return preg_replace('/(' . preg_quote($term, '/') . ')/i', '<span class="highlight">$1</span>', $text);
    }
    ?>
</body>
</html>