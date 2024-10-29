<?php
session_start();
include 'connect_db.php';

if (isset($_GET['rental_id'])) {
    $rental_id = $_GET['rental_id'];

    $stmt = $conn->prepare("DELETE FROM rentals WHERE rental_id = ?");
    $stmt->bind_param('i', $rental_id);
    $stmt->execute();

    header('Location: customer_dashboard.php');
}
?>
