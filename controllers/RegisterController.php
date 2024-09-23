
<?php
session_start();
require "../models/User.php";




// Capture form values
$fullName = sanitize($_POST['full_name']);
$email = sanitize($_POST['email']);
$password = sanitize($_POST['password']);
$confirmPassword = sanitize($_POST['confirm_password']);
$contactNumber = sanitize($_POST['contact_number']);
$gender = sanitize($_POST['gender']);
$role = sanitize($_POST['role']);
$specialization = isset($_POST['specialization']) ? sanitize($_POST['specialization']) : ''; // Capture specialist
$isValid = true;

// Store form values into session to repopulate on validation failure
$_SESSION['full_name'] = $fullName;
$_SESSION['email'] = $email;
$_SESSION['contact_number'] = $contactNumber;
$_SESSION['gender'] = $gender;
$_SESSION['role'] = $role;
$_SESSION['specialization'] = $specialization; // Store specialist as well

// Reset error messages
$_SESSION['err1'] = $_SESSION['err2'] = $_SESSION['err3'] = $_SESSION['err4'] = $_SESSION['err5'] = $_SESSION['err6'] = $_SESSION['err7'] = $_SESSION['err8'] = "";

// Validate the form fields
if (empty($fullName)) {
    $_SESSION['err1'] = "Full Name is required";
    $isValid = false;
}

if (empty($email)) {
    $_SESSION['err2'] = "Email is required";
    $isValid = false;
} elseif (emailExists($email)) {
    $_SESSION['err2'] = "Email already exists";
    $isValid = false;
}

if (empty($password)) {
    $_SESSION['err3'] = "Password is required";
    $isValid = false;
}

if ($password !== $confirmPassword) {
    $_SESSION['err4'] = "Passwords do not match";
    $isValid = false;
}

if (empty($contactNumber)) {
    $_SESSION['err5'] = "Contact Number is required";
    $isValid = false;
}

if (empty($gender)) {
    $_SESSION['err6'] = "Gender is required";
    $isValid = false;
}

if (empty($role)) {
    $_SESSION['err7'] = "Role is required";
    $isValid = false;
}

if ($role === 'doctor' && empty($specialization)) {
    $_SESSION['err8'] = "Specialization is required for doctors";
    $isValid = false;
}

// If all validations pass, attempt to register the user
if ($isValid) {
    if (registerUser($fullName, $email, $password, $contactNumber, $gender, $role, $specialization)) {
        // Clear session data after successful registration
        session_unset();
        header("Location: ../views/Login.php");
        exit();
    } else {
        $_SESSION['err7'] = "Registration failed. Please try again.";
        header("Location: ../views/register.php");
        exit();
    }
} else {
    header("Location: ../views/register.php");
    exit();
}
?>