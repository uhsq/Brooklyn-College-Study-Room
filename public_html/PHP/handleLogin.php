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
    die('Please fill all the fields.');
}

$conn = new mysqli($config['host'], $config['username'], $config['password'], $config['db_name']);

if ($conn->connect_error) {
    die("Connection Failed: $conn->connect_error");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $stmt = $conn->prepare("SELECT password FROM accounts WHERE email = ? AND otp IS NULL");
  if ($stmt === false) {
    die("Error preparing statement: $conn->error");
  }

  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($stmt->execute() === false) {
    die("Error executing statement: $conn->error");
  }

  $res = $result->fetch_assoc();

  if ($result->num_rows === 1 && hash("md5", $password) === $res['password']) {

    $_SESSION["email"] = $email;
    echo json_encode(["success" => true]);
  } else {

    die("This user does not exist in our system.");
  }
}