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

// รับค่าจากฟอร์ม (ตั้งค่าเริ่มต้นเป็น 0 ถ้าไม่ได้กรอก)
$life_insurance = isset($_POST['life_insurance']) ? floatval($_POST['life_insurance']) : 0;
$health_insurance = isset($_POST['health_insurance']) ? floatval($_POST['health_insurance']) : 0;
$parent_health_insurance = isset($_POST['parent_health_insurance']) ? floatval($_POST['parent_health_insurance']) : 0;
$long_term_life_insurance = isset($_POST['long_term_life_insurance']) ? floatval($_POST['long_term_life_insurance']) : 0;

// เตรียมคำสั่ง SQL
$stmt = $conn->prepare("INSERT INTO insurance_deductions 
    (user_id, life_insurance, health_insurance, parent_health_insurance, long_term_life_insurance) 
    VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("idddd", $user_id, $life_insurance, $health_insurance, $parent_health_insurance, $long_term_life_insurance);

// บันทึกข้อมูล
if ($stmt->execute()) {
    header("Location: ../frontend/step5.html");
    exit();
} else {
    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึก'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
