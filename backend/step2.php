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
$conn->set_charset("utf8mb4");


// รับค่าจากฟอร์ม
$marital_status = $_POST["marital_status"];
$personal_deduction = $_POST["personal_deduction"];
$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

// ตรวจสอบค่าว่าง
if (empty($marital_status)) {
    echo "<script>alert('กรุณาเลือกสถานะสมรส'); window.history.back();</script>";
    exit;
}
if (empty($user_id)) {
    echo "<script>alert('ไม่พบผู้ใช้งานในระบบ กรุณาเข้าสู่ระบบใหม่'); window.location.href = '../frontend/login.html';</script>";
    exit;
}

// บันทึกลงฐานข้อมูล
$stmt = $conn->prepare("INSERT INTO family_deductions (user_id, marital_status, personal_deduction) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $marital_status, $personal_deduction);

if ($stmt->execute()) {
    header("Location: ../frontend/step3.html");
    exit();
} else {
    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึก'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
