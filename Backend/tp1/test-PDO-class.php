<?php
// 加载数据库连接文件
require_once("initPDO.php");

/**
 * User类 - 表示users表中的用户
 */
class User {
    // 用户属性（对应数据库字段）
    public $id;
    public $name;
    public $email;
    
    /**
     * 获取所有用户
     * @return array 包含所有User实例的数组
     */
    public static function getAllUsers() {
        global $pdo;
        
        $request = $pdo->prepare("SELECT * FROM users");
        $request->execute();
        
        // 使用FETCH_CLASS获取模式，直接创建User类的实例
        return $request->fetchAll(PDO::FETCH_CLASS, 'User');
    }
    
    /**
     * 以HTML表格形式显示所有用户
     */
    public static function showAllUsersAsTable() {
        $users = self::getAllUsers();
        
        echo "<table border='1' style='border-collapse: collapse; width: 80%;'>";
        echo "<tr><th>Id</th><th>Nom</th><th>Email</th></tr>";
        
        foreach ($users as $user) {
            echo $user->toHtml();
        }
        
        echo "</table>";
    }
    
    /**
     * 将用户信息转换为HTML表格行
     * @return string HTML表格行
     */
    public function toHtml() {
        return "<tr>
                <td>{$this->id}</td>
                <td>{$this->name}</td>
                <td>{$this->email}</td>
               </tr>";
    }
    
    /**
     * 添加新用户到数据库
     * @param string $name 用户名
     * @param string $email 用户邮箱
     * @return bool 添加是否成功
     */
    public static function addUser($name, $email) {
        global $pdo;
        
        $request = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
        $request->bindParam(':name', $name);
        $request->bindParam(':email', $email);
        
        return $request->execute();
    }
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['name']) && isset($_POST['email'])) {
        User::addUser($_POST['name'], $_POST['email']);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users Management (OOP)</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Users</h1>
    <?php
        User::showAllUsersAsTable();
    ?>
    
    <h2>Add New User</h2>
    <form method="post" action="">
        <p>name : <input type="text" name="name" required></p>
        <p>email : <input type="email" name="email" required></p>
        <p><input type="submit" value="Add"></p>
    </form>
    <p>Formulaire POST d'ajout d'un utilisateur</p>
</body>
</html>

<?php
// 关闭数据库连接
$pdo = null;
?>