<?php

include 'connect_db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = password_hash($_POST['user_password'], PASSWORD_DEFAULT);

  $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    echo "User exist already!";
    echo "<br><a href='customer_login.html'>Go back</a>";
  } else {

    $stmt = $conn->prepare("INSERT INTO customers (username, email, password) VALUES (?, ?, ?) ");
    $stmt->bind_param('sss', $username, $email, $password);
    
    if ($stmt->execute()) {
      echo "NEW RECORD CREATED";
      header('Location: customer_login.html');
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }
}
?>
