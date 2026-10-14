<?php
session_start();
include 'connect.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $credits = $_POST['credits'];
    $departmentId = $_POST['department_id'];

    // Validate inputs
    if (empty($code) || empty($name) || empty($credits) || empty($departmentId)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($credits)) {
        $error = "Credits must be a number.";
    } else {
        // Check if course code already exists
        $sqlCheck = "SELECT * FROM courses WHERE code=?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("s", $code);
        $stmtCheck->execute();
        $exists = $stmtCheck->get_result()->num_rows > 0;

        if ($exists) {
            $error = "Course code already exists.";
        } else {
            // Insert new course
            $sqlInsert = "INSERT INTO courses (code, name, description, credits, department_id) VALUES (?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("sssis", $code, $name, $description, $credits, $departmentId);

            if ($stmtInsert->execute()) {
                $success = "Course added successfully.";
            } else {
                $error = "Error adding course: " . $conn->error;
            }
        }
    }
}

// Fetch departments for the dropdown
$sqlDepartments = "SELECT id, name FROM departments";
$stmtDepartments = $conn->prepare($sqlDepartments);
$stmtDepartments->execute();
$departmentsResult = $stmtDepartments->get_result();

$pageTitle = "Add Course";
ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Add New Course</h2>
    <?php if (isset($success)): ?>
    <div class="bg-green-100 text-green-700 p-4 rounded mb-4"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
    <div class="bg-red-100 text-red-700 p-4 rounded mb-4"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form action="add_course.php" method="POST" class="space-y-4">
        <div>
            <label for="code" class="block text-gray-700">Course Code</label>
            <input type="text" id="code" name="code" class="border border-gray-300 rounded p-2 w-full" required>
        </div>
        <div>
            <label for="name" class="block text-gray-700">Course Name</label>
            <input type="text" id="name" name="name" class="border border-gray-300 rounded p-2 w-full" required>
        </div>
        <div>
            <label for="description" class="block text-gray-700">Description</label>
            <textarea id="description" name="description" rows="4" class="border border-gray-300 rounded p-2 w-full"></textarea>
        </div>
        <div>
            <label for="credits" class="block text-gray-700">Credits</label>
            <input type="number" id="credits" name="credits" class="border border-gray-300 rounded p-2 w-full" required>
        </div>
        <div>
            <label for="department_id" class="block text-gray-700">Department</label>
            <select id="department_id" name="department_id" class="border border-gray-300 rounded p-2 w-full" required>
                <option value="">Select Department</option>
                <?php while ($department = $departmentsResult->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($department['id']); ?>"><?php echo htmlspecialchars($department['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Course</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
