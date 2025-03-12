<?php
require "config.php";
require "DatabaseConnector.php";
require "UserModel.php";

header("Content-Type: application/json; charset=UTF-8");

try {
    error_log("Attempting to get users");
    $pdo = DatabaseConnector::current();
    $query = "SELECT * FROM users";
    $statement = $pdo->prepare($query);
    $statement->execute();
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Found " . count($users) . " users");
    echo json_encode($users);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => $e->getMessage()]);
}