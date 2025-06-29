<?php
session_start();

// เชื่อมต่อฐานข้อมูล
$host = "localhost:3307";
$username = "root";
$password = "12345678";
$dbname = "taxsite";

$conn = new mysqli($host, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// รับข้อมูลจากฟอร์ม
$salary = isset($_POST['salary']) ? floatval($_POST['salary']) : 0;
$bonus = isset($_POST['bonus']) ? floatval($_POST['bonus']) : 0;
$other_income = isset($_POST['other_income']) ? floatval($_POST['other_income']) : 0;

$total_income = $salary + $bonus + $other_income;

// สมมุติว่ามี user_id ใน session (ถ้ามีระบบ login แล้ว)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// เตรียมคำสั่ง SQL
$stmt = $conn->prepare("INSERT INTO incomes (user_id, salary, bonus, other_income, total_income) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("idddd", $user_id, $salary, $bonus, $other_income, $total_income);

// บันทึกข้อมูล
if ($stmt->execute()) {
    header("Location: ../frontend/step2.html");
    exit();
} else {
    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึก'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
