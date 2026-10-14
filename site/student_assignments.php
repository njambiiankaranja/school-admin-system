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
$pageTitle = "Student Assignments";

// Fetch assignments
$assignments = $conn->query("
    SELECT a.id, a.title, c.name as course_name
    FROM assignments a
    JOIN course_offerings co ON a.course_offering_id = co.id
    JOIN courses c ON co.course_id = c.id
    JOIN enrollments e ON co.id = e.course_offering_id
    WHERE e.student_id = $studentId
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
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Your Assignments</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if ($assignments->num_rows > 0): ?>
                    <?php while ($assignment = $assignments->fetch_assoc()): ?>
                        <div class="bg-white p-4 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($assignment['title']); ?></h3>
                            <p class="text-sm text-gray-500">Course: <?php echo htmlspecialchars($assignment['course_name']); ?></p>
                            <a href="assignment_details.php?id=<?php echo htmlspecialchars($assignment['id']); ?>" class="mt-2 inline-block bg-blue-500 text-white p-2 rounded-lg shadow hover:bg-blue-600 transition">View Details</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-gray-500">You have no assignments at the moment.</p>
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
