<?php
require_once __DIR__ . '/config.php';
require_login();

$totalStudents = (int) db()->query('SELECT COUNT(*) FROM students')->fetchColumn();
$courses = (int) db()->query('SELECT COUNT(DISTINCT course) FROM students')->fetchColumn();
$recent = db()->query('SELECT * FROM students ORDER BY created_at DESC LIMIT 5')->fetchAll();

$pageTitle = 'Dashboard';
require __DIR__ . '/partials/header.php';
?>

<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="page-shell">
    <?php if ($notice = flash()): ?>
        <div class="alert <?= e($notice['type']) ?>"><?= e($notice['message']) ?></div>
    <?php endif; ?>

    <section class="page-heading">
        <div>
            <p class="eyebrow">Admin dashboard</p>
            <h1>Welcome, <?= e($_SESSION['admin_name'] ?? 'Admin') ?></h1>
        </div>
        <a class="primary-button" href="student_form.php">Add Student</a>
    </section>

    <section class="stats-grid">
        <article class="stat-card">
            <span>Total Students</span>
            <strong><?= $totalStudents ?></strong>
        </article>
        <article class="stat-card">
            <span>Active Courses</span>
            <strong><?= $courses ?></strong>
        </article>
        <article class="stat-card">
            <span>Profile Storage</span>
            <strong>Local</strong>
        </article>
    </section>

    <section class="content-section">
        <div class="section-title">
            <h2>Recently Registered</h2>
            <a href="students.php">View all</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Admission No.</th>
                        <th>Course</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent as $student): ?>
                        <tr>
                            <td class="student-cell">
                                <img src="<?= e($student['profile_image'] ?: 'assets/avatar.svg') ?>" alt="">
                                <span><?= e($student['first_name'] . ' ' . $student['last_name']) ?></span>
                            </td>
                            <td><?= e($student['admission_no']) ?></td>
                            <td><?= e($student['course']) ?></td>
                            <td><?= e($student['email']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$recent): ?>
                        <tr><td colspan="4" class="empty-state">No students registered yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>

