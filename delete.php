<?php
require_once "config.php";

if (!isset($_GET["id"])) {
    die("رقم الطالب غير موجود.");
}

$id = (int) $_GET["id"];

$stmt = $conn->prepare(
    "DELETE FROM students WHERE student_id = ?"
);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: students.php?deleted=1");
    exit;
} else {
    die("حدث خطأ أثناء حذف الطالب.");
}

$stmt->close();
?>