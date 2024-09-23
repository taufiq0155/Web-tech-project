<?php


// Ensure the user is logged in
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../views/logout.php");
    exit();
}

// Include the User model
require_once "../models/User.php";

// Fetch appointments for the logged-in doctor
$doctorId = $_SESSION['id'];
if (is_null($doctorId)) {
    die("Doctor ID is not set."); // Debugging line
}

// Fetch appointments based on the doctor
$appointments = getAppointmentsForDoctor($doctorId);

// Handle appointment actions via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $appointmentId = $_POST['appointment_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        // Approve the appointment
        $success = approveAppointment($appointmentId);
        echo json_encode(['success' => $success, 'status' => 'approved']);
        exit(); // Exit after sending the response
    } elseif ($action === 'reject') {
        // Reject the appointment
        $success = rejectAppointment($appointmentId);
        echo json_encode(['success' => $success, 'status' => 'rejected']);
        exit(); // Exit after sending the response
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment List</title>

    <style>
        table {
            margin-left: 20rem;
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
        }

        .approve {
            background-color: #4CAF50;
        }

        .reject {
            background-color: #f44336;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.btn').click(function(e) {
                e.preventDefault(); // Prevent form submission

                var action = $(this).val();
                var appointmentId = $(this).closest('form').find('input[name="appointment_id"]').val();
                var row = $(this).closest('tr'); // Get the current row for updates

                $.ajax({
                    url: '', // Current page
                    type: 'POST',
                    data: {
                        action: action,
                        appointment_id: appointmentId
                    },
                    success: function(response) {
                        console.log(response); // Log the raw response to console for debugging
                        var data = JSON.parse(response);
                        if (data.success) {
                            alert('Successfully updated appointment status.');
                            row.find('.status').text(data.status.charAt(0).toUpperCase() + data.status.slice(1)); // Capitalize the first letter
                        } else {
                            alert('Error updating appointment status. Please try again.');
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
</head>

<body>
    <div class="main-content">
        <h1>Appointment Requests</h1>

        <?php if (!empty($appointments)): ?>
            <table>
                <tr>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Appointment Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['patient_email']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                        <td class="status"><?php echo htmlspecialchars($appointment['status']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment['id']); ?>">
                                <button type="button" name="action" value="approve" class="btn approve">Approve</button>
                                <button type="button" name="action" value="reject" class="btn reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No appointment requests at this time.</p>
        <?php endif; ?>
    </div>
</body>

</html>