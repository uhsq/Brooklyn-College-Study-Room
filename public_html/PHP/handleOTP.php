<?php
session_start();

$config = require '../../Private/db_config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$email = isset($data['email']) ? trim($data['email']) : '';
$otp = isset($data['otp']) ? trim($data['otp']) : '';

if (empty($email) || empty($otp)) {
    echo json_encode(["success" => false, "message" => 'Empty input found.']);
}

// check otp
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
    // set otp to null if valid
    $res = $result->fetch_assoc();
    $res_otp = $res['otp'];
    if ($res_otp == $otp) {
      $stmt2 = $conn->prepare("UPDATE accounts SET otp = NULL WHERE email = ?");

      if ($stmt2 === false) {
        echo json_encode(["success" => false, "message" => "Error preparing statement: $conn->error"]);
      }

      $stmt2->bind_param("s", $email);
      
      if ($stmt2->execute() === false) {
        echo json_encode(["success" => false, "message" => "Error executing statement: $conn->error"]);
      }

      $_SESSION['email'] = $email;
      echo json_encode(["success" => true]);
    } else {
      echo json_encode(["success" => false, "message" => 'Incorrect otp']);
    }
  } else {
    echo json_encode(["success" => false, "message" => 'query failed']);
  }
}