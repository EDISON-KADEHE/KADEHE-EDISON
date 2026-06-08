<?php
require_once __DIR__ . '/config.php';
require_login();

$query = trim($_GET['q'] ?? '');

if ($query !== '') {
    $term = '%' . $query . '%';
    $stmt = db()->prepare(
        'SELECT * FROM students
         WHERE admission_no LIKE ?
            OR first_name LIKE ?
            OR last_name LIKE ?
            OR email LIKE ?
            OR course LIKE ?
         ORDER BY created_at DESC'
    );
    $stmt->execute([$term, $term, $term, $term, $term]);
    $students = $stmt->fetchAll();
} else {
    $students = db()->query('SELECT * FROM students ORDER BY created_at DESC')->fetchAll();
}

$pageTitle = 'Students';
require __DIR__ . '/partials/header.php';
?>

<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="page-shell">
    <?php if ($notice = flash()): ?>
        <div class="alert <?= e($notice['type']) ?>"><?= e($notice['message']) ?></div>
    <?php endif; ?>

    <section class="page-heading">
        <div>
            <p class="eyebrow">Student records</p>
            <h1>Manage Students</h1>
        </div>
        <a class="primary-button" href="student_form.php">Add Student</a>
    </section>

    <form class="search-bar" method="get">
        <input type="search" name="q" value="<?= e($query) ?>" placeholder="Search by name, admission number, email, or course">
        <button type="submit">Search</button>
        <?php if ($query): ?>
            <a href="students.php">Clear</a>
        <?php endif; ?>
    </form>

    <section class="content-section">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Admission No.</th>
                        <th>Course</th>
                        <th>Phone</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td class="student-cell">
                                <img src="<?= e($student['profile_image'] ?: 'assets/avatar.svg') ?>" alt="">
                                <div>
                                    <strong><?= e($student['first_name'] . ' ' . $student['last_name']) ?></strong>
                                    <small><?= e($student['email']) ?></small>
                                </div>
                            </td>
                            <td><?= e($student['admission_no']) ?></td>
                            <td><?= e($student['course']) ?></td>
                            <td><?= e($student['phone']) ?></td>
                            <td><?= e(date('M j, Y', strtotime($student['created_at']))) ?></td>
                            <td class="actions">
                                <a href="student_form.php?id=<?= (int) $student['id'] ?>">Edit</a>
                                <form method="post" action="delete_student.php" onsubmit="return confirm('Delete this student record?');">
                                    <input type="hidden" name="id" value="<?= (int) $student['id'] ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$students): ?>
                        <tr><td colspan="6" class="empty-state">No matching students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>

