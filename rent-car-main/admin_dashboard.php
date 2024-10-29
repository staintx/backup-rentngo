<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.html");
    exit();
}

include 'connect_db.php';

$cars_sql = "SELECT * FROM cars";
$cars_result = $conn->query($cars_sql);

$sql = "SELECT r.rental_id, r.car_id, r.customer_id, r.days, r.payment_amount, r.points, c.make, c.model, u.username, u.email, r.status, r.start_rent_date, r.return_rent_date 
        FROM rentals r
        JOIN cars c ON r.car_id = c.car_id
        JOIN customers u ON r.customer_id = u.customer_id
        WHERE r.status = 'pending'";

$rentals_result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f4f4f4;
            color: #333;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #0c2340;
            color: white;
            padding: 2rem 1rem;
            position: fixed;
            height: 100%;
        }

        .sidebar h2 {
            font-size: 24px;
            margin-bottom: 2rem;
            color: #fff;
        }

        .sidebar ul {
            list-style-type: none;
        }

        .sidebar ul li {
            margin-bottom: 1.5rem;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            padding: 0.8rem 1rem;
            display: block;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .sidebar ul li a:hover {
            background-color: #1A3967;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            width: 100%;
        }

        .main-content h2 {
            font-size: 28px;
            margin-bottom: 1.5rem;
            color: #0C2340;
        }

        .main-content a {
            background-color: #0C2340;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-bottom: 1rem;
            display: inline-block;
        }

        .main-content a:hover {
            background-color: #1A3967;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 1rem;
            text-align: center;
        }

        th {
            background-color: #0C2340;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        /* Buttons inside table */
        td a {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            color: white;
        }

        td a:hover {
            opacity: 0.9;
        }

        td a:first-child {
            background-color: #28A745;
            /* Green for approve */
        }

        td a:last-child {
            background-color: #DC3545;
            /* Red for disapprove */
        }

        .sidebar ul li a[href="logout.php"] {
            margin-top: 2rem;
            background-color: rgba(255, 107, 107, 0.1);
            border: 1px solid #ff6b6b;
        }

        .sidebar ul li a[href="logout.php"]:hover {
            background-color: #ff6b6b;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            table {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="#">Dashboard</a></li>
                <li><a href="#manage-cars">Manage Cars</a></li>
                <li><a href="#rental-request">Rental Requests</a></li>
                <li><a href="admin_logout.php" style="color: #ff6b6b;">Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <h2 id="manage-cars">Manage Cars</h2>
            <a href="add_car.html">Add New Car</a>
            <table border="1">
                <tr>
                    <th>Car ID</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Availability</th>
                    <th>Price per day</th>
                    <th>Actions</th>
                </tr>
                <?php while ($cars = $cars_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $cars['car_id'] ?></td>
                        <td><?= $cars['make'] ?></td>
                        <td><?= $cars['model'] ?></td>
                        <td><?= $cars['year'] ?></td>
                        <td><?= $cars['availability'] ? 'Available' : 'Not available' ?></td>
                        <td><?= $cars['price_per_day'] ?></td>
                        <td>
                            <a href="edit_car.php?car_id=<?= $cars['car_id'] ?>">Edit</a>
                            <a href="delete_car.php?car_id=<?= $cars['car_id'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <h2 id="rental-request">Pending Rental Requests</h2>
            <table border="1">
                <tr>
                    <th>Customer</th>
                    <th>Customer Email</th>
                    <th>Car</th>
                    <th>Days to Rent</th>
                    <th>Payment Amount</th>
                    <th>Points</th>
                    <th>Departure date</th>
                    <th>Return date</th>
                    <th>Actions</th>
                </tr>
                <?php while ($rental = $rentals_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($rental['username']) ?></td>
                        <td><?= htmlspecialchars($rental['email']) ?></td>
                        <td><?= htmlspecialchars($rental['make']) ?></td>
                        <td><?= htmlspecialchars($rental['days']) ?></td>
                        <td><?= htmlspecialchars($rental['payment_amount']) ?></td>
                        <td><?= htmlspecialchars($rental['points']) ?></td>
                        <td><?= htmlspecialchars($rental['start_rent_date']) ?></td>
                        <td><?= htmlspecialchars($rental['return_rent_date']) ?></td>
                        <td>
                            <a href="admin_approve_rental.php?rental_id=<?= $rental['rental_id'] ?>">Approve</a>
                            <a href="admin_disapprove_rental.php?rental_id=<?= $rental['rental_id'] ?>">Disapprove</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>

</html>