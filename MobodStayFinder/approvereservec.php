<?php
// Check if the booking ID is set and not empty
if(isset($_POST['bookingId']) && !empty($_POST['bookingId'])) {
    // Include your database connection file
    include 'config.php';

    // Get the booking ID from the POST data
    $bookingId = $_POST['bookingId'];

    // Update the status in the database to 'approved'
    $sql = "UPDATE tblreservations SET status = 'approved' WHERE id = $bookingId";

    if ($conn->query($sql) === TRUE) {
        
        $conn->close();
        header("Location: owner_db.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
} 
?>