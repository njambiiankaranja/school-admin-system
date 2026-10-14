<?php
session_start();
include 'connect.php';

// Check if user is logged in and has lecturer role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email=? AND role='lecturer'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header("Location: index.php?error=unauthorized");
    exit();
}

$user = $result->fetch_assoc();
$firstName = htmlspecialchars($user['first_name']);
$lastName = htmlspecialchars($user['last_name']);

// Fetch counts for dashboard
$sqlCounts = "SELECT 
                (SELECT COUNT(*) FROM course_offerings WHERE lecturer_id = (SELECT id FROM users WHERE email = ?)) AS active_courses,
                (SELECT COUNT(*) FROM enrollments JOIN course_offerings ON enrollments.course_offering_id = course_offerings.id WHERE course_offerings.lecturer_id = (SELECT id FROM users WHERE email = ?)) AS total_students,
                (SELECT COUNT(*) FROM assignments WHERE course_offering_id IN (SELECT id FROM course_offerings WHERE lecturer_id = (SELECT id FROM users WHERE email = ?)) AND id NOT IN (SELECT assignment_id FROM submissions)) AS pending_grading";

$stmtCounts = $conn->prepare($sqlCounts);
$stmtCounts->bind_param("sss", $email, $email, $email);
$stmtCounts->execute();
$resultCounts = $stmtCounts->get_result();
$counts = $resultCounts->fetch_assoc();

$pageTitle = "Lecturer Dashboard";
ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Welcome, Dr. <?php echo $lastName; ?>!</h2>
    <p class="mb-4">Here's an overview of your teaching activities:</p>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-blue-100 p-4 rounded-lg">
            <h3 class="font-bold text-lg mb-2">Active Courses</h3>
            <p class="text-3xl font-bold"><?php echo htmlspecialchars($counts['active_courses']); ?></p>
        </div>
        <div class="bg-green-100 p-4 rounded-lg">
            <h3 class="font-bold text-lg mb-2">Total Students</h3>
            <p class="text-3xl font-bold"><?php echo htmlspecialchars($counts['total_students']); ?></p>
        </div>
        <div class="bg-yellow-100 p-4 rounded-lg">
            <h3 class="font-bold text-lg mb-2">Pending Grading</h3>
            <p class="text-3xl font-bold"><?php echo htmlspecialchars($counts['pending_grading']); ?></p>
        </div>
    </div>

    
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
