<?php
session_start();
include 'connect.php';

// Check if user is logged in and has lecturer role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$email = $_SESSION['email'];

// Fetch available course offerings for the lecturer
$sqlCourses = "SELECT co.id, c.code, c.name
                FROM course_offerings co
                JOIN courses c ON co.course_id = c.id
                WHERE co.lecturer_id = (SELECT id FROM users WHERE email=?)";
$stmtCourses = $conn->prepare($sqlCourses);
$stmtCourses->bind_param("s", $email);
$stmtCourses->execute();
$resultCourses = $stmtCourses->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $total_points = $_POST['total_points'];
    $course_offering_id = $_POST['course_offering_id'];

    // Validate inputs
    if (empty($title) || empty($due_date) || empty($total_points) || empty($course_offering_id)) {
        $error = "Please fill all required fields.";
    } else {
        // Insert new assignment
        $sqlInsert = "INSERT INTO assignments (course_offering_id, title, description, due_date, total_points) VALUES (?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("isssi", $course_offering_id, $title, $description, $due_date, $total_points);
        if ($stmtInsert->execute()) {
            header("Location: assignments.php?success=added");
            exit();
        } else {
            $error = "Failed to add assignment. Please try again.";
        }
    }
}

$pageTitle = "Add Assignment";
ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Add New Assignment</h2>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 text-red-800 p-4 mb-6 rounded-lg">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="space-y-4">
        <div>
            <label for="title" class="block text-gray-700">Title</label>
            <input type="text" id="title" name="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div>
            <label for="description" class="block text-gray-700">Description</label>
            <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
        </div>
        <div>
            <label for="due_date" class="block text-gray-700">Due Date</label>
            <input type="datetime-local" id="due_date" name="due_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div>
            <label for="total_points" class="block text-gray-700">Total Points</label>
            <input type="number" id="total_points" name="total_points" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div>
            <label for="course_offering_id" class="block text-gray-700">Course Offering</label>
            <select id="course_offering_id" name="course_offering_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Select a course offering</option>
                <?php while ($course = $resultCourses->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($course['id']); ?>">
                        <?php echo htmlspecialchars($course['code']) . ' - ' . htmlspecialchars($course['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">Add Assignment</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
