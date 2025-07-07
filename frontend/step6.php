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

// ดึงรายได้
$incomeResult = $conn->query("SELECT * FROM incomes WHERE user_id = $user_id ORDER BY id DESC LIMIT 1");
$income = $incomeResult ? $incomeResult->fetch_assoc() : null;
$total_income = $income['total_income'] ?? 0;

// ดึงยอดลดหย่อนรวมจาก table ที่เกี่ยวข้อง
$deductions = [
  'family_deductions' => ['personal_duction'],
  'social_funds_deductions' => ['pvd', 'social_security', 'home_loan_interest'],
  'insurance_deductions' => ['life', 'health', 'parent_health', 'pension'],
  'other_fund_deductions' => ['gov_fund', 'nsf', 'rmf']  // ✅ แก้ชื่อตรงนี้ให้ถูก
];


$total_deduction = 0;

foreach ($deductions as $table => $fields) {
    $sql = "SELECT * FROM $table WHERE user_id = $user_id ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if (!$result) {
        echo "<p>Query Error in table `$table`: " . $conn->error . "</p>";
        continue;
    }

    if ($row = $result->fetch_assoc()) {
        if (is_array($fields)) {
            foreach ($fields as $f) {
                $total_deduction += floatval($row[$f] ?? 0);
            }
        } else {
            $total_deduction += floatval($row[$fields] ?? 0);
        }
    }
}

// คำนวณรายได้สุทธิ
$net_income = max($total_income - $total_deduction, 0);

// คำนวณภาษีแบบขั้นบันได
function calculateTax($net_income)
{
    $brackets = [
        [0, 150000, 0.00],
        [150001, 300000, 0.05],
        [300001, 500000, 0.10],
        [500001, 750000, 0.15],
        [750001, 1000000, 0.20],
        [1000001, 2000000, 0.25],
        [2000001, 5000000, 0.30],
        [5000001, PHP_INT_MAX, 0.35]
    ];

    $tax = 0;
    foreach ($brackets as [$start, $end, $rate]) {
        if ($net_income > $start) {
            $taxable = min($net_income, $end) - $start;
            $tax += $taxable * $rate;
        } else {
            break;
        }
    }
    return $tax;
}

$tax_amount = calculateTax($net_income);
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>คำนวณภาษี</title>
  <link href="https://fonts.googleapis.com/css2?family=Sarabun&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/step6.css">
</head>
<body class="animated">

  <nav class="navbar">
    <h1 class="logo">MyTaxWebsite</h1>
    <div class="menu-bar">
      <a href="dashboard.html">Home</a>
      <a href="login.html">Logout</a>
    </div>
  </nav>

  <div class="container">
    <h1>คำนวณภาษี</h1>
    <p class="subtitle">ผลการคำนวณภาษีเงินได้บุคคลธรรมดาประจำปี</p>

    <div class="step-bar">
      <a href="dashboard.html" class="step"><span>1</span><br>รายรับ</a>
      <a href="step2.html" class="step"><span>2</span><br>ลดหย่อนครอบครัว</a>
      <a href="step3.html" class="step"><span>3</span><br>กองทุนฯ<br>ประกันสังคม<br>คู่สมรส</a>
      <a href="step4.html" class="step"><span>4</span><br>ประกัน</a>
      <a href="step5.html" class="step"><span>5</span><br>กองทุนอื่น ๆ</a>
      <a href="step6.php" class="step active"><span>6</span><br>คำนวณภาษี</a>
    </div>

    <div class="result-box">
      <h2>สรุปผลภาษีของคุณ</h2>
      <p><strong>รายได้รวม:</strong> <?= number_format($total_income, 2) ?> บาท</p>
      <p><strong>ยอดลดหย่อน:</strong> <?= number_format($total_deduction, 2) ?> บาท</p>
      <p><strong>รายได้สุทธิ:</strong> <?= number_format($net_income, 2) ?> บาท</p>
      <hr>
      <p><strong>ภาษีที่ต้องชำระ:</strong> <?= number_format($tax_amount, 2) ?> บาท</p>
    </div>

    <div class="button-group">
      <button class="btn-back" onclick="location.href='step5.html'">ย้อนกลับ</button>
      <button class="btn-next" onclick="location.href='dashboard.html'">เริ่มทำใหม่</button>
    </div>
  </div>

</body>
</html>
