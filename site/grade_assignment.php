<?php
session_start();
include 'connect.php';

// Check if user is logged in and has lecturer role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: index.php?error=unauthorized");
    exit();
}

// Check if assignment ID is provided
if (!isset($_GET['assignment_id'])) {
    header("Location: assignments.php?error=missing_parameters");
    exit();
}

$assignmentId = intval($_GET['assignment_id']);

// Fetch submissions for the assignment
$sqlSubmissions = "SELECT s.id, s.student_id, s.file_path, s.comments
                    FROM submissions s
                    WHERE s.assignment_id = ?";
$stmtSubmissions = $conn->prepare($sqlSubmissions);
if (!$stmtSubmissions) {
    die("Database prepare error: " . $conn->error);
}
$stmtSubmissions->bind_param("i", $assignmentId);
$stmtSubmissions->execute();
$resultSubmissions = $stmtSubmissions->get_result();

// Handle grading
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submissionId = intval($_POST['submission_id']);
    $pointsEarned = $_POST['points_earned'];
    $feedback = $_POST['feedback'];

    // Validate input
    if (empty($pointsEarned) || !is_numeric($pointsEarned)) {
        $error = "Invalid points.";
    } else {
        $sqlGrade = "INSERT INTO grades (submission_id, points_earned, feedback, graded_by) 
                     VALUES (?, ?, ?, ?) 
                     ON DUPLICATE KEY UPDATE points_earned=?, feedback=?, graded_by=?, graded_at=NOW()";
        $stmtGrade = $conn->prepare($sqlGrade);
        if (!$stmtGrade) {
            die("Database prepare error: " . $conn->error);
        }
        $stmtGrade->bind_param("idssiis", $submissionId, $pointsEarned, $feedback, $_SESSION['id'], $pointsEarned, $feedback, $_SESSION['id']);
        $stmtGrade->execute();

        header("Location: assignments.php?success=graded");
        exit();
    }
}

$pageTitle = "Grade Submissions";
ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Grade Submissions</h2>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 text-red-800 p-4 mb-6 rounded-lg">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($resultSubmissions->num_rows > 0): ?>
        <?php while ($submission = $resultSubmissions->fetch_assoc()): ?>
            <div class="bg-gray-100 shadow-md rounded-lg p-4 mb-6 border border-gray-200">
                <h3 class="text-lg font-bold mb-2">Submission ID: <?php echo htmlspecialchars($submission['id']); ?></h3>
                <p class="text-gray-600 mb-2">Student ID: <?php echo htmlspecialchars($submission['student_id']); ?></p>
                <p class="text-gray-600 mb-2">File Path: <a href="<?php echo htmlspecialchars($submission['file_path']); ?>" class="text-blue-500 hover:underline" target="_blank">View File</a></p>
                <p class="text-gray-600 mb-2">Comments: <?php echo htmlspecialchars($submission['comments']); ?></p>

                <form method="post" class="space-y-4 mt-4">
                    <input type="hidden" name="submission_id" value="<?php echo htmlspecialchars($submission['id']); ?>" />
                    
                    <div>
                        <label for="points_earned" class="block text-sm font-medium text-gray-700">Points Earned</label>
                        <input type="number" id="points_earned" name="points_earned" step="0.01" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" />
                    </div>
                    
                    <div>
                        <label for="feedback" class="block text-sm font-medium text-gray-700">Feedback</label>
                        <textarea id="feedback" name="feedback" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                    </div>
                    
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">Submit Grade</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No submissions found.</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
