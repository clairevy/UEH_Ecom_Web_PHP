<?php
/**
 * Script để seed dữ liệu mẫu vào database
 * Chạy: php seed_data.php
 */

// Load database configuration
require_once __DIR__ . '/configs/database.php';
require_once __DIR__ . '/configs/config.php';

try {
    $db = Database::getInstance();
    
    echo "Bắt đầu seed dữ liệu mẫu...\n";
    
    // 1. Xóa dữ liệu cũ (nếu có)
    echo "Xóa dữ liệu cũ...\n";
    $db->query("DELETE FROM image_usages WHERE ref_type IN ('product', 'category')");
    $db->execute();
    $db->query("DELETE FROM images");
    $db->execute();
    $db->query("DELETE FROM product_categories");
    $db->execute();
    $db->query("DELETE FROM products");
    $db->execute();
    $db->query("DELETE FROM categories");
    $db->execute();
    $db->query("DELETE FROM collection");
    $db->execute();
    
    // 2. Tạo Collections
    echo "Tạo collections...\n";
    $collections = [
        ['Luxury Gold Collection', 'luxury-gold-collection', 'Bộ sưu tập vàng cao cấp với thiết kế tinh tế'],
        ['Diamond Star Series', 'diamond-star-series', 'Những viên kim cương lấp lánh nhất'],
        ['Pearl Beauty Line', 'pearl-beauty-line', 'Vẻ đẹp tinh tế của ngọc trai'],
        ['Elegant Silver', 'elegant-silver', 'Bạc thanh lịch cho mọi dịp']
    ];
    
    foreach ($collections as $index => $collection) {
        $db->query("INSERT INTO collection (collection_id, collection_name, slug, description, is_active, created_at) VALUES (:id, :name, :slug, :desc, 1, NOW())");
        $db->bind(':id', $index + 1);
        $db->bind(':name', $collection[0]);
        $db->bind(':slug', $collection[1]);
        $db->bind(':desc', $collection[2]);
        $db->execute();
    }
    
    // 3. Tạo Categories
    echo "Tạo categories...\n";
    $categories = [
        ['Nhẫn', 'Rings', 'Nhẫn vàng, nhẫn kim cương cao cấp'],
        ['Dây chuyền', 'Necklaces', 'Dây chuyền vàng, bạc đẹp mắt'],
        ['Bông tai', 'Earrings', 'Bông tai thiết kế độc đáo'],
        ['Vòng tay', 'Bracelets', 'Vòng tay thời trang'],
        ['Đồng hồ', 'Watches', 'Đồng hồ cao cấp']
    ];
    
    foreach ($categories as $index => $category) {
        $db->query("INSERT INTO categories (category_id, name, slug, is_active, created_at) VALUES (:id, :name, :slug, 1, NOW())");
        $db->bind(':id', $index + 1);
        $db->bind(':name', $category[0]);
        $db->bind(':slug', $category[1]);
        $db->execute();
    }
    
    // 4. Tạo Products
    echo "Tạo products...\n";
    $products = [
        [
            'Nhẫn Kim Cương Vàng Trắng 18K',
            'Nhẫn kim cương vàng trắng 18K cao cấp với thiết kế tinh tế và sang trọng. Kim cương chất lượng cao được chế tác thủ công tỉ mỉ, tạo nên món trang sức hoàn hảo cho những dịp đặc biệt.',
            45000000,
            'RING-DIAMOND-001',
            'ring-diamond-001',
            1, // Luxury Gold Collection
            [1], // Nhẫn category
            'https://images.unsplash.com/photo-1603561591411-07134e71a2a9?w=500'
        ],
        [
            'Dây Chuyền Ngọc Trai Akoya',
            'Dây chuyền ngọc trai Akoya tự nhiên với chất lượng cao nhất. Mỗi viên ngọc trai được lựa chọn kỹ lưỡng để tạo nên vẻ đẹp tinh tế và sang trọng.',
            18500000,
            'NECKLACE-PEARL-001',
            'necklace-pearl-001',
            3, // Pearl Beauty Line
            [2], // Dây chuyền category
            'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=500'
        ],
        [
            'Bông Tai Kim Cương Thiên Nhiên',
            'Bông tai kim cương thiên nhiên với thiết kế cổ điển nhưng hiện đại. Kim cương được cắt tỉa hoàn hảo để tối đa hóa độ lấp lánh.',
            25000000,
            'EARRING-DIAMOND-001',
            'earring-diamond-001',
            2, // Diamond Star Series
            [3], // Bông tai category
            'https://images.unsplash.com/photo-1617038260897-41a1f14a8ca0?w=500'
        ],
        [
            'Vòng Tay Bạc 925 Charm',
            'Vòng tay bạc 925 với các charm thiết kế độc đáo. Chất liệu bạc cao cấp, bền đẹp theo thời gian.',
            2800000,
            'BRACELET-SILVER-001',
            'bracelet-silver-001',
            4, // Elegant Silver
            [4], // Vòng tay category
            'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=500'
        ],
        [
            'Đồng Hồ Diamond Luxury Swiss',
            'Đồng hồ Thụy Sĩ cao cấp với kim cương đính kèm. Thiết kế sang trọng, phù hợp cho những dịp đặc biệt.',
            45000000,
            'WATCH-DIAMOND-001',
            'watch-diamond-001',
            2, // Diamond Star Series
            [5], // Đồng hồ category
            'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500'
        ],
        [
            'Nhẫn Cưới Vàng Trắng 18K',
            'Nhẫn cưới vàng trắng 18K với thiết kế đơn giản nhưng tinh tế. Hoàn hảo cho cặp đôi trong ngày trọng đại.',
            12000000,
            'RING-WEDDING-001',
            'ring-wedding-001',
            1, // Luxury Gold Collection
            [1], // Nhẫn category
            'https://images.unsplash.com/photo-1606800052052-a08af7148866?w=500'
        ],
        [
            'Dây Chuyền Vàng 18K Trái Tim',
            'Dây chuyền vàng 18K với charm hình trái tim. Thiết kế lãng mạn, phù hợp làm quà tặng ý nghĩa.',
            7800000,
            'NECKLACE-HEART-001',
            'necklace-heart-001',
            1, // Luxury Gold Collection
            [2], // Dây chuyền category
            'https://images.unsplash.com/photo-1602173574767-37ac01994b2a?w=500'
        ],
        [
            'Bông Tai Vàng 24K Hoa Hồng',
            'Bông tai vàng 24K với thiết kế hoa hồng tinh tế. Chất liệu vàng nguyên chất cao cấp.',
            6200000,
            'EARRING-ROSE-001',
            'earring-rose-001',
            1, // Luxury Gold Collection
            [3], // Bông tai category
            'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=500'
        ]
    ];
    
    $productIds = [];
    foreach ($products as $index => $product) {
        $db->query("INSERT INTO products (product_id, name, description, base_price, sku, slug, collection_id, is_active, created_at) VALUES (:id, :name, :desc, :price, :sku, :slug, :collection, 1, NOW())");
        $db->bind(':id', $index + 1);
        $db->bind(':name', $product[0]);
        $db->bind(':desc', $product[1]);
        $db->bind(':price', $product[2]);
        $db->bind(':sku', $product[3]);
        $db->bind(':slug', $product[4]);
        $db->bind(':collection', $product[5]);
        $db->execute();
        
        $productIds[] = $index + 1;
        
        // Thêm product vào category
        foreach ($product[6] as $categoryId) {
            $db->query("INSERT INTO product_categories (product_id, category_id) VALUES (:product_id, :category_id)");
            $db->bind(':product_id', $index + 1);
            $db->bind(':category_id', $categoryId);
            $db->execute();
        }
        
        // Tạo images cho product
        createProductImages($db, $index + 1, $product[7], $product[0]);
    }
    
    // 5. Tạo category banner images
    echo "Tạo category banner images...\n";
    $categoryImages = [
        1 => 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=300',
        2 => 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=300',
        3 => 'https://images.unsplash.com/photo-1617038260897-41a1f14a8ca0?w=300',
        4 => 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=300',
        5 => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300'
    ];
    
    foreach ($categoryImages as $categoryId => $imageUrl) {
        createCategoryBanner($db, $categoryId, $imageUrl);
    }
    
    echo "Hoàn thành seed dữ liệu mẫu!\n";
    echo "Đã tạo:\n";
    echo "- 4 Collections\n";
    echo "- 5 Categories\n";
    echo "- 8 Products\n";
    echo "- Product images và category banners\n";
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}

