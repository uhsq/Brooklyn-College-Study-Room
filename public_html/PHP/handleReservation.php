<?php
session_start();

// Load database configuration
$config = require '../../Private/db_config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establish database connection
$conn = new mysqli($config['host'], $config['username'], $config['password'], $config['db_name']);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection Failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Fetch all reservations
    $stmt = $conn->prepare("SELECT * FROM reservations");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]);
        exit;
    }

    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "message" => "Error executing statement: " . $stmt->error]);
        exit;
    }

    $result = $stmt->get_result();
    $reservations = [];

    while ($res = $result->fetch_assoc()) {

      $room = $res['room'];
      if (!isset($reservations[$room])) {
          $reservations[$room] = [];
      }
      $reservations[$room] = array_merge($reservations[$room], [
          "8:00AM" => $res['8am'],
          "9:00AM" => $res['9am'],
          "10:00AM" => $res['10am'],
          "11:00AM" => $res['11am'],
          "12:00PM" => $res['12pm'],
          "1:00PM" => $res['1pm'],
          "2:00PM" => $res['2pm'],
          "3:00PM" => $res['3pm'],
          "4:00PM" => $res['4pm'],
          "5:00PM" => $res['5pm'],
          "6:00PM" => $res['6pm'],
          "7:00PM" => $res['7pm'],
          "8:00PM" => $res['8pm']
      ]);
    }

    echo json_encode(["success" => true, "reservations" => $reservations]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["success" => false, "message" => "Invalid JSON payload"]);
        exit;
    }

    if (empty($_SESSION["email"])) {
        echo json_encode(["success" => false, "message" => "User not logged in"]);
        exit;
    }

    $room = isset($data['room']) ? trim($data['room']) : '';
    $times = isset($data['times']) ? $data['times'] : [];

    if (empty($room) || empty($times)) {
        echo json_encode(["success" => false, "message" => "Missing room or times data"]);
        exit;
    }

    $email = $_SESSION["email"];
    $dbTimes = [
        "8:00AM" => '8am',
        "9:00AM" => '9am',
        "10:00AM" => '10am',
        "11:00AM" => '11am',
        "12:00PM" => '12pm',
        "1:00PM" => '1pm',
        "2:00PM" => '2pm',
        "3:00PM" => '3pm',
        "4:00PM" => '4pm',
        "5:00PM" => '5pm',
        "6:00PM" => '6pm',
        "7:00PM" => '7pm',
        "8:00PM" => '8pm'
    ];

    foreach ($times as $time) {
        if (!isset($dbTimes[$time])) {
            echo json_encode(["success" => false, "message" => "Invalid time provided: $time"]);
            exit;
        }

        $resTime = $dbTimes[$time];
        if (!in_array($resTime, $dbTimes)) {
          echo json_encode(["success" => false, "message" => "Invalid time slot provided"]);
          exit;
      }
      
      $query = "UPDATE `reservations` SET `$resTime` = ? WHERE `room` = ? AND `$resTime` IS NULL";
      $stmt = $conn->prepare($query);
      if (!$stmt) {
          echo json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]);
          exit;
      }
      
      $stmt->bind_param('si', $email, $room);
      if (!$stmt->execute()) {
          echo json_encode(["success" => false, "message" => "Error executing update for $time"]);
          exit;
      }
    }

    // Send confirmation email
    $subject = "Study Room Reservation Confirmation";
    $timeList = implode(", ", $times);
    $message = "You have reserved study room $room for $timeList. Happy Studying!";
    $headers = [
        "From: BrooklynCollegeLibrary@bcmail.cuny.edu",
        "Reply-To: No-reply@bcmail.cuny.edu",
        "Content-Type: text/html;charset=UTF-8"
    ];

    if (mail($email, $subject, $message, implode("\r\n", $headers))) {
        echo json_encode(["success" => true, "message" => "Reservation confirmed and email sent"]);
    } else {
        echo json_encode(["success" => false, "message" => "Reservation confirmed but email failed to send"]);
    }

    exit;
}
?>
