<?php
session_start();
// Ensure the user is logged in
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../views/logout.php");
    exit();
}

// Retrieve user information based on the stored email
require "../models/User.php";

$userInfo = null; // Initialize userInfo to avoid undefined variable warning

// Debug: Check if the email is set in the session
if (!isset($_SESSION['email'])) {
    echo "Email is not set in the session.";
    exit();
} else {
    // Fetch user info
    $userInfo = getUserInfoByEmail($_SESSION['email']); // Fetch user info

    if (!$userInfo) {
        echo "No user information found for the email: " . htmlspecialchars($_SESSION['email']);
        exit();
    }
}

// Handle actions based on the button clicked
$action = isset($_GET['action']) ? $_GET['action'] : 'home';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Home</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #93bd9e;
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
        }

        .sidebarH1 {
            text-align: center;
        }

        h1 {
            text-align: center;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: block;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #575757;
        }

        .main-content {
            flex-grow: 1;
            padding: 20px;
            background-color: #ffffff;
            min-height: 95vh;
        }

        p {
            margin-left: 20rem;
        }

        .main-content h1 {
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <h2 class="sidebarH1">Doctor's Dashboard</h2>
            <ul>
                <li><a href="?action=home">Home</a></li>
                <li><a href="?action=change_password">Change Password</a></li>
                <li><a href="?action=update_info">Update Information</a></li>
                <li><a href="?action=appointment_list">Appointment List</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        <div class="main-content">
            <?php
            // Include the appropriate content based on the action
            switch ($action) {
                case 'change_password':
                    include 'change_password.php';
                    break;
                case 'update_info':
                    include 'update_info.php';
                    break;
                case 'appointment_list':
                    include 'appointment_list.php'; // Add this file for appointment management
                    break;
                default:
                    // Display doctor's information
                    echo "<h1>Welcome to Doctor's Dashboard</h1>";
                    echo "<h2>Doctor Information</h2>";
                    if ($userInfo) {
                        echo "<p><strong>Full Name:</strong> {$userInfo['full_name']}</p>";
                        echo "<p><strong>Email:</strong> {$userInfo['email']}</p>";
                        echo "<p><strong>Contact Number:</strong> {$userInfo['contact_number']}</p>";
                        echo "<p><strong>Gender:</strong> {$userInfo['gender']}</p>";
                        echo "<p><strong>Specialist:</strong> {$userInfo['specialization']}</p>";
                    } else {
                        echo "<p>Unable to retrieve your information. Please contact support.</p>";
                    }
            }
            ?>
        </div>
    </div>
</body>

</html>