/**
 * Tạo images cho product
 */
function createProductImages($db, $productId, $primaryImageUrl, $productName) {
    // Tạo primary image
    $imageId = createImage($db, $primaryImageUrl, $productName . ' - Main Image');
    createImageUsage($db, $imageId, 'product', $productId, 1); // is_primary = 1
    
    // Tạo thêm 2-3 images cho mỗi product
    $additionalImages = [
        'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=500',
        'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=500',
        'https://images.unsplash.com/photo-1617038260897-41a1f14a8ca0?w=500',
        'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=500',
        'https://images.unsplash.com/photo-1603561591411-07134e71a2a9?w=500'
    ];
    
    // Chọn ngẫu nhiên 2-3 images bổ sung
    $selectedImages = array_rand(array_flip($additionalImages), rand(2, 3));
    if (!is_array($selectedImages)) {
        $selectedImages = [$selectedImages];
    }
    
    foreach ($selectedImages as $imageUrl) {
        $imageId = createImage($db, $imageUrl, $productName . ' - Gallery Image');
        createImageUsage($db, $imageId, 'product', $productId, 0); // is_primary = 0
    }
}

/**
 * Tạo category banner
 */
function createCategoryBanner($db, $categoryId, $imageUrl) {
    $imageId = createImage($db, $imageUrl, 'Category Banner');
    createImageUsage($db, $imageId, 'category', $categoryId, 1); // is_primary = 1
}

/**
 * Tạo image record
 */
function createImage($db, $imageUrl, $altText) {
    $db->query("INSERT INTO images (file_path, file_name, alt_text, created_at) VALUES (:path, :name, :alt, NOW())");
    $db->bind(':path', $imageUrl);
    $db->bind(':name', basename($imageUrl));
    $db->bind(':alt', $altText);
    $db->execute();
    return $db->lastInsertId();
}

/**
 * Tạo image usage record
 */
function createImageUsage($db, $imageId, $refType, $refId, $isPrimary) {
    $db->query("INSERT INTO image_usages (image_id, ref_type, ref_id, is_primary, created_at) VALUES (:image_id, :ref_type, :ref_id, :is_primary, NOW())");
    $db->bind(':image_id', $imageId);
    $db->bind(':ref_type', $refType);
    $db->bind(':ref_id', $refId);
    $db->bind(':is_primary', $isPrimary);
    $db->execute();
}
?>
