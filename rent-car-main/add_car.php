<?php
session_start();

include 'connect_db.php';  // Use the connection from db_connect.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $price_per_day = $_POST['price_per_day'];

    //handle file upload
    $target_dir = "pic/car-pics/"; // Create this directory in your project
    $car_img = null;

    // Check if image was uploaded
    if(isset($_FILES["car_img"]) && $_FILES["car_img"]["error"] == 0) {
        $file_extension = strtolower(pathinfo($_FILES["car_img"]["name"], PATHINFO_EXTENSION));
        
        // Generate unique filename
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Allow certain file formats
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        
        if(in_array($file_extension, $allowed_types)) {
            if(move_uploaded_file($_FILES["car_img"]["tmp_name"], $target_file)) {
                $car_img = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
        } else {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            exit;
        }
    }
    

    // Insert car into the database
    $stmt = $conn->prepare("INSERT INTO cars (make, model, year, price_per_day, car_img) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssids', $make, $model, $year, $price_per_day, $car_img);

    if ($stmt->execute()) {
        echo "Car added successfully!";
        header('Location: admin_dashboard.php');
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
