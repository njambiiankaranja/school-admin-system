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

$pageTitle = "Edit User";
ob_start();

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $first_name, $last_name, $email, $role, $status, $user_id);

    if ($stmt->execute()) {
        header("Location: manage_users.php?success=1");
        exit();
    } else {
        $error = "Error updating user: " . $conn->error;
    }
} else {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        header("Location: manage_users.php");
        exit();
    }
    $user = $result->fetch_assoc();
}
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Edit User</h2>
    
    <?php if (isset($error)): ?>
        <p class="text-red-500 mb-4"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="edit_user.php?id=<?php echo $user_id; ?>" method="post" class="space-y-4">
        <div>
            <label for="first_name" class="block mb-1">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="last_name" class="block mb-1">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="email" class="block mb-1">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="role" class="block mb-1">Role:</label>
            <select id="role" name="role" required class="w-full px-3 py-2 border rounded">
                <option value="student" <?php if ($user['role'] == 'student') echo 'selected'; ?>>Student</option>
                <option value="lecturer" <?php if ($user['role'] == 'lecturer') echo 'selected'; ?>>Lecturer</option>
                <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                <option value="worker" <?php if ($user['role'] == 'worker') echo 'selected'; ?>>Worker</option>
            </select>
        </div>
        <div>
            <label for="status" class="block mb-1">Status:</label>
            <select id="status" name="status" required class="w-full px-3 py-2 border rounded">
                <option value="active" <?php if ($user['status'] == 'active') echo 'selected'; ?>>Active</option>
                <option value="inactive" <?php if ($user['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                <option value="suspended" <?php if ($user['status'] == 'suspended') echo 'selected'; ?>>Suspended</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Update User</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>