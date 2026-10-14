
<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE worker_tasks SET title=?, description=?, due_date=?, status=? WHERE id=?");
    $stmt->bind_param("ssssi", $title, $description, $due_date, $status, $id);
    if ($stmt->execute()) {
        header("Location: manage_tasks.php");
        exit();
    } else {
        $error = "Error updating task.";
    }
}

$stmt = $conn->prepare("SELECT * FROM worker_tasks WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Edit Task</h2>
    <?php if (isset($error)): ?>
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    <form action="edit_task.php?id=<?php echo $id; ?>" method="POST">
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" id="title" name="title" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($task['title']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="description" name="description" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" rows="4" required><?php echo htmlspecialchars($task['description']); ?></textarea>
        </div>
        <div class="mb-4">
            <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
            <input type="date" id="due_date" name="due_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($task['due_date']); ?>">
        </div>
        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="status" name="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                <option value="pending" <?php echo $task['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="in_progress" <?php echo $task['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                <option value="completed" <?php echo $task['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
            Update Task
        </button>
    </form>
</div>




<?php
$content = ob_get_clean();
include 'template.php';
?>