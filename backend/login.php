<?php
session_start();
$host = "localhost";
$username = "root";
$password = "12345678";
$dbname = "taxsite";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $passwordInput = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $fullname, $password);
        $stmt->fetch();

        // ตรงนี้ใช้เปรียบเทียบแบบไม่เข้ารหัส
        if ($passwordInput === $password) {
            $_SESSION["userid"] = $id;
            $_SESSION["fullname"] = $fullname;
            header("Location: ../dashboard.html");
            exit();
        } else {
            echo "<script>alert('รหัสผ่านไม่ถูกต้อง'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('ไม่พบอีเมลในระบบ'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
