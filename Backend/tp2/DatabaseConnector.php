<?php

class DatabaseConnector {
    protected static $pdo = NULL;

    public static function current(){
        if(is_null(static::$pdo))
            static::createPDO();
            
        return static::$pdo;
    }
    
    protected static function createPDO() {
        // $db = new PDO("sqlite::memory");
        
        $connectionString = "mysql:host=". DB_HOST;
        
        if(defined('_MYSQL_PORT'))
            $connectionString .= ";port=". DB_PORT;
            
        $connectionString .= ";dbname=". DB_DATABASE;
        
        static::$pdo = new PDO($connectionString,DB_USERNAME,DB_PASSWORD);
        static::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}