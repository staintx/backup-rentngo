<?php
include ("connect_db.php");

if (isset($_GET["rental_id"])) {
    $rental_id = $_GET['rental_id'];
    
    $sql = "DELETE FROM rentals WHERE rental_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rental_id);
    if ($stmt->execute()) {
        echo "Succes";
        header('Location: customer_dashboard.php');
    } else {
        echo $stmt->error;
    }
}
?>