<?php
class Database{
    private static $conn;
    public static function connPDO(){
       
        try {
            self::$conn = new PDO(_DRIVER.":host="._HOST.";dbname="._DB, _USER, _PASSWORD);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
        return self::$conn;
    }
}