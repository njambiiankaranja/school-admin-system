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

$pageTitle = "Add User";
ob_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $status = $_POST['status'];

    $sql = "INSERT INTO users (first_name, last_name, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $password, $role, $status);

    if ($stmt->execute()) {
        header("Location: manage_users.php?success=1");
        exit();
    } else {
        $error = "Error adding user: " . $conn->error;
    }
}
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Add User</h2>
    
    <?php if (isset($error)): ?>
        <p class="text-red-500 mb-4"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="add_user.php" method="post" class="space-y-4">
        <div>
            <label for="first_name" class="block mb-1">First Name:</label>
            <input type="text" id="first_name" name="first_name" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="last_name" class="block mb-1">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="email" class="block mb-1">Email:</label>
            <input type="email" id="email" name="email" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="password" class="block mb-1">Password:</label>
            <input type="password" id="password" name="password" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="role" class="block mb-1">Role:</label>
            <select id="role" name="role" required class="w-full px-3 py-2 border rounded">
                <option value="student">Student</option>
                <option value="lecturer">Lecturer</option>
                <option value="admin">Admin</option>
                <option value="worker">Worker</option>
            </select>
        </div>
        <div>
            <label for="status" class="block mb-1">Status:</label>
            <select id="status" name="status" required class="w-full px-3 py-2 border rounded">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Add User</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>