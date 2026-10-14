
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

if($result->num_rows === 0){
    // If no matching student user found, log out and redirect
    session_destroy();
    header("Location: index.php?error=unauthorized");
    exit();
}

$user = $result->fetch_assoc();
$studentId = $user['id'];
$firstName = htmlspecialchars($user['first_name']);
$lastName = htmlspecialchars($user['last_name']);
$pageTitle = "Student Dashboard";

// Fetch current enrollments
$currentEnrollments = $conn->query("
    SELECT c.name, co.semester, co.year, e.status
    FROM enrollments e
    JOIN course_offerings co ON e.course_offering_id = co.id
    JOIN courses c ON co.course_id = c.id
    WHERE e.student_id = $studentId AND co.year = YEAR(CURDATE())
    ORDER BY co.semester DESC
");

// Fetch upcoming assignments
$upcomingAssignments = $conn->query("
    SELECT a.title, a.due_date, c.name as course_name
    FROM assignments a
    JOIN course_offerings co ON a.course_offering_id = co.id
    JOIN courses c ON co.course_id = c.id
    JOIN enrollments e ON co.id = e.course_offering_id
    WHERE e.student_id = $studentId AND a.due_date > CURDATE()
    ORDER BY a.due_date ASC
    LIMIT 5
");

// Fetch recent grades
$recentGrades = $conn->query("
    SELECT g.points_earned, a.total_points, a.title, c.name as course_name
    FROM grades g
    JOIN submissions s ON g.submission_id = s.id
    JOIN assignments a ON s.assignment_id = a.id
    JOIN course_offerings co ON a.course_offering_id = co.id
    JOIN courses c ON co.course_id = c.id
    WHERE s.student_id = $studentId
    ORDER BY g.graded_at DESC
    LIMIT 5
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
            <h2 class="text-2xl font-bold mb-4">Welcome, <?php echo $firstName; ?>!</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-xl font-semibold mb-2">Current Enrollments</h3>
                    <ul class="space-y-2">
                        <?php while ($enrollment = $currentEnrollments->fetch_assoc()): ?>
                            <li class="bg-blue-50 p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold"><?php echo htmlspecialchars($enrollment['name']); ?></h4>
                                <p class="text-sm"><?php echo $enrollment['semester'] . ' ' . $enrollment['year']; ?></p>
                                <p class="text-xs text-gray-500">Status: <?php echo ucfirst($enrollment['status']); ?></p>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-xl font-semibold mb-2">Upcoming Assignments</h3>
                    <ul class="space-y-2">
                        <?php while ($assignment = $upcomingAssignments->fetch_assoc()): ?>
                            <li class="bg-yellow-50 p-4 rounded-lg shadow-sm">
                                <h4 class="font-semibold"><?php echo htmlspecialchars($assignment['title']); ?></h4>
                                <p class="text-sm"><?php echo htmlspecialchars($assignment['course_name']); ?></p>
                                <p class="text-xs text-gray-500">Due: <?php echo $assignment['due_date']; ?></p>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
            
            <div class="mt-6">
                <h3 class="text-xl font-semibold mb-2">Recent Grades</h3>
                <ul class="space-y-2">
                    <?php while ($grade = $recentGrades->fetch_assoc()): ?>
                        <li class="bg-green-50 p-4 rounded-lg shadow-sm">
                            <h4 class="font-semibold"><?php echo htmlspecialchars($grade['title']); ?></h4>
                            <p class="text-sm"><?php echo htmlspecialchars($grade['course_name']); ?></p>
                            <p class="text-sm">Score: <?php echo $grade['points_earned'] . ' / ' . $grade['total_points']; ?></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            
            
        </div>
    </div>
</body>
</html>
<?php
$content = ob_get_clean();
include 'template.php';
?>
