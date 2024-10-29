<?php
session_start();

include 'connect_db.php';  // Use the connection from db_connect.php

if (isset($_GET['car_id'])) {
    $car_id = $_GET['car_id'];
    $stmt = $conn->prepare("DELETE FROM cars WHERE car_id = ?");
    $stmt->bind_param('i', $car_id);
    
    if ($stmt->execute()) {
        echo "Car deleted successfully";
        header('Location: admin_dashboard.php');
    } else {
        echo 'ERROR: ' . $stmt->error;
    }
} else {
    echo "<h1>No ID specified</h1>";
}
?>