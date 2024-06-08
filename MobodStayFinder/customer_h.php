<?php

session_start();
include 'config.php';


if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'customer') {
    echo "Access Denied. Please log in as a customer.";
    exit;
}
// Check if the user is logged in

// Fetch uploaded data from the database
$sql = "SELECT id, house_category, business_name, business_location, phone_number, number_of_rooms, description, picture1, picture2, picture3, picture4, status FROM tblboardinghouses where status = 'approved' ORDER BY id DESC LIMIT 4";
$result = $conn->query($sql);

// Initialize variables to store fetched data
$uploadedData = array();
$errorMessage = "";

if ($result->num_rows > 0) {
    // Store fetched data in an array
    while ($row = $result->fetch_assoc()) {
        $uploadedData[] = $row;
    }
} else {
    $errorMessage = "No data found";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MobodStayFinderCustomer</title>
    <link rel="icon" type="image/x-icon" href="assets/logo.png" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
        .content {
            padding: 20px;
            display: none;
        }
        .content.active {
            display: block;
        }
        .upload-button {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }
        .upload-button .btn {
            padding: 20px;
            font-size: 18px;
        }
        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 10px;
        }
        .modal-content .form-group label {
            font-weight: bold;
        }
        .modal-content .btn {
            margin-top: 10px;
        }
        .carousel-item img {
    object-fit: cover;
    height: 400px; /* Set the desired height */
}

.carousel-caption {
    background: rgba(0, 0, 0, 0.5);
    width: 100%;
    padding: 10px;
}

.carousel-caption h5, .carousel-caption p {
    color: #fff;
    margin: 0;
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
                <a class="nav-link" href="#" onclick="showContent('home')">Home</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Profile
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="viewprofilecustomer.php" >Account Settings</a>
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

<div id="home" class="content active">
<div class="container">
        <div class="row">
            <?php foreach ($uploadedData as $data): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div id="carousel-<?php echo $data['business_name']; ?>" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <?php $picture = $data['picture' . $i]; ?>
                                    <?php if (!empty($picture)): ?>
                                        <div class="carousel-item <?php echo ($i == 1) ? 'active' : ''; ?>">
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($picture); ?>" class="d-block w-100" alt="House Image <?php echo $i; ?>">
                                        </div>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <a class="carousel-control-prev" href="#carousel-<?php echo $data['business_name']; ?>" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carousel-<?php echo $data['business_name']; ?>" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                        <div class="card-body">
                        <p><b>Boarding House :</b> <?php echo htmlspecialchars($data['business_name']); ?></p>
                            <p><b>Location :</b> <?php echo htmlspecialchars($data['business_location']); ?></p>

                            <p><b>Phone Number :</b> <?php echo htmlspecialchars($data['phone_number']); ?></p>
                            <p><b>Description :</b> <?php echo htmlspecialchars($data['description']); ?></p>
                            <a href="viewroomscustomer.php?id=<?php echo $data['id']; ?>" class="btn btn-primary">View Rooms</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function showContent(contentId) {
        // Hide all content
        const contents = document.querySelectorAll('.content');
        contents.forEach(content => content.classList.remove('active'));

        // Show the selected content
        const selectedContent = document.getElementById(contentId);
        selectedContent.classList.add('active');
        
    }

    function showModal() {
        $('#myModal').modal('show');
    }

    function closeModal() {
        $('#myModal').modal('hide');
    }

    // Close the modal if the user clicks outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('myModal');
        if (event.target == modal) {
            $('#myModal').modal('hide');
        }
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
