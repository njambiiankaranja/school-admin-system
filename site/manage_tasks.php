<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$pageTitle = "Manage Tasks";
ob_start();

// Fetch tasks
$tasks = $conn->query("SELECT * FROM worker_tasks ORDER BY due_date");

?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Manage Tasks</h2>
    <a href="create_task.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Create New Task
    </a>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-4 border-b">Title</th>
                    <th class="py-2 px-4 border-b">Description</th>
                    <th class="py-2 px-4 border-b">Due Date</th>
                    <th class="py-2 px-4 border-b">Status</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($task = $tasks->fetch_assoc()): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($task['title']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($task['description']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($task['due_date']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars(ucfirst($task['status'])); ?></td>
                    <td class="py-2 px-4 border-b">
                        <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                        |
                        <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this task?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
