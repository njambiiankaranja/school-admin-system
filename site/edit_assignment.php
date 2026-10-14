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

// Handle assignment update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_assignment'])) {
    $assignmentId = $_POST['assignment_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $dueDate = $_POST['due_date'];
    $totalPoints = $_POST['total_points'];

    $sqlUpdate = "UPDATE assignments 
                  SET title=?, description=?, due_date=?, total_points=? 
                  WHERE id=?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("sssii", $title, $description, $dueDate, $totalPoints, $assignmentId);
    $stmtUpdate->execute();
    header("Location: assignments.php?success=updated");
    exit();
}

// Fetch assignment details
$assignmentId = $_GET['id'];
$sqlAssignment = "SELECT * FROM assignments WHERE id=?";
$stmtAssignment = $conn->prepare($sqlAssignment);
$stmtAssignment->bind_param("i", $assignmentId);
$stmtAssignment->execute();
$resultAssignment = $stmtAssignment->get_result();

if ($resultAssignment->num_rows === 0) {
    header("Location: assignments.php?error=notfound");
    exit();
}

$assignment = $resultAssignment->fetch_assoc();

$pageTitle = "Edit Assignment";
ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Edit Assignment</h2>

    <form action="edit_assignment.php" method="POST">
        <input type="hidden" name="assignment_id" value="<?php echo htmlspecialchars($assignment['id']); ?>">
        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-bold mb-2">Title</label>
            <input type="text" name="title" id="title" class="form-input mt-1 block w-full" value="<?php echo htmlspecialchars($assignment['title']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" id="description" class="form-textarea mt-1 block w-full" required><?php echo htmlspecialchars($assignment['description']); ?></textarea>
        </div>
        <div class="mb-4">
            <label for="due_date" class="block text-gray-700 font-bold mb-2">Due Date</label>
            <input type="date" name="due_date" id="due_date" class="form-input mt-1 block w-full" value="<?php echo htmlspecialchars($assignment['due_date']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="total_points" class="block text-gray-700 font-bold mb-2">Total Points</label>
            <input type="number" name="total_points" id="total_points" class="form-input mt-1 block w-full" value="<?php echo htmlspecialchars($assignment['total_points']); ?>" required>
        </div>
        <button type="submit" name="update_assignment" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">Update Assignment</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
