<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product->name ?? 'Chi tiết sản phẩm' ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .breadcrumb { margin-bottom: 20px; }
        .breadcrumb a { color: #007bff; text-decoration: none; }
        .product-detail { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .product-images img { width: 100%; border-radius: 5px; }
        .product-info h1 { margin-top: 0; }
        .price { font-size: 24px; color: #e74c3c; font-weight: bold; margin: 20px 0; }
        .product-meta { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .product-meta p { margin: 5px 0; }
        .btn { background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 10px 10px 0; }
        .btn-secondary { background: #6c757d; }
        .variants { margin: 20px 0; }
        .variant-option { border: 1px solid #ddd; padding: 10px; margin: 5px; display: inline-block; cursor: pointer; border-radius: 3px; }
        .variant-option:hover { background: #f8f9fa; }
        .related-products { margin-top: 60px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
        .product-card { border: 1px solid #ddd; padding: 15px; border-radius: 5px; text-align: center; }
        .product-card img { width: 100%; height: 150px; object-fit: cover; background: #f5f5f5; }
        @media (max-width: 768px) {
            .product-detail { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="/Ecom_website/products">Tất cả sản phẩm</a> > 
            <?php if (isset($product->category_name)): ?>
                <a href="/Ecom_website/products/category/<?= $product->category_slug ?>"><?= $product->category_name ?></a> > 
            <?php endif; ?>
            <strong><?= $product->name ?? 'Sản phẩm' ?></strong>
        </div>

        <!-- Product Detail -->
        <div class="product-detail">
            <!-- Product Images -->
            <div class="product-images">
                <?php if (isset($product->primary_image) && $product->primary_image): ?>
                    <img src="<?= $product->primary_image ?>" alt="<?= $product->name ?>">
                <?php else: ?>
                    <div style="width: 100%; height: 400px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; color: #666; border-radius: 5px;">
                        Chưa có ảnh sản phẩm
                    </div>
                <?php endif; ?>
                
                <!-- Additional images could go here -->
                <?php if (isset($product->images) && $product->images): ?>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 15px;">
                        <?php foreach ($product->images as $image): ?>
                            <img src="<?= $image->image_url ?>" alt="<?= $product->name ?>" style="width: 100%; height: 80px; object-fit: cover; border-radius: 3px;">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <h1><?= $product->name ?? 'Tên sản phẩm' ?></h1>
                
                <div class="price">
                    <?= number_format($product->base_price ?? 0, 0, ',', '.') ?>đ
                </div>

                <?php if (isset($product->short_description) && $product->short_description): ?>
                    <p><strong><?= $product->short_description ?></strong></p>
                <?php endif; ?>

                <?php if (isset($product->description) && $product->description): ?>
                    <div>
                        <h3>Mô tả sản phẩm</h3>
                        <p><?= nl2br($product->description) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Product Meta Info -->
                <div class="product-meta">
                    <p><strong>Mã sản phẩm:</strong> <?= $product->sku ?? 'N/A' ?></p>
                    <?php if (isset($product->collection_name)): ?>
                        <p><strong>Collection:</strong> <?= $product->collection_name ?></p>
                    <?php endif; ?>
                    <?php if (isset($product->category_name)): ?>
                        <p><strong>Danh mục:</strong> <a href="/Ecom_website/products/category/<?= $product->category_slug ?>"><?= $product->category_name ?></a></p>
                    <?php endif; ?>
                    <p><strong>Trạng thái:</strong> 
                        <?= ($product->status ?? '') == 'active' ? '<span style="color: green;">Còn hàng</span>' : '<span style="color: red;">Hết hàng</span>' ?>
                    </p>
                </div>

                <!-- Product Variants -->
                <?php if (isset($product->variants) && $product->variants): ?>
                    <div class="variants">
                        <h3>Tùy chọn sản phẩm:</h3>
                        <?php foreach ($product->variants as $variant): ?>
                            <div class="variant-option">
                                <strong><?= $variant->variant_name ?></strong><br>
                                <small>Giá: <?= number_format($variant->price, 0, ',', '.') ?>đ</small>
                                <?php if ($variant->stock_quantity > 0): ?>
                                    <small style="color: green;"> - Còn <?= $variant->stock_quantity ?> sản phẩm</small>
                                <?php else: ?>
                                    <small style="color: red;"> - Hết hàng</small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div style="margin-top: 30px;">
                    <a href="#" class="btn">Thêm vào giỏ hàng</a>
                    <a href="#" class="btn btn-secondary">Mua ngay</a>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (isset($relatedProducts) && $relatedProducts): ?>
            <div class="related-products">
                <h2>Sản phẩm cùng danh mục</h2>
                <div class="product-grid">
                    <?php foreach ($relatedProducts as $related): ?>
                        <div class="product-card">
                            <?php if (isset($related->primary_image) && $related->primary_image): ?>
                                <img src="<?= $related->primary_image ?>" alt="<?= $related->name ?>">
                            <?php else: ?>
                                <div style="width: 100%; height: 150px; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                                    No Image
                                </div>
                            <?php endif; ?>
                            <h4><a href="/Ecom_website/products/show/<?= $related->slug ?>"><?= $related->name ?></a></h4>
                            <p><?= number_format($related->base_price, 0, ',', '.') ?>đ</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Back Button -->
        <div style="margin-top: 40px; text-align: center;">
            <a href="javascript:history.back()" class="btn btn-secondary">← Quay lại</a>
            <a href="/Ecom_website/products" class="btn btn-secondary">Xem tất cả sản phẩm</a>
        </div>
    </div>
</body>
</html>