<?php
session_start();

// ตรวจสอบว่า login แล้วหรือยัง
if (!isset($_SESSION["user_id"])) {
    header("Location: ../frontend/login.html");
    exit();
}

// เชื่อมต่อฐานข้อมูล
$host = "localhost:3307";
$username = "root";
$password = "12345678";
$dbname = "taxsite";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// รับข้อมูลจากฟอร์ม
$salary = isset($_POST['salary']) ? floatval($_POST['salary']) : 0;
$bonus = isset($_POST['bonus']) ? floatval($_POST['bonus']) : 0;
$other_income = isset($_POST['other_income']) ? floatval($_POST['other_income']) : 0;
$total_income = $salary + $bonus + $other_income;

$user_id = $_SESSION['user_id']; 

// เตรียม SQL และ execute
$stmt = $conn->prepare("INSERT INTO incomes (user_id, salary, bonus, other_income, total_income) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("idddd", $user_id, $salary, $bonus, $other_income, $total_income);

if ($stmt->execute()) {
    header("Location: ../frontend/step2.html");
    exit();
} else {
    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึก'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
