<?php

class UsersController {
    private $requestMethod;
    private $userId;
    
    public function __construct($requestMethod, $userId = null)
    {
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;
    }
    
    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->userId) {
                    $response = $this->getUser($this->userId);
                } else {
                    $response = $this->getAllUsers();
                }
                break;
            case 'POST':
                $this->requireAuth(); // 添加认证检查
                $response = $this->createUser();
                break;
            case 'PUT':
                $this->requireAuth(); // 添加认证检查
                $response = $this->updateUser($this->userId);
                break;
            case 'DELETE':
                $this->requireAuth(); // 添加认证检查
                $response = $this->deleteUser($this->userId);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }
    
    private function getAllUsers()
    {
        $result = UserModel::getAllUsers();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }
    
    private function getUser($id)
    {
        $result = UserModel::getUser($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }
    
    private function createUser()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateUser($input)) {
            return $this->unprocessableEntityResponse();
        }
        
        $userId = UserModel::createUser($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = json_encode(['id' => $userId]);
        
        // 添加Location头部指向新创建的资源
        header('Location: /user/' . $userId);
        
        return $response;
    }
    
    private function updateUser($id)
    {
        $result = UserModel::getUser($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateUser($input)) {
            return $this->unprocessableEntityResponse();
        }
        
        $rows = UserModel::updateUser($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['rows_updated' => $rows]);
        return $response;
    }
    
    private function deleteUser($id)
    {
        $result = UserModel::getUser($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        
        $rows = UserModel::deleteUser($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['rows_deleted' => $rows]);
        return $response;
    }
    
    private function validateUser($input)
    {
        if (!isset($input['name']) || !isset($input['email'])) {
            return false;
        }
        return true;
    }
    
    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input. Required fields: name, email'
        ]);
        return $response;
    }
    
    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode(['error' => 'Resource not found']);
        return $response;
    }

    private function requireAuth() {
        if (!AuthMiddleware::isAuthorized()) {
            $response['status_code_header'] = 'HTTP/1.1 401 Unauthorized';
            $response['body'] = json_encode([
                'error' => 'Authentication required'
            ]);
            header($response['status_code_header']);
            if ($response['body']) {
                echo $response['body'];
            }
            exit();
        }
    }
}