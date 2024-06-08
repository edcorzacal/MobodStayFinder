<?php
session_start();
include 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'owner') {
    echo "Access Denied. Please log in as a owner.";
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['roomType'])) {
        // Code for adding rooms
        // Escape user inputs for security
        $roomType = $conn->real_escape_string($_POST['roomType']);
        $roomNumber = $conn->real_escape_string($_POST['roomNumber']);
        $availability = $conn->real_escape_string($_POST['availability']);
        $price = $conn->real_escape_string($_POST['price']);
        $boardingHouseId = $conn->real_escape_string($_POST['boardingHouseId']);

        // Retrieve username and business_name from the session
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

        // Prepare image data
        $roomPicture1 = isset($_FILES['roomPicture1']) && !empty($_FILES['roomPicture1']['tmp_name']) ? addslashes(file_get_contents($_FILES['roomPicture1']['tmp_name'])) : NULL;
        $roomPicture2 = isset($_FILES['roomPicture2']) && !empty($_FILES['roomPicture2']['tmp_name']) ? addslashes(file_get_contents($_FILES['roomPicture2']['tmp_name'])) : NULL;
        $roomPicture3 = isset($_FILES['roomPicture3']) && !empty($_FILES['roomPicture3']['tmp_name']) ? addslashes(file_get_contents($_FILES['roomPicture3']['tmp_name'])) : NULL;
        $roomPicture4 = isset($_FILES['roomPicture4']) && !empty($_FILES['roomPicture4']['tmp_name']) ? addslashes(file_get_contents($_FILES['roomPicture4']['tmp_name'])) : NULL;

        // Insert data into tblrooms
        $sql = "INSERT INTO tblrooms (room_type, room_number, availability, price, picture1, picture2, picture3, picture4, boarding_house_id, username) 
        VALUES ('$roomType', '$roomNumber', '$availability', '$price', '$roomPicture1', '$roomPicture2', '$roomPicture3', '$roomPicture4', '$boardingHouseId', '$username')";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Room added successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // Code for uploading a boarding house
        // Escape user inputs for security
        $houseCategory = $conn->real_escape_string($_POST['houseCategory']);
        $businessName = $conn->real_escape_string($_POST['businessName']);
        $businessLocation = $conn->real_escape_string($_POST['businessLocation']);
        $phoneNumber = $conn->real_escape_string($_POST['phoneNumber']);
        $numberOfRooms = $conn->real_escape_string($_POST['numberOfRooms']);
        $description = $conn->real_escape_string($_POST['description']); // New line for description
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : ''; // Fetching the username from the session

        // Prepare image data
        $image1 = isset($_FILES['picture1']) && !empty($_FILES['picture1']['tmp_name']) ? addslashes(file_get_contents($_FILES['picture1']['tmp_name'])) : NULL;
        $image2 = isset($_FILES['picture2']) && !empty($_FILES['picture2']['tmp_name']) ? addslashes(file_get_contents($_FILES['picture2']['tmp_name'])) : NULL;
        $image3 = isset($_FILES['picture3']) && !empty($_FILES['picture3']['tmp_name']) ? addslashes(file_get_contents($_FILES['picture3']['tmp_name'])) : NULL;
        $image4 = isset($_FILES['picture4']) && !empty($_FILES['picture4']['tmp_name']) ? addslashes(file_get_contents($_FILES['picture4']['tmp_name'])) : NULL;
        $licensePermit = isset($_FILES['licensePermit']) && !empty($_FILES['licensePermit']['tmp_name']) ? addslashes(file_get_contents($_FILES['licensePermit']['tmp_name'])) : NULL;

        // Insert data into tblboardinghouses
        // Insert data into tblboardinghouses with status as pending
        $sql = "INSERT INTO tblboardinghouses (house_category, business_name, business_location, phone_number, number_of_rooms, description, picture1, picture2, picture3, picture4, license_permit, username, status) 
        VALUES ('$houseCategory', '$businessName', '$businessLocation', '$phoneNumber', '$numberOfRooms', '$description', '$image1', '$image2', '$image3', '$image4', '$licensePermit', '$username', 'pending')";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Boarding house uploaded successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Fetch uploaded data from the database associated with the current user
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

$sql = "SELECT house_category, business_name, business_location, phone_number, number_of_rooms, description, picture1, picture2, picture3, picture4, status FROM tblboardinghouses WHERE username = '$username' ORDER BY id DESC LIMIT 4";

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


$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$sqlApproved = "SELECT id, business_name FROM tblboardinghouses WHERE status = 'approved' AND username = '$username'";
$resultApproved = $conn->query($sqlApproved);

$approvedHouses = array();
if ($resultApproved->num_rows > 0) {
    while ($row = $resultApproved->fetch_assoc()) {
        $approvedHouses[] = $row;
    }
}


$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$sqlRooms = "SELECT room_type, room_number, availability, price,  picture1, picture2, picture3, picture4
             FROM tblrooms WHERE username = '$username'
             ORDER BY id DESC
             LIMIT 4";

$resultRooms = $conn->query($sqlRooms);

// Initialize variables to store fetched room data
$uploadedRooms = array();

if ($resultRooms->num_rows > 0) {
    // Store fetched room data in an array
    while ($row = $resultRooms->fetch_assoc()) {
        $uploadedRooms[] = $row;
    }
} else {
    // Handle case when no room data found
    $errorMessageRooms = "No room data found";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MobodStayFinderOwner</title>
    <link rel="icon" type="image/x-icon" href="assets/logo.png" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
         body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #f8f9fa, #e0f7fa);
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
        .content {
            padding: 20px;
            display: none;
        }
        .content.active {
            display: block;
        }
        #home {
            text-align: center;
            padding: 50px 20px;
            background: linear-gradient(to right, #0062E6, #33AEFF);
            color: white;
        }
        #home h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        #home p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .feature {
            background: linear-gradient(to right, #ffffff, #e0f7fa);
            border-radius: 15px;
            padding: 20px;
            width: 250px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s, background-color 0.3s;
        }
        .feature:hover {
            transform: translateY(-10px);
            background: linear-gradient(to right, #33AEFF, #0062E6);
            color: white;
        }
        .feature h3 {
            color: #0062E6;
            margin-bottom: 15px;
        }
        .feature p {
            color: #333;
        }
        .btn-primary {
            background-color: #0062E6;
            border-color: #0062E6;
        }
        .btn-primary:hover {
            background-color: #004bbd;
            border-color: #004bbd;
        }
        .carousel-item img {
            max-height: 300px;
            object-fit: cover;
            width: 100%;
        }
        .description {
            margin-top: 20px;
        }
        .card {
            border: none;
            margin: 20px 0;
        }
        .card-header {
            background-color: #0062E6;
            color: white;
            padding: 15px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .card-body {
            padding: 20px;
            background-color: #f8f9fa;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        .badge {
            font-size: 1rem;
        }
        .modal-header {
            background-color: #0062E6;
            color: white;
        }
        .modal-footer .btn-secondary {
            background-color: #6c757d;
        }
        .modal-footer .btn-secondary:hover {
            background-color: #5a6268;
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
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showContent('upload')">Upload</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showContent('rooms')">Rooms</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showContent('booking')">Booking</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="showContent('customer-list')">Customer List</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Profile Picture
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="viewprofileowner.php">Account Settings</a>
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

    <!-- Content Sections -->
    <div class="container mt-4">
    <div id="home" class="content active">
    <?php
            $username = $_SESSION['username'];
        ?>
        <h1>Welcome to MobodStayFinder, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>Find the best boarding houses and manage your bookings with ease.</p>
        <div class="features">
            <div class="feature">
                <h3>Upload Boarding House</h3>
                <p>Share your available properties with ease and manage your listings.</p>
            </div>
            <div class="feature">
                <h3>View Rooms</h3>
                <p>Browse through the rooms available in your boarding houses.</p>
            </div>
            <div class="feature">
                <h3>Manage Bookings</h3>
                <p>Keep track of customer bookings and stay organized.</p>
            </div>
            <div class="feature">
                <h3>Customer List</h3>
                <p>Maintain a list of your customers and their booking details.</p>
            </div>
        </div>
    </div>
        <div id="upload" class="content">
         <h1>Upload Boarding House</h1> 
            <button class="btn btn-primary" onclick="showModal()">Click Here</button>
              <hr>

                <div id="uploadedData" class="row">
                <?php foreach ($uploadedData as $index => $data): ?>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                            <h5 class="card-title">
                                            <?php if ($data['status'] == 'pending'): ?>
                                                <p>Status : <span class="badge badge-pill badge-warning text-white">Pending</span></p>
                                            <?php elseif ($data['status'] == 'approved'): ?>
                                                <p>Status : <span class="badge badge-pill badge-success">Approved</span></p>
                                            <?php elseif ($data['status'] == 'declined'): ?>
                                                <p>Status : <span class="badge badge-pill badge-danger">Declined</span></p>
                                            <?php endif; ?>
                                            <br>
                                            <?php echo $data['business_name']; ?>
                                        </h5>              
                                </div>
                                <div id="carousel<?php echo $index; ?>" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach (range(1, 4) as $pictureNumber): ?>
                                            <?php if (!empty($data['picture' . $pictureNumber])): ?>
                                                <div class="carousel-item <?php echo $pictureNumber === 1 ? 'active' : ''; ?>">
                                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($data['picture' . $pictureNumber]); ?>" class="d-block w-100" alt="Picture <?php echo $pictureNumber; ?>">
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <a class="carousel-control-prev" href="#carousel<?php echo $index; ?>" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carousel<?php echo $index; ?>" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </div>
                                <!-- Description -->
                                <div class="description">
                                    <p><strong>House Category:</strong> <?php echo isset($data['house_category']) ? $data['house_category'] : 'N/A'; ?></p>
                                    <p><strong>Business Location:</strong> <?php echo $data['business_location']; ?></p>
                                    <p><strong>Phone Number:</strong> <?php echo $data['phone_number']; ?></p>
                                    <p><strong>Description:</strong> <?php echo $data['description']; ?></p>
                                    
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
        </div>
        <div id="myModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Boarding House</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="houseCategory">House Category</label>
                            <input type="text" class="form-control" id="houseCategory" value="Boarding House" name="houseCategory" readonly>
                        </div>
                        <div class="form-group">
                            <label for="businessName">Business Name</label>
                            <input type="text" class="form-control" id="businessName" name="businessName" placeholder="Enter business name" required>
                        </div>
                        <div class="form-group">
                            <label for="businessLocation">Business Location</label>
                            <input type="text" class="form-control" id="businessLocation" name="businessLocation" placeholder="Enter business location" required>
                        </div>
                        <div class="form-group">
                            <label for="phoneNumber">Phone Number</label>
                            <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="Enter phone number" required>
                        </div>
                        <div class="form-group">
                            <label for="numberOfRooms">Number of Rooms</label>
                            <input type="number" class="form-control" id="numberOfRooms" name="numberOfRooms" placeholder="Enter number of rooms" required>
                        </div>
                        <div class="form-group">
                            <label for="picture1">Picture of Boarding House 1</label>
                            <input type="file" class="form-control-file" id="picture1" name="picture1">
                        </div>
                        <div class="form-group">
                            <label for="picture2">Picture of Boarding House 2</label>
                            <input type="file" class="form-control-file" id="picture2" name="picture2">
                        </div>
                        <div class="form-group">
                            <label for="picture3">Picture of Boarding House 3</label>
                            <input type="file" class="form-control-file" id="picture3" name="picture3">
                        </div>
                        <div class="form-group">
                            <label for="picture4">Picture of Boarding House 4</label>
                            <input type="file" class="form-control-file" id="picture4" name="picture4">
                        </div>
                        <div class="form-group">
                            <label for="licensePermit">Picture of Business License Permit</label>
                            <input type="file" class="form-control-file" id="licensePermit" name="licensePermit" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
         <div id="rooms" class="content">
             <h1>Rooms</h1>
               <button class="btn btn-primary" onclick="handleAddRoom()">Add Room</button>
                  <hr>
                  <div id="uploadedRooms" class="row">
                    <?php foreach ($uploadedRooms as $index => $room): ?>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <h5 class="card-header">Room Number: <?php echo $room['room_number']; ?></h5>
                                <div class="card-body">
                                    <div id="roomCarousel<?php echo $index; ?>" class="carousel slide" data-ride="carousel">
                                        <div class="carousel-inner">
                                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                                <?php if (!empty($room['picture'.$i])): ?>
                                                    <div class="carousel-item <?php echo ($i === 1) ? 'active' : ''; ?>">
                                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($room['picture'.$i]); ?>" class="d-block w-100" alt="Room Image <?php echo $i; ?>" style="max-height: 200px;">
                                                    </div>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                  <a class="carousel-control-prev" href="#roomCarousel<?php echo $index; ?>" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#roomCarousel<?php echo $index; ?>" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                        <div class="description">
                            <p><strong>Room Type:</strong> <?php echo $room['room_type']; ?></p>
                            <p><strong>Availability:</strong> <?php echo $room['availability']; ?>/<?php echo $room['availability']; ?></p>
                            <p><strong>Price:</strong> ₱<?php echo number_format($room['price'], 2); ?></p>

                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
                  
            <div id="approvedHousesModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Boarding House</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeApprovedHousesModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="selectBoardingHouseForm">
                        <div class="form-group">
                            <label for="selectBoardingHouse">Which boarding house would you like to add a room to?</label>
                            <select class="form-control" id="selectBoardingHouse" name="boardingHouseId">
                                <?php foreach ($approvedHouses as $house): ?>
                                    <option value="<?php echo $house['id']; ?>"><?php echo $house['business_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="showRoomModal()">Next</button>
                        <button type="button" class="btn btn-secondary" onclick="closeApprovedHousesModal()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="roomModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Room</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeRoomModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addRoomForm" method="POST" enctype="multipart/form-data">
                    <!-- Add input fields for room data -->
                    <input type="hidden" id="boardingHouseId" name="boardingHouseId" value="">
                    <div class="form-group">
                        <label for="roomType">Room Type</label>
                        <select class="form-control" id="roomType" name="roomType">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="roomNumber">Room Number</label>
                        <input type="number" class="form-control" id="roomNumber" name="roomNumber" required>
                    </div>
                    <div class="form-group">
                        <label for="availability">Availability</label>
                        <input type="number" class="form-control" id="availability" name="availability" placeholder="Enter availability" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="roomPicture1">Picture of Room 1</label>
                        <input type="file" class="form-control-file" id="roomPicture1" name="roomPicture1">
                    </div>
                    <div class="form-group">
                        <label for="roomPicture2">Picture of Room 2</label>
                        <input type="file" class="form-control-file" id="roomPicture2" name="roomPicture2">
                    </div>
                    <div class="form-group">
                        <label for="roomPicture3">Picture of Room 3</label>
                        <input type="file" class="form-control-file" id="roomPicture3" name="roomPicture3">
                    </div>
                    <div class="form-group">
                        <label for="roomPicture4">Picture of Room 4</label>
                        <input type="file" class="form-control-file" id="roomPicture4" name="roomPicture4">
                    </div>
                    <!-- Add a submit button -->
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" onclick="closeRoomModal()">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div id="booking" class="content">
    <h1>Booking</h1> 
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Room Number</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
// Fetch the username of the logged-in user
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Query to fetch data from tblreservation associated with the logged-in user
$sqlReservation = "SELECT r.id, r.firstname, r.lastname, r.room_number, r.price, r.date, r.time 
                    FROM tblreservations AS r
                    JOIN tblrooms AS rm ON r.room_number = rm.room_number
                    WHERE r.status = 'pending' AND rm.username = '$username'";

$resultReservation = $conn->query($sqlReservation);


if ($resultReservation === false) {
    // Handle query error
    echo "Error executing query: " . $conn->error;
} else {
    // Check if any rows are returned
    if ($resultReservation->num_rows > 0) {
        // Output data of each row
        while ($row = $resultReservation->fetch_assoc()) {
            // Format date
            $formattedDate = date('F j, Y', strtotime($row['date']));
            // Format time
            $formattedTime = date('h:ia', strtotime($row['time']));
            echo "<tr>";
            echo "<td>" . $row['firstname'] . "</td>";
            echo "<td>" . $row['lastname'] . "</td>";
            echo "<td>" . $row['room_number'] . "</td>";
            echo "<td>" . $row['price'] . "</td>";
            echo "<td>" . $formattedDate . "</td>";
            echo "<td>" . $formattedTime . "</td>";
            echo "<td>
                <button class='btn btn-primary' onclick='approveBooking(" . $row['id'] . ")'>Approve</button>
                <button class='btn btn-danger' onclick='declineBooking(" . $row['id'] . ")'>Decline</button>
            </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No reservations found</td></tr>";
    }
}
?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal" id="confirmationModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmation</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Are you sure you want to approve this customer?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmApprove">Yes</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="declineConfirmationModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Decline Confirmation</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Are you sure you want to decline this booking?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-danger" id="confirmDecline">Yes</button>
            </div>
        </div>
    </div>
</div>


                <div id="customer-list" class="content">
                    <h1>Customer List</h1>
                    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Room Number</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
// Query to fetch data from tblreservation
$sqlReservation = "SELECT r.id, r.firstname, r.lastname, r.room_number, r.price, r.date, r.time, r.status 
                    FROM tblreservations AS r
                    JOIN tblrooms AS rm ON r.room_number = rm.room_number
                    WHERE r.status = 'approved' AND rm.username = '$username'";

// Execute the query
$resultReservation = $conn->query($sqlReservation);

// Check if the query executed successfully
if ($resultReservation === false) {
    // Handle query error
    echo "Error executing query: " . $conn->error;
} else {
    // Proceed with fetching data
    if ($resultReservation->num_rows > 0) {
        // Output data of each row
        while ($row = $resultReservation->fetch_assoc()) {
            // Format date
            $formattedDate = date('F j, Y', strtotime($row['date']));
            // Format time
            $formattedTime = date('h:ia', strtotime($row['time']));
            echo "<tr>";
            echo "<td>" . $row['firstname'] . "</td>";
            echo "<td>" . $row['lastname'] . "</td>";
            echo "<td>" . $row['room_number'] . "</td>";
            echo "<td>" . $row['price'] . "</td>";
            echo "<td>" . $formattedDate . "</td>";
            echo "<td>" . $formattedTime . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No reservations found</td></tr>";
    }
}
?>
            </tbody>
        </table>
    </div> 
                </div>


                <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Custom JS -->
    <script>


        function showContent(contentId) {
            var contents = document.getElementsByClassName('content');
            for (var i = 0; i < contents.length; i++) {
                contents[i].classList.remove('active');
            }
            document.getElementById(contentId).classList.add('active');
        }

        function showModal() {
            $('#myModal').modal('show');
        }

        function closeModal() {
            $('#myModal').modal('hide');
        }

        function handleAddRoom() {
            $('#approvedHousesModal').modal('show');
        }

        function closeApprovedHousesModal() {
            $('#approvedHousesModal').modal('hide');
        }

        function showRoomModal() {
            var selectedBoardingHouseId = document.getElementById('selectBoardingHouse').value;
            document.getElementById('boardingHouseId').value = selectedBoardingHouseId;
            $('#approvedHousesModal').modal('hide');
            $('#roomModal').modal('show');
        }

        function closeRoomModal() {
            $('#roomModal').modal('hide');
        }
        function showLogoutModal() {
            $('#logoutModal').modal('show');
        }

        function closeLogoutModal() {
            $('#logoutModal').modal('hide');
        }
        
        function handleAction(action, bookingId) {
        // Set the confirmation message based on the action
        var confirmationMessage = action === 'approve' ? 'Are you sure you want to approve this customer?' : 'Are you sure you want to decline this customer?';
        // Set the onclick event for Yes button to call the corresponding function
        var confirmAction = action === 'approve' ? 'confirmApproveBooking' : 'confirmDeclineBooking';
        document.getElementById("confirmationMessage").textContent = confirmationMessage;
        document.getElementById("confirmAction").setAttribute("onclick", confirmAction + "(" + bookingId + ")");
        // Show the confirmation modal
        $('#confirmationModal').modal('show');
    }

    // Function to confirm approval
    function confirmApproveBooking(bookingId) {
        // Create a form dynamically
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'approvereservec.php'; // Replace with your PHP file handling approval
        // Create a hidden input field for booking ID
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'bookingId';
        input.value = bookingId;
        // Append the input field to the form
        form.appendChild(input);
        // Append the form to the document body
        document.body.appendChild(form);
        // Submit the form
        form.submit();
        // Close the confirmation modal
        $('#confirmationModal').modal('hide');
    }
    function approveBooking(bookingId) {
    // Set the onclick event for Yes button to call the approve function
    document.getElementById("confirmApprove").setAttribute("onclick", "confirmApproveBooking(" + bookingId + ")");
    // Show the confirmation modal
    $('#confirmationModal').modal('show');
}

    

    // Function to confirm decline
    function confirmDeclineBooking(bookingId) {
        // Create a form dynamically
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'declinereservec.php'; // Replace with your PHP file handling decline
        // Create a hidden input field for booking ID
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'bookingId';
        input.value = bookingId;
        // Append the input field to the form
        form.appendChild(input);
        // Append the form to the document body
        document.body.appendChild(form);
        // Submit the form
        form.submit();
        // Close the confirmation modal
        $('#confirmationModal').modal('hide');
    }
    function declineBooking(bookingId) {
    // Set the onclick event for Yes button to call the decline function
    document.getElementById("confirmDecline").setAttribute("onclick", "confirmDeclineBooking(" + bookingId + ")");
    // Show the confirmation modal
    $('#declineConfirmationModal').modal('show');
}

function declineBooking(bookingId) {
    // Set the onclick event for Yes button to call the decline function
    document.getElementById("confirmDecline").setAttribute("onclick", "confirmDeclineBooking(" + bookingId + ")");
    // Show the confirmation modal
    $('#declineConfirmationModal').modal('show');
}


        
    </script>
</body>
</html>
