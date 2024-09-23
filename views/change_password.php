<?php

require_once "../models/User.php"; // Include the User model

// Check if user is logged in
if (empty($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] === false) {
    $_SESSION['err1'] = "Unauthorized Access...!";
    header("Location: ../views/logout.php");
    exit();
}

// Initialize variables
$currentPassword = '';
$newPassword = '';
$retypePassword = '';
$isValid = true;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = sanitize($_POST['current_password'] ?? '');
    $newPassword = sanitize($_POST['new_password'] ?? '');
    $retypePassword = sanitize($_POST['confirm_new_password'] ?? '');

    // Validate input fields
    if (empty($currentPassword) || empty($newPassword) || empty($retypePassword)) {
        $_SESSION['err1'] = "All fields are required";
        $isValid = false;
    }

    if ($newPassword !== $retypePassword) {
        $_SESSION['err2'] = "New passwords do not match";
        $isValid = false;
    }

    if (strlen($newPassword) < 6) {
        $_SESSION['err3'] = "New password must be at least 6 characters long";
        $isValid = false;
    }

    if ($isValid) {
        $email = $_SESSION['email'];
        $role = $_SESSION['role']; // Get the role from the session

        // Validate current password before updating
        $user = matchCredentials($email, $currentPassword);
        if ($user) {
            // Update password
            if (updatePassword($email, $newPassword, $role)) {
                $_SESSION['msg'] = "Password changed successfully";
                header("Location: logout.php");
                // Clear error messages after success
                unset($_SESSION['err1'], $_SESSION['err2'], $_SESSION['err3']);
            } else {
                $_SESSION['err1'] = "Error updating password";
            }
        } else {
            $_SESSION['err1'] = "Current password is incorrect";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        .change {
            text-align: center;
            color: #333;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        input[type="password"]:focus {
            border-color: #007BFF;
            outline: none;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Error messages */
        .error {
            color: red;
            font-size: 0.9em;
            margin-top: 10px;
            text-align: center;
        }

        .success {
            color: green;
            font-size: 0.9em;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>

<body>

    <h2 class="change">Change Password</h2>
    <form action="" method="POST"> <!-- Use empty action to submit to the same page -->
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" value="<?php echo htmlspecialchars($currentPassword); ?>">

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" value="<?php echo htmlspecialchars($newPassword); ?>">

        <label for="confirm_new_password">Confirm New Password:</label>
        <input type="password" id="confirm_new_password" name="confirm_new_password" value="<?php echo htmlspecialchars($retypePassword); ?>">

        <button type="submit">Change Password</button>
    </form>

    <!-- Display error messages if any -->
    <?php if (isset($_SESSION['err1'])): ?>
        <div class="error"><?php echo $_SESSION['err1'];
                            unset($_SESSION['err1']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['err2'])): ?>
        <div class="error"><?php echo $_SESSION['err2'];
                            unset($_SESSION['err2']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['err3'])): ?>
        <div class="error"><?php echo $_SESSION['err3'];
                            unset($_SESSION['err3']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['msg'])): ?>
        <div class="success"><?php echo $_SESSION['msg'];
                                unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

</body>

</html>

<?php
$_SESSION['err1'] = "";
$_SESSION['err2'] = "";
$_SESSION['err3'] = "";

?>