<?php
include 'config.php'; // Include your database configuration file

// Check if connection is successful
if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Prepare a select statement
    $sql = "SELECT license_permit FROM tblboardinghouses WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $param_id);
        
        // Set parameters
        $param_id = $id;
        
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Store result
            $stmt->store_result();
            
            // Check if ID exists
            if ($stmt->num_rows == 1) {                    
                // Bind result variables
                $stmt->bind_result($license_permit);
                if ($stmt->fetch()) {
                    // Ensure we have image data
                    if (!empty($license_permit)) {
                        // Output image as base64 encoded string
                        echo base64_encode($license_permit);
                    } else {
                        echo "Image data is empty.";
                    }
                }
            } else {
                echo "Image not found.";
            }
        } else {
            echo "ERROR: Could not execute query.";
        }
    }
    
    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
