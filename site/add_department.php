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

$pageTitle = "Add Department";
ob_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $code = $_POST['code'];
    $head_id = $_POST['head_id'] ? $_POST['head_id'] : null;

    $sql = "INSERT INTO departments (name, code, head_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $code, $head_id);

    if ($stmt->execute()) {
        header("Location: manage_departments.php?success=1");
        exit();
    } else {
        $error = "Error adding department: " . $conn->error;
    }
}

// Fetch lecturers for department head selection
$lecturers = $conn->query("SELECT id, first_name, last_name FROM users WHERE role = 'lecturer' ORDER BY last_name, first_name");
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Add Department</h2>
    
    <?php if (isset($error)): ?>
        <p class="text-red-500 mb-4"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="add_department.php" method="post" class="space-y-4">
        <div>
            <label for="name" class="block mb-1">Department Name:</label>
            <input type="text" id="name" name="name" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="code" class="block mb-1">Department Code:</label>
            <input type="text" id="code" name="code" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="head_id" class="block mb-1">Department Head:</label>
            <select id="head_id" name="head_id" class="w-full px-3 py-2 border rounded">
                <option value="">Select Department Head</option>
                <?php while ($lecturer = $lecturers->fetch_assoc()): ?>
                    <option value="<?php echo $lecturer['id']; ?>"><?php echo htmlspecialchars($lecturer['first_name'] . ' ' . $lecturer['last_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Add Department</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>