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

// Count tasks
$sql = "SELECT COUNT(*) AS total FROM worker_tasks WHERE worker_id=? AND status='pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $workerId);
$stmt->execute();
$pendingTasksCount = $stmt->get_result()->fetch_assoc()['total'];

$sql = "SELECT COUNT(*) AS total FROM worker_tasks WHERE worker_id=? AND status='in_progress'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $workerId);
$stmt->execute();
$inProgressTasksCount = $stmt->get_result()->fetch_assoc()['total'];

$sql = "SELECT COUNT(*) AS total FROM worker_tasks WHERE worker_id=? AND status='completed'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $workerId);
$stmt->execute();
$completedTasksCount = $stmt->get_result()->fetch_assoc()['total'];

$pageTitle = "Worker Dashboard";
ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h2>
    <p class="mb-4">Here's an overview of your work tasks:</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-100 p-4 rounded-lg">
            <h3 class="font-bold text-lg mb-2">Pending Tasks</h3>
            <p class="text-3xl font-bold"><?php echo htmlspecialchars($pendingTasksCount); ?></p>
            <a href="assigned_tasks.php" class="text-blue-600 hover:underline">View Pending Tasks</a>
        </div>
        <div class="bg-yellow-100 p-4 rounded-lg">
            <h3 class="font-bold text-lg mb-2">In Progress Tasks</h3>
            <p class="text-3xl font-bold"><?php echo htmlspecialchars($inProgressTasksCount); ?></p>
            <a href="in_progress_tasks.php" class="text-yellow-600 hover:underline">View In Progress Tasks</a>
        </div>
        <div class="bg-green-100 p-4 rounded-lg">
            <h3 class="font-bold text-lg mb-2">Completed Tasks</h3>
            <p class="text-3xl font-bold"><?php echo htmlspecialchars($completedTasksCount); ?></p>
            <a href="completed_tasks.php" class="text-green-600 hover:underline">View Completed Tasks</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
