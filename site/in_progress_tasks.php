<?php
session_start();
include 'connect.php';

// Check if user is logged in and has worker role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'worker') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email=? AND role='worker'";
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
$workerId = $user['id'];

$sql = "SELECT * FROM worker_tasks WHERE worker_id=? AND status='in_progress' ORDER BY due_date";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $workerId);
$stmt->execute();
$tasks = $stmt->get_result();

$pageTitle = "In Progress Tasks";
ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">In Progress Tasks</h2>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php while ($task = $tasks->fetch_assoc()): ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($task['title']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($task['description']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($task['due_date']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <form action="mark_task.php" method="get" class="inline">
                        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                        <select name="status" class="border border-gray-300 rounded p-1" onchange="this.form.submit()">
                            <option value="" disabled selected>Select Status</option>
                            <option value="pending" <?php if ($task['status'] === 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="completed" <?php if ($task['status'] === 'completed') echo 'selected'; ?>>Completed</option>
                        </select>
                    </form>
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
