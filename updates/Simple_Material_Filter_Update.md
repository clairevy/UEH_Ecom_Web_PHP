# Simple Material Filter Update

## ProductService Methods to Update

Replace these methods in `app/services/ProductService.php`:

### 1. Update getProductsWithFilters method

Find and replace the materials filter section:

```php
// OLD - Materials filter (around line 73-83)
// Materials filter - search in product name and description only
if (!empty($filters['materials']) && is_array($filters['materials'])) {
    // ... old code
}

// NEW - Replace with:
// Materials filter - use the new material column
if (!empty($filters['materials']) && is_array($filters['materials'])) {
    $materialPlaceholders = [];
    foreach ($filters['materials'] as $index => $material) {
        $placeholder = ":material_$index";
        $materialPlaceholders[] = $placeholder;
        $params[$placeholder] = $material;
    }
    $materialList = implode(',', $materialPlaceholders);
    $sql .= " AND p.material IN ($materialList)";
}
```

### 2. Update countProductsWithFilters method

Find and replace the materials filter section in countProductsWithFilters:

```php
// OLD - Materials filter (around line 211-221)
// Materials filter - search in product name and description only
if (!empty($filters['materials']) && is_array($filters['materials'])) {
    // ... old code
}

// NEW - Replace with:
// Materials filter - use the new material column
if (!empty($filters['materials']) && is_array($filters['materials'])) {
    $materialPlaceholders = [];
    foreach ($filters['materials'] as $index => $material) {
        $placeholder = ":material_$index";
        $materialPlaceholders[] = $placeholder;
        $params[$placeholder] = $material;
    }
    $materialList = implode(',', $materialPlaceholders);
    $sql .= " AND p.material IN ($materialList)";
}
```

### 3. Add new method for getting available materials

Add this new method to ProductService:

```php
/**
 * Get available materials from products
 */
public function getAvailableMaterials() {
    $sql = "SELECT DISTINCT material 
            FROM products 
            WHERE material IS NOT NULL 
            AND is_active = 1 
            ORDER BY 
                CASE material 
                    WHEN 'gold' THEN 1 
                    WHEN 'silver' THEN 2 
                    WHEN 'diamond' THEN 3 
                    WHEN 'pearl' THEN 4 
                    ELSE 5 
                END";
    $this->db->query($sql);
    $materials = $this->db->resultSet();
    
    return array_map(function($item) {
        return $item->material;
    }, $materials);
}
```

## Benefits of Simple Approach:

✅ **Keep existing categories unchanged**  
✅ **Simple material filtering via products.material column**  
✅ **No complex hierarchical categories**  
✅ **Easy to maintain and understand**  
✅ **Fast queries with material index**  

## Filter Structure:

- **Categories**: Nhẫn, Dây chuyền, Bông tai, Vòng tay... (from categories table)
- **Materials**: Vàng, Bạc, Kim cương, Ngọc trai (from products.material column)

Users can filter by both:
- Category: "Nhẫn" 
- Material: "Vàng"
- Result: "Nhẫn vàng"