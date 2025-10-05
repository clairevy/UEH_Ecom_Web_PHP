<?php
class SiteAssets extends BaseModel {
    // Virtual table - doesn't exist, we use entity_type = 'site' in image_usages
    protected $table = 'site_assets'; 
    
    // Site asset types (using entity_id to differentiate)
    const SLIDER_TYPE = 1;
    const BANNER_TYPE = 2;
    const LOGO_TYPE = 3;
    const FAVICON_TYPE = 4;
    
    // =================== SITE IMAGE METHODS ===================
    
    /**
     * Get all site images by type
     */
    private function getSiteImages($typeId) {
        $sql = "SELECT i.*, iu.usage_id, iu.ref_type, iu.ref_id, iu.is_primary
                FROM images i 
                JOIN image_usages iu ON i.image_id = iu.image_id 
                WHERE iu.ref_type = 'site' 
                AND iu.ref_id = :type_id 
                ORDER BY iu.is_primary DESC, iu.created_at ASC";
        
        $this->db->query($sql);
        $this->db->bind(':type_id', $typeId);
        
        return $this->db->resultSet();
    }
    
    /**
     * Get homepage sliders
     */
    public function getSliders() {
        return $this->getSiteImages(self::SLIDER_TYPE);
    }
    
    /**
     * Get site banners
     */
    public function getBanners() {
        return $this->getSiteImages(self::BANNER_TYPE);
    }
    
    /**
     * Get site logo
     */
    public function getLogo() {
        $logos = $this->getSiteImages(self::LOGO_TYPE);
        return $logos ? $logos[0] : null;
    }
    
    /**
     * Get site favicon
     */
    public function getFavicon() {
        $favicons = $this->getSiteImages(self::FAVICON_TYPE);
        return $favicons ? $favicons[0] : null;
    }
    
    /**
     * Add slider image
     */
    public function addSlider($imagePath) {
        return $this->addSiteImage($imagePath, self::SLIDER_TYPE);
    }
    
    /**
     * Add banner image
     */
    public function addBanner($imagePath) {
        return $this->addSiteImage($imagePath, self::BANNER_TYPE);
    }
    
    /**
     * Set site logo (replaces existing)
     */
    public function setLogo($imagePath) {
        // Delete existing logo first
        $this->deleteAllSiteImages(self::LOGO_TYPE);
        return $this->addSiteImage($imagePath, self::LOGO_TYPE);
    }
    
    /**
     * Set site favicon (replaces existing)
     */
    public function setFavicon($imagePath) {
        // Delete existing favicon first
        $this->deleteAllSiteImages(self::FAVICON_TYPE);
        return $this->addSiteImage($imagePath, self::FAVICON_TYPE);
    }
    
    /**
     * Generic method to add site image
     */
    private function addSiteImage($imagePath, $typeId) {
        // Insert into images table first
        $this->db->query("INSERT INTO images (file_path, file_name, alt_text, created_at) VALUES (:path, :name, :alt, NOW())");
        $this->db->bind(':path', $imagePath);
        $this->db->bind(':name', basename($imagePath));
        $this->db->bind(':alt', basename($imagePath));
        
        if (!$this->db->execute()) {
            return false;
        }
        
        $imageId = $this->db->lastInsertId();
        
        // Insert into image_usages table
        $this->db->query("INSERT INTO image_usages (image_id, ref_type, ref_id, is_primary, created_at) 
                         VALUES (:image_id, 'site', :ref_id, 0, NOW())");
        $this->db->bind(':image_id', $imageId);
        $this->db->bind(':ref_id', $typeId);
        
        return $this->db->execute() ? $imageId : false;
    }
    
    /**
     * Delete a specific site image by usage_id
     */
    public function deleteSiteImage($usageId) {
        // Get image_id first
        $this->db->query("SELECT image_id FROM image_usages WHERE usage_id = :usage_id");
        $this->db->bind(':usage_id', $usageId);
        $imageRecord = $this->db->single();
        
        if (!$imageRecord) return false;
        
        // Delete from image_usages
        $this->db->query("DELETE FROM image_usages WHERE usage_id = :usage_id");
        $this->db->bind(':usage_id', $usageId);
        $result1 = $this->db->execute();
        
        // Check if image is used elsewhere
        $this->db->query("SELECT COUNT(*) as count FROM image_usages WHERE image_id = :image_id");
        $this->db->bind(':image_id', $imageRecord->image_id);
        $usage_count = $this->db->single();
        
        // If not used elsewhere, delete from images table
        if ($usage_count->count == 0) {
            $this->db->query("DELETE FROM images WHERE image_id = :image_id");
            $this->db->bind(':image_id', $imageRecord->image_id);
            $this->db->execute();
        }
        
        return $result1;
    }
    
    /**
     * Delete specific slider
     */
    public function deleteSlider($usageId) {
        return $this->deleteSiteImage($usageId);
    }
    
    /**
     * Delete specific banner
     */
    public function deleteBanner($usageId) {
        return $this->deleteSiteImage($usageId);
    }
    
    /**
     * Delete all site images of a specific type
     */
    private function deleteAllSiteImages($typeId) {
        $this->db->query("SELECT usage_id FROM image_usages 
                         WHERE ref_type = 'site' AND ref_id = :type_id");
        $this->db->bind(':type_id', $typeId);
        $usages = $this->db->resultSet();
        
        foreach ($usages as $usage) {
            $this->deleteSiteImage($usage->usage_id);
        }
        
        return true;
    }
    
    /**
     * Delete all sliders
     */
    public function deleteAllSliders() {
        return $this->deleteAllSiteImages(self::SLIDER_TYPE);
    }
    
    /**
     * Delete all banners
     */
    public function deleteAllBanners() {
        return $this->deleteAllSiteImages(self::BANNER_TYPE);
    }
    
    /**
     * Get all site assets organized by type
     */
    public function getAllSiteAssets() {
        return [
            'sliders' => $this->getSliders(),
            'banners' => $this->getBanners(),
            'logo' => $this->getLogo(),
            'favicon' => $this->getFavicon()
        ];
    }
}