<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'admin') {
    echo "Access Denied. Please log in as a admin.";
    exit;
}

include 'config.php';

// Check if the request is a POST request and if the 'action' parameter is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'approve') {
        $id = $_POST['id'];
        $sql = "UPDATE tblboardinghouses SET status = 'approved' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();
        exit;
    } elseif ($_POST['action'] == 'decline') {
        $id = $_POST['id'];
        $sql = "UPDATE tblboardinghouses SET status = 'declined' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Error: " . $conn->error;
        }
        $stmt->close();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MobodStayFinderAdmin</title>
    <!-- Font Awesome -->
    <link rel="icon" type="image/x-icon" href="assets/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <!-- Custom CSS -->
    
    <style>
        .bg-custom {
            background-color: #17a2b8 !important;
        }
        .small-box:hover {
            color: #fff;
        }
        .nav-sidebar .nav-link {
            color: #ffffff !important; /* Change this to your desired color */
        }
        .nav-sidebar .nav-link:hover {
            background-color: #1a73e8 !important; /* Change this to your desired hover background color */
            color: #ffffff !important; /* Change this to your desired hover text color */
        }
        .nav-sidebar .nav-link.active {
            background-color: #0056b3 !important; /* Change this to your desired active background color */
            color: #ffffff !important; /* Change this to your desired active text color */
        }
        .modal-header {
    background-color: #007bff;
    color: white;
}

.modal-title {
    font-weight: bold;
}

.btn-close {
    background-color: #007bff;
    border: none;
    font-size: 3rem;
    padding: 0rem 0.75rem;
    cursor: pointer;
}

.modal-body {
    background-color: #f8f9fa;
    padding: 2rem;
}

.modal-body img {
    max-height: 80vh; /* Ensure the image fits within the viewport */
    border: 2px solid #007bff;
}

