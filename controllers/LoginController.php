
<?php
session_start();
require "../models/User.php";

// Clear previous error messages and login state
$_SESSION['err1'] = "";
$_SESSION['err2'] = "";
$_SESSION['err3'] = "";

// Store the submitted email for repopulation in case of errors
$email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
$password = isset($_POST['password']) ? sanitize($_POST['password']) : '';
$isValid = true;

// Validate the input fields
if (empty($email)) {
    $_SESSION['err1'] = "Please fill up the email properly.";
    $isValid = false;
}

if (empty($password)) {
    $_SESSION['err2'] = "Please fill up the password properly.";
    $isValid = false;
}

if ($isValid) {
    // Attempt to match credentials
    $user = matchCredentials($email, $password);
    if ($user) {
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['email'] = $email;
        $_SESSION['id'] = $user['id']; // Ensure you assign the user ID correctly
        $_SESSION['role'] = $user['role']; // Store user role in session

        // Redirect based on user role
        if ($user['role'] === 'doctor') {
            header("Location: ../views/doctorHome.php");
        } else {
            header("Location: ../views/patientHome.php");
        }
        exit();
    } else {
        $_SESSION['err3'] = "Login failed. Please check your credentials.";
        header("Location: ../views/Login.php");
        exit();
    }
} else {
    // If validation fails, repopulate the email field
    $_SESSION['email'] = $email; // Store email to repopulate in the login form
    header("Location: ../views/Login.php");
    exit();
}
?>