<?php
// 初始化一个连接到本地数据库的$pdo变量
require_once("initPDO.php");    // 参考文档/课程

$request = $pdo->prepare("select * from users");
// 执行SQL查询
$request->execute();

// 开始生成HTML表格
echo "<h1>Users</h1>";
echo "<table border='1'>";
echo "<tr><th>Id</th><th>Nom</th><th>Email</th></tr>";

// 使用fetch(PDO::FETCH_OBJ)遍历结果并生成表格行
while($user = $request->fetch(PDO::FETCH_OBJ)) {
    echo "<tr>";
    echo "<td>" . $user->id . "</td>";
    echo "<td>" . $user->name . "</td>";
    echo "<td>" . $user->email . "</td>";
    echo "</tr>";
}

echo "</table>";

/*** 关闭数据库连接 ***/
$pdo = null;