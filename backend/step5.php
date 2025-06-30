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
$gov_fund = isset($_POST['gov_fund']) ? floatval($_POST['gov_fund']) : 0;
$nsf = isset($_POST['nsf']) ? floatval($_POST['nsf']) : 0;
$rmf = isset($_POST['rmf']) ? floatval($_POST['rmf']) : 0;

// เตรียมคำสั่ง SQL
$stmt = $conn->prepare("INSERT INTO other_fund_deductions (user_id, gov_fund, nsf, rmf) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iddd", $user_id, $gov_fund, $nsf, $rmf);

// บันทึกข้อมูล
if ($stmt->execute()) {
    header("Location: ../frontend/step6.php");
    exit();
} else {
    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึก'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
