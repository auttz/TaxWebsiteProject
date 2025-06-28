<?php
session_start();

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$host = "localhost";
$username = "root";       // ค่าเริ่มต้นของ XAMPP
$password = "12345678";           // ถ้าไม่ตั้งรหัสผ่าน ปล่อยว่าง
$dbname = "taxsite";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($host, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่งข้อมูลมาหรือไม่
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];

    // ตรวจสอบรหัสผ่านว่าตรงกันไหม
    if ($password !== $confirm) {
        echo "<script>alert('รหัสผ่านไม่ตรงกัน'); window.history.back();</script>";
        exit();
    }

    // ตรวจสอบว่า email มีอยู่ในระบบหรือยัง
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('อีเมลนี้ถูกใช้งานแล้ว'); window.history.back();</script>";
        exit();
    }
    $stmt->close();

    // เพิ่มข้อมูลลงในฐานข้อมูล
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $email, $password);

    if ($stmt->execute()) {
        echo "<script>alert('สมัครสมาชิกสำเร็จ'); window.location.href = '../login.html';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>