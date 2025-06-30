<?php
session_start();

// เชื่อมต่อฐานข้อมูล
$host = "localhost:3307";
$username = "root";
$password = "12345678";
$dbname = "taxsite";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อน'); window.location.href = '../frontend/login.html';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// รับค่าจากฟอร์ม
$pvd = isset($_POST['pvd']) ? floatval($_POST['pvd']) : 0;
$social_security = isset($_POST['social_security']) ? floatval($_POST['social_security']) : 0;
$home_loan_interest = isset($_POST['home_loan_interest']) ? floatval($_POST['home_loan_interest']) : 0;

// เตรียมคำสั่ง SQL
$stmt = $conn->prepare("INSERT INTO social_funds_deductions (user_id, pvd, social_security, home_loan_interest) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iddd", $user_id, $pvd, $social_security, $home_loan_interest);

// บันทึกข้อมูล
if ($stmt->execute()) {
    header("Location: ../frontend/step4.html");
    exit();
} else {
    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึก'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
