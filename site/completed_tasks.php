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

$sql = "SELECT * FROM worker_tasks WHERE worker_id=? AND status='completed' ORDER BY completed_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $workerId);
$stmt->execute();
$tasks = $stmt->get_result();

$pageTitle = "Completed Tasks";
ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Completed Tasks</h2>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php while ($task = $tasks->fetch_assoc()): ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($task['title']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($task['description']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($task['due_date']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($task['completed_date']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
