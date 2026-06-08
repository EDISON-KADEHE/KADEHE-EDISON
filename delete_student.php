<?php
require_once __DIR__ . '/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('students.php');
}

$id = (int) ($_POST['id'] ?? 0);

$stmt = db()->prepare('SELECT profile_image FROM students WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();

if ($student) {
    $delete = db()->prepare('DELETE FROM students WHERE id = ?');
    $delete->execute([$id]);

    if (!empty($student['profile_image'])) {
        $path = __DIR__ . DIRECTORY_SEPARATOR . basename(dirname($student['profile_image'])) . DIRECTORY_SEPARATOR . basename($student['profile_image']);
        if (is_file($path)) {
            unlink($path);
        }
    }

    flash('Student deleted successfully.');
} else {
    flash('Student record was not found.', 'danger');
}

redirect('students.php');

