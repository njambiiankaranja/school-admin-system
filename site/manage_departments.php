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

$pageTitle = "Manage Departments";
ob_start();

// Fetch departments
$departments = $conn->query("SELECT d.*, u.first_name, u.last_name FROM departments d LEFT JOIN users u ON d.head_id = u.id ORDER BY d.name");

?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Manage Departments</h2>
    
    <a href="add_department.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Add New Department
    </a>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Name</th>
                    <th class="py-2 px-4 border-b">Code</th>
                    <th class="py-2 px-4 border-b">Head</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($dept = $departments->fetch_assoc()): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($dept['name']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($dept['code']); ?></td>
                    <td class="py-2 px-4 border-b">
                        <?php echo $dept['head_id'] ? htmlspecialchars($dept['first_name'] . ' ' . $dept['last_name']) : 'Not Assigned'; ?>
                    </td>
                    <td class="py-2 px-4 border-b">
                        <a href="edit_department.php?id=<?php echo $dept['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                        |
                        <a href="delete_department.php?id=<?php echo $dept['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this department?')">Delete</a>
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