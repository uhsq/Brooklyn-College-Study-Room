<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "GET") {

  unset($_SESSION["email"]);
  if (session_destroy() == false) {
    echo json_encode(["success" => false]);
  } else {
    echo json_encode(["success" => true]);
  }
}