.modal-footer {
    background-color: #f8f9fa;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <!-- Dropdown User Menu -->
               <!-- Dropdown User Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#account-settings" id="account-settings-link"><i class="fas fa-user-cog mr-2"></i> Account Settings</a></li>
                        <li><a class="dropdown-item" href="#" class="dropdown-item"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a></li>
                    </ul>
                </li>

            </ul>
        </nav>

        <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Logout Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="index.php" class="btn btn-primary">Logout</a>
            </div>
        </div>
    </div>
</div>
        
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Approve Boarding House</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to approve this boarding house?
                <!-- Hidden field to store the row ID -->
                <input type="hidden" name="rowId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmApprove">Approve</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal for declining boarding house -->
<div id="declineModal" class="modal fade" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="declineModalLabel">Decline Boarding House</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to decline this boarding house?</p>
                    <input type="hidden" name="rowId" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDecline" class="btn btn-danger">Decline</button>
                </div>
            </div>
        </div>
        </div>


        <!-- Main Sidebar Container -->
        <aside class="main-sidebar bg-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index.html" class="brand-link">
                <img src="assets/logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">MobodStayFinder</span>
            </a>
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                       
                    </div>
                    <div class="info">
                      
                    </div>
                </div>
                <!-- Sidebar Menu -->
                <nav class="mt-4">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="#dashboard" class="nav-link active" id="dashboard-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a href="#house" class="nav-link" id="house-link">
                                <i class="nav-icon fas fa-home"></i>
                                <p>
                                    House
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#message" class="nav-link" id="message-link">
                                <i class="nav-icon fas fa-envelope"></i>
                                <p>Messages</p>
                            </a>
                        </li>
                        <li class="nav-item has-treeview mt-3">
                            <a href="#" class="nav-link" id="userlist-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>
                                    Userlist
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#owner" class="nav-link" id="owner-link">
                                        <i class="fas fa-user-tie nav-icon"></i>
                                        <p>Owner</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#customer" class="nav-link" id="customer-link">
                                    <i class="fas fa-user nav-icon"></i>
                                        <p>Customer</p>
                                    </a>
                                </li>
                                
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 id="content-title">Dashboard</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Dashboard Content -->
                    <div id="dashboard-content">
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-info">

                                            <div class="inner">
                                                <h3>
                                                    <?php
                                                        // Query to get the count of owners
                                                        $sql = "SELECT COUNT(*) as owner_count FROM tblownersuser";
                                                        $result = $conn->query($sql);

                                                        if ($result && $result->num_rows > 0) {
                                                            $row = $result->fetch_assoc();
                                                            echo $row["owner_count"];
                                                        } else {
                                                            echo "0"; // If no owners found
                                                        }
                                                    ?>
                                                </h3>
                                                <p>Number of Owners</p> <!-- Changed text here -->
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-user-tie"></i> <!-- Changed icon class here -->
                                            </div>

                                        </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-success">
                                            <div class="inner">
                                                <h3>
                                                    <?php
                                                        // Query to get the count of customers
                                                        $sql = "SELECT COUNT(*) as customer_count FROM tblcustomersuser";
                                                        $result = $conn->query($sql);

                                                        if ($result && $result->num_rows > 0) {
                                                            $row = $result->fetch_assoc();
                                                            echo $row["customer_count"];
                                                        } else {
                                                            echo "0"; // If no customers found
                                                        }
                                                    ?>
                                                </h3>
                                                <p>Number of Customers</p> <!-- Changed text here -->
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-user"></i> <!-- Changed icon class here -->
                                            </div>
                                        </div>

                            </div>

                            <!-- ./col -->
                            <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>
                                            <?php
                                                // Query to get the count of pending records
                                                $sql = "SELECT COUNT(*) as pending_count FROM tblboardinghouses WHERE status = 'pending'";
                                                $result = $conn->query($sql);

                                                if ($result && $result->num_rows > 0) {
                                                    $row = $result->fetch_assoc();
                                                    echo $row["pending_count"];
                                                } else {
                                                    echo "0"; // If no pending records found
                                                }
                                            ?>
                                        </h3>
                                        <p>Total Pending</p> <!-- Changed text here -->
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                </div>

                            </div>
                            <!-- ./col -->
                            <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>
                                <?php
                                    // Query to get the count of approved records
                                    $sql = "SELECT COUNT(*) as approved_count FROM tblboardinghouses WHERE status = 'approved'";
                                    $result = $conn->query($sql);

                                    if ($result && $result->num_rows > 0) {
                                        $row = $result->fetch_assoc();
                                        echo $row["approved_count"];
                                    } else {
                                        echo "0"; // If no approved records found
                                    }
                                ?>
                            </h3>
                            <p>Total Approved</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>

                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary"> <!-- Change bg color as needed -->
                        <div class="inner">
                            <h3>
                                <?php
                                    // Query to get the count of pending records
                                    $sql = "SELECT COUNT(*) as declined_count FROM tblboardinghouses WHERE status = 'declined'";
                                    $result = $conn->query($sql);

                                    if ($result && $result->num_rows > 0) {
                                        $row = $result->fetch_assoc();
                                        echo $row["declined_count"];
                                    } else {
                                        echo "0"; // If no pending records found
                                    }
                                ?>
                            </h3>
                            <p>Total Declined</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>
                                <?php
                                    // Query to get the count of boardinghouses
                                    $sql = "SELECT COUNT(*) as boardinghouses_count FROM tblboardinghouses";
                                    $result = $conn->query($sql);

                                    if ($result && $result->num_rows > 0) {
                                        $row = $result->fetch_assoc();
                                        echo $row["boardinghouses_count"];
                                    } else {
                                        echo "0"; // If no boardinghouses found
                                    }
                                ?>
                            </h3>
                            <p>Total Boardinghouses</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-home"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
           
                            <!-- ./col -->
                        </div>
                        <!-- /.row -->
                        <div class="row">
                          
                        </div>
                    </div>
                    <!-- House Content -->
                    <!-- House Content -->
                    <div id="house-content" style="display: none;">
                        <ul class="nav nav-tabs" id="houseTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pending-tab" data-bs-toggle="tab" href="#pending" role="tab" aria-controls="pending" aria-selected="true">Pending</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="approved-tab" data-bs-toggle="tab" href="#approved" role="tab" aria-controls="approved" aria-selected="false">Approved</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="declined-tab" data-bs-toggle="tab" href="#declined" role="tab" aria-controls="declined" aria-selected="false">Declined</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="houseTabsContent">
                            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                                <!-- Content for pending houses -->
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead style="background-color: #007bff; color: white;">
                                            <tr>
                                                <th>Business Name</th>
                                                <th>Owner</th>
                                                <th>License/Permit</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    <?php
                                    // SQL query to retrieve pending houses
                                    $sql = "SELECT id, business_name, username, license_permit, status FROM tblboardinghouses WHERE status = 'pending'";
                                    $result = $conn->query($sql);

                                 if ($result->num_rows > 0) {
                            // Output data of each row
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>".$row["business_name"]."</td>";
                                        echo "<td>".$row["username"]."</td>";
                                        echo "<td> <button class='btn btn-warning view-btn' data-row-id='".$row["id"]."'>
                                                    <i class='fas fa-eye'></i>
                                        </button></td>";
                                echo "<td>".$row["status"]."</td>";
                                echo "<td>
                                        <button class='btn btn-success approve-btn' data-row-id='".$row["id"]."'>
                                            <i class='fas fa-check-circle'></i>
                                        </button>
                                        <button class='btn btn-danger decline-btn' data-row-id='".$row["id"]."'>
                                            <i class='fas fa-times-circle'></i>
                                        </button>
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>0 results</td></tr>";
                        }
                        ?>
                </tbody>
            </table>
        </div>
    </div>

                            <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                                <!-- Content for approved houses -->
                                <div class="table-responsive">
        <table class="table">
            <thead style="background-color: #007bff; color: white;">
                <tr>
                    <th>Business Name</th>
                    <th>Username</th>
                    <th>License/Permit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // SQL query to retrieve approved houses
                $sql_approved = "SELECT id, business_name, username, license_permit, status FROM tblboardinghouses WHERE status = 'approved'";
                $result_approved = $conn->query($sql_approved);

                if ($result_approved->num_rows > 0) {
                    // Output data of each row
                    while($row_approved = $result_approved->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row_approved["business_name"]."</td>";
                        echo "<td>".$row_approved["username"]."</td>";
                        echo "<td> <button class='btn btn-warning view-btn' data-row-id='".$row_approved["id"]."'>
                                    <i class='fas fa-eye'></i>
                                </button></td>";
                        echo "<td>".$row_approved["status"]."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>0 results</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
                                
                            </div>
                            <div class="tab-pane fade" id="declined" role="tabpanel" aria-labelledby="declined-tab">
                                <!-- Content for declined houses -->

                                <div class="table-responsive">
        <table class="table">
            <thead style="background-color: #007bff; color: white;">
                <tr>
                    <th>Business Name</th>
                    <th>Username</th>
                    <th>License/Permit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // SQL query to retrieve declined houses
                $sql_declined = "SELECT id, business_name, username, license_permit, status FROM tblboardinghouses WHERE status = 'declined'";
                $result_declined = $conn->query($sql_declined);

                if ($result_declined->num_rows > 0) {
                    // Output data of each row
                    while($row_declined = $result_declined->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row_declined["business_name"]."</td>";
                        echo "<td>".$row_declined["username"]."</td>";
                        echo "<td> <button class='btn btn-warning view-btn' data-row-id='".$row_declined["id"]."'>
                                    <i class='fas fa-eye'></i>
                                </button></td>";
                        echo "<td>".$row_declined["status"]."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>0 results</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
                            </div>
                        </div>
                    </div>
</div>


<div id="message-content" style="display: none;">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Messages</h3>
                </div>
                <div class="card-body table-responsive">
                    <!-- PHP code to retrieve and display messages -->
                    <?php
                    // Establish database connection (assuming $conn is already defined)
                    // Example: $conn = new mysqli($servername, $username, $password, $dbname);

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // SQL query to retrieve messages
                    $sql_messages = "SELECT * FROM tblmessage";
                    $result_messages = $conn->query($sql_messages);

                    // Check if there are messages
                    if ($result_messages->num_rows > 0) {
                        // Output data of each row
                        echo "<table class='table'>";
                        echo "<thead><tr><th>ID</th><th>Sender</th><th>Message</th><th>Date</th></tr></thead>";
                        echo "<tbody>";
                        while($row_message = $result_messages->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>".$row_message["id"]."</td>";
                            echo "<td>".$row_message["name"]."</td>";
                            echo "<td>".$row_message["message"]."</td>";
                            echo "<td>".$row_message["created_at"]."</td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        // If no messages found
                        echo "No messages available";
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


                    <!-- Owner Content -->
                    <div id="owner-content" style="display: none;">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Owner List</h3>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <?php
                                        $sql = "SELECT * FROM tblownersuser";
                                        $result = $conn->query($sql);

                                        // Output table header
                                        echo "<table class='table'>";
                                        echo "<tr>
                                                <th class='bg-primary'>ID</th>
                                                <th class='bg-primary'>Username</th>
                                                <th class='bg-primary'>Email</th>
                                              </tr>";

                                        if ($result) {
                                            if ($result->num_rows > 0) {
                                                // Output data of each row
                                                while($row = $result->fetch_assoc()) {
                                                    // Output row data
                                                    echo "<tr><td>".$row["id"]."</td><td>".$row["username"]."</td><td>".$row["email"]."</td></tr>";
                                                }
                                            } else {
                                                // If no results, display a row with "No data" message
                                                echo "<tr><td colspan='3'>No data available</td></tr>";
                                            }
                                        } else {
                                            // If there's an error in the query, display the error
                                            echo "<tr><td colspan='3'>Error: " . $conn->error . "</td></tr>";
                                        }

                                        // Close the table
                                        echo "</table>";
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="customer-content" style="display: none;">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Owner List</h3>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <?php
                                        $sql = "SELECT * FROM tblcustomersuser";
                                        $result = $conn->query($sql);

                                        // Output table header
                                        echo "<table class='table'>";
                                        echo "<tr>
                                                <th class='bg-primary'>ID</th>
                                                <th class='bg-primary'>Username</th>
                                                <th class='bg-primary'>Email</th>
                                              </tr>";

                                        if ($result) {
                                            if ($result->num_rows > 0) {
                                                // Output data of each row
                                                while($row = $result->fetch_assoc()) {
                                                    // Output row data
                                                    echo "<tr><td>".$row["id"]."</td><td>".$row["username"]."</td><td>".$row["email"]."</td></tr>";
                                                }
                                            } else {
                                                // If no results, display a row with "No data" message
                                                echo "<tr><td colspan='3'>No data available</td></tr>";
                                            }
                                        } else {
                                            // If there's an error in the query, display the error
                                            echo "<tr><td colspan='3'>Error: " . $conn->error . "</td></tr>";
                                        }

                                        // Close the table
                                        echo "</table>";
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="account-settings-content" style="display: none;">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Account Settings Content</h3>
                                    </div>
                                    <div class="card-body">
                                        <!-- Add account settings related content here -->
                                        <p>This is the account settings content.</p>
                                        <p>You can add your account settings form or any other relevant content here.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        <div class="modal fade" id="viewImageModal" tabindex="-1" aria-labelledby="viewImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewImageModalLabel">License/Permit Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-center">
                <img id="licenseImage" src="" class="img-fluid rounded shadow" alt="License/Permit Image">
            </div>
           
        </div>
    </div>
</div>

<!-- Add this below the existing content sections -->





        <aside class="control-sidebar control-sidebar-dark">

        </aside>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">
                
            </div>
            <strong>Copyright &copy; 2024 <a href="https://MobodStayFinder">MobodStayFinder</a>.</strong> All rights reserved.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

    <script>
    $(document).ready(function () {
    // Bootstrap 5 dropdown initialization
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });

    // Sidebar link click events
    $('#dashboard-link').on('click', function () {
        $('#content-title').text('Dashboard');
        $('#dashboard-content').show();
        $('#house-content, #owner-content, #customer-content, #account-settings-content, #message-content').hide();
    });
    $('#house-link').on('click', function () {
        $('#content-title').text('House');
        $('#house-content').show();
        $('#dashboard-content, #owner-content, #customer-content, #account-settings-content, #message-content').hide();
    });
    $('#owner-link').on('click', function () {
        $('#content-title').text('Owner');
        $('#owner-content').show();
        $('#dashboard-content, #house-content, #customer-content, #account-settings-content, #message-content').hide();
    });
    $('#customer-link').on('click', function () {
        $('#content-title').text('Customer');
        $('#customer-content').show();
        $('#dashboard-content, #house-content, #owner-content, #account-settings-content, #message-content').hide();
    });
    $('#account-settings-link').on('click', function () {
        $('#content-title').text('Account Settings');
        $('#account-settings-content').show();
        $('#dashboard-content, #house-content, #owner-content, #customer-content, #message-content').hide();
    });
    $('#message-link').on('click', function () {
        $('#content-title').text('Messages');
        $('#message-content').show();
        $('#dashboard-content, #house-content, #owner-content, #customer-content, #account-settings-content').hide();
    });




        // Initial active content
        $('#dashboard-link').trigger('click');





        $('#approved-tab').on('click', function () {
        $('#approved-tab').tab('show');
    });

    // Switch to declined tab
    $('#declined-tab').on('click', function () {
        $('#declined-tab').tab('show');
    });
    // Switch to declined tab
    

    });


    $('a.dropdown-item:contains("Logout")').click(function(e) {
        e.preventDefault(); // Prevent default link action
        $('#logoutModal').modal('show');
    });

    // Manually close logout modal when cancel button is clicked
    $('#logoutModal .btn-secondary').click(function() {
        $('#logoutModal').modal('hide');
    });

    // Manually close logout modal when "x" button is clicked
    $('#logoutModal .close').click(function() {
        $('#logoutModal').modal('hide');
    });


    $(document).ready(function() {
    // Function to handle view button click
    $('.view-btn').click(function() {
        var rowId = $(this).data('row-id');
        
        // Make an AJAX request to get the image data
        $.ajax({
            url: 'get_image.php', // Server-side script to get the image based on row ID
            type: 'GET',
            data: {id: rowId},
            success: function(response) {
                // Log the response to the console for debugging
                console.log("Response:", response);

                // Check if response is not empty
                if (response.trim() === "Image not found." || response.trim() === "Image data is empty.") {
                    alert(response); // Show alert for debugging
                } else {
                    // Set the image source and show the modal
                    $('#licenseImage').attr('src', 'data:image/jpeg;base64,' + response);
                    $('#viewImageModal').modal('show');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", xhr.responseText);
            }
        });
    });
    $('.approve-btn').click(function () {
        var rowId = $(this).data('row-id');
        $('#approveModal').modal('show');

        // Save the row ID in a hidden field in the modal
        $('#approveModal').find('.modal-body input[name="rowId"]').val(rowId);
    });

    // Function to handle confirmation in the approve modal
    $('#confirmApprove').click(function () {
        var rowId = $('#approveModal').find('.modal-body input[name="rowId"]').val();

        // Make an AJAX request to update the status of the boarding house
        $.ajax({
            url: window.location.href, // Use the current URL for the request
            type: 'POST',
            data: { action: 'approve', id: rowId },
            success: function (response) {
                // Reload the page or update the UI as needed
                location.reload();
            },
            error: function (xhr, status, error) {
                console.error("Error:", xhr.responseText);
            }
        });
    });
    $('.decline-btn').click(function () {
        var rowId = $(this).data('row-id');
        $('#declineModal').modal('show');

        // Save the row ID in a hidden field in the modal
        $('#declineModal').find('.modal-body input[name="rowId"]').val(rowId);
    });

    // Function to handle confirmation in the decline modal
    $('#confirmDecline').click(function () {
        var rowId = $('#declineModal').find('.modal-body input[name="rowId"]').val();

        // Make an AJAX request to update the status of the boarding house
        $.ajax({
            url: window.location.href, // Use the current URL for the request
            type: 'POST',
            data: { action: 'decline', id: rowId },
            success: function (response) {
                // Reload the page or update the UI as needed
                location.reload();
            },
            error: function (xhr, status, error) {
                console.error("Error:", xhr.responseText);
            }
        });
    });
});

    
</script>

</body>
</html>
