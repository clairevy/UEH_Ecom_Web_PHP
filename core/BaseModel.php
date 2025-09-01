<?php

class BaseModel {
    protected $conn;

    public function __construct() {
        $this->conn = Database::connPDO();
        var_dump($this->conn);
        die();
    }
    public function getALL($sql){
        $stm = $this->conn->prepare($sql);
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }
    //đếm số dòng
     public function getRow($sql){
        $stm = $this->conn->prepare($sql);
        $stm->execute();
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

}