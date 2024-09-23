<?php
require_once "../models/User.php"; // Use User.php to access functions


if (!isset($_SESSION['isLoggedIn']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../views/logout.php");
    exit();
}

// Initialize response variable
$response = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $conn = mysqli_connect("localhost", "root", "", "mvc_example");
    if (!$conn) {
        $response['message'] = 'Database connection failed: ' . mysqli_connect_error();
        echo json_encode($response);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['email'], $data['doctorId'], $data['phoneNumber'], $data['problem'])) {
        $patientEmail = mysqli_real_escape_string($conn, $data['email']);
        $doctorId = mysqli_real_escape_string($conn, $data['doctorId']);
        $phoneNumber = mysqli_real_escape_string($conn, $data['phoneNumber']);
        $problem = mysqli_real_escape_string($conn, $data['problem']);

        // Check if the fields are properly populated
        if (empty($patientEmail) || empty($doctorId) || empty($phoneNumber) || empty($problem)) {
            $response['message'] = 'Missing required fields.';
            echo json_encode($response);
            exit();
        }

        // Construct the SQL query
        $sql = "INSERT INTO appointments (patient_email, doctor_id, phone_number, problem) 
                VALUES ('$patientEmail', '$doctorId', '$phoneNumber', '$problem')";

        // Execute the query
        if (mysqli_query($conn, $sql)) {
            $response['message'] = 'Appointment saved successfully!';
        } else {
            // Output the error if the query fails
            $response['message'] = 'Failed to save appointment.';
            $response['error'] = mysqli_error($conn);
            $response['query'] = $sql; // Log the failed query for debugging
        }
    } else {
        $response['message'] = 'Invalid input data.';
    }

    mysqli_close($conn);
    echo json_encode($response);
    exit();
}



$doctors = getAllDoctors(); // Fetch all doctors for the appointment form
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Form</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f5;
        }

        #appointment-section {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .title {
            text-align: center;
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
            font-size: 1.1rem;
        }

        td {
            background-color: #f9f9f9;
            border-bottom: 1px solid #ddd;
        }

        .appoint-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .appoint-btn:hover {
            background-color: #45a049;
        }

        #appointment-form {
            display: none;
            margin-top: 20px;
            padding: 20px;
            border-radius: 10px;
            background-color: #f7f7f7;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        #appointment-form label {
            font-size: 1.1rem;
            color: #333;
            margin-top: 10px;
            display: block;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            background-color: #fff;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        #save-appointment {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        #save-appointment:hover {
            background-color: #45a049;
        }

        #appointment-response {
            margin-top: 20px;
            color: green;
            font-size: 1.2rem;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <div id="appointment-section">
        <h2>Available Doctors</h2>
        <table id="doctor-table">
            <thead>
                <tr>
                    <th>Doctor Name</th>
                    <th>Specialization</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doctor['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                        <td>
                            <button class="appoint-btn" data-doctor-id="<?php echo $doctor['id']; ?>">Make Appointment</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="appointment-form">
            <h3>Appointment Form</h3>
            <label for="phone-number">Phone Number:</label>
            <input type="text" id="phone-number" placeholder="Enter your phone number" required>
            <span class="error" id="phone-error"></span>
            <br>

            <label for="problem">Problem:</label>
            <textarea id="problem" placeholder="Describe your problem" required></textarea>
            <span class="error" id="problem-error"></span>
            <br>

            <button id="save-appointment">Save Appointment</button>
        </div>

        <div id="appointment-response"></div>
    </div>

    <script>
        document.querySelectorAll('.appoint-btn').forEach(button => {
            button.addEventListener('click', function() {
                const doctorId = this.getAttribute('data-doctor-id');
                document.getElementById('appointment-form').style.display = 'block';
                document.getElementById('save-appointment').setAttribute('data-doctor-id', doctorId);
            });
        });

        document.getElementById('save-appointment').addEventListener('click', function() {
            const doctorId = this.getAttribute('data-doctor-id');
            const phoneNumber = document.getElementById('phone-number').value;
            const problem = document.getElementById('problem').value;

            // Clear previous error messages
            document.getElementById('phone-error').innerText = '';
            document.getElementById('problem-error').innerText = '';

            let hasError = false;

            // Basic validation
            if (phoneNumber === '') {
                document.getElementById('phone-error').innerText = 'Phone number is required';
                hasError = true;
            }
            if (problem === '') {
                document.getElementById('problem-error').innerText = 'Problem description is required';
                hasError = true;
            }

            if (!hasError) {
                fetch('', { // Use empty string to refer to the same PHP file
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            email: '<?php echo $_SESSION['email']; ?>',
                            doctorId: doctorId,
                            phoneNumber: phoneNumber,
                            problem: problem
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('appointment-response').innerText = data.message;
                        if (data.error) {
                            console.error('Error details:', data.error); // Log the error details for debugging
                        }
                        document.getElementById('appointment-form').style.display = 'none';
                        document.getElementById('phone-number').value = '';
                        document.getElementById('problem').value = '';
                    })
                    .catch(error => {
                        document.getElementById('appointment-response').innerText = 'Saved appointment';
                        console.error('Error:', error);
                    });
            }
        });
    </script>
</body>

</html>