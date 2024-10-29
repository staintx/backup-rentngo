<?php
session_start();

include 'connect_db.php';

if (isset($_GET['car_id'])) {
    $car_id = $_GET['car_id'];

    $stmt = $conn->prepare("SELECT * FROM cars WHERE car_id = ?");
    $stmt->bind_param('i', $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();

    if (!$car) {
       echo "Car not found";
       exit();
    }
    
} else {
    echo "car_id not specified!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $availability = $_POST['availability'];
    $price_per_day = $_POST['price_per_day'];

    $stmt = $conn->prepare("UPDATE cars SET make = ?, model = ?, year = ?, availability = ?, price_per_day = ? WHERE car_id = ?");
    $stmt->bind_param('ssiidi', $make, $model, $year, $availability, $price_per_day, $car_id);

    if ($stmt->execute()) {
        echo "Car updated successfully!";
        header('Location: admin_dashboard.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit cars</title>
</head>
<body>
    <form action="edit_car.php?car_id=<?=$car_id?>" method="POST">
        <input type="text" id="make" name="make" value="<?=htmlspecialchars($car['make'])?>">
        <input type="text" id="model" name="model" value="<?=htmlspecialchars($car['model'])?>">
        <input type="number" id="year" name="year" value="<?=htmlspecialchars($car['year'])?>">
        <select name="availability" id="availability">
            <option value="1">Available</option>
            <option value="0">Not available</option>
        </select id="availability" name="availability">
        <input type="number" id="price_per_day" name="price_per_day" value="<?= htmlspecialchars($car['price_per_day'])?>" step=".01">
        
        <input type="submit" value="Submit">
    </form>
</body>
</html>