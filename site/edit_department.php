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

$pageTitle = "Edit Department";

ob_start();

if (!isset($_GET['id'])) {
    header("Location: manage_departments.php");
    exit();
}

$dept_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $code = $_POST['code'];
    $head_id = $_POST['head_id'] ? $_POST['head_id'] : null;

    $sql = "UPDATE departments SET name = ?, code = ?, head_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $name, $code, $head_id, $dept_id);

    if ($stmt->execute()) {
        header("Location: manage_departments.php?success=1");
        exit();
    } else {
        $error = "Error updating department: " . $conn->error;
    }
} else {
    $sql = "SELECT * FROM departments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $dept_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        header("Location: manage_departments.php");
        exit();
    }
    $department = $result->fetch_assoc();
}

// Fetch lecturers for department head selection
$lecturers = $conn->query("SELECT id, first_name, last_name FROM users WHERE role = 'lecturer' ORDER BY last_name, first_name");
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Edit Department</h2>
    
    <?php if (isset($error)): ?>
        <p class="text-red-500 mb-4"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="edit_department.php?id=<?php echo $dept_id; ?>" method="post" class="space-y-4">
        <div>
            <label for="name" class="block mb-1">Department Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($department['name']); ?>" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="code" class="block mb-1">Department Code:</label>
            <input type="text" id="code" name="code" value="<?php echo htmlspecialchars($department['code']); ?>" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="head_id" class="block mb-1">Department Head:</label>
            <select id="head_id" name="head_id" class="w-full px-3 py-2 border rounded">
                <option value="">Select Department Head</option>
                <?php while ($lecturer = $lecturers->fetch_assoc()): ?>
                    <option value="<?php echo $lecturer['id']; ?>" <?php if ($lecturer['id'] == $department['head_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($lecturer['first_name'] . ' ' . $lecturer['last_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Update Department</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>