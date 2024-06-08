<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Fetch user data from the database
$username = $_SESSION['username'];
$sql = "SELECT * FROM tblcustomersuser WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Initialize variables to store user data
$userData = array();

if ($result->num_rows > 0) {
    // Fetch user data
    $userData = $result->fetch_assoc();
} else {
    // Handle error if user data is not found
    echo "User data not found!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Website</title>
    <link rel="icon" type="image/x-icon" href="assets/logo.png" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
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
        .profile-image {
            width: 150px; /* Adjust the width as needed */
            height: 150px; /* Adjust the height as needed */
            border-radius: 50%; /* Make the image circular */
            object-fit: cover; /* Ensure the image fills the container without stretching */
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="#">MobodStayFinder</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="customer_h.php">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Profile
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#" onclick="showLogoutModal()">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Logout Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeLogoutModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to logout?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeLogoutModal()">Cancel</button>
                <a href="index.php" class="btn btn-primary">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- User Profile Image -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col text-center">
            <div class="profile-image-container">
                <?php if (!empty($userData['customer_image'])) : ?>
                    <img src="<?php echo $userData['customer_image']; ?>" alt="Profile Image" class="profile-image">
                <?php else : ?>
                    <i class="fas fa-user-circle fa-7x"></i> <!-- Font Awesome user-circle icon -->
                <?php endif; ?>
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-primary" onclick="location.href='cpasswordcustomer.php'">Change Password</button>
                <button type="button" class="btn btn-primary ml-2" onclick="location.href='editprofilecustomer.php'">Edit Profile</button>
            </div>
        </div>
    </div>
</div>

<div class="content mt-5">
    <div class="container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="about-tab" data-toggle="tab" href="#about" role="tab" aria-controls="about" aria-selected="true">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="reservation-tab" data-toggle="tab" href="#reservation" role="tab" aria-controls="reservation" aria-selected="false">Reservation</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <!-- About Tab -->
            <div class="tab-pane fade show active" id="about" role="tabpanel" aria-labelledby="about-tab">
                <h2>About</h2>
                <div class="form-group">
                    <p><label>Username:</label> <?php echo $userData['username']; ?></p>
                </div>
                <div class="form-group">
                    <p><label>First Name:</label> <?php echo $userData['firstname']; ?> <?php echo $userData['lastname']; ?></p>
                </div>
                <div class="form-group">
                    <p><label>Gender:</label> <?php echo $userData['gender']; ?></p>
                </div>
                <div class="form-group">
                    <p><label>Email:</label> <?php echo $userData['email']; ?></p>
                </div>
                <div class="form-group">
                    <p><label>Phone:</label> <?php echo $userData['phonenumber']; ?></p>
                </div>
                <div class="form-group">
                    <p><label>Birthdate:</label> <?php echo $userData['birthdate']; ?></p>
                </div>
            </div>
            
            <!-- Reservation Tab -->
            <div class="tab-pane fade" id="reservation" role="tabpanel" aria-labelledby="reservation-tab">
                <h2>Reservation</h2>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Phone Number</th>
                                <th>Business Name</th>
                                <th>Room Type</th>
                                <th>Room Number</th>
                                <th>Price</th>
                                <th>Created At</th>
                                <th>Countdown</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch reservation data from the database
                            $reservationSql = "SELECT * FROM tblreservations";
                            $reservationStmt = $conn->prepare($reservationSql);
                            $reservationStmt->execute();
                            $reservationResult = $reservationStmt->get_result();

                            while ($reservationData = $reservationResult->fetch_assoc()) {
                                // Combine date and time into a single datetime string
                                $reservationDateTime = $reservationData['date'] . ' ' . $reservationData['time'];
                                $reservationTimestamp = strtotime($reservationDateTime);
                                $countdownTime = strtotime('+3 days', $reservationTimestamp);
                                $currentTime = time();
                                $countdown = $countdownTime - $currentTime;
                            
                                // Convert countdown to days, hours, minutes, and seconds
                                $countdownDays = floor($countdown / (60 * 60 * 24));
                                $countdownHours = floor(($countdown % (60 * 60 * 24)) / (60 * 60));
                                $countdownMinutes = floor(($countdown % (60 * 60)) / 60);
                                $countdownSeconds = $countdown % 60;
                            
                                echo "<tr>";
                                echo "<td>" . $reservationData['date'] . "</td>";
                                echo "<td>" . $reservationData['time'] . "</td>";
                                echo "<td>" . $reservationData['status'] . "</td>";
                                echo "<td>" . $reservationData['firstname'] . "</td>";
                                echo "<td>" . $reservationData['lastname'] . "</td>";
                                echo "<td>" . $reservationData['phonenumber'] . "</td>";
                                echo "<td>" . $reservationData['business_name'] . "</td>";
                                echo "<td>" . $reservationData['room_type'] . "</td>";
                                echo "<td>" . $reservationData['room_number'] . "</td>";
                                echo "<td>" . $reservationData['price'] . "</td>";
                                echo "<td>" . $reservationData['created_at'] . "</td>";
                                echo "<td><span id='countdown_$reservationData[id]'></span></td>"; // Unique ID for each countdown
                            
                                // Display the appropriate action based on the status
                                if ($reservationData['status'] == 'pending') {
                                    echo "<td><button class='btn btn-warning' onclick='showCancelModal($reservationData[id])'>Cancel</button></td>";
                                } elseif ($reservationData['status'] == 'approved') {
                                    echo "<td>Approved</td>";
                                } elseif ($reservationData['status'] == 'declined') {
                                    echo "<td><button class='btn btn-danger' onclick='showDeleteModal($reservationData[id])'>Delete</button></td>";
                                }
                            
                                // JavaScript code to initialize the countdown
                                echo "<script>";
                                echo "var countdown_$reservationData[id] = $countdown;";
                                echo "function updateCountdown_$reservationData[id]() {";
                                echo "  var days = Math.floor(countdown_$reservationData[id] / (60 * 60 * 24));";
                                echo "  var hours = Math.floor((countdown_$reservationData[id] % (60 * 60 * 24)) / (60 * 60));";
                                echo "  var minutes = Math.floor((countdown_$reservationData[id] % (60 * 60)) / 60);";
                                echo "  var seconds = countdown_$reservationData[id] % 60;";
                                echo "  document.getElementById('countdown_$reservationData[id]').innerHTML = days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's';";
                                echo "  countdown_$reservationData[id]--;";
                                echo "  if (countdown_$reservationData[id] < 0) {";
                                echo "    clearInterval(countdownInterval_$reservationData[id]);";
                                echo "    document.getElementById('countdown_$reservationData[id]').innerHTML = 'Expired';";
                                echo "  }";
                                echo "}";
                                echo "var countdownInterval_$reservationData[id] = setInterval(updateCountdown_$reservationData[id], 1000);";
                            
                                // Stop countdown if the status is approved or declined
                                if ($reservationData['status'] == 'approved' || $reservationData['status'] == 'declined') {
                                    echo "clearInterval(countdownInterval_$reservationData[id]);";
                                }
                            
                                echo "</script>";
                            
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Reservation Modal -->
<div id="cancelModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Reservation Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeCancelModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this reservation?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeCancelModal()">Cancel</button>
                <a href="#" id="confirmCancel" class="btn btn-warning">Confirm Cancel</a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Reservation Modal -->
<div id="deleteModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Reservation Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeDeleteModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this reservation?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeDeleteModal()">Cancel</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Confirm Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
function showCancelModal(reservationId) {
    $('#confirmCancel').attr('href', 'cancel_reservation.php?id=' + reservationId);
    $('#cancelModal').modal('show');
}

function closeCancelModal() {
    $('#cancelModal').modal('hide');
}

function showDeleteModal(reservationId) {
    $('#confirmDelete').attr('href', 'delete_reservation.php?id=' + reservationId);
    $('#deleteModal').modal('show');
}

function closeDeleteModal() {
    $('#deleteModal').modal('hide');
}

function showLogoutModal() {
    $('#logoutModal').modal('show');
}

function closeLogoutModal() {
    $('#logoutModal').modal('hide');
}
</script>
</body>
</html>
