<?php
require_once "config.php";

$result = $conn->query(
    "SELECT student_id, attendance, grades, assignments, participation, success
     FROM students
     ORDER BY student_id ASC"
);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيانات الطلاب</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container wide">

    <h1>بيانات الطلاب</h1>

    <?php if (isset($_GET["added"])): ?>
        <div class="success-message">
            تم إضافة الطالب بنجاح.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET["updated"])): ?>
        <div class="success-message">
            تم تعديل بيانات الطالب بنجاح.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET["deleted"])): ?>
        <div class="success-message">
            تم حذف الطالب بنجاح.
        </div>
    <?php endif; ?>

    <div class="top-buttons">
        <a href="index.php" class="back">الرئيسية</a>
        <a href="add.php" class="button">➕ إضافة طالب</a>
    </div>

    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>رقم الطالب</th>
                    <th>الحضور</th>
                    <th>الدرجات</th>
                    <th>الواجبات</th>
                    <th>المشاركة</th>
                    <th>النجاح</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>

            <tbody>

            <?php while ($student = $result->fetch_assoc()): ?>

                <tr>

                    <td><?php echo $student["student_id"]; ?></td>

                    <td><?php echo $student["attendance"]; ?></td>

                    <td><?php echo $student["grades"]; ?></td>

                    <td><?php echo $student["assignments"]; ?></td>

                    <td><?php echo $student["participation"]; ?></td>

                    <td>
                        <?php if ($student["success"] == 1): ?>
                            <span class="passed">ناجح</span>
                        <?php else: ?>
                            <span class="failed">غير ناجح</span>
                        <?php endif; ?>
                    </td>

                    <td class="actions">

                        <a
                            href="edit.php?id=<?php echo $student["student_id"]; ?>"
                            class="edit-button"
                        >
                            ✏️ تعديل
                        </a>

                        <a
                            href="delete.php?id=<?php echo $student["student_id"]; ?>"
                            class="delete-button"
                            onclick="return confirm('هل أنت متأكد من حذف هذا الطالب؟');"
                        >
                            🗑️ حذف
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>