<?php
// 初始化一个连接到本地数据库的$pdo变量
require_once("initPDO.php");

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 检查是否提交了name和email
    if (isset($_POST['name']) && isset($_POST['email'])) {
        // 准备INSERT语句 - 使用预处理语句防止SQL注入
        $insertRequest = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
        
        // 绑定参数
        $insertRequest->bindParam(':name', $_POST['name']);
        $insertRequest->bindParam(':email', $_POST['email']);
        
        // 执行插入
        $insertRequest->execute();
    }
}

// 准备查询以获取所有用户
$request = $pdo->prepare("SELECT * FROM users");
$request->execute();

// HTML页面头部
echo "<!DOCTYPE html>
<html>
<head>
    <title>Users Management</title>
    <meta charset='utf-8'>
</head>
<body>";

// 显示用户表格
echo "<h1>Users</h1>";
echo "<table border='1' style='border-collapse: collapse; width: 80%;'>";
echo "<tr><th>Id</th><th>Nom</th><th>Email</th></tr>";

// 使用fetch(PDO::FETCH_OBJ)遍历结果并生成表格行
while ($user = $request->fetch(PDO::FETCH_OBJ)) {
    echo "<tr>";
    echo "<td>" . $user->id . "</td>";
    echo "<td>" . $user->name . "</td>";
    echo "<td>" . $user->email . "</td>";
    echo "</tr>";
}

echo "</table>";

// 添加用户的表单
echo "<form method='post' action=''>";
echo "<p>name : <input type='text' name='name' required></p>";
echo "<p>email : <input type='email' name='email' required></p>";
echo "<p><input type='submit' value='Add'></p>";
echo "</form>";
echo "<p>Formulaire POST d'ajout d'un utilisateur</p>";

echo "</body></html>";

// 关闭数据库连接
$pdo = null;
?>