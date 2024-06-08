<?php
// Check if the booking ID is set and not empty
if(isset($_POST['bookingId']) && !empty($_POST['bookingId'])) {
    // Include your database connection file
    include 'config.php';

    // Get the booking ID from the POST data (sanitize it to prevent SQL injection)
    $bookingId = mysqli_real_escape_string($conn, $_POST['bookingId']);

    // Update the status in the database to 'declined'
    $sql = "UPDATE tblreservations SET status = 'declined' WHERE id = $bookingId";

    if ($conn->query($sql) === TRUE) {
        // Close the database connection
        $conn->close();
        header("Location: owner_db.php");
        exit();
    } else {
        // Handle the error (consider logging it for debugging)
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
} 
?>
