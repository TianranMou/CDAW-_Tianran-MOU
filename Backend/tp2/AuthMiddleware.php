<?php
include_once __DIR__ . '/libs/php-jwt/src/BeforeValidException.php';
include_once __DIR__ . '/libs/php-jwt/src/ExpiredException.php';
include_once __DIR__ . '/libs/php-jwt/src/SignatureInvalidException.php';
include_once __DIR__ . '/libs/php-jwt/src/JWT.php';
use \Firebase\JWT\JWT;

class AuthMiddleware {
    public static function getJwtToken() {
        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_LOWER);
        
        if (!isset($headers['authorization'])) {
            throw new Exception("Authorization header not found");
        }
        
        $authorization = $headers['authorization'];
        $arr = explode(" ", $authorization);
        
        if (count($arr) < 2 || $arr[0] !== 'Bearer') {
            throw new Exception("Invalid authorization format");
        }
        
        return $arr[1];
    }
    
    public static function validateToken() {
        try {
            $jwt_token = self::getJwtToken();
            $decodedJWT = JWT::decode($jwt_token, JWT_BACKEND_KEY, ['HS256']);
            return $decodedJWT;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public static function isAuthorized() {
        return self::validateToken() !== false;
    }
    
    public static function getUserId() {
        $decodedJWT = self::validateToken();
        if ($decodedJWT && isset($decodedJWT->data->id)) {
            return $decodedJWT->data->id;
        }
        return null;
    }
}