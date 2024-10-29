<?php
session_start();

include 'connect_db.php';

$cars_sql = "SELECT * FROM cars WHERE car_id NOT IN (SELECT car_id FROM rentals WHERE status='pending') AND availability = 1";

$cars_results = $conn->query($cars_sql);

$rentals_sql = "SELECT r.rental_id, c.make, c.model, r.days, r.status, r.start_rent_date, r.return_rent_date FROM rentals r JOIN cars c ON r.car_id = c.car_id WHERE r.customer_id = ? ";
$stmt = $conn->prepare($rentals_sql);
$stmt->bind_param('i', $_SESSION['customer_id']);
$stmt->execute();
$rentals_result = $stmt->get_result();

$sql = "SELECT * FROM customers WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $_SESSION['customer_id']);
$stmt->execute();
$customer = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <style>
                /* General body and dashboard layout */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            background-color: #0c2340;
            color: #fff;
            width: 250px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar h1 {
            color: #ffcc00;
            font-size: 24px;
        }

        .sidebar h4 {
            margin: 10px 0;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 20px 0;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            transition: color 0.3s;
        }

        .sidebar ul li a:hover {
            color: #ffcc00;
        }

        .sidebar a {
            color: #ffcc00;
            text-decoration: none;
            margin-top: 20px;
            font-size: 18px;
        }

        /* Main content */
        .main-content {
            flex-grow: 1;
            padding: 20px;
        }

        /* Car display */
        .cars-container-dets {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-around;
            margin-bottom: 40px;
        }

        .box {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 280px;
            transition: transform 0.3s;
        }

        .box:hover {
            transform: translateY(-5px);
        }

        .box img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .info {
            padding: 15px;
        }

        .info h5 {
            font-size: 20px;
            margin: 0;
            color: black;
        }

        .info p {
            margin: 10px 0 0;
            font-size: 16px;
            color: #555;
        }

        .tag {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 10px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: #0c2340;
            color: #fff;
        }

        td {
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-buttons button {
            background-color: #ffcc00;
            border: none;
            padding: 8px 12px;
            color: #333;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .action-buttons button:hover {
            background-color: #e6b800;
        }

        /* Form Styling */
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-container form {
            display: flex;
            flex-direction: column;
        }

        .form-container label {
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-container select,
        .form-container input[type="number"],
        .form-container input[type="submit"] {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-container input[type="checkbox"] {
            margin-left: 10px;
        }

        .form-container input[type="submit"] {
            background-color: #333;
            color: #fff;
            cursor: pointer;
        }

        .form-container input[type="submit"]:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <div id="dashboard-section" class="crud-section">
                <h1>Welcome, <?= $_SESSION['username'] ?></h1>
                <?php while ($customer_points = $customer->fetch_assoc()):?>
                <h4>Rental points: <?=$customer_points['points']?></h4>
                <input type="hidden" id="current_points" value="<?=$customer_points['points']?>">
                <?php endwhile;?>
            </div>
            <ul>
                <li><a href="customer_dashboard.php">Available cars</a></li>
                <li><a href="customer_dashboard.php">Pending rent</a></li>

            </ul>
            <a href="logout.php">Logout</a>
        </div>
        
        <div class="main-content">
            <div class="container cars-container-dets">
            <?php while ($car = $cars_results->fetch_assoc()): ?>
                <div class="box">
                    <img src="pic\cars pixels (1).jpg" alt="">
                    <div class="info">
                        <div class="tag">
                            <span class="lnr lnr-pointer-right"></span>
                            <p><?= $car['price_per_day'] ?>/DAY</p>
                            <p><?= $car['availability'] ? "Available" : "Rented" ?></p>
                        </div>
                        <h5 ><?= $car['make'] ?></h5>
                        <p><?= $car['model'] ?></p>
                        <div>
                            <a href="car-info.php"><?= $car['year'] ?></a>
                            
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <table border="1">
            <tr>
                <th>Make</th>
                <th>Status</th>
                <th>Departure</th>
                <th>Return</th>
                <th>Action</th>
            </tr> 
            
                <?php while ($rental = $rentals_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($rental['make']) . ' ' . htmlspecialchars($rental['model']) ?></td>
                        <td><?= htmlspecialchars($rental['status']) ?></td>
                        <td><?= htmlspecialchars($rental['start_rent_date']) ?></td>
                        <td><?= htmlspecialchars($rental['return_rent_date']) ?></td>
                        <td class="action-buttons">
                            <?php if ($rental['status'] == 'pending'): ?>
                                <a href="cancel_rental.php?rental_id=<?= $rental['rental_id'] ?>"><button>Cancel</button></a>
                            <?php else: ?>
                               
                                <a href="delete_rent_history.php?rental_id=<?= $rental['rental_id'] ?>"><button>Delete history</button></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <!--  Rent  -->
            <div class="form-container">
                <form action="rent_car.php" method="POST">
                    <label for="car_id">Select Car:</label>
                    <select name="car_id" required>
                        <?php
                        $cars_results->data_seek(0); // Reset the result set
                        while ($car = $cars_results->fetch_assoc()): 
                        ?>
                            <option value="<?= $car['car_id'] ?>">
                                <?= htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']) ?>
                            </option>
                        <?php endwhile; ?>

                    </select><br><br>

                    <div class="points-redemption">
                        <label for="redeem_points">Redeem Points:</label>
                        <input type="checkbox" id="redeem_points" name="redeem_points" value="yes">
                        <span id="points-message">500 discount for 50 rent points</span>
                    </div>
                    


                    <script>
                        
                        window.onload = function() {
                         // Points-related functionality
                            const pointsCheckbox = document.getElementById('redeem_points');
                            const currentPoints = parseInt(document.getElementById('current_points').value);
                            const pointsMessage = document.getElementById('points-message');
                            
                            // Check if user has enough points
                            if (currentPoints < 50) {
                                pointsCheckbox.disabled = true;
                                pointsMessage.innerHTML = '500 discount for 50 rent points (Insufficient points)';
                                pointsMessage.style.color = '#999';
                            }
                            const today = new Date();
                            const minDate = new Date();
                            const maxDate = new Date();

                            // Set min date to today
                            minDate.setDate(today.getDate() + 1); // 1 day from today
                            // Set max date to 7 days from today
                            maxDate.setDate(today.getDate() + 7); // 7 days from today

                            // Format dates as YYYY-MM-DD
                            const formatDate = (date) => date.toISOString().split('T')[0];

                            // Set min and max for departure date
                            const depInput = document.getElementById("departure_date");
                            depInput.setAttribute("min", formatDate(minDate));
                            depInput.setAttribute("max", formatDate(maxDate));

                            // Set min for return date based on departure date selection
                            depInput.addEventListener('change', function() {
                                const depDate = new Date(depInput.value);
                                const returnInput = document.getElementById("return_date");
                                returnInput.setAttribute("min", formatDate(new Date(depDate.getTime() + 1 * 24 * 60 * 60 * 1000))); // 1 day after departure
                                returnInput.setAttribute("max", formatDate(new Date(depDate.getTime() + 7 * 24 * 60 * 60 * 1000))); // Max return is 7 days after departure
                                returnInput.value = ""; // Reset return date when departure changes
                            });
                        }
                    </script>

                    <label for="departure_date">Departure Date:</label>
                    <input type="date" id="departure_date" name="departure_date" required>
                    
                    <label for="return_date">Return Date:</label>
                    <input type="date" id="return_date" name="return_date" required>

                    <input type="submit"  value="Submit Rental Request">
                </form>
            </div>
        </div>
    </div>
</body>
</html>