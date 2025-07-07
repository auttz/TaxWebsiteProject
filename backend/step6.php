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

// ตรวจสอบ session
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href = '../frontend/login.html';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. ดึงข้อมูลรายได้ล่าสุด
$income = 0;
$result = $conn->query("SELECT total_income FROM incomes WHERE user_id = $user_id ORDER BY id DESC LIMIT 1");
if ($row = $result->fetch_assoc()) {
    $income = floatval($row['total_income']);
}

// 2. รวมค่าลดหย่อนจากทุกตาราง
$deduction = 0;

$tables = [
    "family_deductions" => "personal_deduction",
    "social_funds_deductions" => "pvd + social_security + home_loan_interest",
    "insurance_deductions" => "life_insurance + health_insurance + parent_health_insurance + pension_insurance",
    "other_fund_deductions" => "gov_fund + nsf + rmf"
];

foreach ($tables as $table => $fields) {
    $sql = "SELECT ($fields) AS sum_deduction FROM $table WHERE user_id = $user_id ORDER BY id DESC LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        $deduction += floatval($row['sum_deduction']);
    }
}

// 3. คำนวณรายได้สุทธิ
$net_income = max(0, $income - $deduction);

// 4. คำนวณภาษีตามช่วง
$tax = 0;

if ($net_income <= 150000) {
    $tax = 0;
} elseif ($net_income <= 300000) {
    $tax = ($net_income - 150000) * 0.05;
} elseif ($net_income <= 500000) {
    $tax = 7500 + ($net_income - 300000) * 0.10;
} elseif ($net_income <= 750000) {
    $tax = 27500 + ($net_income - 500000) * 0.15;
} elseif ($net_income <= 1000000) {
    $tax = 65000 + ($net_income - 750000) * 0.20;
} elseif ($net_income <= 2000000) {
    $tax = 115000 + ($net_income - 1000000) * 0.25;
} else {
    $tax = 365000 + ($net_income - 2000000) * 0.30;
}

// แสดงผล
echo "รายได้รวม: " . number_format($income, 2) . " บาท<br>";
echo "ยอดลดหย่อน: " . number_format($deduction, 2) . " บาท<br>";
echo "รายได้สุทธิ: " . number_format($net_income, 2) . " บาท<br>";
echo "ภาษีที่ต้องชำระ: " . number_format($tax, 2) . " บาท";
?>
