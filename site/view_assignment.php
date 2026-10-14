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

if($result->num_rows === 0){
    session_destroy();
    header("Location: index.php?error=unauthorized");
    exit();
}

$user = $result->fetch_assoc();
$lecturerId = $user['id'];

$assignmentId = $_GET['id'];

$pageTitle = "Assignment Details";
ob_start();

// Fetch assignment details
$sqlAssignment = "SELECT assignments.*, courses.name AS course_name 
                   FROM assignments 
                   JOIN course_offerings ON assignments.course_offering_id = course_offerings.id 
                   JOIN courses ON course_offerings.course_id = courses.id 
                   WHERE assignments.id=?";
$stmt = $conn->prepare($sqlAssignment);
$stmt->bind_param("i", $assignmentId);
$stmt->execute();
$assignmentResult = $stmt->get_result()->fetch_assoc();

// Fetch submissions
$sqlSubmissions = "SELECT submissions.*, CONCAT(users.first_name, ' ', users.last_name) AS student_name 
                    FROM submissions 
                    JOIN users ON submissions.student_id = users.id 
                    WHERE assignment_id=?";
$stmt = $conn->prepare($sqlSubmissions);
$stmt->bind_param("i", $assignmentId);
$stmt->execute();
$submissionsResult = $stmt->get_result();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($assignmentResult['title']); ?></h2>
    <p class="mb-4"><strong>Course:</strong> <?php echo htmlspecialchars($assignmentResult['course_name']); ?></p>
    <p class="mb-4"><strong>Description:</strong> <?php echo htmlspecialchars($assignmentResult['description']); ?></p>
    <p class="mb-4"><strong>Due Date:</strong> <?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($assignmentResult['due_date']))); ?></p>
    
    <h3 class="text-xl font-bold mt-6 mb-4">Submissions</h3>
    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-4 border-b">Student</th>
                <th class="py-2 px-4 border-b">Submission Date</th>
                <th class="py-2 px-4 border-b">File</th>
                <th class="py-2 px-4 border-b">Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($submission = $submissionsResult->fetch_assoc()): ?>
            <tr>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($submission['student_name']); ?></td>
                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($submission['submission_date']))); ?></td>
                <td class="py-2 px-4 border-b"><a href="<?php echo htmlspecialchars($submission['file_path']); ?>" class="text-blue-500 hover:underline" target="_blank">View File</a></td>
                <td class="py-2 px-4 border-b">
                    <?php
                    // Check if grade exists for this submission
                    $submissionId = $submission['id'];
                    $sqlGrade = "SELECT points_earned FROM grades WHERE submission_id=?";
                    $stmt = $conn->prepare($sqlGrade);
                    $stmt->bind_param("i", $submissionId);
                    $stmt->execute();
                    $gradeResult = $stmt->get_result()->fetch_assoc();
                    echo htmlspecialchars($gradeResult ? $gradeResult['points_earned'] : 'Not Graded');
                    ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
