<?php
require "bootstrap.php";

// https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin
header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");

// https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Methods
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");

header("Access-Control-Max-Age: 3600"); // Maximum number of seconds the results can be cached.

// https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Headers
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 处理OPTIONS预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

function getRoute($url){
    $url = trim($url, '/');
    $urlSegments = explode('/', $url);
    
    $scheme = ['controller', 'params'];
    $route = [];
    
    foreach ($urlSegments as $index => $segment){
        if ($scheme[$index] == 'params'){
            $route['params'] = array_slice($urlSegments, $index);
            break;
        } else {
            $route[$scheme[$index]] = $segment;
        }
    }
    
    return $route;
}

// $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
$route = getRoute($uri);

$requestMethod = $_SERVER["REQUEST_METHOD"];

$controllerName = isset($route['controller']) ? $route['controller'] : '';
$userId = null;

// 检查是否是单个用户操作
if ($controllerName === 'user' && isset($route['params']) && !empty($route['params'])) {
    $userId = $route['params'][0];
    $controller = new UsersController($requestMethod, $userId);
} 
// 用户列表操作
else if ($controllerName === 'users') {
    $controller = new UsersController($requestMethod);
} 
// 无效路由
else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["error" => "Endpoint not found"]);
    exit();
}

$controller->processRequest();