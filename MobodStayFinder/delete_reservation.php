<?php
// Include your database connection script
include 'config.php';

// Check if reservation ID is provided
if (!isset($_GET['id'])) {
    echo "Reservation ID not provided!";
    exit();
}

$reservationId = $_GET['id'];

// Perform deletion
$sql = "DELETE FROM tblreservations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reservationId);

if ($stmt->execute()) {
    // Deletion successful
    header("Location: customer_h.php"); // Redirect back to the customer dashboard
    exit();
} else {
    // Error occurred
    echo "Error: Unable to delete reservation.";
    exit();
}
?>
