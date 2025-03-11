<?php
// Load database connection file
require_once("initPDO.php");

/**
 * User class - Represents users in the users table
 */
class User {
    // User properties
    public $id;
    public $name;
    public $email;
    
    /**
     * Get all users
     * @return array Array containing all User instances
     */
    public static function getAllUsers() {
        global $pdo;
        
        $request = $pdo->prepare("SELECT * FROM users");
        $request->execute();
        
        return $request->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'User');
    }
    
    /**
     * Get a single user by ID
     * @param int $id User ID
     * @return User|false User object or false
     */
    public static function getUserById($id) {
        global $pdo;
        
        $request = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $request->bindParam(':id', $id, PDO::PARAM_INT);
        $request->execute();
        
        $request->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'User');
        return $request->fetch();
    }
    
    /**
     * Display HTML table with all users
     */
    public static function showAllUsersAsTable() {
        $users = self::getAllUsers();
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
              </tr>";
        
        foreach ($users as $user) {
            $editMode = isset($_GET['action']) && $_GET['action'] == 'edit' && $_GET['id'] == $user->id;
            echo $user->toHtml($editMode);
        }
        
        echo "</table>";
    }
    
    /**
     * Convert user information to HTML table row
     * @param bool $editMode Whether in edit mode
     * @return string HTML table row
     */
    public function toHtml($editMode = false) {
        if ($editMode) {
            // Row in edit mode
            return "<tr>
                    <td>{$this->id}</td>
                    <td>
                        <form method='post' action='' class='inline-form'>
                            <input type='hidden' name='id' value='{$this->id}'>
                            <input type='text' name='name' value='{$this->name}' required>
                    </td>
                    <td>
                            <input type='email' name='email' value='{$this->email}' required>
                    </td>
                    <td>
                            <button type='submit' class='btn btn-success'>Save</button>
                            <a href='?' class='btn btn-primary'>Cancel</a>
                        </form>
                    </td>
                   </tr>";
        } else {
            // Normal display mode
            return "<tr>
                    <td>{$this->id}</td>
                    <td>{$this->name}</td>
                    <td>{$this->email}</td>
                    <td>
                        <a href='?action=edit&id={$this->id}' class='btn btn-primary'>Edit</a>
                        <a href='?action=delete&id={$this->id}' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
                    </td>
                   </tr>";
        }
    }
    
    /**
     * Add a new user
     * @param string $name Username
     * @param string $email User email
     * @return bool Success status
     */
    public static function addUser($name, $email) {
        global $pdo;
        
        $request = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
        $request->bindParam(':name', $name);
        $request->bindParam(':email', $email);
        
        return $request->execute();
    }
    
    /**
     * Update user information
     * @param int $id User ID
     * @param string $name New username
     * @param string $email New email
     * @return bool Success status
     */
    public static function updateUser($id, $name, $email) {
        global $pdo;
        
        $request = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $request->bindParam(':id', $id, PDO::PARAM_INT);
        $request->bindParam(':name', $name);
        $request->bindParam(':email', $email);
        
        return $request->execute();
    }
    
    /**
     * Delete user
     * @param int $id User ID
     * @return bool Success status
     */
    public static function deleteUser($id) {
        global $pdo;
        
        $request = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $request->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $request->execute();
    }
}

// Handle operation requests
$message = '';

// Handle delete operation
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    if (User::deleteUser($_GET['id'])) {
        $message = '<div class="alert alert-success">User successfully deleted!</div>';
    } else {
        $message = '<div class="alert alert-danger">Error deleting user!</div>';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process add or update operations
    if (isset($_POST['name']) && isset($_POST['email'])) {
        // If there's an ID, update the user
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            if (User::updateUser($_POST['id'], $_POST['name'], $_POST['email'])) {
                $message = '<div class="alert alert-success">User successfully updated!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error updating user!</div>';
            }
        } 
        // Otherwise add a new user
        else {
            if (User::addUser($_POST['name'], $_POST['email'])) {
                $message = '<div class="alert alert-success">User successfully added!</div>';
            } else {
                $message = '<div class="alert alert-danger">Error adding user!</div>';
            }
        }
    }
}

// Add form display status, hidden by default
$showAddForm = isset($_GET['showAddForm']) ? $_GET['showAddForm'] == '1' : false;
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management System</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        h1, h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn {
            display: inline-block;
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-success {
            background-color: #28a745;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 3px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .inline-form {
            margin: 0;
        }
        .inline-form input {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Management System</h1>
        
        <?php echo $message; // Display operation message ?>
        
        <!-- User list -->
        <h2>User List</h2>
        <?php User::showAllUsersAsTable(); ?>
        
        <!-- Toggle button for add form -->
        <p>
            <a href="?showAddForm=<?php echo $showAddForm ? '0' : '1'; ?>" class="btn btn-primary">
                <?php echo $showAddForm ? 'Hide Add Form' : 'Show Add Form'; ?>
            </a>
        </p>
        
        <!-- Add new user form, show or hide based on status -->
        <div class="card <?php echo $showAddForm ? '' : 'hidden'; ?>" id="addUserForm">
            <h2>Add New User</h2>
            <form method="post" action="?showAddForm=1">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <button type="submit" class="btn btn-success">Add User</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
// Close database connection
$pdo = null;
?>