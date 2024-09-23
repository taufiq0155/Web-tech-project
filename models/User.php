
<?php
// Function to sanitize data
function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
}
// Function to match credentials for login (for both doctor and patient)
function matchCredentials($email, $password)
{
    $conn = mysqli_connect("localhost", "root", "", "mvc_example");
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password); // No password hashing

    // Check doctors table for matching credentials
    $sql = "SELECT id, 'doctor' AS role FROM doctors WHERE email = '$email' AND password = '$password'
            UNION ALL 
            SELECT id, 'patient' AS role FROM patients WHERE email = '$email' AND password = '$password'";

    $result = mysqli_query($conn, $sql);



    $user = mysqli_fetch_assoc($result);
    mysqli_close($conn);

    return $user; // Return user info (id and role)
}


// Function to register a new doctor or patient based on role
function registerUser($fullName, $email, $password, $contactNumber, $gender, $role, $specialization = null)
{
    $conn = mysqli_connect("localhost", "root", "", "mvc_example");

    // Sanitize inputs
    $fullName = mysqli_real_escape_string($conn, $fullName);
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password); // Plain text password
    $contactNumber = mysqli_real_escape_string($conn, $contactNumber);
    $gender = mysqli_real_escape_string($conn, $gender);
    $role = mysqli_real_escape_string($conn, $role);

    // Determine which table to insert into based on role
    if ($role === 'doctor') {
        // Include specialization in the insert statement
        $specialization = mysqli_real_escape_string($conn, $specialization); // Sanitize specialization
        $sql = "INSERT INTO doctors (full_name, email, password, contact_number, gender, specialization) 
                VALUES ('$fullName', '$email', '$password', '$contactNumber', '$gender', '$specialization')";
    } else if ($role === 'patient') {
        $sql = "INSERT INTO patients (full_name, email, password, contact_number, gender) 
                VALUES ('$fullName', '$email', '$password', '$contactNumber', '$gender')";
    }

    $success = mysqli_query($conn, $sql);
    mysqli_close($conn);

    return $success;
}


// Function to check if email already exists in doctors or patients table
function emailExists($email)
{
    $conn = mysqli_connect("localhost", "root", "", "mvc_example");
    $email = mysqli_real_escape_string($conn, $email);

    // Check in both doctors and patients tables
    $sql = "SELECT id FROM doctors WHERE email = '$email' 
            UNION 
            SELECT id FROM patients WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    $exists = (mysqli_num_rows($result) > 0);
    mysqli_close($conn);

    return $exists;
}

// Function to retrieve user information (either doctor or patient) by email
function getUserInfoByEmail($email)
{
    $conn = mysqli_connect("localhost", "root", "", "mvc_example");
    $email = mysqli_real_escape_string($conn, $email);

    // Query to get user information
    $sql = "SELECT full_name, email, contact_number, gender, specialization FROM doctors WHERE email = '$email'
            UNION ALL
            SELECT full_name, email, contact_number, gender, NULL AS specialization FROM patients WHERE email = '$email'";

    $result = mysqli_query($conn, $sql);
    $userInfo = null; // Initialize the variable

    if (mysqli_num_rows($result) > 0) {
        $userInfo = mysqli_fetch_assoc($result);
    }

    mysqli_close($conn); // Close the connection

    return $userInfo; // Return user info or null
}


// Function to update the password (for password change purposes)
function updatePassword($email, $newPassword, $role)
{
    $conn = mysqli_connect("localhost", "root", "", "mvc_example");



    $email = mysqli_real_escape_string($conn, $email);
    $newPassword = mysqli_real_escape_string($conn, $newPassword); // No password hashing

    // Determine which table to update based on user role
    if ($role === 'doctor') {
        $sql = "UPDATE doctors SET password = '$newPassword' WHERE email = '$email'";
    } else if ($role === 'patient') {
        $sql = "UPDATE patients SET password = '$newPassword' WHERE email = '$email'";
    }
    $success = mysqli_query($conn, $sql);
    mysqli_close($conn);

    return $success;
}
// Function to update user information (full name and contact number)
function updateUserInfo($email, $fullName, $contactNumber, $role)
{
    $conn = mysqli_connect("localhost", "root", "", "mvc_example");

    // Sanitize inputs
    $fullName = mysqli_real_escape_string($conn, $fullName);
    $contactNumber = mysqli_real_escape_string($conn, $contactNumber);
    $email = mysqli_real_escape_string($conn, $email); // Ensure email is sanitized

    // Determine which table to update based on user role
    if ($role === 'doctor') {
        $sql = "UPDATE doctors SET full_name = '$fullName', contact_number = '$contactNumber' WHERE email = '$email'";
    } else if ($role === 'patient') {
        $sql = "UPDATE patients SET full_name = '$fullName', contact_number = '$contactNumber' WHERE email = '$email'";
    }

    $success = mysqli_query($conn, $sql);
    mysqli_close($conn);

    return $success;
}
function getAllDoctors()
{
    $conn = mysqli_connect("localhost", "root", "", "mvc_example");
    $sql = "SELECT id, full_name, specialization FROM doctors";
    $result = mysqli_query($conn, $sql);
    $doctors = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $doctors[] = $row;
    }

    mysqli_close($conn);
    return $doctors;
}


function getAppointmentsByEmail($email)
{
    $conn = mysqli_connect("localhost", "root", "", "mvc_example");
    if (!$conn) {
        return false; // Return false if the connection fails
    }

    $email = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT * FROM appointments WHERE patient_email = '$email'";
    $result = mysqli_query($conn, $sql);

    $appointments = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $appointments[] = $row; // Fetch each appointment into an array
        }
    }

    mysqli_close($conn);
    return $appointments; // Return the appointments array
}
//appointments show for doctors
function getDatabaseConnection()
{
    $conn = mysqli_connect("localhost", "root", "", "mvc_example");
    if (!$conn) {
        return false; // Return false if the connection fails
    }
    return $conn; // Return the connection
}

function getAppointmentsForDoctor($doctorId)
{
    $conn = getDatabaseConnection();
    if (!$conn) {
        return []; // Return an empty array if the connection fails
    }

    $stmt = $conn->prepare("SELECT * FROM appointments WHERE doctor_id = ? AND status = 'pending'");
    $stmt->bind_param("i", $doctorId); // 'i' indicates the type is integer
    $stmt->execute();
    $result = $stmt->get_result();
    $appointments = $result->fetch_all(MYSQLI_ASSOC); // Fetch all results as an associative array

    $stmt->close(); // Close the statement
    mysqli_close($conn); // Close the connection

    return $appointments; // Return the appointments
}

function approveAppointment($appointmentId)
{
    $conn = getDatabaseConnection();
    if (!$conn) {
        return false; // Return false if the connection fails
    }

    $stmt = $conn->prepare("UPDATE appointments SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $appointmentId); // 'i' indicates the type is integer
    $success = $stmt->execute(); // Execute the query

    $stmt->close(); // Close the statement
    mysqli_close($conn); // Close the connection

    return $success; // Returns true on success
}

function rejectAppointment($appointmentId)
{
    $conn = getDatabaseConnection();
    if (!$conn) {
        return false; // Return false if the connection fails
    }

    $stmt = $conn->prepare("UPDATE appointments SET status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $appointmentId); // 'i' indicates the type is integer
    $success = $stmt->execute(); // Execute the query

    $stmt->close(); // Close the statement
    mysqli_close($conn); // Close the connection

    return $success; // Returns true on success
}

?>