<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "GET") {

  if (session_status() === PHP_SESSION_ACTIVE && array_key_exists("email", $_SESSION)) {

    echo json_encode(["logged" => true, "email" => $_SESSION["email"]]);
  } else {

    echo json_encode(["logged" => false, "email" => ""]);
  }
}