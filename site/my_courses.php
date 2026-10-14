<?php
session_start();
include 'connect.php';

// Check if user is logged in and has student role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email=? AND role='student'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // If no matching student user found, log out and redirect
    session_destroy();
    header("Location: index.php?error=unauthorized");
    exit();
}

$user = $result->fetch_assoc();
$studentId = $user['id'];
$firstName = htmlspecialchars($user['first_name']);
$lastName = htmlspecialchars($user['last_name']);
$pageTitle = "My Courses";

// Fetch current enrollments
$currentEnrollments = $conn->query("
    SELECT c.name, co.semester, co.year
    FROM enrollments e
    JOIN course_offerings co ON e.course_offering_id = co.id
    JOIN courses c ON co.course_id = c.id
    WHERE e.student_id = $studentId AND co.year = YEAR(CURDATE())
    ORDER BY co.semester DESC
");

ob_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.3/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">Courses for <?php echo $firstName . ' ' . $lastName; ?></h2>

            <div class="space-y-4">
                <?php if ($currentEnrollments->num_rows > 0): ?>
                    <ul class="space-y-2">
                        <?php while ($enrollment = $currentEnrollments->fetch_assoc()): ?>
                            <li class="bg-blue-50 p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold"><?php echo htmlspecialchars($enrollment['name']); ?></h4>
                                <p class="text-sm"><?php echo htmlspecialchars($enrollment['semester']) . ' ' . htmlspecialchars($enrollment['year']); ?></p>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-500">You are not enrolled in any courses for this academic year.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
<?php
$content = ob_get_clean();
include 'template.php';
?>
