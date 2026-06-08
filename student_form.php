<?php
require_once __DIR__ . '/config.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$student = [
    'admission_no' => '',
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'gender' => 'Male',
    'date_of_birth' => '',
    'course' => '',
    'address' => '',
    'profile_image' => '',
];

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM students WHERE id = ?');
    $stmt->execute([$id]);
    $student = $stmt->fetch();

    if (!$student) {
        flash('Student record was not found.', 'danger');
        redirect('students.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student = array_merge($student, [
        'admission_no' => trim($_POST['admission_no'] ?? ''),
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'gender' => $_POST['gender'] ?? 'Male',
        'date_of_birth' => $_POST['date_of_birth'] ?: null,
        'course' => trim($_POST['course'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
    ]);

    try {
        $student['profile_image'] = upload_profile_image($_FILES['profile_image'] ?? [], $student['profile_image'] ?: null);

        if ($id > 0) {
            $stmt = db()->prepare(
                'UPDATE students
                 SET admission_no = ?, first_name = ?, last_name = ?, email = ?, phone = ?, gender = ?,
                     date_of_birth = ?, course = ?, address = ?, profile_image = ?
                 WHERE id = ?'
            );
            $stmt->execute([
                $student['admission_no'],
                $student['first_name'],
                $student['last_name'],
                $student['email'],
                $student['phone'],
                $student['gender'],
                $student['date_of_birth'],
                $student['course'],
                $student['address'],
                $student['profile_image'],
                $id,
            ]);
            flash('Student updated successfully.');
        } else {
            $stmt = db()->prepare(
                'INSERT INTO students
                    (admission_no, first_name, last_name, email, phone, gender, date_of_birth, course, address, profile_image)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $student['admission_no'],
                $student['first_name'],
                $student['last_name'],
                $student['email'],
                $student['phone'],
                $student['gender'],
                $student['date_of_birth'],
                $student['course'],
                $student['address'],
                $student['profile_image'],
            ]);
            flash('Student registered successfully.');
        }

        redirect('students.php');
    } catch (Throwable $exception) {
        $error = strpos($exception->getMessage(), 'Duplicate') !== false
            ? 'Admission number or email already exists.'
            : $exception->getMessage();
    }
}

$pageTitle = $id ? 'Edit Student' : 'Register Student';
require __DIR__ . '/partials/header.php';
?>

<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="page-shell">
    <section class="page-heading">
        <div>
            <p class="eyebrow"><?= $id ? 'Update record' : 'New record' ?></p>
            <h1><?= $id ? 'Edit Student' : 'Register Student' ?></h1>
        </div>
        <a class="secondary-button" href="students.php">Back to Students</a>
    </section>

    <?php if ($error): ?>
        <div class="alert danger"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="student-form">
        <section class="form-grid">
            <label>
                Admission Number
                <input name="admission_no" value="<?= e($student['admission_no']) ?>" required>
            </label>
            <label>
                First Name
                <input name="first_name" value="<?= e($student['first_name']) ?>" required>
            </label>
            <label>
                Last Name
                <input name="last_name" value="<?= e($student['last_name']) ?>" required>
            </label>
            <label>
                Email
                <input type="email" name="email" value="<?= e($student['email']) ?>" required>
            </label>
            <label>
                Phone
                <input name="phone" value="<?= e($student['phone']) ?>">
            </label>
            <label>
                Gender
                <select name="gender" required>
                    <?php foreach (['Male', 'Female', 'Other'] as $gender): ?>
                        <option value="<?= e($gender) ?>" <?= $student['gender'] === $gender ? 'selected' : '' ?>><?= e($gender) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Date of Birth
                <input type="date" name="date_of_birth" value="<?= e($student['date_of_birth']) ?>">
            </label>
            <label>
                Course
                <input name="course" value="<?= e($student['course']) ?>" required>
            </label>
            <label class="wide-field">
                Address
                <textarea name="address" rows="4"><?= e($student['address']) ?></textarea>
            </label>
            <label class="wide-field">
                Profile Image
                <input type="file" name="profile_image" accept="image/jpeg,image/png,image/webp">
            </label>
        </section>

        <div class="profile-preview">
            <img src="<?= e($student['profile_image'] ?: 'assets/avatar.svg') ?>" alt="">
            <span><?= $student['profile_image'] ? 'Current profile image' : 'Default profile image' ?></span>
        </div>

        <div class="form-actions">
            <button class="primary-button" type="submit"><?= $id ? 'Save Changes' : 'Register Student' ?></button>
        </div>
    </form>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
