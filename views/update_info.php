<?php

require_once "../models/User.php"; // Include the User model

// Check if user is logged in
if (empty($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] === false) {
    $_SESSION['err1'] = "Unauthorized Access...!";
    header("Location: ../views/logout.php");
    exit();
}

// Initialize variables
$fullName = '';
$contactNumber = '';
$isValid = true;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitize($_POST['full_name'] ?? '');
    $contactNumber = sanitize($_POST['contact_number'] ?? '');

    // Validate input fields
    if (empty($fullName) || empty($contactNumber)) {
        $_SESSION['err1'] = "All fields are required";
        $isValid = false;
    }

    if ($isValid) {
        $email = $_SESSION['email'];
        $role = $_SESSION['role']; // Get the role from the session

        // Update user information
        if (updateUserInfo($email, $fullName, $contactNumber, $role)) {
            $_SESSION['msg'] = "Information updated successfully";

            // Clear error messages after success
            unset($_SESSION['err1']);
        } else {
            $_SESSION['err1'] = "Error updating information";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Information</title>
    <style>
        .update {
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

        input[type="text"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
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

    <h2 class="update">Update Information</h2>
    <form action="" method="POST"> <!-- Use empty action to submit to the same page -->
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($fullName); ?>">

        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($contactNumber); ?>">

        <button type="submit">Update Information</button>
    </form>

    <!-- Display error messages if any -->
    <?php if (isset($_SESSION['err1'])): ?>
        <div class="error"><?php echo $_SESSION['err1'];
                            unset($_SESSION['err1']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['msg'])): ?>
        <div class="success"><?php echo $_SESSION['msg'];
                                unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

</body>

</html>

<?php
$_SESSION['err1'] = "";
?>