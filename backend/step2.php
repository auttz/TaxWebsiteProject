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

// รับค่าจากฟอร์ม
$marital_status = $_POST["marital_status"];
$personal_deduction = $_POST["personal_deduction"];

// บันทึกลงฐานข้อมูล
$stmt = $conn->prepare("INSERT INTO family_deductions (marital_status, personal_deduction) VALUES (?, ?)");
$stmt->bind_param("ss", $marital_status, $personal_deduction);

//เพิ่มตรวจสอบก่อน insert
if (empty($marital_status)) {
    echo "<script>alert('กรุณาเลือกสถานะสมรส'); window.history.back();</script>";
    exit;
}

if ($stmt->execute()) {
    echo "<script>alert('บันทึกข้อมูลเรียบร้อย'); window.location.href='../frontend/step3.html';</script>";
} else {
    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึก'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
