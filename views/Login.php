<?php 
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background-color: #007bff; /* Blue background */
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
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #218838;
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
        .error {
            color: red;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Login</h2>
    <form method="post" action="../controllers/LoginController.php" novalidate>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email">
        <span class="error"><?php echo empty($_SESSION['err1']) ? "" : htmlspecialchars($_SESSION['err1']); ?></span>
        <br>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password">
        <span class="error"><?php echo empty($_SESSION['err2']) ? "" : htmlspecialchars($_SESSION['err2']); ?></span>
        <br>
        <input type="submit" value="Login">
        <span class="error"><?php echo empty($_SESSION['err3']) ? "" : htmlspecialchars($_SESSION['err3']); ?></span>
    </form>
   
    <button class="back-button" onclick="location.href='register.php'">Register</button>
</div>
</body>
</html>
<?php
$_SESSION['err1'] = "";
$_SESSION['err2'] = "";
$_SESSION['err3'] ="";

?>