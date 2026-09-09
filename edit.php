<?php
require_once "config.php";

if (!isset($_GET["id"])) {
    die("رقم الطالب غير موجود.");
}

$id = (int) $_GET["id"];

$stmt = $conn->prepare(
    "SELECT student_id, attendance, grades, assignments, participation, success
     FROM students
     WHERE student_id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();

$stmt->close();

if (!$student) {
    die("الطالب غير موجود.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $attendance = (int) $_POST["attendance"];
    $grades = (int) $_POST["grades"];
    $assignments = (int) $_POST["assignments"];
    $participation = (int) $_POST["participation"];
    $success = (int) $_POST["success"];

    $stmt = $conn->prepare(
        "UPDATE students
         SET attendance = ?,
             grades = ?,
             assignments = ?,
             participation = ?,
             success = ?
         WHERE student_id = ?"
    );

    $stmt->bind_param(
        "iiiiii",
        $attendance,
        $grades,
        $assignments,
        $participation,
        $success,
        $id
    );

    if ($stmt->execute()) {
        header("Location: students.php?updated=1");
        exit;
    } else {
        $error = "حدث خطأ أثناء تعديل البيانات.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل بيانات الطالب</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h1>تعديل بيانات الطالب رقم <?php echo $student["student_id"]; ?></h1>

    <?php if (isset($error)): ?>
        <div class="error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="form-box">

        <label>الحضور</label>
        <input
            type="number"
            name="attendance"
            min="0"
            max="100"
            value="<?php echo $student["attendance"]; ?>"
            required
        >

        <label>الدرجات</label>
        <input
            type="number"
            name="grades"
            min="0"
            max="100"
            value="<?php echo $student["grades"]; ?>"
            required
        >

        <label>الواجبات</label>
        <input
            type="number"
            name="assignments"
            min="0"
            max="100"
            value="<?php echo $student["assignments"]; ?>"
            required
        >

        <label>المشاركة</label>
        <input
            type="number"
            name="participation"
            min="0"
            max="100"
            value="<?php echo $student["participation"]; ?>"
            required
        >

        <label>حالة النجاح</label>

        <select name="success" required>

            <option value="1"
                <?php echo ($student["success"] == 1) ? "selected" : ""; ?>>
                ناجح
            </option>

            <option value="0"
                <?php echo ($student["success"] == 0) ? "selected" : ""; ?>>
                غير ناجح
            </option>

        </select>

        <button type="submit">💾 حفظ التعديلات</button>

    </form>

    <a href="students.php" class="back">العودة إلى بيانات الطلاب</a>

</div>

</body>
</html>