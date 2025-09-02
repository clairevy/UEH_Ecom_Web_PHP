<?php

class BaseModel {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(){
        $this->db->query("SELECT * FROM " . $this->table);
        return $this->db->resultSet();
    }
    //đếm số dòng
     public function findById($id){
        $this->db->query("SELECT * FROM " . $this->table . " WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    public function deleteById($id){
        $this->db->query("DELETE FROM " . $this->table . " WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}