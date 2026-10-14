


<?php
session_start();
include 'connect.php';



// Check if user is logged in and has admin role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email=? AND role='admin'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    // If no matching admin user found, log out and redirect
    session_destroy();
    header("Location: index.php?error=unauthorized");
    exit();
}


$user = $result->fetch_assoc();
$firstName = htmlspecialchars($user['first_name']);
$lastName = htmlspecialchars($user['last_name']);

// Fetch summary data
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$activeCourses = $conn->query("SELECT COUNT(*) FROM course_offerings WHERE year = YEAR(CURDATE())")->fetch_row()[0];
$pendingTasks = $conn->query("SELECT COUNT(*) FROM worker_tasks WHERE status = 'pending'")->fetch_row()[0];

$pageTitle = "Admin Dashboard";
ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Welcome, Admin <?php echo $firstName; ?>!</h2>
    <p class="mb-4">Here's an overview of your administrative tasks:</p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-blue-100 p-4 rounded-lg">
            <h3 class="font-bold text-lg mb-2">Total Users</h3>
            <p class="text-3xl font-bold"><?php echo $totalUsers; ?></p>
        </div>
        <div class="bg-green-100 p-4 rounded-lg">
            <h3 class="font-bold text-lg mb-2">Active Courses</h3>
            <p class="text-3xl font-bold"><?php echo $activeCourses; ?></p>
        </div>
        <div class="bg-yellow-100 p-4 rounded-lg">
            <h3 class="font-bold text-lg mb-2">Pending Tasks</h3>
            <p class="text-3xl font-bold"><?php echo $pendingTasks; ?></p>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>