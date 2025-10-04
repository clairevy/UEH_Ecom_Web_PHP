<?php
/**
 * Category Model
 * Quản lý danh mục
 */

require_once __DIR__ . '/BaseModel.php';

class CategoryModel extends BaseModel {
    protected $table = 'categories';
    protected $primaryKey = 'category_id';

    /**
     * Lấy danh mục với số lượng sản phẩm
     */
    public function getCategoriesWithProductCount() {
        $sql = "SELECT c.*, 
                       COUNT(pc.product_id) as product_count
                FROM categories c
                LEFT JOIN product_categories pc ON c.category_id = pc.category_id
                WHERE c.is_active = 1
                GROUP BY c.category_id
                ORDER BY c.name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tạo danh mục mới (sử dụng procedure)
     */
    public function createCategory($name, $slug, $parent_id = null) {
        $sql = "CALL sp_CreateCategory(:name, :slug, :parent_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':parent_id', $parent_id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
