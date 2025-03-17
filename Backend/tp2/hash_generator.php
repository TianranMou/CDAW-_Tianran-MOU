<?php
// 检查是否有提交的密码
if (isset($_POST['password'])) {
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // 打印结果
    $result = "原始密码: " . $password . "\n\n";
    $result .= "哈希值: " . $password_hash . "\n\n";
    
    // 验证哈希是否有效
    $verify = password_verify($password, $password_hash);
    $result .= "验证结果: " . ($verify ? "成功" : "失败");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>密码哈希生成器</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        form {
            margin: 20px 0;
        }
        input[type="text"] {
            padding: 8px;
            width: 300px;
        }
        input[type="submit"] {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        pre {
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>密码哈希生成器</h1>
    <p>输入一个密码，将生成对应的BCRYPT哈希值</p>
    
    <form method="post">
        <input type="text" name="password" placeholder="输入密码" required>
        <input type="submit" value="生成哈希">
    </form>
    
    <?php if (isset($result)): ?>
    <h2>结果:</h2>
    <pre><?php echo $result; ?></pre>
    <?php endif; ?>
</body>
</html>