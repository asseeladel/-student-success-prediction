<?php
require_once "config.php";

$result = $conn->query("SELECT COUNT(*) AS total FROM students");
$row = $result->fetch_assoc();
$total = $row['total'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة الطلاب</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h1>نظام إدارة الطلاب</h1>

    <p class="welcome">
        مرحبًا بك في نظام إدارة بيانات الطلاب
    </p>

    <div class="cards">

        <a href="add.php" class="card">
            <h2>➕ إضافة بيانات</h2>
            <p>إضافة طالب جديد إلى قاعدة البيانات</p>
        </a>

        <a href="students.php" class="card">
            <h2>📋 عرض البيانات</h2>
            <p>عرض وتعديل وحذف بيانات الطلاب</p>
        </a>

    </div>

    <div class="total">
        <h2>عدد الطلاب</h2>
        <strong><?php echo $total; ?></strong>
    </div>

</div>

</body>
</html>