<?php
session_start();
include 'connect.php';

// Check if user is logged in and has lecturer role
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: index.php?error=unauthorized");
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE email=? AND role='lecturer'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header("Location: index.php?error=unauthorized");
    exit();
}

$user = $result->fetch_assoc();

// Handle attendance form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseOfferingId = $_POST['course_offering_id'];
    $date = $_POST['date'];
    $students = $_POST['students'];

    foreach ($students as $studentId => $status) {
        $sqlInsert = "INSERT INTO attendance (course_offering_id, student_id, date, status) 
                      VALUES (?, ?, ?, ?)
                      ON DUPLICATE KEY UPDATE status=?";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("iisss", $courseOfferingId, $studentId, $date, $status, $status);
        $stmtInsert->execute();
    }
    header("Location: attendance.php?success=updated");
    exit();
}

// Fetch courses for dropdown
$sqlCourses = "SELECT course_offerings.id, courses.code, courses.name, course_offerings.semester, course_offerings.year
               FROM course_offerings
               JOIN courses ON course_offerings.course_id = courses.id
               WHERE course_offerings.lecturer_id=?";
$stmtCourses = $conn->prepare($sqlCourses);
$stmtCourses->bind_param("i", $user['id']);
$stmtCourses->execute();
$coursesResult = $stmtCourses->get_result();

// Fetch students for the selected course
$students = [];
if (isset($_GET['course_offering_id'])) {
    $courseOfferingId = $_GET['course_offering_id'];
    $sqlStudents = "SELECT users.id, CONCAT(users.first_name, ' ', users.last_name) AS full_name
                    FROM enrollments
                    JOIN users ON enrollments.student_id = users.id
                    WHERE enrollments.course_offering_id=?";
    $stmtStudents = $conn->prepare($sqlStudents);
    $stmtStudents->bind_param("i", $courseOfferingId);
    $stmtStudents->execute();
    $studentsResult = $stmtStudents->get_result();
    $students = $studentsResult->fetch_all(MYSQLI_ASSOC);
}

$pageTitle = "Manage Attendance";
ob_start();
?>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-4">Manage Attendance</h2>
    <?php if (isset($_GET['success']) && $_GET['success'] === 'updated'): ?>
        <div class="bg-green-100 text-green-800 p-4 mb-6 rounded-lg">
            Attendance records updated successfully.
        </div>
    <?php endif; ?>

    <form action="attendance.php" method="POST" class="mb-6">
        <div class="mb-4">
            <label for="course_offering_id" class="block text-gray-700 font-bold mb-2">Select Course</label>
            <select name="course_offering_id" id="course_offering_id" class="form-select mt-1 block w-full" required>
                <option value="" disabled selected>Select a course</option>
                <?php while ($course = $coursesResult->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($course['id']); ?>">
                        <?php echo htmlspecialchars($course['code']) . ' - ' . htmlspecialchars($course['name']) . ' (' . htmlspecialchars($course['semester']) . ' ' . htmlspecialchars($course['year']) . ')'; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-4">
            <label for="date" class="block text-gray-700 font-bold mb-2">Date</label>
            <input type="date" name="date" id="date" class="form-input mt-1 block w-full" required>
        </div>

        <?php if ($students): ?>
            <div class="mb-4">
                <h3 class="text-lg font-bold mb-2">Student Attendance</h3>
                <?php foreach ($students as $student): ?>
                    <div class="flex items-center mb-2">
                        <input type="hidden" name="students[<?php echo htmlspecialchars($student['id']); ?>]" value="present">
                        <label class="inline-flex items-center mr-4">
                            <input type="radio" name="students[<?php echo htmlspecialchars($student['id']); ?>]" value="present" class="form-radio">
                            <span class="ml-2"><?php echo htmlspecialchars($student['full_name']); ?> - Present</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="students[<?php echo htmlspecialchars($student['id']); ?>]" value="absent" class="form-radio">
                            <span class="ml-2"><?php echo htmlspecialchars($student['full_name']); ?> - Absent</span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">Submit Attendance</button>
        <?php else: ?>
            <p>No students found for the selected course.</p>
        <?php endif; ?>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'template.php';
?>
