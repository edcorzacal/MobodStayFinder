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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $middlename = $_POST['middlename'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $phonenumber = $_POST['phonenumber'];
    $birthdate = $_POST['birthdate'];

    // Handle file upload
    if (!empty($_FILES['customer_image']['name'])) {
        $targetDir = "uploads/";

        // Ensure the uploads directory exists
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = basename($_FILES['customer_image']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Allow certain file formats
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            // Upload file to server
            if (move_uploaded_file($_FILES['customer_image']['tmp_name'], $targetFilePath)) {
                $customer_image = $targetFilePath;
            } else {
                echo "Sorry, there was an error uploading your file.";
                $customer_image = $userData['customer_image']; // Use the existing image if upload fails
            }
        } else {
            echo "Sorry, only JPG, JPEG, PNG, & GIF files are allowed.";
            $customer_image = $userData['customer_image']; // Use the existing image if invalid format
        }
    } else {
        $customer_image = $userData['customer_image']; // Use the existing image if no new image is uploaded
    }

    // Update user data in the database
    $updateSql = "UPDATE tblcustomersuser SET firstname = ?, lastname = ?, middlename = ?, email = ?, gender = ?, phonenumber = ?, birthdate = ?, customer_image = ? WHERE username = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssssssss", $firstname, $lastname, $middlename, $email, $gender, $phonenumber, $birthdate, $customer_image, $username);

    if ($updateStmt->execute()) {
        // Redirect back to viewprofilecustomer.php after successful update
        header('Location: viewprofilecustomer.php');
        exit();
    } else {
        // Handle error if update fails
        echo "Error updating profile!";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
        .profile-image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        .profile-image {
        width: 100px; /* Adjust the width as needed */
        height: 100px; /* Adjust the height as needed */
        border-radius: 50%; /* Makes it circular */
        object-fit: cover; /* Ensures the image covers the container */
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

<div class="container mt-5">
    <h2>Edit Profile</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group text-center">
            <label for="customer_image">Profile Image</label>
            <div class="profile-image-container">
    <?php if (!empty($userData['customer_image'])) : ?>
        <img src="<?php echo htmlspecialchars($userData['customer_image']); ?>" alt="Profile Image" class="profile-image" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
    <?php else : ?>
        <i class="fas fa-user-circle fa-7x"></i> <!-- Font Awesome user-circle icon -->
    <?php endif; ?>
</div>

            <input type="file" class="form-control-file mt-3" id="customer_image" name="customer_image">
        </div>
        <div class="form-group">
            <label for="firstname">First Name</label>
            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($userData['firstname'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="lastname">Last Name</label>
            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($userData['lastname'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="middlename">Middle Name</label>
            <input type="text" class="form-control" id="middlename" name="middlename" value="<?php echo htmlspecialchars($userData['middlename'] ?? ''); ?>">
            </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="Male" <?php if (($userData['gender'] ?? '') == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if (($userData['gender'] ?? '') == 'Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if (($userData['gender'] ?? '') == 'Other') echo 'selected'; ?>>Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="phonenumber">Phone Number</label>
            <input type="text" class="form-control" id="phonenumber" name="phonenumber" value="<?php echo htmlspecialchars($userData['phonenumber'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="birthdate">Birthdate</label>
            <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($userData['birthdate'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>" readonly>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
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
