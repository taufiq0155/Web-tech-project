<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            background-color: #007bff;
            /* Blue background */
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        select {
            width: 75%;
            padding: 5px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: white;
        }

        .error {
            color: red;
            text-align: left;
            margin-bottom: 10px;
            font-size: 14px;
            justify-content: center;
            text-align: center;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .gender-container,
        .role-container {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            text-align: left;
        }

        label {
            margin-top: 10px;
            text-align: left;
            display: block;
        }

        .back-button {
            background-color: #ffc107;
            color: white;
            border: none;
            padding: 10px;
            margin-top: 10px;
            width: 100%;
            cursor: pointer;
        }
    </style>
    <script src="script.js"> </script>
</head>

<body>

    <div class="form-container">
        <h2>Register</h2>
        <form method="post" action="../controllers/RegisterController.php" onsubmit="return validateForm()" novalidate>
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo empty($_SESSION['full_name']) ? '' : htmlspecialchars($_SESSION['full_name']); ?>">
            <span class="error"><?php echo empty($_SESSION['err1']) ? '' : htmlspecialchars($_SESSION['err1']); ?></span>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo empty($_SESSION['email']) ? '' : htmlspecialchars($_SESSION['email']); ?>" oninput="validateEmail()">
            <p class="error" id="emailError"><?php echo empty($_SESSION['err2']) ? '' : htmlspecialchars($_SESSION['err2']); ?></p>

            <label for="password">Password</label>
            <input type="password" id="password" name="password">
            <span class="error"><?php echo empty($_SESSION['err3']) ? '' : htmlspecialchars($_SESSION['err3']); ?></span>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password">
            <span class="error"><?php echo empty($_SESSION['err4']) ? '' : htmlspecialchars($_SESSION['err4']); ?></span>

            <label for="contact_number">Contact Number</label>
            <input type="text" id="contact_number" name="contact_number" value="<?php echo empty($_SESSION['contact_number']) ? '' : htmlspecialchars($_SESSION['contact_number']); ?>">
            <span class="error"><?php echo empty($_SESSION['err5']) ? '' : htmlspecialchars($_SESSION['err5']); ?></span>

            <div class="gender-container">
                <label>Gender</label>
                <label for="male"><input type="radio" id="male" name="gender" value="male" <?php echo (!empty($_SESSION['gender']) && $_SESSION['gender'] == 'male') ? 'checked' : ''; ?>> Male</label>
                <label for="female"><input type="radio" id="female" name="gender" value="female" <?php echo (!empty($_SESSION['gender']) && $_SESSION['gender'] == 'female') ? 'checked' : ''; ?>> Female</label>
            </div>
            <span class="error"><?php echo empty($_SESSION['err6']) ? '' : htmlspecialchars($_SESSION['err6']); ?></span>

            <!-- Role Selection -->
            <div class="role-container">
                <label for="role">Role</label>
                <select id="role" name="role" onchange="updateButtonText()">
                    <option value="">-- Select Role --</option> <!-- Empty value at first -->
                    <option value="patient" <?php echo (!empty($_SESSION['role']) && $_SESSION['role'] == 'patient') ? 'selected' : ''; ?>>Patient</option>
                    <option value="doctor" <?php echo (!empty($_SESSION['role']) && $_SESSION['role'] == 'doctor') ? 'selected' : ''; ?>>Doctor</option>
                </select>
            </div>
            <span class="error" id="roleError"><?php echo empty($_SESSION['err7']) ? '' : htmlspecialchars($_SESSION['err7']); ?></span>
            <!-- Specialist Input Field -->
            <div id="specialistContainer" style="display: none;">
                <label for="specialist">Specialization</label>
                <input type="text" id="specialist" name="specialization" value="<?php echo empty($_SESSION['specialization']) ? '' : htmlspecialchars($_SESSION['specialization']); ?>">
                <span class="error"><?php echo empty($_SESSION['err8']) ? '' : htmlspecialchars($_SESSION['err8']); ?></span>
            </div>
            <!-- Submit Button -->
            <input type="submit" id="submitBtn" value="Register">
        </form>
        <button class="back-button" onclick="location.href='Login.php'">Back to Login</button>
    </div>

</body>

</html>

<?php
// Clear session errors after display
$_SESSION['err1'] = $_SESSION['err2'] = $_SESSION['err3'] = $_SESSION['err4'] = $_SESSION['err5'] = $_SESSION['err6'] = $_SESSION['err7'] = '';
?>