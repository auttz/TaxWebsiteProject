<?php
// เชื่อมต่อฐานข้อมูล
$host = "localhost";
$username = "root";
$password = "12345678"; // ถ้ามีรหัสผ่านใส่ตรงนี้
$database = "taxsite";

$conn = new mysqli($host, $username, $password, $database);

// เช็กการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่าเป็น POST request หรือไม่
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    // ตรวจสอบรูปแบบอีเมล
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // เตรียมคำสั่ง SQL
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // ตรวจสอบว่าเจอ email หรือไม่
        if ($stmt->num_rows > 0) {
            echo "ส่งลิงก์รีเซ็ตรหัสผ่านไปยัง $email แล้ว (ถ้าอีเมลนี้มีอยู่ในระบบ)";
            // *ในระบบจริงควรสร้าง token แล้วส่งลิงก์ reset จริง
        } else {
            echo "ส่งลิงก์รีเซ็ตรหัสผ่านไปยัง $email แล้ว (ถ้าอีเมลนี้มีอยู่ในระบบ)";
            // *อย่าบอกว่า "ไม่มี email นี้" เพื่อป้องกัน brute-force
        }

        $stmt->close();
    } else {
        echo "รูปแบบอีเมลไม่ถูกต้อง";
    }
} else {
    header("Location: ../frontend/forgot-password.html");
    exit();
}

$conn->close();
?>
