<?php
session_start();
include 'config.php';

// Check if user is logged in


// Fetch user data from the database
$username = $_SESSION['username'];
$sql = "SELECT * FROM tblownersuser WHERE username = ?";
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
            background-color: #343a40;
        }
        .navbar .navbar-brand, .navbar .nav-link {
            color: white !important;
        }
        .navbar .nav-link:hover {
            background-color: #495057;
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
                <a class="nav-link" href="owner_db.php" onclick="showContent('home')">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Profile Picture
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
                <?php if (!empty($userData['owner_image'])) : ?>
                    <img src="<?php echo $userData['owner_image']; ?>" alt="Profile Image" class="profile-image">
                <?php else : ?>
                    <i class="fas fa-user-circle fa-7x"></i> <!-- Font Awesome user-circle icon -->
                <?php endif; ?>
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-primary" onclick="location.href='cpasswordowner.php'">Change Password</button>
                <button type="button" class="btn btn-primary ml-2" onclick="location.href='editprofileowner.php'">Edit Profile</button>
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
        </ul>
        <div class="tab-content" id="myTabContent">
            <!-- About Tab -->
            <div class="tab-pane fade show active" id="about" role="tabpanel" aria-labelledby="about-tab">
                <h2></h2>
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
            

        </div>
    </div>
</div>

<script>
function showLogoutModal() {
    $('#logoutModal').modal('show');
}

function closeLogoutModal() {
    $('#logoutModal').modal('hide');
}


</script>
</body>
</html>

