<?php
session_start();

include 'connect_db.php';
$customer_id = $_SESSION['customer_id'];
$car_id = $_POST['car_id'];
$redeem_points = isset($_POST['redeem_points']) && $_POST['redeem_points'] === 'yes';
$start_date = $_POST['departure_date'];
$return_date = $_POST['return_date'];

// Calculate the number of days between the two dates
$date1 = new DateTime($start_date);
$date2 = new DateTime($return_date);
$interval = $date1->diff($date2);
$days = $interval->days; // Get number of days

$sql = "SELECT email FROM customers WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$stmt->bind_result($customer_email);
$stmt->fetch();
$stmt->close();

$sql = "SELECT * FROM cars WHERE car_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $car_id);
$stmt->execute();

$car_result = $stmt->get_result();
$car = $car_result->fetch_assoc();

$total_rent_payment = $car['price_per_day'] * $days;


if ($redeem_points) {
    // Check how many points the user has
    $sql = "SELECT points FROM customers WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    $stmt->bind_result($points);
    $stmt->fetch();
    $stmt->close();
    echo $points;
    // Apply discount if they have enough points
    if ($points >= 50) {
        
        $rent_points = 50;
        
        $total_rent_payment = $total_rent_payment - 500;

        // Insert rental request into database with discount applied
        $sql = "INSERT INTO rentals (customer_id, days, payment_amount, car_id, customer_email, status, start_rent_date, return_rent_date, points) 
        VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iiiisssi', $customer_id, $days, $total_rent_payment, $car_id, $customer_email, $start_date, $return_date, $rent_points);

        if ($stmt->execute()) {
            header('Location: customer_dashboard.php?msg=Rental request submitted');
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo '<script type="text/JavaScript"> prompt("Not enough rental points"); </script>';
        header('Location: customer_dashboard.php');
    }

} else {
    // Insert rental request into database with discount applied
    $sql = "INSERT INTO rentals (customer_id, days, payment_amount, car_id, customer_email, status, start_rent_date, return_rent_date) 
    VALUES (?, ?, ?, ?, ?, 'pending', ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiiisss', $customer_id, $days, $total_rent_payment, $car_id, $customer_email, $start_date, $return_date);

    if ($stmt->execute()) {
    header('Location: customer_dashboard.php?msg=Rental request submitted');
    } else {
    echo "Error: " . $stmt->error;
    }
}

