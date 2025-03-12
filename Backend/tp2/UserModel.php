<?php

class UserModel
{
    public static function getAllUsers() {
        $pdo = DatabaseConnector::current();
        $query = "SELECT * FROM users";
        $statement = $pdo->prepare($query);
        $statement->execute();
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getUser($id) {
        $pdo = DatabaseConnector::current();
        $query = "SELECT * FROM users WHERE id = :id";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
        
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function createUser($data) {
        $pdo = DatabaseConnector::current();
        $query = "INSERT INTO users (name, email) VALUES (:name, :email)";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':name', $data['name']);
        $statement->bindParam(':email', $data['email']);
        $statement->execute();
        
        return $pdo->lastInsertId();
    }
    
    public static function updateUser($id, $data) {
        $pdo = DatabaseConnector::current();
        $query = "UPDATE users SET name = :name, email = :email WHERE id = :id";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->bindParam(':name', $data['name']);
        $statement->bindParam(':email', $data['email']);
        $statement->execute();
        
        return $statement->rowCount();
    }
    
    public static function deleteUser($id) {
        $pdo = DatabaseConnector::current();
        $query = "DELETE FROM users WHERE id = :id";
        $statement = $pdo->prepare($query);
        $statement->bindParam(':id', $id);
        $statement->execute();
        
        return $statement->rowCount();
    }
}