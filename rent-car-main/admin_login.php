<?php
session_start();

include 'connect_db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_username = $_POST["admin_username"];
    $password = $_POST['admin_password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param('s', $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $password == $user['password']) {
        $_SESSION['admin_id'] = $user['admin_id'];
        $_SESSION['admin_username'] = $admin_username;
        header('Location: admin_dashboard.php');

    } else {
        echo "<script>
            alert('Invalid login credentials');
            window.location.href = 'admin_login.html';
        </script>";
        exit();
        
    }

}

?>