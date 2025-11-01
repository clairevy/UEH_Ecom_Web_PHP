<?php
// require_once __DIR__ . '/../models/Product.php'; // Tái sử dụng model có sẵn

// class SliderService {
//     private $db;

//     public function __construct() {
//         // Tái sử dụng Database singleton
//         $this->db = Database::getInstance();
//     }

//     /**
//      * Lấy danh sách ảnh hero slider từ hệ thống image hiện có
//      * Sử dụng ref_type = 'banner' và ref_id để phân loại
//      * 
//      * @return array Danh sách slider với thông tin ảnh và metadata
//      */
//     public function getHeroSliders() {
//         $sql = "SELECT 
//                     i.image_id,
//                     i.file_path,
//                     i.file_name,
//                     i.alt_text,
//                     iu.ref_id as slider_order,
//                     iu.is_primary
//                 FROM images i
//                 INNER JOIN image_usages iu ON i.image_id = iu.image_id 
//                 WHERE iu.ref_type = 'banner'
//                 AND iu.ref_id IN (1, 2, 3) -- Chỉ lấy hero slider (ref_id 1,2,3)
//                 ORDER BY iu.ref_id ASC";

//         $this->db->query($sql);
//         $sliders = $this->db->resultSet();

//         // Thêm metadata cho mỗi slider dựa trên ref_id
//         foreach ($sliders as $slider) {
//             $slider->metadata = $this->getSliderMetadata($slider->slider_order);
//         }

//         return $sliders;
//     }

//     /**
//      * Lấy metadata cho slider dựa trên ref_id
//      * Metadata này có thể được lưu trong file config hoặc database khác
//      * 
//      * @param int $sliderId
//      * @return object
//      */
//     private function getSliderMetadata($sliderId) {
//         // Định nghĩa metadata tĩnh cho từng slider
//         $metadata = [
//             1 => [
//                 'title' => 'Bộ Sưu Tập Mùa Đông 2025',
//                 'subtitle' => 'Khám phá vẻ đẹp tinh xảo và đẳng cấp', 
//                 'description' => 'Những thiết kế độc đáo từ kim cương và vàng 18K cao cấp',
//                 'button_text' => 'Xem Ngay',
//                 'button_link' => '/Ecom_website/products',
//                 'text_position' => 'left',
//                 'text_color' => '#FFFFFF'
//             ],
//             2 => [
//                 'title' => 'Trang Sức Kim Cương Mới',
//                 'subtitle' => 'Tỏa sáng cùng những thiết kế độc đáo',
//                 'description' => 'Bộ sưu tập kim cương thiên nhiên với chất lượng hoàn hảo',
//                 'button_text' => 'Mua Ngay', 
//                 'button_link' => '/Ecom_website/products?category=nhan',
//                 'text_position' => 'center',
//                 'text_color' => '#FFFFFF'
//             ],
//             3 => [
//                 'title' => 'Ưu Đãi Đặc Biệt',
//                 'subtitle' => 'Giảm giá lên đến 30%',
//                 'description' => 'Cơ hội sở hữu trang sức cao cấp với giá ưu đãi nhất',
//                 'button_text' => 'Mua Ngay',
//                 'button_link' => '/Ecom_website/products?sale=1', 
//                 'text_position' => 'right',
//                 'text_color' => '#FFFFFF'
//             ]
//         ];

//         return (object) ($metadata[$sliderId] ?? [
//             'title' => 'Slider ' . $sliderId,
//             'subtitle' => '',
//             'description' => '',
//             'button_text' => 'Xem thêm',
//             'button_link' => '/Ecom_website/products',
//             'text_position' => 'left',
//             'text_color' => '#FFFFFF'
//         ]);
//     }

//     /**
//      * Lấy ảnh banner khác (không phải hero slider)
//      * Có thể sử dụng ref_id > 3 cho các banner khác
//      * 
//      * @return array
//      */
//     public function getOtherBanners() {
//         $sql = "SELECT 
//                     i.image_id,
//                     i.file_path,
//                     i.file_name,
//                     i.alt_text,
//                     iu.ref_id
//                 FROM images i
//                 INNER JOIN image_usages iu ON i.image_id = iu.image_id 
//                 WHERE iu.ref_type = 'banner'
//                 AND iu.ref_id > 3 -- Banner khác ngoài hero slider
//                 ORDER BY iu.ref_id ASC";

//         $this->db->query($sql);
//         return $this->db->resultSet();
//     }
