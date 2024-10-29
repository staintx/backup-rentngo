<?php
session_start();
include 'connect_db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['user_password'];

    $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['customer_id'] = $user['customer_id'];
        $_SESSION['email'] = $email;
        $_SESSION['username'] = $user['username'];
        header('Location: customer_dashboard.php');

    } else {
        echo "Invalid log in credentials<br>";
        echo "<a href='customer_login.html'>Go back</a>";
    }
}

?>