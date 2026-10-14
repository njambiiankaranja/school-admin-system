<?php
session_start();
include 'connect.php';

// Check if user is logged in and has lecturer role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$email = $_SESSION['email'];
$sqlLecturer = "SELECT id FROM users WHERE email=? AND role='lecturer'";
$stmtLecturer = $conn->prepare($sqlLecturer);
if (!$stmtLecturer) {
    die("Database prepare error: " . $conn->error);
}
$stmtLecturer->bind_param("s", $email);
$stmtLecturer->execute();
$resultLecturer = $stmtLecturer->get_result();

if ($resultLecturer->num_rows === 0) {
    session_destroy();
    header("Location: index.php?error=unauthorized");
    exit();
}

$lecturerId = $resultLecturer->fetch_assoc()['id'];

// Handle deletion of assignment
if (isset($_GET['delete'])) {
    $assignmentId = intval($_GET['delete']);
    $sqlDelete = "DELETE FROM assignments WHERE id=?";
    $stmtDelete = $conn->prepare($sqlDelete);
    if (!$stmtDelete) {
        die("Database prepare error: " . $conn->error);
    }
    $stmtDelete->bind_param("i", $assignmentId);
    $stmtDelete->execute();
    header("Location: assignments.php?success=deleted");
    exit();
}

// Fetch existing assignments for courses the lecturer is teaching
$sqlAssignments = "SELECT a.id, a.title, a.due_date, a.total_points, c.code, c.name
                    FROM assignments a
                    JOIN course_offerings co ON a.course_offering_id = co.id
                    JOIN courses c ON a.course_offering_id = c.id
                    WHERE co.lecturer_id = ?";
$stmtAssignments = $conn->prepare($sqlAssignments);
if (!$stmtAssignments) {
    die("Database prepare error: " . $conn->error);
}
$stmtAssignments->bind_param("i", $lecturerId);
$stmtAssignments->execute();
$resultAssignments = $stmtAssignments->get_result();

$pageTitle = "Manage Assignments";
ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Manage Assignments</h2>

    <a href="add_assignment.php" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 mb-6 inline-block">Add New Assignment</a>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
        <div class="bg-red-100 text-red-800 p-4 mb-6 rounded-lg">
            Assignment deleted successfully.
        </div>
    <?php endif; ?>

    <?php if ($resultAssignments->num_rows > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php while ($assignment = $resultAssignments->fetch_assoc()): ?>
                <div class="bg-white shadow-md rounded-lg p-4 border border-gray-200">
                    <h3 class="text-lg font-bold mb-2"><?php echo htmlspecialchars($assignment['title']); ?></h3>
                    <p class="text-gray-600 mb-2">Course: <?php echo htmlspecialchars($assignment['code']) . ' - ' . htmlspecialchars($assignment['name']); ?></p>
                    <p class="text-gray-600 mb-2">Due Date: <?php echo htmlspecialchars($assignment['due_date']); ?></p>
                    <p class="text-gray-600 mb-2">Total Points: <?php echo htmlspecialchars($assignment['total_points']); ?></p>
                    <div class="mt-4">
                        <a href="edit_assignment.php?id=<?php echo htmlspecialchars($assignment['id']); ?>" class="text-blue-500 hover:underline">Edit</a>
                        <a href="assignments.php?delete=<?php echo htmlspecialchars($assignment['id']); ?>" class="text-red-500 hover:underline ml-4" onclick="return confirm('Are you sure you want to delete this assignment?');">Delete</a>
                        <a href="grade_assignment.php?assignment_id=<?php echo htmlspecialchars($assignment['id']); ?>" class="text-green-500 hover:underline ml-4">Grade Submissions</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No assignments found.</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
