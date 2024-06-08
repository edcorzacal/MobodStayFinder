<?php
include 'config.php';
session_start();

if (isset($_GET['boarding_house_id'])) {
    $boarding_house_id = intval($_GET['boarding_house_id']);

    // Fetch business name and room details for the given boarding house id
    $sql = "SELECT business_name, room_type, room_number, price, availability FROM tblboardinghouses 
            LEFT JOIN tblrooms ON tblboardinghouses.id = tblrooms.boarding_house_id 
            WHERE tblboardinghouses.id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $boarding_house_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $business_name = $row['business_name'];
            $room_type = $row['room_type'];
            $room_number = $row['room_number'];
            $price = $row['price'];
            $availability = $row['availability'];
        } else {
            $errorMessage = "Boarding house not found";
        }
        $stmt->close();
    } else {
        // Print error message if prepare statement fails
        $errorMessage = "Failed to prepare the SQL statement: " . $conn->error;
    }
} else {
    $errorMessage = "Invalid boarding house ID";
}

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Fetch user details from the database
    $sql = "SELECT firstname, lastname, phonenumber FROM tblcustomersuser WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $firstname = $user['firstname'];
            $lastname = $user['lastname'];
            $phonenumber = $user['phonenumber'];
        } else {
            $errorMessage = "User not found";
        }
        $stmt->close();
    } else {
        $errorMessage = "Failed to prepare the SQL statement: " . $conn->error;
    }
} else {
    $errorMessage = "Username not set in session";
}

// Handle form submission to save reservation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = date("Y-m-d");
    $time = date("H:i:s");
    $status = "Pending";
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $phonenumber = $_POST['phonenumber'];
    $business_name = $_POST['business_name'];
    $room_type = $_POST['room_type'];
    $room_number = $_POST['room_number'];
    $price = $_POST['price'];
    $created_at = date("Y-m-d H:i:s");

    $conn->begin_transaction();
    try {
        // Insert reservation
        $sql = "INSERT INTO tblreservations (date, time, status, firstname, lastname, phonenumber, business_name, room_type, room_number, price, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $date, $time, $status, $firstname, $lastname, $phonenumber, $business_name, $room_type, $room_number, $price, $created_at);
        $stmt->execute();
        $stmt->close();

        // Update availability
        $sql = "UPDATE tblrooms SET availability = availability - 1 WHERE boarding_house_id = ? AND room_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $boarding_house_id, $room_number);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        echo "<script>alert('Reservation complete!'); window.location.href='customer_h.php';</script>";
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $errorMessage = "Failed to complete reservation: " . $e->getMessage();
    }
}

// Set date and status for the form
$date = date("Y-m-d");
$status = "Pending";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <link rel="icon" type="image/x-icon" href="assets/logo.png" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .card-header div {
            font-weight: bold;
            font-size: 1.1em;
        }
        .card-body {
            background-color: #fff;
        }
        .table th, .table td {
            text-align: center;
        }
        .table th {
            background-color: #f1f1f1;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar .navbar-brand, .navbar .nav-link {
            color: white !important;
        }
        .navbar .nav-link:hover {
            background-color: #0056b3;
            border-radius: 4px;
        }
        .content {
            padding: 20px;
            display: none;
        }
        .content.active {
            display: block;
        }
    </style>
    <script>
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            document.getElementById('current-time').textContent = timeString;
        }
        setInterval(updateTime, 1000); // Update time every second
    </script>
</head>
<body onload="updateTime()">

<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="#">MobodStayFinder</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
        <li class="nav-item">
                <!-- Update the href attribute to customer_h.php -->
                <a class="nav-link" href="customer_h.php">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Profile
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="editprofilecustomer.php" onclick="showContent('account-settings')">Account Settings</a>
                    <a class="dropdown-item" href="#" onclick="showLogoutModal()">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <h2 class="text-center">Reservation Summary</h2>
    <a href="javascript:history.back()" class="btn btn-primary mb-3">Back</a>
    <hr>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-start">
            <div>Date: <span><?php echo htmlspecialchars($date); ?></span><br></div>
            <div>Time: <span id="current-time"></span></div>
            <div>Status: <span><?php echo htmlspecialchars($status); ?></span></div>
        </div>
        <div class="card-body">
            <span class="font-weight-bold">Customer Information</span><br>
            <br>
            <span>Name: <?php echo isset($firstname) ? htmlspecialchars($firstname . ' ' . $lastname) : ''; ?></span><br>
            <br>
            <span>Phone Number: <?php echo isset($phonenumber) ? htmlspecialchars($phonenumber) : ''; ?></span>
            <hr>
            <?php if (isset($business_name)): ?>
                <span class="font-weight-bold">Room Details</span>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Business Name</th>
                            <th>Room Type</th>
                            <th>Room Number</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo htmlspecialchars($business_name); ?></td>
                            <td><?php echo htmlspecialchars($room_type); ?></td>
                            <td><?php echo htmlspecialchars($room_number); ?></td>
                            <td>₱<?php echo htmlspecialchars(number_format($price, 2)); ?></td>
                        </tr>
                    </tbody>
                </table>
                <form method="post" action="">
                    <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
                    <input type="hidden" name="time" value="<?php echo htmlspecialchars(date("H:i:s")); ?>">
                    <input type="hidden" name="status" value="Pending">
                    <input type="hidden" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>">
                    <input type="hidden" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>">
                    <input type="hidden" name="phonenumber" value="<?php echo htmlspecialchars($phonenumber); ?>">
                    <input type="hidden" name="business_name" value="<?php echo htmlspecialchars($business_name); ?>">
                    <input type="hidden" name="room_type" value="<?php echo htmlspecialchars($room_type); ?>">
                    <input type="hidden" name="room_number" value="<?php echo htmlspecialchars($room_number); ?>">
                    <input type="hidden" name="price" value="<?php echo htmlspecialchars($price); ?>">
                    <div class="mt-3 text-center">
                        <button type="submit" class="btn btn-primary mt-3">Proceed with Reservation</button>
                    </div>
                </form>
                <?php if (isset($successMessage)): ?>
                    <div class="alert alert-success mt-3"><?php echo htmlspecialchars($successMessage); ?></div>
                <?php elseif (isset($errorMessage)): ?>
                    <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?>
            <?php else: ?>
                <p><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
