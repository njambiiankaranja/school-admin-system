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

$pageTitle = "Manage Users";
ob_start();

// Fetch users
$users = $conn->query("SELECT * FROM users ORDER BY role, last_name, first_name");

?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Manage Users</h2>
    
    <a href="add_user.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
        Add New User
    </a>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Name</th>
                    <th class="py-2 px-4 border-b">Email</th>
                    <th class="py-2 px-4 border-b">Role</th>
                    <th class="py-2 px-4 border-b">Status</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['role']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['status']); ?></td>
                    <td class="py-2 px-4 border-b">
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
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