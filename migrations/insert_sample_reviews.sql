-- Insert sample product reviews
-- Tạo dữ liệu đánh giá mẫu cho sản phẩm

USE db_ecom;

-- Tạo thêm user mẫu để có đủ reviewer
INSERT IGNORE INTO users (user_id, email, password_hash, name, phone, is_active, role_id) VALUES
(1, 'minh.anh@gmail.com', '$2y$10$hash1', 'Minh Anh', '0901234567', 1, 1),
(2, 'thu.huong@gmail.com', '$2y$10$hash2', 'Thu Hương', '0901234568', 1, 1), 
(3, 'van.hung@gmail.com', '$2y$10$hash3', 'Văn Hùng', '0901234569', 1, 1),
(4, 'my.linh@gmail.com', '$2y$10$hash4', 'Mỹ Linh', '0901234570', 1, 1);

-- Lấy một vài product_id có sẵn để tạo review
-- Insert sample reviews cho các sản phẩm
INSERT INTO product_reviews (product_id, user_id, rating, title, comment, status, created_at) VALUES
-- Reviews cho product_id 1
(1, 1, 5, 'Nhẫn rất đẹp, kim cương láp lánh', 'Nhẫn rất đẹp, kim cương lấp lánh, chất lượng tuyệt vời. Giao hàng nhanh, đóng gói cẩn thận.', 'approved', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 2, 4, 'Thiết kế sang trọng', 'Thiết kế sang trọng, đúng như mô tả. Rất hài lòng với sản phẩm này!', 'approved', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 3, 5, 'Tuyệt vời!', 'Sản phẩm vượt mong đợi, chất lượng cao, đáng đồng tiền bát gạo.', 'approved', DATE_SUB(NOW(), INTERVAL 7 DAY)),

-- Reviews cho product_id 2  
(2, 4, 4, 'Dây chuyền đẹp', 'Dây chuyền rất đẹp và tinh tế. Chất liệu vàng thật, không bị đen.', 'approved', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 1, 5, 'Quá đẹp luôn!', 'Mình rất thích dây chuyền này. Đeo lên rất sang và quý phái.', 'approved', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 6, 3, 'Tạm được', 'Sản phẩm ổn nhưng hơi nhỏ so với mong đợi. Tuy nhiên chất lượng vẫn tốt.', 'approved', DATE_SUB(NOW(), INTERVAL 6 DAY)),

-- Reviews cho product_id 3
(3, 2, 5, 'Bông tai tuyệt đẹp', 'Bông tai thiết kế rất đẹp, đeo lên rất tôn da. Rất hài lòng!', 'approved', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(3, 3, 4, 'Chất lượng tốt', 'Chất lượng sản phẩm rất tốt, đóng gói cẩn thận. Sẽ mua lại!', 'approved', DATE_SUB(NOW(), INTERVAL 8 DAY)),

-- Reviews cho product_id 4
(4, 4, 5, 'Vòng tay đẹp quá!', 'Vòng tay rất đẹp và chất lượng. Kim cương nhỏ nhưng rất lấp lánh.', 'approved', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 6, 4, 'Hài lòng với sản phẩm', 'Sản phẩm đúng như mô tả, giao hàng nhanh chóng. Recommended!', 'approved', DATE_SUB(NOW(), INTERVAL 9 DAY)),

-- Reviews cho product_id 5
(5, 1, 5, 'Đồng hồ sang trọng', 'Đồng hồ rất đẹp và sang trọng. Máy chạy chính xác, thiết kế tinh tế.', 'approved', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(5, 2, 4, 'Đáng đồng tiền', 'Chất lượng tốt, thiết kế đẹp. Đeo lên rất phù hợp với trang phục công sở.', 'approved', DATE_SUB(NOW(), INTERVAL 5 DAY)),

-- Thêm một số review với rating thấp hơn để realistic
(1, 4, 3, 'Tạm ổn', 'Sản phẩm ổn nhưng hơi đắt so với chất lượng. Giao hàng hơi chậm.', 'approved', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(2, 3, 2, 'Không như mong đợi', 'Sản phẩm không như hình ảnh quảng cáo. Hơi thất vọng.', 'approved', DATE_SUB(NOW(), INTERVAL 12 DAY)),

-- Reviews chờ duyệt
(3, 1, 4, 'Đẹp nhưng hơi nhỏ', 'Bông tai đẹp nhưng kích thước hơi nhỏ so với mong đợi.', 'pending', NOW()),
(4, 2, 5, 'Perfect!', 'Hoàn hảo luôn! Sẽ giới thiệu cho bạn bè.', 'pending', NOW());

-- Kiểm tra kết quả
SELECT 'Review Statistics:' as info;
SELECT 
    p.name as product_name,
    COUNT(pr.review_id) as total_reviews,
    ROUND(AVG(pr.rating), 1) as average_rating,
    pr.status
FROM products p 
LEFT JOIN product_reviews pr ON p.product_id = pr.product_id 
WHERE pr.status = 'approved'
GROUP BY p.product_id, p.name, pr.status
ORDER BY total_reviews DESC;

SELECT 'Sample Reviews:' as info;
SELECT 
    p.name as product,
    pr.rating,
    pr.title,
    LEFT(pr.comment, 50) as comment_preview,
    pr.created_at
FROM product_reviews pr
JOIN products p ON pr.product_id = p.product_id
WHERE pr.status = 'approved'
ORDER BY pr.created_at DESC
LIMIT 10;