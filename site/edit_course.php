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

$pageTitle = "Edit Course";
ob_start();

if (!isset($_GET['id'])) {
    header("Location: manage_courses.php");
    exit();
}

$course_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $credits = $_POST['credits'];
    $department_id = $_POST['department_id'];

    $sql = "UPDATE courses SET code = ?, name = ?, description = ?, credits = ?, department_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiii", $code, $name, $description, $credits, $department_id, $course_id);

    if ($stmt->execute()) {
        header("Location: manage_courses.php?success=1");
        exit();
    } else {
        $error = "Error updating course: " . $conn->error;
    }
} else {
    $sql = "SELECT * FROM courses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        header("Location: manage_courses.php");
        exit();
    }
    $course = $result->fetch_assoc();
}

// Fetch departments for selection
$departments = $conn->query("SELECT id, name FROM departments ORDER BY name");
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Edit Course</h2>
    
    <?php if (isset($error)): ?>
        <p class="text-red-500 mb-4"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="edit_course.php?id=<?php echo $course_id; ?>" method="post" class="space-y-4">
        <div>
            <label for="code" class="block mb-1">Course Code:</label>
            <input type="text" id="code" name="code" value="<?php echo htmlspecialchars($course['code']); ?>" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="name" class="block mb-1">Course Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($course['name']); ?>" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="description" class="block mb-1">Description:</label>
            <textarea id="description" name="description" class="w-full px-3 py-2 border rounded"><?php echo htmlspecialchars($course['description']); ?></textarea>
        </div>
        <div>
            <label for="credits" class="block mb-1">Credits:</label>
            <input type="number" id="credits" name="credits" value="<?php echo htmlspecialchars($course['credits']); ?>" required class="w-full px-3 py-2 border rounded">
        </div>
        <div>
            <label for="department_id" class="block mb-1">Department:</label>
            <select id="department_id" name="department_id" required class="w-full px-3 py-2 border rounded">
                <option value="">Select Department</option>
                <?php while ($dept = $departments->fetch_assoc()): ?>
                    <option value="<?php echo $dept['id']; ?>" <?php if ($dept['id'] == $course['department_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($dept['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Update Course</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>