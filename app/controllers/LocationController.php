<?php
require_once __DIR__ . '/../../core/BaseController.php';

class LocationController extends BaseController {
    private $apiBaseUrl = 'https://provinces.open-api.vn/api/v2';
    public function getProvinces() {
        try {
            // Gọi API v2 để lấy danh sách tất cả tỉnh/thành
            $provinces = $this->callApi("{$this->apiBaseUrl}/p/");
            
            if ($provinces === null) {
                throw new Exception("Không thể lấy dữ liệu tỉnh/thành phố từ API v2.");
            }
            
            $this->jsonResponse(true, 'Success', $provinces);
        } catch (Exception $e) {
            error_log("Get Provinces Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi xảy ra khi lấy dữ liệu tỉnh/thành phố');
        }
    }
    public function getWards() {
        try {
            // 1. Lấy mã tỉnh từ JavaScript (thử cả 'province' và 'province_code')
            $provinceCode = $_GET['province'] ?? $_GET['province_code'] ?? ''; 
            
            if (empty($provinceCode) || !is_numeric($provinceCode)) {
                $this->jsonResponse(false, 'Thiếu hoặc sai mã tỉnh/thành phố');
                return;
            }

            // 2. Gọi API v2
            $urlToCall = "{$this->apiBaseUrl}/p/{$provinceCode}?depth=2";
            $response = $this->callApi($urlToCall);

            if ($response === null) {
                throw new Exception("Lỗi gọi API hoặc API trả về null.");
            }

            // 3. "Làm phẳng" dữ liệu
            $allWards = [];

            // --- LOGIC XỬ LÝ MỚI ---
            
            // TRƯỜNG HỢP 1: API trả về Tỉnh (có key 'districts')
            if (isset($response['districts']) && is_array($response['districts'])) {
                foreach ($response['districts'] as $district) {
                    if (isset($district['wards']) && is_array($district['wards'])) {
                        
                        // Thêm 'district_name' vào cho JS
                        $wardsWithDistrict = array_map(function($ward) use ($district) {
                            $ward['district_name'] = $district['name']; 
                            return $ward;
                        }, $district['wards']);

                        $allWards = array_merge($allWards, $wardsWithDistrict);
                    }
                }
            } 
            // TRƯỜNG HỢP 2: API trả về Thành phố TW (có key 'wards' ở cấp 1)
            // (Đây chính là trường hợp trong ảnh của bạn)
            elseif (isset($response['wards']) && is_array($response['wards'])) {
                // Không có district_name, JS của bạn sẽ tự động chỉ hiển thị tên phường
                $allWards = $response['wards'];
            }
            // TRƯỜNG HỢP 3: API trả về cấu trúc không mong muốn
            else {
                throw new Exception("Cấu trúc API trả về không hợp lệ.");
            }
            // --- KẾT THÚC LOGIC MỚI ---

            // 7. Trả về danh sách PHẲNG (đã bỏ district) cho JavaScript
            $this->jsonResponse(true, 'Success', $allWards);

        } catch (Exception $e) {
            // Ghi lại lỗi chi tiết để bạn kiểm tra nếu cần
            error_log("Get Wards Error: " . $e->getMessage() . " - URL: " . ($urlToCall ?? ''));
            $this->jsonResponse(false, 'Có lỗi xảy ra khi lấy dữ liệu phường/xã');
        }
    }
    private function callApi($url) {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Mozilla/5.0 (compatible; MyWebApp/1.0)'
                ]
            ]);
            
            $response = file_get_contents($url, false, $context);
            
            if ($response === false) {
                return null;
            }
            
            return json_decode($response, true);
        } catch (Exception $e) {
            error_log("API Call Error: " . $e->getMessage());
            return null;
        }
    }
}
?>