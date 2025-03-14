<?php
require "bootstrap.php";

// 添加调试日志
error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
error_log("Query string: " . $_SERVER['QUERY_STRING']);
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Request body: " . file_get_contents('php://input'));

// 设置HTTP头
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 处理OPTIONS预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

function getRoute($url){
    // 移除前后斜杠
    $url = trim($url, '/');
    
    // 如果URL为空，返回空路由
    if (empty($url)) {
        return ['controller' => ''];
    }
    
    // 按斜杠分割URL
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

// 获取查询字符串
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY));
error_log("Parsed query string: " . $uri);

// 改进的路由处理
if ($uri && !strpos($uri, '/')) {   
    // 如果查询字符串不包含斜杠，直接作为控制器名
    $route = ['controller' => $uri];
    error_log("Simple route: controller=" . $uri);
} else {
    // 否则使用标准路由解析
    $route = getRoute($uri);
    error_log("Complex route: " . json_encode($route));
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

$controllerName = isset($route['controller']) ? $route['controller'] : '';
$userId = null;

error_log("Controller name: " . $controllerName);

// 路由分发逻辑
if ($controllerName === 'user' && isset($route['params']) && !empty($route['params'])) {
    // 单个用户操作
    $userId = $route['params'][0];
    error_log("User operation with ID: " . $userId);
    $controller = new UsersController($requestMethod, $userId);
} 
else if ($controllerName === 'users') {
    // 用户集合操作
    error_log("Users collection operation");
    $controller = new UsersController($requestMethod);
} 
else if ($controllerName === 'login') {
    // 登录路由
    error_log("Login operation");
    $controller = new LoginController($requestMethod);
} 
else if ($controllerName === 'validatetoken') {
    // 令牌验证路由
    error_log("Token validation operation");
    $controller = new ValidateTokenController($requestMethod);
}
else {
    // 无效路由
    error_log("Invalid route: " . $controllerName);
    header("HTTP/1.1 404 Not Found");
    echo json_encode([
        "error" => "Endpoint not found",
        "debug" => [
            "uri" => $uri,
            "controller" => $controllerName,
            "method" => $requestMethod,
            "route" => $route
        ]
    ]);
    exit();
}

// 处理请求
$controller->processRequest();