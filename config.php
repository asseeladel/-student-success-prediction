<?php

$host = "sql109.infinityfree.com";
$dbname = "if0_42867031_students";
$username = "if0_42867031";
$password = "asseel712"; // اكتب هنا كلمة مرور حساب InfinityFree

$conn = new mysqli($host, $username, $password, $dbname, 3306);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>