<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch room data for the given boarding house id
    $sql = "SELECT room_type, price, availability, picture1, picture2, picture3, picture4 FROM tblrooms WHERE boarding_house_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $rooms = array();
        $noAvailableRooms = true;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rooms[] = $row;
                if ($row['availability'] > 0) {
                    $noAvailableRooms = false;
                }
            }
        } else {
            $errorMessage = "No rooms found for this boarding house";
        }
        $stmt->close();
    } else {
        // Print error message if prepare statement fails
        $errorMessage = "Failed to prepare the SQL statement: " . $conn->error;
    }
} else {
    $errorMessage = "Invalid boarding house ID";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rooms</title>
    <link rel="icon" type="image/x-icon" href="assets/logo.png" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
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
        .carousel-item img {
            height: 300px; /* Set a fixed height for images */
            width: 100%;
            object-fit: cover; /* Maintain aspect ratio and cover the area */
        }
        .card {
            height: 200px; /* Make the card take full height */
            display: flex;
            flex-direction: column;
        }
        .card-body {
            flex: 1 1 auto; /* Ensure the body takes available space */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Space out the contents evenly */
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
                    <a class="dropdown-item" href="viewprofilecustomer.php">Account Settings</a>
                    <a class="dropdown-item" href="#" onclick="showLogoutModal()">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<!-- Logout Modal -->
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

<!-- No Rooms Available Modal -->
<div id="noRoomsModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">No Rooms Available</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeNoRoomsModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>There are no rooms available at this boarding house.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeNoRoomsModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- No Availability Modal -->
<div id="noAvailabilityModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">No Availability</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeNoAvailabilityModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>There is no availability for this room.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeNoAvailabilityModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <h2>Rooms</h2>
    <a href="javascript:history.back()" class="btn btn-primary mb-3">Back</a>
    <?php if (!empty($rooms)): ?>
        <div class="row">
            <?php foreach ($rooms as $room): ?>
                <div class="col-md-6 mb-4">
                    <div id="carousel-<?php echo htmlspecialchars($room['room_type']); ?>" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            <?php $hasPictures = false; ?>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <?php $picture = $room['picture'.$i]; ?>
                                <?php if (!empty($picture)): ?>
                                    <?php $hasPictures = true; ?>
                                    <div class="carousel-item <?php echo ($i === 1) ? 'active' : ''; ?>">
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($picture); ?>" class="d-block w-100" alt="Room Image <?php echo $i; ?>">
                                    </div>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <?php if ($hasPictures): ?>
                            <a class="carousel-control-prev" href="#carousel-<?php echo htmlspecialchars($room['room_type']); ?>" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carousel-<?php echo htmlspecialchars($room['room_type']); ?>" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Room <?php echo htmlspecialchars($room['room_type']); ?></h5>
                            <p class="card-text">Price: ₱<?php echo number_format($room['price'], 2); ?></p>
                            <p class="card-text">Availability: <?php echo htmlspecialchars($room['availability']); ?></p>
                            <?php if ($room['availability'] > 0): ?>
                                <a href="reservec.php?room_type=<?php echo urlencode($room['room_type']); ?>&price=<?php echo urlencode($room['price']); ?>&availability=<?php echo urlencode($room['availability']); ?>&boarding_house_id=<?php echo urlencode($id); ?>" class="btn btn-primary">Make Reservation</a>
                            <?php else: ?>
                                <button class="btn btn-secondary" onclick="showNoAvailabilityModal()">Make Reservation</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p><?php echo $errorMessage; ?></p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function showLogoutModal() {
        $('#logoutModal').modal('show');
    }

    function closeLogoutModal() {
        $('#logoutModal').modal('hide');
    }

    function showNoRoomsModal() {
        $('#noRoomsModal').modal('show');
    }

    function closeNoRoomsModal() {
        $('#noRoomsModal').modal('hide');
    }

    function showNoAvailabilityModal() {
        $('#noAvailabilityModal').modal('show');
    }

    function closeNoAvailabilityModal() {
        $('#noAvailabilityModal').modal('hide');
    }

    // Check availability of rooms and show modal if none are available
    document.addEventListener('DOMContentLoaded', function() {
        var noAvailableRooms = <?php echo json_encode($noAvailableRooms); ?>;
        if (noAvailableRooms) {
            showNoRoomsModal();
        }
    });
</script>
</body>
</html>
