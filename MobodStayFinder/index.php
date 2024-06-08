<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['signupOwnerForm'])) {
        $firstName = $_POST['ownerFirstName'];
        $lastName = $_POST['ownerLastName'];
        $middleName = $_POST['ownerMiddleName'];
        $email = $_POST['ownerEmail'];
        $phoneNumber = $_POST['ownerPhoneNumber'];
        $username = $_POST['ownerUsername'];
        $password = password_hash($_POST['ownerPassword'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO tblownersuser (firstName, lastName, middleName, email, phoneNumber, username, password) VALUES ('$firstName', '$lastName', '$middleName', '$email', '$phoneNumber', '$username', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert('Sign up successful! Please log in.');
                    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    loginModal.show();
                  </script>";
        } else {
            echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>";
        }
    } elseif (isset($_POST['signupCustomerForm'])) {
        $firstName = $_POST['customerFirstName'];
        $lastName = $_POST['customerLastName'];
        $middleName = $_POST['customerMiddleName'];
        $email = $_POST['customerEmail'];
        $phoneNumber = $_POST['customerPhoneNumber'];
        $username = $_POST['customerUsername'];
        $password = password_hash($_POST['customerPassword'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO tblcustomersuser (firstName, lastName, middleName, email, phoneNumber, username, password) VALUES ('$firstName', '$lastName', '$middleName', '$email', '$phoneNumber', '$username', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert('Sign up successful! Please log in.');
                    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    loginModal.show();
                  </script>";
        } else {
            echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>";
        }
    } else {
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        if ($username && $password) {
            $sql_owner = "SELECT * FROM tblownersuser WHERE username='$username'";
            $result_owner = $conn->query($sql_owner);

            $sql_customer = "SELECT * FROM tblcustomersuser WHERE username='$username'";
            $result_customer = $conn->query($sql_customer);

            $sql_admin = "SELECT * FROM tbladminuser WHERE username='$username'";
            $result_admin = $conn->query($sql_admin);

            if ($result_owner && $result_owner->num_rows > 0) {
                $row = $result_owner->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $username;
                    $_SESSION['user_type'] = 'owner';
                    header("Location: owner_db.php");
                    exit;
                } else {
                    echo "<script>alert('Invalid password.');</script>";
                }
            } elseif ($result_customer && $result_customer->num_rows > 0) {
                $row = $result_customer->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $username;
                    $_SESSION['user_type'] = 'customer';
                    header("Location: customer_h.php");
                    exit;
                } else {
                    echo "<script>alert('Invalid password.');</script>";
                }
            } elseif ($result_admin && $result_admin->num_rows > 0) {
                $row = $result_admin->fetch_assoc();
                if ($password === $row['password']) {
                    $_SESSION['loggedin'] = true; // Direct comparison for plain text password
                    $_SESSION['username'] = $username;
                    $_SESSION['user_type'] = 'admin';
                    $_SESSION['admin_user_id'] = $row['id'];
                    
                    header("Location: admin_db.php");
                    exit;
                } else {
                    echo "<script>alert('Invalid password.');</script>";
                }
            } else {
                echo "<script>alert('No user found with this username.');</script>";
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the contact form is submitted
    if (isset($_POST['message'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        // Prepare and execute the SQL query to insert the data into tblmessage
        $sql = "INSERT INTO tblmessage (name, email, message) VALUES ('$name', '$email', '$message')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Message sent successfully!');</script>";
        } else {
            echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>";
        }
    }
}


$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Discover your ideal boarding accommodation in Mobod</title>
        <link rel="icon" type="image/x-icon" href="assets/logo.png" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic" rel="stylesheet" type="text/css" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
        
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body id="page-top">
     
<nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
    <div class="container px-4 px-lg-5">
        <a class="navbar-brand" href="#page-top"><img src="assets/logo.png" width="50px">MobodStayFinder</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto my-2 my-lg-0">
                <li class="nav-item "><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a></li>
            </ul>
        </div>
    </div>
</nav>


<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login or Sign Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Login Form -->
     <!-- Login Form -->
<form id="loginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" autocomplete="off" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Login</button>
    </div>
</form>

                <hr>

                <p class="text-center">Don't have an account?</p>
                <div class="text-center">
                    <a href="#" class="me-3" data-bs-toggle="modal" data-bs-target="#signupOwnerModal">Sign Up as Owner</a>
                    <a href="#" class="ms-3" data-bs-toggle="modal" data-bs-target="#signupCustomerModal">Sign Up as Customer</a>
                    
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Sign Up as Owner Modal -->
<div class="modal fade" id="signupOwnerModal" tabindex="-1" aria-labelledby="signupOwnerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                
                <h5 class="modal-title" id="signupOwnerModalLabel">Sign Up as Owner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Sign Up as Owner Form -->
                <form id="signupOwnerForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="signupOwnerForm" value="1" >
                    <div class="mb-3">
                        <label for="ownerFirstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="ownerFirstName" name="ownerFirstName" placeholder="Enter your first name" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="ownerLastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="ownerLastName" name="ownerLastName" placeholder="Enter your last name" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="ownerMiddleName" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="ownerMiddleName" name="ownerMiddleName" placeholder="Enter your middle name" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="ownerEmail" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="ownerEmail" name="ownerEmail" placeholder="Enter your email address" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="ownerPhoneNumber" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="ownerPhoneNumber" name="ownerPhoneNumber" placeholder="Enter your phone number" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="ownerUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="ownerUsername" name="ownerUsername" placeholder="Enter your username" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="ownerPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="ownerPassword" name="ownerPassword" placeholder="Enter your password" autocomplete="off" required>
                    </div>
                    <div class="text-center">
        <button type="submit" class="btn btn-primary">Sign up</button>
    </div>

                </form>
                <hr>
                <p class="text-center">Already have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a></p>
            </div>
        </div>
    </div>
</div>

<!-- Sign Up as Customer Modal -->
<div class="modal fade" id="signupCustomerModal" tabindex="-1" aria-labelledby="signupCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signupCustomerModalLabel">Sign Up as Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Sign Up as Customer Form -->
                <form id="signupCustomerForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="signupCustomerForm" value="1">
                    <div class="mb-3">
                        <label for="customerFirstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="customerFirstName" name="customerFirstName" placeholder="Enter your first name" autocomplet e="off"required>
                    </div>
                    <div class="mb-3">
                        <label for="customerLastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="customerLastName" name="customerLastName" placeholder="Enter your last name" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="customerMiddleName" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="customerMiddleName" name="customerMiddleName" placeholder="Enter your middle name" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label for="customerEmail" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="customerEmail" name="customerEmail" placeholder="Enter your email address" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="customerPhoneNumber" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="customerPhoneNumber" name="customerPhoneNumber" placeholder="Enter your phone number" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="customerUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="customerUsername" name="customerUsername" placeholder="Enter your username" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="customerPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="customerPassword" name="customerPassword" placeholder="Enter your password" autocomplete="off" required>
                    </div>
                    <div class="text-center">
                       <button type="submit" class="btn btn-primary">Sign up</button>
                </div>
                </form>
                <hr>
                <p class="text-center">Already have an account? <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a></p>
            </div>
        </div>
    </div>
</div>


        <!-- Masthead-->
        <header class="masthead">
            <div class="container px-4 px-lg-5 h-100">
                <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-8 align-self-end">
                        <h1 class="text-white font-weight-bold">Welcome To MobodStayFinder</h1>
                        <hr class="divider" />
                    </div>
                    <div class="col-lg-8 align-self-baseline">
                        <p class="text-white-75 mb-5">Discover your ideal boarding accommodation in Mobod</p>
                        <a class="btn btn-primary btn-xl" href="#about">Find Out More</a>
                    </div>
                </div>
            </div>
        </header>
  
        <!-- About-->
        <section class="page-section " style="background-color: #0056b3;" id="about">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="text-white mt-0">We've Got You Covered!</h2>
                        <hr class="divider divider-light" />
                        <p class="text-white-75 mb-4">Our platform provides a comprehensive list of boarding houses, ensuring you find the perfect fit for your needs. Say goodbye to the hassle of searching for accommodation!</p>
                        <a class="btn btn-light btn-xl" href="login.html" data-bs-toggle="modal" data-bs-target="#loginModal">Get Started!</a>
                    </div>
                </div>
            </div>
        </section>
        <!-- Services-->
     <section class="page-section" id="services">
    <div class="container px-4 px-lg-5">
        <h2 class="text-center mt-0">Our Services</h2>
        <hr class="divider" />
        <div class="row gx-4 gx-lg-5">
            <div class="col-lg-3 col-md-6 text-center">
                <div class="mt-5">
                    <div class="mb-2"><i class="fas fa-search fs-1" style="color: #0056B3;"></i></div>
                    <h3 class="h4 mb-2">Search</h3>
                    <p class="text-muted mb-0">Effortlessly find available boarding houses that match your preferences and budget.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
                <div class="mt-5">
                    <div class="mb-2"><i class="fas fa-check-double fs-1" style="color: #0056B3;"></i></div>
                    <h3 class="h4 mb-2">Verify</h3>
                    <p class="text-muted mb-0">Rest assured, all listed properties are thoroughly verified for safety and reliability.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
                <div class="mt-5">
                    <div class="mb-2"><i class="fas fa-calendar-plus fs-1" style="color: #0056B3;"></i></div>
                    <h3 class="h4 mb-2">Book</h3>
                    <p class="text-muted mb-0">Easily schedule viewings and communicate with landlords to finalize your boarding house arrangement.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
                <div class="mt-5">
                    <div class="mb-2"><i class="fas fa-home fs-1" style="color: #0056B3;"></i></div>
                    <h3 class="h4 mb-2">Settle In</h3>
                    <p class="text-muted mb-0">Join our community and share experiences with other residents of boarding houses.</p>
                </div>
            </div>
        </div>
    </div>
</section>


        <!-- Portfolio-->
        <div id="portfolio">
            <div class="container-fluid p-0">
                <div class="row g-0">
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="assets/img/portfolio/fullsize/bh1.jpg" >
                            <img class="img-fluid" src="assets/img/portfolio/thumbnails/bh1.jpg" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Category</div>
                                
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="assets/img/portfolio/fullsize/bh2.jpg">
                            <img class="img-fluid" src="assets/img/portfolio/thumbnails/bh2.jpg" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Category</div>
                                
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="assets/img/portfolio/fullsize/bh3.jpg" >
                            <img class="img-fluid" src="assets/img/portfolio/thumbnails/bh3.jpg" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Category</div>
                                <!--<div class="project-name">Project Name</div>-->
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="assets/img/portfolio/fullsize/bh4.jpg" title="Project Name">
                            <img class="img-fluid" src="assets/img/portfolio/thumbnails/bh4.jpg" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Category</div>
                                
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="assets/img/portfolio/fullsize/5.jpg">
                            <img class="img-fluid" src="assets/img/portfolio/thumbnails/5.jpg" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Category</div>
                              
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="assets/img/portfolio/fullsize/6.jpg">
                            <img class="img-fluid" src="assets/img/portfolio/thumbnails/6.jpg" alt="..." />
                            <div class="portfolio-box-caption p-3">
                                <div class="project-category text-white-50">Category</div>
                                
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact-->
        <section class="page-section" id="contact">
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-lg-8 col-xl-6 text-center">
                        <h2 class="mt-0">Get in Touch</h2>
                        <hr class="divider" />
                        <p class="text-muted mb-5">Have questions or need assistance? Reach out to us, and we'll be happy to help!</p>
                    </div>
                </div>
                <div class="row gx-4 gx-lg-5 justify-content-center mb-5">
                    <div class="col-lg-6">
                        <!-- Contact form-->
                        <form id="contactForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="form-floating mb-3">
        <input class="form-control" id="name" type="text" name="name" placeholder="Enter your name..." required />
        <label for="name">Full Name</label>
    </div>
    <div class="form-floating mb-3">
        <input class="form-control" id="email" type="email" name="email" placeholder="name@example.com" required />
        <label for="email">Email Address</label>
    </div>
    <div class="form-floating mb-3">
        <textarea class="form-control" id="message" name="message" placeholder="Enter your message here..." style="height: 10rem" required></textarea>
        <label for="message">Message</label>
    </div>
    <div class="d-grid"><button class="btn btn-primary btn-xl" id="submitButton" type="submit">Submit</button></div>
</form>

                    </div>
                </div>
            </div>
        </section>
        <!-- Footer-->
        <!-- Footer -->
<!-- Remove the container if you want to extend the Footer to full width. -->

<footer class="text-center text-lg-start" style="background-color: #0056b3; width: 100%;">
  <div class="d-flex justify-content-center py-5">
    <button type="button" class="btn btn-primary btn-lg btn-floating mx-2" style="background-color: #54456b;">
      <i class="fab fa-facebook-f"></i>
    </button>
    <button type="button" class="btn btn-primary btn-lg btn-floating mx-2" style="background-color: #54456b;">
      <i class="fab fa-youtube"></i>
    </button>
    <button type="button" class="btn btn-primary btn-lg btn-floating mx-2" style="background-color: #54456b;">
      <i class="fab fa-instagram"></i>
    </button>
    <button type="button" class="btn btn-primary btn-lg btn-floating mx-2" style="background-color: #54456b;">
      <i class="fab fa-twitter"></i>
    </button>
  </div>

  <!-- Copyright -->
  <div class="text-center text-white p-3" style="background-color: rgba(0, 0, 0, 0.2);">
    © 2024 Copyright:
    <a class="text-white" href="https://mobodstayfinder.com/">MobodStayFinder.com</a>
  </div>
  <!-- Copyright -->
</footer>




  

  
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- SimpleLightbox plugin JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <!-- * *                               SB Forms JS                               * *-->
        <!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
      
    </body>
</html>
  