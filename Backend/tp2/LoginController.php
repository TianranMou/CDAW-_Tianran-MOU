<?php
include_once __DIR__ . '/libs/php-jwt/src/BeforeValidException.php';
include_once __DIR__ . '/libs/php-jwt/src/ExpiredException.php';
include_once __DIR__ . '/libs/php-jwt/src/SignatureInvalidException.php';
include_once __DIR__ . '/libs/php-jwt/src/JWT.php';
use \Firebase\JWT\JWT;

class LoginController {
    private $requestMethod;
    
    public function __construct($requestMethod) {
        $this->requestMethod = $requestMethod;
    }
    
    public function processRequest() {
        if ($this->requestMethod !== 'POST') {
            $response = $this->unsupportedMethodResponse();
        } else {
            $response = $this->login();
        }
        
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }
    
    private function login() {
        //
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        //change!!!
        $input = $this->request->getContents();
        
        if (!isset($input['login']) || !isset($input['password'])) {
            return $this->unprocessableEntityResponse();
        }
        
        $user = UserModel::tryLogin($input['login']);
        
        // use hash_equals to 
        if (empty($user) || !hash_equals($user['password'], $input['password'])) {
            return $this->unauthorizedResponse();
        }
        
        // 生成JWT令牌
        $issued_at = time();
        $expiration_time = $issued_at + (60 * 60); // 有效期1小时
        
        $token = array(
            "iat" => $issued_at,
            "exp" => $expiration_time,
            "iss" => JWT_ISSUER,
            "data" => array(
                "id" => $user['id'],
                "name" => $user['name'],
                "email" => $user['email'],
                "role" => isset($user['user_role']) ? $user['user_role'] : 1
            )
        );
        
        $jwt = JWT::encode($token, JWT_BACKEND_KEY, 'HS256');
        
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'jwt_token' => $jwt
        ]);
        
        return $response;
    }
    
    private function unprocessableEntityResponse() {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Login and password fields are mandatory'
        ]);
        return $response;
    }
    
    private function unauthorizedResponse() {
        $response['status_code_header'] = 'HTTP/1.1 401 Unauthorized';
        $response['body'] = json_encode([
            'error' => 'Invalid credentials'
        ]);
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