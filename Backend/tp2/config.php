<?php
// 原有常量保持不变
define('_MYSQL_HOST', '127.0.0.1');
define('_MYSQL_PORT', 3306);
define('_MYSQL_DBNAME', 'dbtest');
define('_MYSQL_USER', 'root');
define('_MYSQL_PASSWORD', 'root');

// 添加新的常量以匹配DatabaseConnector.php中使用的名称
define('DB_HOST', _MYSQL_HOST);
define('DB_PORT', _MYSQL_PORT);
define('DB_DATABASE', _MYSQL_DBNAME);
define('DB_USERNAME', _MYSQL_USER);
define('DB_PASSWORD', _MYSQL_PASSWORD);

// JWT配置
define('JWT_BACKEND_KEY', '6d8HbcZndVGNAbo4Ih1TGaKcuA1y2BKs-I5CmP'); 
define('JWT_ISSUER', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');

$connectionString = "mysql:host=" . _MYSQL_HOST;

if(defined('_MYSQL_PORT'))
    $connectionString .= ";port=" . _MYSQL_PORT;

$connectionString .= ";dbname=" . _MYSQL_DBNAME;
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

try {
    $pdo = new PDO($connectionString, _MYSQL_USER, _MYSQL_PASSWORD, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $erreur) {
    myLog('Erreur : '.$erreur->getMessage());
}