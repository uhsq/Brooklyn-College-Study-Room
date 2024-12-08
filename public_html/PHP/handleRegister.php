<?php
session_start();

$config = require '../../Private/db_config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$input = file_get_contents('php://input');
$data = json_decode($input, true);


$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => 'Please fill all the fields.']);
}

$conn = new mysqli($config['host'], $config['username'], $config['password'], $config['db_name']);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection Failed: $conn->connect_error"]);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $stmt = $conn->prepare("SELECT otp FROM accounts WHERE email = ? AND otp IS NOT NULL");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Error preparing statement: $conn->error"]);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // user is not verified
        $res = $result->fetch_assoc();
        $otp = $res['otp'];

        // send email with otp
        $subject = "Brooklyn College Library Verification Code";
        $message = "Please verify your account with the following code: $otp";
        $headers = array(
            "MIME-Version" => "1.0",
            "Content-Type" => "text/html;charset=UTF-8",
            "From" => "BrooklynCollegeLibrary@bcmail.cuny.edu",
            "Reply-To" => "No-reply@bcmail.cuny.edu"
        );

        if (mail($email, $subject, $message, $headers)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => 'Failed to send verification code. Please try again.']);
        }

    } else {
        $stmt2 = $conn->prepare("SELECT * FROM accounts WHERE email = ? AND otp IS NULL");
        if ($stmt2 === false) {
            echo json_encode(["success" => false, "message" => "Error preparing statement: $conn->error"]);
        }

        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2->num_rows === 1) {
            // user is verified
            echo json_encode(["success" => false, "message" => 'User already exists. Please log in.']);
        } else {

            // create and send email with otp
            $new_otp = random_int(100000, 999999);
            $hashed_pass = hash("md5", $password);
            
            // insert into database table
            $stmt3 = $conn->prepare("INSERT INTO `accounts`(`user_role`, `email`, `password`, `otp`) VALUES (?,?,?,?)");
            if ($stmt3 === false) {
                echo json_encode(["success" => false, "message" => 'Error preparing statement: ' . $conn->error]);
            }
            $user_role = "STUDENT"; // default role. Admins can be manually set in db
            $stmt3->bind_param("sssi", $user_role, $email, $hashed_pass, $new_otp);
            // Execute the query
            if (!$stmt3->execute()) {
                echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
            }

            $subject = "Brooklyn College Library Verification Code";
            $message = "Please verify your account with the following code:  $new_otp";
            $headers = array(
                "MIME-Version" => "1.0",
                "Content-Type" => "text/html;charset=UTF-8",
                "From" => "BrooklynCollegeLibrary@bcmail.cuny.edu",
                "Reply-To" => "No-reply@bcmail.cuny.edu"
            );


            if (mail($email, $subject, $message, $headers)) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "message" => 'Failed to send verification code. Please try again.']);
            }
        }
        $stmt2->close();
    }
    $stmt->close();
    
}

$conn->close();

