<?php
$config = require '../../Private/db_config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);


$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($email) || empty($password)) {
    die('Please fill all the fields.');
}

$_SESSION['email'] = $email;
$conn = new mysqli($config['host'], $config['username'], $config['password'], $config['db_name']);

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $stmt = $conn->prepare("SELECT otp FROM accounts WHERE email = ? AND otp IS NOT NULL");
    if ($stmt === false) {
        die('Error preparing statement: ' . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // user is not verified
        $otp = $result->fetch_assoc();
        // send email with otp
        $subject = "Brooklyn College Library Verification Code";
        $message = 'Please verify your account with the following code: ' . $otp;

        if (mail($email, $subject, $message)) {
            echo json_encode(["success" => true]);
        } else {
            die('Failed to send verification code. Please try again.');
        }
        echo json_encode(["success" => true]);

    } else {
        $stmt2 = $conn->prepare("SELECT * FROM accounts WHERE email = ? AND otp IS NULL");
        if ($stmt2 === false) {
            die('Error preparing statement: ' . $conn->error);
        }

        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2->num_rows === 1) {
            // user is verified
            die('User already exists. Please log in.');
        } else {

            // create and send email with otp
            $new_otp = random_int(100000, 999999);
            $hashed_pass = hash("md5", $password);
            
            // insert into database table
            $stmt3 = $conn->prepare("INSERT INTO `accounts`(`user_role`, `email`, `password`, `otp`) VALUES (?,?,?,?)");
            if ($stmt3 === false) {
                die('Error preparing statement: ' . $conn->error);
            }
            $user_role = "STUDENT"; // default role. Admins can be manually set in db
            $stmt3->bind_param("sssi", $user_role, $email, $hashed_pass, $otp);
            // Execute the query
            if (!$stmt3->execute()) {
                die("Error: " . $stmt->error);
            }

            $subject = "Brooklyn College Library Verification Code";
            $message = 'Please verify your account with the following code: ' . $new_otp;

            if (mail($email, $subject, $message)) {
                echo json_encode(["success" => true]);
            } else {
                die('Failed to send verification code. Please try again.');
            }
            echo json_encode(["success" => true]);
        }
        $stmt2->close();
    }
    $stmt->close();
    
}

$conn->close();

