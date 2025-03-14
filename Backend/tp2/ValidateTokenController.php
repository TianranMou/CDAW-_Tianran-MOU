<?php
include_once __DIR__ . '/libs/php-jwt/src/BeforeValidException.php';
include_once __DIR__ . '/libs/php-jwt/src/ExpiredException.php';
include_once __DIR__ . '/libs/php-jwt/src/SignatureInvalidException.php';
include_once __DIR__ . '/libs/php-jwt/src/JWT.php';
use \Firebase\JWT\JWT;

class ValidateTokenController {
    private $requestMethod;
    
    public function __construct($requestMethod) {
        $this->requestMethod = $requestMethod;
    }
    
    public function processRequest() {
        if ($this->requestMethod !== 'GET') {
            $response = $this->unsupportedMethodResponse();
        } else {
            $response = $this->validateToken();
        }
        
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }
    
    private function validateToken() {
        try {
            $jwt_token = AuthMiddleware::getJwtToken();
            $decodedJWT = JWT::decode($jwt_token, JWT_BACKEND_KEY, ['HS256']);
            
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode([
                'message' => 'Access granted.',
                'data' => $decodedJWT
            ]);
            
        } catch (Exception $e) {
            header('WWW-Authenticate: Bearer realm="'.JWT_ISSUER.'"');
            
            $response['status_code_header'] = 'HTTP/1.1 401 Unauthorized';
            $response['body'] = json_encode([
                'message' => 'Access denied.',
                'error' => $e->getMessage()
            ]);
        }
        
        return $response;
    }
    
    private function unsupportedMethodResponse() {
        $response['status_code_header'] = 'HTTP/1.1 405 Method Not Allowed';
        $response['body'] = json_encode([
            'error' => 'Method not supported'
        ]);
        return $response;
    }
}