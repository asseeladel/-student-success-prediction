<?php
require_once "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $attendance = (int) $_POST["attendance"];
    $grades = (int) $_POST["grades"];
    $assignments = (int) $_POST["assignments"];
    $participation = (int) $_POST["participation"];
    $success = (int) $_POST["success"];

    $stmt = $conn->prepare(
        "INSERT INTO students 
        (attendance, grades, assignments, participation, success)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "iiiii",
        $attendance,
        $grades,
        $assignments,
        $participation,
        $success
    );

    if ($stmt->execute()) {
        header("Location: students.php?added=1");
        exit;
    } else {
        $message = "حدث خطأ أثناء إضافة البيانات.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة بيانات طالب</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h1>إضافة بيانات طالب</h1>

    <?php if ($message): ?>
        <div class="error">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="form-box">

        <label>الحضور</label>
        <input type="number" name="attendance" min="0" max="100" required>

        <label>الدرجات</label>
        <input type="number" name="grades" min="0" max="100" required>

        <label>الواجبات</label>
        <input type="number" name="assignments" min="0" max="100" required>

        <label>المشاركة</label>
        <input type="number" name="participation" min="0" max="100" required>

        <label>حالة النجاح</label>
        <select name="success" required>
            <option value="1">ناجح</option>
            <option value="0">غير ناجح</option>
        </select>

        <button type="submit">إضافة الطالب</button>

    </form>

    <a href="index.php" class="back">العودة إلى الرئيسية</a>

</div>

</body>
</html>